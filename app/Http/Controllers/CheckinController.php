<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOffDay;
use App\Models\AttendanceSchedule;
use App\Models\OfficeLocation;
use App\Models\Notification;
use App\Models\User;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckInController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index()
    {
        $user = Auth::user();
        
        $todayAttendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('attendance_date', today())
                                    ->first();
        
        return view('home.checkin', compact('todayAttendance'));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();
        
        $todayAttendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('attendance_date', today())
                                    ->first();
        
        $officeLocation = OfficeLocation::getDefaultLocation();
        
        if (!$officeLocation) {
            return redirect()->route('checkin')
                           ->with('error', 'No office location configured. Please contact administrator.');
        }
        
        return view('verify.map', compact('todayAttendance', 'officeLocation'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'action' => 'required|in:checkin,checkout',
            'session' => 'required|in:morning,afternoon',
            'note' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        $officeLocation = OfficeLocation::getDefaultLocation();
        
        if (!$officeLocation) {
            return redirect()->route('checkin')
                           ->with('error', 'No office location configured. Please contact administrator.');
        }
        
        // Verify location is within allowed radius
        $distance = $officeLocation->calculateDistance(
            $request->latitude,
            $request->longitude
        );

        if (!$officeLocation->isWithinRadius($request->latitude, $request->longitude)) {
            // Send location alert to admins
            try {
                $alertMessage = $this->telegram->sendLocationAlert($user, $distance, $officeLocation);
                $this->telegram->notifyAdmins($alertMessage);
                
                // Create notification for admins about location violation
                $this->createLocationAlertNotifications($user, $distance, $officeLocation);
            } catch (\Exception $e) {
                Log::error('Failed to send location alert: ' . $e->getMessage());
            }
            
            return redirect()->route('checkin')
                           ->with('error', "You are {$distance}m away from {$officeLocation->name}. You must be within {$officeLocation->radius}m to check in.");
        }

        if ($request->action === 'checkin') {
            return $this->processCheckIn($request, $officeLocation);
        } else {
            return $this->processCheckOut($request, $officeLocation);
        }
    }

    private function processCheckIn(Request $request, OfficeLocation $officeLocation)
    {
        $user = Auth::user();
        $session = $request->session;
        
        $existingAttendance = Attendance::where('user_id', $user->id)
                                       ->whereDate('attendance_date', today())
                                       ->first();
        
        if ($existingAttendance) {
            if ($session === 'morning' && $existingAttendance->morning_check_in) {
                return redirect()->route('checkin')->with('error', 'You have already checked in for the morning session.');
            }
            if ($session === 'afternoon' && $existingAttendance->afternoon_check_in) {
                return redirect()->route('checkin')->with('error', 'You have already checked in for the afternoon session.');
            }
        }

        $isDayOff = AttendanceOffDay::where('user_id', $user->id)
                                   ->whereDate('off_date', today())
                                   ->exists();

        if ($isDayOff) {
            return redirect()->route('checkin')->with('error', 'Today is your scheduled day off.');
        }

        // Check time restrictions
        $currentTime = now()->format('H:i');
        $hasNote = !empty($request->note);
        $timeViolation = false;
        $violationReason = '';
        
        if ($session === 'morning' && $currentTime > '09:00') {
            $timeViolation = true;
            $violationReason = "Late check-in at {$currentTime} (allowed until 09:00)";
            
            if (!$hasNote) {
                return redirect()->route('checkin')
                               ->with('error', 'Morning check-in after 9:00 AM requires a note/reason. Please provide a note to proceed.');
            }
        }
        
        if ($session === 'afternoon' && $currentTime > '15:00') {
            $timeViolation = true;
            $violationReason = "Late check-in at {$currentTime} (allowed until 15:00)";
            
            if (!$hasNote) {
                return redirect()->route('checkin')
                               ->with('error', 'Afternoon check-in after 3:00 PM requires a note/reason. Please provide a note to proceed.');
            }
        }

        $schedule = AttendanceSchedule::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->first();

        $status = 'on_time';
        if ($schedule) {
            $checkInTime = now();
            
            if ($session === 'morning') {
                $scheduledTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $schedule->scheduled_check_in_morining);
            } else {
                $scheduledTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $schedule->scheduled_check_in_afternoon);
            }
            
            $lateThreshold = $scheduledTime->copy()->addMinutes($schedule->late_allowed_min);

            if ($checkInTime->gt($lateThreshold)) {
                $status = 'late';
            }
        }

        // Override status if there's a time violation
        if ($timeViolation) {
            $status = 'late';
        }

        $checkInField = $session === 'morning' ? 'morning_check_in' : 'afternoon_check_in';
        
        $attendanceData = [
            'user_id' => $user->id,
            'attendance_date' => today(),
        ];

        // Prepare note with violation reason if applicable
        $finalNote = $request->note;
        if ($timeViolation && $hasNote) {
            $finalNote = "[{$violationReason}] {$request->note}";
        }

        $updateData = [
            $checkInField => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'note' => $finalNote,
        ];

        if (!$existingAttendance || $existingAttendance->status !== 'late') {
            $updateData['status'] = $status;
        }

        $attendance = Attendance::updateOrCreate($attendanceData, $updateData);
        $attendance->refresh();

        $this->sendCheckInNotifications($user, $attendance, $officeLocation, $schedule, $session);
        $this->createCheckInNotifications($user, $attendance, $officeLocation, $session);

        $sessionName = ucfirst($session);
        $successMessage = "{$sessionName} check-in successful at {$officeLocation->name}!";
        
        if ($timeViolation) {
            $successMessage .= " (Late check-in recorded with reason)";
        } else {
            $successMessage .= " Status: " . ucfirst(str_replace('_', ' ', $status));
        }
        
        return redirect()->route('checkin')->with('success', $successMessage);
    }

    private function processCheckOut(Request $request, OfficeLocation $officeLocation)
    {
        $user = Auth::user();
        $session = $request->session;
        
        $attendance = Attendance::where('user_id', $user->id)
                               ->whereDate('attendance_date', today())
                               ->first();
        
        $checkInField = $session === 'morning' ? 'morning_check_in' : 'afternoon_check_in';
        $checkOutField = $session === 'morning' ? 'morning_check_out' : 'afternoon_check_out';
        
        if (!$attendance || !$attendance->$checkInField) {
            $sessionName = ucfirst($session);
            return redirect()->route('checkin')->with('error', "You must check in for the {$session} session first.");
        }

        if ($attendance->$checkOutField) {
            $sessionName = ucfirst($session);
            return redirect()->route('checkin')->with('error', "You have already checked out for the {$session} session.");
        }

        // Check early checkout restrictions
        $currentTime = now()->format('H:i');
        $hasNote = !empty($request->note);
        $earlyCheckout = false;
        $checkoutReason = '';
        
        if ($session === 'morning' && $currentTime < '11:00') {
            $earlyCheckout = true;
            $checkoutReason = "Early check-out at {$currentTime} (minimum 11:00)";
            
            if (!$hasNote) {
                return redirect()->route('checkin')
                               ->with('error', 'Morning check-out before 11:00 AM requires a note/reason. Please provide a note to proceed or you will be marked absent.');
            }
        }
        
        if ($session === 'afternoon' && $currentTime < '17:00') {
            $earlyCheckout = true;
            $checkoutReason = "Early check-out at {$currentTime} (minimum 17:00)";
            
            if (!$hasNote) {
                return redirect()->route('checkin')
                               ->with('error', 'Afternoon check-out before 5:00 PM requires a note/reason. Please provide a note to proceed or you will be marked absent.');
            }
        }

        // Prepare note with checkout reason if applicable
        $finalNote = $request->note ?? $attendance->note;
        if ($earlyCheckout && $hasNote) {
            $existingNote = $attendance->note ?? '';
            if ($existingNote) {
                $finalNote = $existingNote . " | [{$checkoutReason}] {$request->note}";
            } else {
                $finalNote = "[{$checkoutReason}] {$request->note}";
            }
        }

        $attendance->update([
            $checkOutField => now(),
            'note' => $finalNote,
        ]);

        // Calculate work hours
        $attendance->calculateWorkHours();
        $attendance->refresh();

        // Send notifications
        $this->sendCheckOutNotifications($user, $attendance, $officeLocation, $session);
        $this->createCheckOutNotifications($user, $attendance, $officeLocation, $session);

        $sessionName = ucfirst($session);
        $sessionHours = $session === 'morning' ? $attendance->formatted_morning_hours : $attendance->formatted_afternoon_hours;
        
        $successMessage = "{$sessionName} check-out successful at {$officeLocation->name}! Session hours: {$sessionHours} | Total today: " . $attendance->formatted_work_hours;
        
        if ($earlyCheckout) {
            $successMessage .= " (Early check-out recorded with reason)";
        }
        
        return redirect()->route('checkin')->with('success', $successMessage);
    }

    /**
     * Send Telegram check-in notifications to user and admins
     */
    private function sendCheckInNotifications($user, $attendance, $officeLocation, $schedule = null, $session = 'morning')
    {
        try {
            $message = $this->telegram->sendCheckInNotification($user, $attendance, $officeLocation, $schedule, $session);
            
            // Send to the user themselves
            if ($user->telegram_chat_id) {
                $this->telegram->notifyUser($user, $message);
                Log::info("Check-in Telegram notification sent to user: {$user->name} ({$session} session)");
            }
            
            // Send to all admins
            $this->telegram->notifyAdmins($message);
            Log::info("Check-in Telegram notification sent to admins for user: {$user->name} ({$session} session)");
            
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram check-in notifications: ' . $e->getMessage());
            // Don't fail the check-in if notification fails
        }
    }

    /**
     * Send Telegram check-out notifications to user and admins
     */
    private function sendCheckOutNotifications($user, $attendance, $officeLocation, $session = 'morning')
    {
        try {
            $message = $this->telegram->sendCheckOutNotification($user, $attendance, $officeLocation, $session);
            
            // Send to the user themselves
            if ($user->telegram_chat_id) {
                $this->telegram->notifyUser($user, $message);
                Log::info("Check-out Telegram notification sent to user: {$user->name} ({$session} session)");
            }
            
            // Send to all admins
            $this->telegram->notifyAdmins($message);
            Log::info("Check-out Telegram notification sent to admins for user: {$user->name} ({$session} session)");
            
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram check-out notifications: ' . $e->getMessage());
            // Don't fail the check-out if notification fails
        }
    }

    /**
     * Create in-app check-in notifications for all admins
     */
    private function createCheckInNotifications($user, $attendance, $officeLocation, $session = 'morning')
    {
        try {
            $admins = User::where('role_type', 'admin')->get();
            
            $checkInTime = $session === 'morning' ? $attendance->morning_check_in : $attendance->afternoon_check_in;
            $sessionIcon = $session === 'morning' ? '🌞' : '🌅';
            $sessionName = ucfirst($session);
            
            $notificationType = $attendance->status === 'late' ? 'late' : 'checkin';
            $title = $attendance->status === 'late' 
                ? "⚠️ Late {$sessionName} Check-In: {$user->name}" 
                : "{$sessionIcon} {$sessionName} Check-In: {$user->name}";
            
            $message = "{$user->name} checked in for {$session} session at {$checkInTime->format('h:i A')} at {$officeLocation->name}";
            
            // Add note info if present
            if ($attendance->note) {
                $message .= " | Note: {$attendance->note}";
            }
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => $notificationType,
                    'title' => $title,
                    'message' => $message,
                    'data' => [
                        'attendance_id' => $attendance->id,
                        'employee_id' => $user->id,
                        'employee_name' => $user->name,
                        'employee_email' => $user->email,
                        'session' => $session,
                        'time' => $checkInTime->format('h:i A'),
                        'location' => $officeLocation->name,
                        'status' => $attendance->status,
                        'note' => $attendance->note,
                        'coordinates' => [
                            'lat' => $attendance->latitude,
                            'lng' => $attendance->longitude,
                        ],
                    ],
                ]);
            }

            Log::info("{$sessionName} check-in in-app notifications created for {$admins->count()} admins");
        } catch (\Exception $e) {
            Log::error('Failed to create check-in in-app notifications: ' . $e->getMessage());
        }
    }

    /**
     * Create in-app check-out notifications for all admins
     */
    private function createCheckOutNotifications($user, $attendance, $officeLocation, $session = 'morning')
    {
        try {
            $admins = User::where('role_type', 'admin')->get();
            
            $checkOutTime = $session === 'morning' ? $attendance->morning_check_out : $attendance->afternoon_check_out;
            $sessionHours = $session === 'morning' ? $attendance->formatted_morning_hours : $attendance->formatted_afternoon_hours;
            $sessionIcon = $session === 'morning' ? '🌞' : '🌅';
            $sessionName = ucfirst($session);
            
            $message = "{$user->name} checked out from {$session} session at {$checkOutTime->format('h:i A')} • {$sessionHours} worked • Total today: {$attendance->formatted_work_hours}";
            
            // Add note info if present
            if ($attendance->note) {
                $message .= " | Note: {$attendance->note}";
            }
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'checkout',
                    'title' => "{$sessionIcon} {$sessionName} Check-Out: {$user->name}",
                    'message' => $message,
                    'data' => [
                        'attendance_id' => $attendance->id,
                        'employee_id' => $user->id,
                        'employee_name' => $user->name,
                        'employee_email' => $user->email,
                        'session' => $session,
                        'check_out_time' => $checkOutTime->format('h:i A'),
                        'location' => $officeLocation->name,
                        'session_hours' => $sessionHours,
                        'total_work_hours' => $attendance->formatted_work_hours,
                        'status' => $attendance->status,
                        'note' => $attendance->note,
                    ],
                ]);
            }

            Log::info("{$sessionName} check-out in-app notifications created for {$admins->count()} admins");
        } catch (\Exception $e) {
            Log::error('Failed to create check-out in-app notifications: ' . $e->getMessage());
        }
    }

    /**
     * Create location alert notifications for admins (when outside radius)
     */
    private function createLocationAlertNotifications($user, $distance, $officeLocation)
    {
        try {
            $admins = User::where('role_type', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'alert',
                    'title' => "🚨 Location Alert: {$user->name}",
                    'message' => "{$user->name} attempted check-in {$distance}m away from {$officeLocation->name} (allowed: {$officeLocation->radius}m)",
                    'data' => [
                        'employee_id' => $user->id,
                        'employee_name' => $user->name,
                        'employee_email' => $user->email,
                        'location' => $officeLocation->name,
                        'actual_distance' => round($distance, 2),
                        'allowed_radius' => $officeLocation->radius,
                        'distance_exceeded' => round($distance - $officeLocation->radius, 2),
                        'timestamp' => now()->format('h:i A'),
                    ],
                ]);
            }

            Log::info("Location alert notifications created for {$admins->count()} admins");
        } catch (\Exception $e) {
            Log::error('Failed to create location alert notifications: ' . $e->getMessage());
        }
    }
}
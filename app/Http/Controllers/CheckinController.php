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
        $session = $request->session; // 'morning' or 'afternoon'
        
        $existingAttendance = Attendance::where('user_id', $user->id)
                                       ->whereDate('attendance_date', today())
                                       ->first();
        
        // Check if already checked in for this session
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

        $schedule = AttendanceSchedule::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->first();

        // Determine if user is late for this session
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

        // Update or create attendance record
        $checkInField = $session === 'morning' ? 'morning_check_in' : 'afternoon_check_in';
        
        $attendanceData = [
            'user_id' => $user->id,
            'attendance_date' => today(),
        ];

        $updateData = [
            $checkInField => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'note' => $request->note,
        ];

        // Only update status if it's not already 'late' (late status should persist)
        if (!$existingAttendance || $existingAttendance->status !== 'late') {
            $updateData['status'] = $status;
        }

        $attendance = Attendance::updateOrCreate($attendanceData, $updateData);
        $attendance->refresh();

        // Send Telegram Notifications
        $this->sendCheckInNotifications($user, $attendance, $officeLocation, $schedule, $session);

        // Create In-App Notifications for Admins
        $this->createCheckInNotifications($user, $attendance, $officeLocation, $session);

        $sessionName = ucfirst($session);
        return redirect()->route('checkin')
                       ->with('success', "{$sessionName} check-in successful at {$officeLocation->name}! Status: " . ucfirst(str_replace('_', ' ', $status)));
    }

    private function processCheckOut(Request $request, OfficeLocation $officeLocation)
    {
        $user = Auth::user();
        $session = $request->session; // 'morning' or 'afternoon'
        
        $attendance = Attendance::where('user_id', $user->id)
                               ->whereDate('attendance_date', today())
                               ->first();
        
        // Check if user has checked in for this session
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

        $attendance->update([
            $checkOutField => now(),
            'note' => $request->note ?? $attendance->note,
        ]);

        // Calculate work hours for both sessions
        $attendance->calculateWorkHours();
        $attendance->refresh();

        // Send Telegram Notifications
        $this->sendCheckOutNotifications($user, $attendance, $officeLocation, $session);

        // Create In-App Notifications for Admins
        $this->createCheckOutNotifications($user, $attendance, $officeLocation, $session);

        $sessionName = ucfirst($session);
        $sessionHours = $session === 'morning' ? $attendance->formatted_morning_hours : $attendance->formatted_afternoon_hours;
        
        return redirect()->route('checkin')
                       ->with('success', "{$sessionName} check-out successful at {$officeLocation->name}! Session hours: {$sessionHours} | Total today: " . $attendance->formatted_work_hours);
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
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => $notificationType,
                    'title' => $title,
                    'message' => "{$user->name} checked in for {$session} session at {$checkInTime->format('h:i A')} at {$officeLocation->name}",
                    'data' => [
                        'attendance_id' => $attendance->id,
                        'employee_id' => $user->id,
                        'employee_name' => $user->name,
                        'employee_email' => $user->email,
                        'session' => $session,
                        'time' => $checkInTime->format('h:i A'),
                        'location' => $officeLocation->name,
                        'status' => $attendance->status,
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
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'checkout',
                    'title' => "{$sessionIcon} {$sessionName} Check-Out: {$user->name}",
                    'message' => "{$user->name} checked out from {$session} session at {$checkOutTime->format('h:i A')} • {$sessionHours} worked • Total today: {$attendance->formatted_work_hours}",
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
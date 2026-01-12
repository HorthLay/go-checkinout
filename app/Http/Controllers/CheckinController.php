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
                                    ->with('officeLocation')
                                    ->first();
        
        return view('home.checkin', compact('todayAttendance'));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();
        
        $todayAttendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('attendance_date', today())
                                    ->with('officeLocation')
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
        
        $existingAttendance = Attendance::where('user_id', $user->id)
                                       ->whereDate('attendance_date', today())
                                       ->first();
        
        if ($existingAttendance && $existingAttendance->check_in) {
            return redirect()->route('checkin')->with('error', 'You have already checked in today.');
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

        $status = 'on_time';
        if ($schedule) {
            $checkInTime = now();
            $scheduledTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $schedule->scheduled_check_in);
            $lateThreshold = $scheduledTime->addMinutes($schedule->late_allowed_min);

            if ($checkInTime->gt($lateThreshold)) {
                $status = 'late';
            }
        }

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => today(),
            ],
            [
                'office_location_id' => $officeLocation->id,
                'check_in' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => $status,
                'note' => $request->note,
            ]
        );

        $attendance->refresh();

        // Send Telegram Notifications
        $this->sendCheckInNotifications($user, $attendance, $officeLocation, $schedule);

        // Create In-App Notifications for Admins
        $this->createCheckInNotifications($user, $attendance, $officeLocation);

        return redirect()->route('checkin')
                       ->with('success', "Check-in successful at {$officeLocation->name}! Status: " . ucfirst(str_replace('_', ' ', $status)));
    }

    private function processCheckOut(Request $request, OfficeLocation $officeLocation)
    {
        $user = Auth::user();
        
        $attendance = Attendance::where('user_id', $user->id)
                               ->whereDate('attendance_date', today())
                               ->first();
        
        if (!$attendance || !$attendance->check_in) {
            return redirect()->route('checkin')->with('error', 'You must check in first.');
        }

        if ($attendance->check_out) {
            return redirect()->route('checkin')->with('error', 'You have already checked out today.');
        }

        $attendance->update([
            'check_out' => now(),
            'note' => $request->note ?? $attendance->note,
        ]);

        $attendance->calculateWorkHours();
        $attendance->refresh();

        // Send Telegram Notifications
        $this->sendCheckOutNotifications($user, $attendance, $officeLocation);

        // Create In-App Notifications for Admins
        $this->createCheckOutNotifications($user, $attendance, $officeLocation);

        return redirect()->route('checkin')
                       ->with('success', "Check-out successful at {$officeLocation->name}! Total work hours: " . $attendance->formatted_work_hours);
    }

    /**
     * Send Telegram check-in notifications to user and admins
     */
    private function sendCheckInNotifications($user, $attendance, $officeLocation, $schedule = null)
    {
        try {
            $message = $this->telegram->sendCheckInNotification($user, $attendance, $officeLocation, $schedule);
            
            // Send to the user themselves
            if ($user->telegram_chat_id) {
                $this->telegram->notifyUser($user, $message);
                Log::info("Check-in Telegram notification sent to user: {$user->name}");
            }
            
            // Send to all admins
            $this->telegram->notifyAdmins($message);
            Log::info("Check-in Telegram notification sent to admins for user: {$user->name}");
            
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram check-in notifications: ' . $e->getMessage());
            // Don't fail the check-in if notification fails
        }
    }

    /**
     * Send Telegram check-out notifications to user and admins
     */
    private function sendCheckOutNotifications($user, $attendance, $officeLocation)
    {
        try {
            $message = $this->telegram->sendCheckOutNotification($user, $attendance, $officeLocation);
            
            // Send to the user themselves
            if ($user->telegram_chat_id) {
                $this->telegram->notifyUser($user, $message);
                Log::info("Check-out Telegram notification sent to user: {$user->name}");
            }
            
            // Send to all admins
            $this->telegram->notifyAdmins($message);
            Log::info("Check-out Telegram notification sent to admins for user: {$user->name}");
            
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram check-out notifications: ' . $e->getMessage());
            // Don't fail the check-out if notification fails
        }
    }

    /**
     * Create in-app check-in notifications for all admins
     */
    private function createCheckInNotifications($user, $attendance, $officeLocation)
    {
        try {
            $admins = User::where('role_type', 'admin')->get();
            
            $notificationType = $attendance->status === 'late' ? 'late' : 'checkin';
            $title = $attendance->status === 'late' 
                ? "⚠️ Late Check-In: {$user->name}" 
                : "✅ Check-In: {$user->name}";
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => $notificationType,
                    'title' => $title,
                    'message' => "{$user->name} checked in at {$attendance->check_in->format('h:i A')} at {$officeLocation->name}",
                    'data' => [
                        'attendance_id' => $attendance->id,
                        'employee_id' => $user->id,
                        'employee_name' => $user->name,
                        'employee_email' => $user->email,
                        'time' => $attendance->check_in->format('h:i A'),
                        'location' => $officeLocation->name,
                        'status' => $attendance->status,
                        'coordinates' => [
                            'lat' => $attendance->latitude,
                            'lng' => $attendance->longitude,
                        ],
                    ],
                ]);
            }

            Log::info("Check-in in-app notifications created for {$admins->count()} admins");
        } catch (\Exception $e) {
            Log::error('Failed to create check-in in-app notifications: ' . $e->getMessage());
        }
    }

    /**
     * Create in-app check-out notifications for all admins
     */
    private function createCheckOutNotifications($user, $attendance, $officeLocation)
    {
        try {
            $admins = User::where('role_type', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'checkout',
                    'title' => "🔴 Check-Out: {$user->name}",
                    'message' => "{$user->name} checked out at {$attendance->check_out->format('h:i A')} • {$attendance->formatted_work_hours} worked",
                    'data' => [
                        'attendance_id' => $attendance->id,
                        'employee_id' => $user->id,
                        'employee_name' => $user->name,
                        'employee_email' => $user->email,
                        'check_in_time' => $attendance->check_in->format('h:i A'),
                        'check_out_time' => $attendance->check_out->format('h:i A'),
                        'location' => $officeLocation->name,
                        'work_hours' => $attendance->formatted_work_hours,
                        'total_hours' => $attendance->work_hours,
                        'status' => $attendance->status,
                    ],
                ]);
            }

            Log::info("Check-out in-app notifications created for {$admins->count()} admins");
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
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
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'action'    => 'required|in:checkin,checkout',
            'session'   => 'required|in:morning,afternoon',
            'note'      => 'nullable|string|max:255',
        ]);
 
        $user           = Auth::user();
        $officeLocation = OfficeLocation::getDefaultLocation();
 
        if (!$officeLocation) {
            return redirect('/checkin')->with('error', 'No office location configured. Please contact administrator.');
        }
 
        $distance = $officeLocation->calculateDistance($request->latitude, $request->longitude);
 
        if (!$officeLocation->isWithinRadius($request->latitude, $request->longitude)) {
            try {
                $alertMessage = $this->telegram->sendLocationAlert($user, $distance, $officeLocation);
                $this->telegram->notifyAdmins($alertMessage);
                $this->createLocationAlertNotifications($user, $distance, $officeLocation);
            } catch (\Exception $e) {
                Log::error('Failed to send location alert: ' . $e->getMessage());
            }
 
            return redirect('/checkin')->with('error', "You are {$distance}m away from {$officeLocation->name}. You must be within {$officeLocation->radius}m to check in.");
        }
 
        return $request->action === 'checkin'
            ? $this->processCheckIn($request, $officeLocation)
            : $this->processCheckOut($request, $officeLocation);
    }
 
    // ─────────────────────────────────────────────────────────────────────────
    //  CHECK IN  (no blocking — violations silently flagged in note)
    // ─────────────────────────────────────────────────────────────────────────
    private function processCheckIn(Request $request, OfficeLocation $officeLocation)
    {
        $user    = Auth::user();
        $session = $request->session;
 
        $existingAttendance = Attendance::where('user_id', $user->id)
                                        ->whereDate('attendance_date', today())
                                        ->first();
 
        // Already checked in for this session
        if ($existingAttendance) {
            if ($session === 'morning'   && $existingAttendance->morning_check_in)   return redirect('/checkin')->with('error', 'Already checked in for morning.');
            if ($session === 'afternoon' && $existingAttendance->afternoon_check_in) return redirect('/checkin')->with('error', 'Already checked in for afternoon.');
        }
 
        // Day off
        if (AttendanceOffDay::where('user_id', $user->id)->whereDate('off_date', today())->exists()) {
            return redirect('/checkin')->with('error', 'Today is your scheduled day off.');
        }
 
        $currentTime = now()->format('H:i');
 
        // ── Violation flags (no blocking, just record) ──────────────────────
        $violations = [];
        if ($session === 'morning'   && $currentTime > '09:00') $violations[] = "Late morning check-in at {$currentTime}";
        if ($session === 'afternoon' && $currentTime > '15:00') $violations[] = "Late afternoon check-in at {$currentTime}";
 
        // ── Status from schedule ────────────────────────────────────────────
        $schedule = AttendanceSchedule::where('user_id', $user->id)->where('is_active', true)->first();
        $status   = 'on_time';
 
        if ($schedule) {
            $scheduledTime = Carbon::parse(
                today()->format('Y-m-d') . ' ' . (
                    $session === 'morning'
                        ? $schedule->scheduled_check_in_morining
                        : $schedule->scheduled_check_in_afternoon
                )
            );
            if (now()->gt($scheduledTime->addMinutes($schedule->late_allowed_min))) {
                $status = 'late';
            }
        }
 
        // Violations always mark late
        if (!empty($violations)) $status = 'late';
 
        $checkInField = $session === 'morning' ? 'morning_check_in' : 'afternoon_check_in';
 
        $updateData = [
            $checkInField => now(),
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'note'        => !empty($violations) ? '[' . implode('] [', $violations) . ']' : null,
        ];
 
        // Only upgrade status to late, never downgrade
        if (!$existingAttendance || $existingAttendance->status !== 'late') {
            $updateData['status'] = $status;
        }
 
        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'attendance_date' => today()],
            $updateData
        );
        $attendance->refresh();
 
        $this->sendCheckInNotifications($user, $attendance, $officeLocation, $schedule, $session);
        $this->createCheckInNotifications($user, $attendance, $officeLocation, $session);
 
        $sessionName = ucfirst($session);
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
 
        // 🔊 Redirect to /checkin (not /) so the flash-message sound system
        //    on the check-in page can play the check-in success chime.
        return redirect('/checkin')->with('success', "{$sessionName} check-in successful at {$officeLocation->name}! Status: {$statusLabel}");
    }
 
    // ─────────────────────────────────────────────────────────────────────────
    //  CHECK OUT  (no blocking — violations silently flagged in note)
    // ─────────────────────────────────────────────────────────────────────────
    private function processCheckOut(Request $request, OfficeLocation $officeLocation)
    {
        $user    = Auth::user();
        $session = $request->session;
 
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('attendance_date', today())
                                ->first();
 
        $checkInField  = $session === 'morning' ? 'morning_check_in'  : 'afternoon_check_in';
        $checkOutField = $session === 'morning' ? 'morning_check_out' : 'afternoon_check_out';
 
        if (!$attendance || !$attendance->$checkInField) {
            return redirect('/checkin')->with('error', "You must check in for the {$session} session first.");
        }
        if ($attendance->$checkOutField) {
            return redirect('/checkin')->with('error', "You have already checked out for the {$session} session.");
        }
 
        $currentTime = now()->format('H:i');
 
        // ── Violation flags (no blocking) ───────────────────────────────────
        $violations = [];
        if ($session === 'morning'   && $currentTime < '11:30') $violations[] = "Early morning check-out at {$currentTime}";
        if ($session === 'afternoon' && $currentTime < '17:00') $violations[] = "Early afternoon check-out at {$currentTime}";
 
        // Append new violation flags to any existing note
        $existingNote = $attendance->note ?? '';
        $newFlags     = !empty($violations) ? '[' . implode('] [', $violations) . ']' : '';
        $finalNote    = trim(implode(' | ', array_filter([$existingNote, $newFlags]))) ?: null;
 
        $attendance->update([
            $checkOutField => now(),
            'note'         => $finalNote,
        ]);
 
        $attendance->calculateWorkHours();
        $attendance->refresh();
 
        $this->sendCheckOutNotifications($user, $attendance, $officeLocation, $session);
        $this->createCheckOutNotifications($user, $attendance, $officeLocation, $session);
 
        $sessionName  = ucfirst($session);
        $sessionHours = $session === 'morning'
            ? $attendance->formatted_morning_hours
            : $attendance->formatted_afternoon_hours;
 
        return redirect('/checkin')->with('success', "{$sessionName} check-out successful! {$sessionHours} | Total: " . $attendance->formatted_work_hours);
    }
 
    // ─────────────────────────────────────────────────────────────────────────
    //  NOTIFICATIONS (unchanged)
    // ─────────────────────────────────────────────────────────────────────────
    private function sendCheckInNotifications($user, $attendance, $officeLocation, $schedule = null, $session = 'morning')
    {
        try {
            $message = $this->telegram->sendCheckInNotification($user, $attendance, $officeLocation, $schedule, $session);
            if ($user->telegram_chat_id) $this->telegram->notifyUser($user, $message);
            $this->telegram->notifyAdmins($message);
            Log::info("Check-in Telegram sent: {$user->name} ({$session})");
        } catch (\Exception $e) {
            Log::error('Telegram check-in failed: ' . $e->getMessage());
        }
    }
 
    private function sendCheckOutNotifications($user, $attendance, $officeLocation, $session = 'morning')
    {
        try {
            $message = $this->telegram->sendCheckOutNotification($user, $attendance, $officeLocation, $session);
            if ($user->telegram_chat_id) $this->telegram->notifyUser($user, $message);
            $this->telegram->notifyAdmins($message);
            Log::info("Check-out Telegram sent: {$user->name} ({$session})");
        } catch (\Exception $e) {
            Log::error('Telegram check-out failed: ' . $e->getMessage());
        }
    }
 
    private function createCheckInNotifications($user, $attendance, $officeLocation, $session = 'morning')
    {
        try {
            $admins      = User::where('role_type', 'admin')->get();
            $checkInTime = $session === 'morning' ? $attendance->morning_check_in : $attendance->afternoon_check_in;
            $sessionIcon = $session === 'morning' ? '🌞' : '🌅';
            $sessionName = ucfirst($session);
 
            $isLate = $attendance->status === 'late';
            $title  = $isLate
                ? "⚠️ Late {$sessionName} Check-In: {$user->name}"
                : "{$sessionIcon} {$sessionName} Check-In: {$user->name}";
 
            $message = "{$user->name} checked in for {$session} at {$checkInTime->format('h:i A')} at {$officeLocation->name}";
            if ($attendance->note) $message .= " | {$attendance->note}";
 
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type'    => $isLate ? 'late' : 'checkin',
                    'title'   => $title,
                    'message' => $message,
                    'data'    => [
                        'attendance_id' => $attendance->id,
                        'employee_id'   => $user->id,
                        'employee_name' => $user->name,
                        'employee_email'=> $user->email,
                        'session'       => $session,
                        'time'          => $checkInTime->format('h:i A'),
                        'location'      => $officeLocation->name,
                        'status'        => $attendance->status,
                        'note'          => $attendance->note,
                        'coordinates'   => ['lat' => $attendance->latitude, 'lng' => $attendance->longitude],
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Check-in notification failed: ' . $e->getMessage());
        }
    }
 
    private function createCheckOutNotifications($user, $attendance, $officeLocation, $session = 'morning')
    {
        try {
            $admins       = User::where('role_type', 'admin')->get();
            $checkOutTime = $session === 'morning' ? $attendance->morning_check_out : $attendance->afternoon_check_out;
            $sessionHours = $session === 'morning' ? $attendance->formatted_morning_hours : $attendance->formatted_afternoon_hours;
            $sessionIcon  = $session === 'morning' ? '🌞' : '🌅';
            $sessionName  = ucfirst($session);
 
            $message = "{$user->name} checked out from {$session} at {$checkOutTime->format('h:i A')} • {$sessionHours} • Total: {$attendance->formatted_work_hours}";
            if ($attendance->note) $message .= " | {$attendance->note}";
 
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type'    => 'checkout',
                    'title'   => "{$sessionIcon} {$sessionName} Check-Out: {$user->name}",
                    'message' => $message,
                    'data'    => [
                        'attendance_id'    => $attendance->id,
                        'employee_id'      => $user->id,
                        'employee_name'    => $user->name,
                        'employee_email'   => $user->email,
                        'session'          => $session,
                        'check_out_time'   => $checkOutTime->format('h:i A'),
                        'location'         => $officeLocation->name,
                        'session_hours'    => $sessionHours,
                        'total_work_hours' => $attendance->formatted_work_hours,
                        'status'           => $attendance->status,
                        'note'             => $attendance->note,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Check-out notification failed: ' . $e->getMessage());
        }
    }
 
    private function createLocationAlertNotifications($user, $distance, $officeLocation)
    {
        try {
            $admins = User::where('role_type', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type'    => 'alert',
                    'title'   => "🚨 Location Alert: {$user->name}",
                    'message' => "{$user->name} attempted check-in {$distance}m away from {$officeLocation->name} (allowed: {$officeLocation->radius}m)",
                    'data'    => [
                        'employee_id'       => $user->id,
                        'employee_name'     => $user->name,
                        'employee_email'    => $user->email,
                        'location'          => $officeLocation->name,
                        'actual_distance'   => round($distance, 2),
                        'allowed_radius'    => $officeLocation->radius,
                        'distance_exceeded' => round($distance - $officeLocation->radius, 2),
                        'timestamp'         => now()->format('h:i A'),
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Location alert notification failed: ' . $e->getMessage());
        }
    }
}
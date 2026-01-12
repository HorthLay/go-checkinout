<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class TelegramService
{
    protected $botToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send a message to a Telegram chat
     */
    public function sendMessage($chatId, $message, $parseMode = 'HTML')
    {
        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info("Telegram message sent to {$chatId}");
                return true;
            }

            Log::error("Failed to send Telegram message to {$chatId}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Telegram send error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send check-in notification
     */
    public function sendCheckInNotification($user, $attendance, $officeLocation, $schedule = null, $session = 'morning')
    {
        $status = ucfirst(str_replace('_', ' ', $attendance->status));
        $statusEmoji = $this->getStatusEmoji($attendance->status);
        $sessionEmoji = $session === 'morning' ? '🌞' : '🌅';
        $sessionName = ucfirst($session);
        
        // Get check-in time based on session
        $checkInTime = $session === 'morning' ? $attendance->morning_check_in : $attendance->afternoon_check_in;
        
        $message = "🟢 <b>CHECK-IN NOTIFICATION</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>Employee Information</b>\n";
        $message .= "   Name: {$user->name}\n";
        $message .= "   Email: {$user->email}\n";
        if ($user->phone) {
            $message .= "   Phone: {$user->phone}\n";
        }
        $message .= "\n";
        
        $message .= "📅 <b>Date & Time</b>\n";
        $message .= "   Date: " . now()->format('l, F j, Y') . "\n";
        $message .= "   {$sessionEmoji} Session: {$sessionName}\n";
        $message .= "   Time: " . $checkInTime->format('h:i A') . "\n";
        $message .= "\n";
        
        $message .= "📍 <b>Location Details</b>\n";
        $message .= "   Office: {$officeLocation->name}\n";
        if ($officeLocation->address) {
            $message .= "   Address: {$officeLocation->address}\n";
        }
        $message .= "   Coordinates: {$attendance->latitude}, {$attendance->longitude}\n";
        $message .= "   Distance: " . round($officeLocation->calculateDistance($attendance->latitude, $attendance->longitude)) . "m\n";
        $message .= "\n";
        
        $message .= "{$statusEmoji} <b>Status: {$status}</b>\n";
        
        // Add schedule information if available
        if ($schedule) {
            $scheduledTimeField = $session === 'morning' ? 'scheduled_check_in_morining' : 'scheduled_check_in_afternoon';
            $scheduledTime = \Carbon\Carbon::parse($schedule->$scheduledTimeField);
            $actualTime = $checkInTime;
            $diff = $scheduledTime->diffInMinutes($actualTime, false);
            
            $message .= "\n";
            $message .= "⏰ <b>Schedule Information</b>\n";
            $message .= "   Expected: " . $scheduledTime->format('h:i A') . "\n";
            $message .= "   Actual: " . $actualTime->format('h:i A') . "\n";
            
            if ($diff > 0) {
                $message .= "   ⚠️ Late by: {$diff} minutes\n";
            } elseif ($diff < 0) {
                $message .= "   ✅ Early by: " . abs($diff) . " minutes\n";
            } else {
                $message .= "   ✅ On time\n";
            }
            $message .= "   Tolerance: {$schedule->late_allowed_min} minutes\n";
        }
        
        // Show session progress
        $message .= "\n";
        $message .= "📊 <b>Session Progress</b>\n";
        if ($session === 'morning') {
            $message .= "   🌞 Morning: ✅ Checked In\n";
            $message .= "   🌅 Afternoon: " . ($attendance->afternoon_check_in ? "✅ Checked In" : "⏳ Pending") . "\n";
        } else {
            $message .= "   🌞 Morning: " . ($attendance->morning_check_in ? "✅ Completed" : "❌ Not checked in") . "\n";
            $message .= "   🌅 Afternoon: ✅ Checked In\n";
        }
        
        if ($attendance->note) {
            $message .= "\n";
            $message .= "📝 <b>Note</b>\n";
            $message .= "   " . $this->escapeHtml($attendance->note) . "\n";
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>Attendify System • " . now()->format('Y-m-d H:i:s') . "</i>";

        return $message;
    }

    /**
     * Send check-out notification
     */
    public function sendCheckOutNotification($user, $attendance, $officeLocation, $session = 'morning')
    {
        $status = ucfirst(str_replace('_', ' ', $attendance->status));
        $statusEmoji = $this->getStatusEmoji($attendance->status);
        $sessionEmoji = $session === 'morning' ? '🌞' : '🌅';
        $sessionName = ucfirst($session);
        
        // Get session times
        $checkInTime = $session === 'morning' ? $attendance->morning_check_in : $attendance->afternoon_check_in;
        $checkOutTime = $session === 'morning' ? $attendance->morning_check_out : $attendance->afternoon_check_out;
        $sessionHours = $session === 'morning' ? $attendance->formatted_morning_hours : $attendance->formatted_afternoon_hours;
        
        $message = "🔴 <b>CHECK-OUT NOTIFICATION</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>Employee Information</b>\n";
        $message .= "   Name: {$user->name}\n";
        $message .= "   Email: {$user->email}\n";
        if ($user->phone) {
            $message .= "   Phone: {$user->phone}\n";
        }
        $message .= "\n";
        
        $message .= "📅 <b>Date & Time</b>\n";
        $message .= "   Date: " . now()->format('l, F j, Y') . "\n";
        $message .= "   {$sessionEmoji} Session: {$sessionName}\n";
        $message .= "   Check-In: " . $checkInTime->format('h:i A') . "\n";
        $message .= "   Check-Out: " . $checkOutTime->format('h:i A') . "\n";
        $message .= "\n";
        
        $message .= "⏱️ <b>Work Duration</b>\n";
        $message .= "   {$sessionEmoji} {$sessionName} Session: {$sessionHours}\n";
        $message .= "   📊 Total Today: " . ($attendance->formatted_work_hours ?? '—') . "\n";
        $message .= "\n";
        
        // Session breakdown
        $message .= "📈 <b>Session Breakdown</b>\n";
        $message .= "   🌞 Morning: " . ($attendance->formatted_morning_hours ?? '—') . "\n";
        $message .= "   🌅 Afternoon: " . ($attendance->formatted_afternoon_hours ?? '—') . "\n";
        
        // Show completion status
        $message .= "\n";
        $message .= "✅ <b>Completion Status</b>\n";
        $message .= "   🌞 Morning: " . ($attendance->isMorningSessionComplete() ? "✅ Complete" : "⏳ Incomplete") . "\n";
        $message .= "   🌅 Afternoon: " . ($attendance->isAfternoonSessionComplete() ? "✅ Complete" : "⏳ Incomplete") . "\n";
        $message .= "   📅 Full Day: " . ($attendance->isFullDayComplete() ? "✅ Complete" : "⏳ Incomplete") . "\n";
        $message .= "\n";
        
        $message .= "📍 <b>Location Details</b>\n";
        $message .= "   Office: {$officeLocation->name}\n";
        if ($officeLocation->address) {
            $message .= "   Address: {$officeLocation->address}\n";
        }
        $message .= "   Coordinates: {$attendance->latitude}, {$attendance->longitude}\n";
        $message .= "\n";
        
        $message .= "{$statusEmoji} <b>Status: {$status}</b>\n";
        
        if ($attendance->note) {
            $message .= "\n";
            $message .= "📝 <b>Note</b>\n";
            $message .= "   " . $this->escapeHtml($attendance->note) . "\n";
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>Attendify System • " . now()->format('Y-m-d H:i:s') . "</i>";

        return $message;
    }

    /**
     * Send daily summary notification
     */
    public function sendDailySummary($user, $attendances)
    {
        $message = "📊 <b>DAILY ATTENDANCE SUMMARY</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>Employee:</b> {$user->name}\n";
        $message .= "📅 <b>Date:</b> " . now()->format('l, F j, Y') . "\n\n";
        
        $totalPresent = $attendances->whereIn('status', ['on_time', 'late'])->count();
        $totalLate = $attendances->where('status', 'late')->count();
        $totalAbsent = $attendances->where('status', 'absent')->count();
        $totalLeave = $attendances->where('status', 'leave')->count();
        $totalHours = $attendances->sum('work_hours');
        
        // Count session completions
        $morningComplete = $attendances->filter(fn($a) => $a->isMorningSessionComplete())->count();
        $afternoonComplete = $attendances->filter(fn($a) => $a->isAfternoonSessionComplete())->count();
        $fullDayComplete = $attendances->filter(fn($a) => $a->isFullDayComplete())->count();
        
        $message .= "📈 <b>Statistics</b>\n";
        $message .= "   ✅ Present: {$totalPresent}\n";
        $message .= "   ⚠️ Late: {$totalLate}\n";
        $message .= "   ❌ Absent: {$totalAbsent}\n";
        $message .= "   🏖️ Leave: {$totalLeave}\n";
        $message .= "   ⏱️ Total Hours: " . number_format($totalHours, 1) . "h\n\n";
        
        $message .= "📊 <b>Session Completion</b>\n";
        $message .= "   🌞 Morning Sessions: {$morningComplete}\n";
        $message .= "   🌅 Afternoon Sessions: {$afternoonComplete}\n";
        $message .= "   ✅ Full Days: {$fullDayComplete}\n";
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>Attendify System • Daily Report</i>";
        
        return $message;
    }

    /**
     * Send location alert when user is outside allowed radius
     */
    public function sendLocationAlert($user, $distance, $officeLocation)
    {
        $message = "⚠️ <b>LOCATION ALERT</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>Employee:</b> {$user->name}\n";
        $message .= "📧 <b>Email:</b> {$user->email}\n\n";
        
        $message .= "🚨 <b>Alert Details</b>\n";
        $message .= "   User attempted check-in outside allowed area\n\n";
        
        $message .= "📍 <b>Location Information</b>\n";
        $message .= "   Office: {$officeLocation->name}\n";
        $message .= "   Allowed Radius: {$officeLocation->radius}m\n";
        $message .= "   Actual Distance: {$distance}m\n";
        $message .= "   ❌ Outside by: " . ($distance - $officeLocation->radius) . "m\n\n";
        
        $message .= "🕐 <b>Time:</b> " . now()->format('h:i A') . "\n";
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>Attendify System • Security Alert</i>";
        
        return $message;
    }

    /**
     * Get emoji based on attendance status
     */
    private function getStatusEmoji($status)
    {
        return match($status) {
            'on_time' => '✅',
            'late' => '⚠️',
            'absent' => '❌',
            'leave' => '🏖️',
            default => '📌',
        };
    }

    /**
     * Send notification to multiple admins
     */
    public function notifyAdmins($message)
    {
        $adminUsers = User::where('role_type', 'admin')
                         ->whereNotNull('telegram_chat_id')
                         ->get();

        $sentCount = 0;
        foreach ($adminUsers as $admin) {
            if ($this->sendMessage($admin->telegram_chat_id, $message)) {
                $sentCount++;
            }
        }

        Log::info("Notification sent to {$sentCount} admins");
        return $sentCount;
    }

    /**
     * Send notification to specific user
     */
    public function notifyUser($user, $message)
    {
        if ($user->telegram_chat_id) {
            return $this->sendMessage($user->telegram_chat_id, $message);
        }
        
        Log::warning("User {$user->id} has no telegram_chat_id set");
        return false;
    }

    /**
     * Escape HTML special characters
     */
    private function escapeHtml($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Send message with location (for map view)
     */
    public function sendLocation($chatId, $latitude, $longitude, $caption = null)
    {
        try {
            $params = [
                'chat_id' => $chatId,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            if ($caption) {
                $params['caption'] = $caption;
            }

            $response = Http::timeout(10)->post("{$this->apiUrl}/sendLocation", $params);

            if ($response->successful()) {
                Log::info("Telegram location sent to {$chatId}");
                return true;
            }

            Log::error("Failed to send Telegram location to {$chatId}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Telegram location send error: " . $e->getMessage());
            return false;
        }
    }
}
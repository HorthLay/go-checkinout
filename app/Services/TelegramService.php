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
        $sessionNameKh = $session === 'morning' ? 'ព្រឹក' : 'រសៀល';
        
        // Get check-in time based on session
        $checkInTime = $session === 'morning' ? $attendance->morning_check_in : $attendance->afternoon_check_in;
        
        // Check if this is a time violation (note contains time violation marker)
        $hasTimeViolation = $attendance->note && (
            str_contains($attendance->note, 'Late check-in at') || 
            str_contains($attendance->note, 'Early check-out at')
        );
        
        // Get day of week in both languages
        $dayEn = now()->format('l');
        $dayKh = $this->getDayInKhmer(now()->dayOfWeek);
        
        $message = "🟢 <b>ការជូនដំណឹងចូលធ្វើការ / CHECK-IN NOTIFICATION</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>ព័ត៌មានបុគ្គលិក / Employee Information</b>\n";
        $message .= "   ឈ្មោះ / Name: {$user->name}\n";
        $message .= "   អ៊ីមែល / Email: {$user->email}\n";
        if ($user->phone) {
            $message .= "   លេខទូរស័ព្ទ / Phone: {$user->phone}\n";
        }
        $message .= "\n";
        
        $message .= "📅 <b>កាលបរិច្ឆេទ និងពេលវេលា / Date & Time</b>\n";
        $message .= "   កាលបរិច្ឆេទ / Date: {$dayKh} / {$dayEn}, " . now()->format('F j, Y') . "\n";
        $message .= "   {$sessionEmoji} វេន / Session: {$sessionNameKh} / {$sessionName}\n";
        $message .= "   ម៉ោង / Time: " . $checkInTime->format('h:i A') . "\n";
        
        // Show time violation warning if applicable
        if ($hasTimeViolation) {
            $message .= "   ⚠️ <b>ការជូនដំណឹងពេលវេលា / Time Notice</b>\n";
            $timeLimit = $session === 'morning' ? '9:00 AM' : '3:00 PM';
            $message .= "   ចូលធ្វើការក្រៅម៉ោងកំណត់ ({$timeLimit})\n";
            $message .= "   Check-in outside normal hours ({$timeLimit})\n";
        }
        $message .= "\n";
        
        $message .= "📍 <b>ព័ត៌មានទីតាំង / Location Details</b>\n";
        $message .= "   ការិយាល័យ / Office: {$officeLocation->name}\n";
        if ($officeLocation->address) {
            $message .= "   អាសយដ្ឋាន / Address: {$officeLocation->address}\n";
        }
        $message .= "   កូអរដោនេ / Coordinates: {$attendance->latitude}, {$attendance->longitude}\n";
        $message .= "   ចម្ងាយ / Distance: " . round($officeLocation->calculateDistance($attendance->latitude, $attendance->longitude)) . "m\n";
        $message .= "\n";
        
        $message .= "{$statusEmoji} <b>ស្ថានភាព / Status: {$status}</b>\n";
        
        // Add schedule information if available
        if ($schedule) {
            $scheduledTimeField = $session === 'morning' ? 'scheduled_check_in_morining' : 'scheduled_check_in_afternoon';
            $scheduledTime = \Carbon\Carbon::parse($schedule->$scheduledTimeField);
            $actualTime = $checkInTime;
            $diff = $scheduledTime->diffInMinutes($actualTime, false);
            
            $message .= "\n";
            $message .= "⏰ <b>ព័ត៌មានកាលវិភាគ / Schedule Information</b>\n";
            $message .= "   រំពឹងទុក / Expected: " . $scheduledTime->format('h:i A') . "\n";
            $message .= "   ពិតប្រាកដ / Actual: " . $actualTime->format('h:i A') . "\n";
            
            if ($diff > 0) {
                $message .= "   ⚠️ យឺតយ៉ាវ / Late by: {$diff} នាទី / minutes\n";
            } elseif ($diff < 0) {
                $message .= "   ✅ មុនម៉ោង / Early by: " . abs($diff) . " នាទី / minutes\n";
            } else {
                $message .= "   ✅ ទាន់ពេលវេលា / On time\n";
            }
            $message .= "   និទ្ទន្តភាព / Tolerance: {$schedule->late_allowed_min} នាទី / minutes\n";
        }
        
        // Show session progress
        $message .= "\n";
        $message .= "📊 <b>វឌ្ឍនភាពវេន / Session Progress</b>\n";
        if ($session === 'morning') {
            $message .= "   🌞 ព្រឹក / Morning: ✅ បានចូលធ្វើការ / Checked In\n";
            $message .= "   🌅 រសៀល / Afternoon: " . ($attendance->afternoon_check_in ? "✅ បានចូលធ្វើការ / Checked In" : "⏳ រង់ចាំ / Pending") . "\n";
        } else {
            $message .= "   🌞 ព្រឹក / Morning: " . ($attendance->morning_check_in ? "✅ បានបញ្ចប់ / Completed" : "❌ មិនទាន់ចូល / Not checked in") . "\n";
            $message .= "   🌅 រសៀល / Afternoon: ✅ បានចូលធ្វើការ / Checked In\n";
        }
        
        if ($attendance->note) {
            $message .= "\n";
            if ($hasTimeViolation) {
                $message .= "📝 <b>មូលហេតុ និងកំណត់ចំណាំ / Reason & Note</b>\n";
                $message .= "   ⚠️ មានមូលហេតុសម្រាប់ម៉ោងមិនធម្មតា\n";
                $message .= "   ⚠️ Reason provided for irregular hours\n";
            } else {
                $message .= "📝 <b>កំណត់ចំណាំ / Note</b>\n";
            }
            $message .= "   " . $this->escapeHtml($attendance->note) . "\n";
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>ប្រព័ន្ធ Attendify / Attendify System • " . now()->format('Y-m-d H:i:s') . "</i>";

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
        $sessionNameKh = $session === 'morning' ? 'ព្រឹក' : 'រសៀល';
        
        // Get session times
        $checkInTime = $session === 'morning' ? $attendance->morning_check_in : $attendance->afternoon_check_in;
        $checkOutTime = $session === 'morning' ? $attendance->morning_check_out : $attendance->afternoon_check_out;
        $sessionHours = $session === 'morning' ? $attendance->formatted_morning_hours : $attendance->formatted_afternoon_hours;
        
        // Check if this is an early checkout (note contains early checkout marker)
        $hasEarlyCheckout = $attendance->note && str_contains($attendance->note, 'Early check-out at');
        
        // Get day of week in both languages
        $dayEn = now()->format('l');
        $dayKh = $this->getDayInKhmer(now()->dayOfWeek);
        
        $message = "🔴 <b>ការជូនដំណឹងចេញពីការងារ / CHECK-OUT NOTIFICATION</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>ព័ត៌មានបុគ្គលិក / Employee Information</b>\n";
        $message .= "   ឈ្មោះ / Name: {$user->name}\n";
        $message .= "   អ៊ីមែល / Email: {$user->email}\n";
        if ($user->phone) {
            $message .= "   លេខទូរស័ព្ទ / Phone: {$user->phone}\n";
        }
        $message .= "\n";
        
        $message .= "📅 <b>កាលបរិច្ឆេទ និងពេលវេលា / Date & Time</b>\n";
        $message .= "   កាលបរិច្ឆេទ / Date: {$dayKh} / {$dayEn}, " . now()->format('F j, Y') . "\n";
        $message .= "   {$sessionEmoji} វេន / Session: {$sessionNameKh} / {$sessionName}\n";
        $message .= "   ចូលធ្វើការ / Check-In: " . $checkInTime->format('h:i A') . "\n";
        $message .= "   ចេញពីការងារ / Check-Out: " . $checkOutTime->format('h:i A') . "\n";
        
        // Show early checkout warning if applicable
        if ($hasEarlyCheckout) {
            $message .= "   ⚠️ <b>ការជូនដំណឹងពេលវេលា / Time Notice</b>\n";
            $timeLimit = $session === 'morning' ? '11:00 AM' : '5:00 PM';
            $message .= "   ចេញពីការងារមុនម៉ោងកំណត់ ({$timeLimit})\n";
            $message .= "   Check-out before minimum hours ({$timeLimit})\n";
        }
        $message .= "\n";
        
        $message .= "⏱️ <b>រយៈពេលធ្វើការ / Work Duration</b>\n";
        $message .= "   {$sessionEmoji} វេន{$sessionNameKh} / {$sessionName} Session: {$sessionHours}\n";
        $message .= "   📊 សរុបថ្ងៃនេះ / Total Today: " . ($attendance->formatted_work_hours ?? '—') . "\n";
        
        if ($hasEarlyCheckout) {
            $message .= "   ⚠️ ម៉ោងបានគណនាតាមពេលវេលាពិតប្រាកដ\n";
            $message .= "   ⚠️ Hours calculated based on actual times\n";
        }
        $message .= "\n";
        
        // Session breakdown
        $message .= "📈 <b>ពិពណ៌នាវេន / Session Breakdown</b>\n";
        $message .= "   🌞 ព្រឹក / Morning: " . ($attendance->formatted_morning_hours ?? '—') . "\n";
        $message .= "   🌅 រសៀល / Afternoon: " . ($attendance->formatted_afternoon_hours ?? '—') . "\n";
        
        // Show completion status
        $message .= "\n";
        $message .= "✅ <b>ស្ថានភាពបញ្ចប់ / Completion Status</b>\n";
        $message .= "   🌞 ព្រឹក / Morning: " . ($attendance->isMorningSessionComplete() ? "✅ បញ្ចប់ / Complete" : "⏳ មិនទាន់បញ្ចប់ / Incomplete") . "\n";
        $message .= "   🌅 រសៀល / Afternoon: " . ($attendance->isAfternoonSessionComplete() ? "✅ បញ្ចប់ / Complete" : "⏳ មិនទាន់បញ្ចប់ / Incomplete") . "\n";
        $message .= "   📅 ពេញមួយថ្ងៃ / Full Day: " . ($attendance->isFullDayComplete() ? "✅ បញ្ចប់ / Complete" : "⏳ មិនទាន់បញ្ចប់ / Incomplete") . "\n";
        $message .= "\n";
        
        $message .= "📍 <b>ព័ត៌មានទីតាំង / Location Details</b>\n";
        $message .= "   ការិយាល័យ / Office: {$officeLocation->name}\n";
        if ($officeLocation->address) {
            $message .= "   អាសយដ្ឋាន / Address: {$officeLocation->address}\n";
        }
        $message .= "   កូអរដោនេ / Coordinates: {$attendance->latitude}, {$attendance->longitude}\n";
        $message .= "\n";
        
        $message .= "{$statusEmoji} <b>ស្ថានភាព / Status: {$status}</b>\n";
        
        if ($attendance->note) {
            $message .= "\n";
            if ($hasEarlyCheckout) {
                $message .= "📝 <b>មូលហេតុ និងកំណត់ចំណាំ / Reason & Note</b>\n";
                $message .= "   ⚠️ មានមូលហេតុសម្រាប់ការចេញមុនម៉ោង\n";
                $message .= "   ⚠️ Reason provided for early check-out\n";
            } else {
                $message .= "📝 <b>កំណត់ចំណាំ / Note</b>\n";
            }
            $message .= "   " . $this->escapeHtml($attendance->note) . "\n";
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>ប្រព័ន្ធ Attendify / Attendify System • " . now()->format('Y-m-d H:i:s') . "</i>";

        return $message;
    }

    /**
     * Send daily summary notification
     */
    public function sendDailySummary($user, $attendances)
    {
        // Get day of week in both languages
        $dayEn = now()->format('l');
        $dayKh = $this->getDayInKhmer(now()->dayOfWeek);
        
        $message = "📊 <b>សង្ខេបវត្តមានប្រចាំថ្ងៃ / DAILY ATTENDANCE SUMMARY</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>បុគ្គលិក / Employee:</b> {$user->name}\n";
        $message .= "📅 <b>កាលបរិច្ឆេទ / Date:</b> {$dayKh} / {$dayEn}, " . now()->format('F j, Y') . "\n\n";
        
        $totalPresent = $attendances->whereIn('status', ['on_time', 'late'])->count();
        $totalLate = $attendances->where('status', 'late')->count();
        $totalAbsent = $attendances->where('status', 'absent')->count();
        $totalLeave = $attendances->where('status', 'leave')->count();
        $totalHours = $attendances->sum('work_hours');
        
        // Count session completions
        $morningComplete = $attendances->filter(fn($a) => $a->isMorningSessionComplete())->count();
        $afternoonComplete = $attendances->filter(fn($a) => $a->isAfternoonSessionComplete())->count();
        $fullDayComplete = $attendances->filter(fn($a) => $a->isFullDayComplete())->count();
        
        // Count time violations
        $timeViolations = $attendances->filter(function($a) {
            return $a->note && (
                str_contains($a->note, 'Late check-in at') || 
                str_contains($a->note, 'Early check-out at')
            );
        })->count();
        
        $message .= "📈 <b>ស្ថិតិ / Statistics</b>\n";
        $message .= "   ✅ មានវត្តមាន / Present: {$totalPresent}\n";
        $message .= "   ⚠️ យឺតយ៉ាវ / Late: {$totalLate}\n";
        $message .= "   ❌ អវត្តមាន / Absent: {$totalAbsent}\n";
        $message .= "   🏖️ សម្រាក / Leave: {$totalLeave}\n";
        $message .= "   ⏱️ ម៉ោងសរុប / Total Hours: " . number_format($totalHours, 1) . "h\n";
        
        if ($timeViolations > 0) {
            $message .= "   ⚠️ ម៉ោងមិនធម្មតា / Irregular Hours: {$timeViolations}\n";
        }
        $message .= "\n";
        
        $message .= "📊 <b>ការបញ្ចប់វេន / Session Completion</b>\n";
        $message .= "   🌞 វេនព្រឹក / Morning Sessions: {$morningComplete}\n";
        $message .= "   🌅 វេនរសៀល / Afternoon Sessions: {$afternoonComplete}\n";
        $message .= "   ✅ ពេញមួយថ្ងៃ / Full Days: {$fullDayComplete}\n";
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>ប្រព័ន្ធ Attendify / Attendify System • របាយការណ៍ប្រចាំថ្ងៃ / Daily Report</i>";
        
        return $message;
    }

    /**
     * Send location alert when user is outside allowed radius
     */
    public function sendLocationAlert($user, $distance, $officeLocation)
    {
        $message = "⚠️ <b>ការជូនដំណឹងទីតាំង / LOCATION ALERT</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "👤 <b>បុគ្គលិក / Employee:</b> {$user->name}\n";
        $message .= "📧 <b>អ៊ីមែល / Email:</b> {$user->email}\n\n";
        
        $message .= "🚨 <b>ព័ត៌មានការជូនដំណឹង / Alert Details</b>\n";
        $message .= "   អ្នកប្រើប្រាស់បានព្យាយាមចូលធ្វើការនៅខាងក្រៅតំបន់អនុញ្ញាត\n";
        $message .= "   User attempted check-in outside allowed area\n\n";
        
        $message .= "📍 <b>ព័ត៌មានទីតាំង / Location Information</b>\n";
        $message .= "   ការិយាល័យ / Office: {$officeLocation->name}\n";
        $message .= "   កាំអនុញ្ញាត / Allowed Radius: {$officeLocation->radius}m\n";
        $message .= "   ចម្ងាយពិតប្រាកដ / Actual Distance: {$distance}m\n";
        $message .= "   ❌ ក្រៅពី / Outside by: " . ($distance - $officeLocation->radius) . "m\n\n";
        
        $message .= "🕐 <b>ពេលវេលា / Time:</b> " . now()->format('h:i A') . "\n";
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $message .= "\n<i>ប្រព័ន្ធ Attendify / Attendify System • ការជូនដំណឹងសុវត្ថិភាព / Security Alert</i>";
        
        return $message;
    }

    /**
     * Get day of week in Khmer
     */
    private function getDayInKhmer($dayOfWeek)
    {
        $daysKhmer = [
            0 => 'អាទិត្យ',      // Sunday
            1 => 'ច័ន្ទ',         // Monday
            2 => 'អង្គារ',        // Tuesday
            3 => 'ពុធ',          // Wednesday
            4 => 'ព្រហស្បតិ៍',   // Thursday
            5 => 'សុក្រ',        // Friday
            6 => 'សៅរ៍',        // Saturday
        ];
        
        return $daysKhmer[$dayOfWeek] ?? '';
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
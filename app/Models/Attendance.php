<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'morning_check_in',
        'morning_check_out',
        'afternoon_check_in',
        'afternoon_check_out',
        'longitude',
        'latitude',
        'status',
        'work_hours',
        'absent_note',
        'note',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'morning_check_in' => 'datetime',
        'morning_check_out' => 'datetime',
        'afternoon_check_in' => 'datetime',
        'afternoon_check_out' => 'datetime',
        'work_hours' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate total work hours from both morning and afternoon sessions
     * Stores total hours as decimal (e.g., 8.5 hours = 8 hours 30 minutes)
     */
    public function calculateWorkHours()
    {
        $totalMinutes = 0;

        // Calculate morning session hours
        if ($this->morning_check_in && $this->morning_check_out) {
            $morningIn = Carbon::parse($this->morning_check_in);
            $morningOut = Carbon::parse($this->morning_check_out);
            $totalMinutes += $morningOut->diffInMinutes($morningIn);
        }

        // Calculate afternoon session hours
        if ($this->afternoon_check_in && $this->afternoon_check_out) {
            $afternoonIn = Carbon::parse($this->afternoon_check_in);
            $afternoonOut = Carbon::parse($this->afternoon_check_out);
            $totalMinutes += $afternoonOut->diffInMinutes($afternoonIn);
        }

        // Convert to decimal hours (e.g., 510 minutes = 8.5 hours)
        $this->work_hours = round($totalMinutes / 60, 2);
        $this->save();
    }

    /**
     * Get morning session hours only
     */
    public function getMorningWorkHoursAttribute()
    {
        if (!$this->morning_check_in || !$this->morning_check_out) {
            return 0;
        }

        $morningIn = Carbon::parse($this->morning_check_in);
        $morningOut = Carbon::parse($this->morning_check_out);
        $totalMinutes = $morningOut->diffInMinutes($morningIn);

        return round($totalMinutes / 60, 2);
    }

    /**
     * Get afternoon session hours only
     */
    public function getAfternoonWorkHoursAttribute()
    {
        if (!$this->afternoon_check_in || !$this->afternoon_check_out) {
            return 0;
        }

        $afternoonIn = Carbon::parse($this->afternoon_check_in);
        $afternoonOut = Carbon::parse($this->afternoon_check_out);
        $totalMinutes = $afternoonOut->diffInMinutes($afternoonIn);

        return round($totalMinutes / 60, 2);
    }

    /**
     * Get formatted work hours as "Xh Ym" (e.g., "8h 30m")
     */
    public function getFormattedWorkHoursAttribute()
    {
        if (!$this->work_hours) {
            return '—';
        }

        $totalMinutes = $this->work_hours * 60;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$hours}h";
    }

    /**
     * Get formatted morning session hours
     */
    public function getFormattedMorningHoursAttribute()
    {
        $hours = $this->morning_work_hours;
        if (!$hours) {
            return '—';
        }

        $totalMinutes = $hours * 60;
        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;

        if ($m > 0) {
            return "{$h}h {$m}m";
        }

        return "{$h}h";
    }

    /**
     * Get formatted afternoon session hours
     */
    public function getFormattedAfternoonHoursAttribute()
    {
        $hours = $this->afternoon_work_hours;
        if (!$hours) {
            return '—';
        }

        $totalMinutes = $hours * 60;
        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;

        if ($m > 0) {
            return "{$h}h {$m}m";
        }

        return "{$h}h";
    }

    /**
     * Get work hours in hours only (integer)
     */
    public function getWorkHoursOnlyAttribute()
    {
        if (!$this->work_hours) {
            return 0;
        }

        return floor($this->work_hours);
    }

    /**
     * Get work minutes only (integer)
     */
    public function getWorkMinutesOnlyAttribute()
    {
        if (!$this->work_hours) {
            return 0;
        }

        $totalMinutes = $this->work_hours * 60;
        return $totalMinutes % 60;
    }

    /**
     * Get total work duration as human-readable string
     * Examples: "8 hours 30 minutes", "5 hours", "45 minutes"
     */
    public function getWorkDurationAttribute()
    {
        if (!$this->work_hours) {
            return 'No data';
        }

        $totalMinutes = $this->work_hours * 60;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $parts = [];
        
        if ($hours > 0) {
            $parts[] = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
        }
        
        if ($minutes > 0) {
            $parts[] = $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
        }

        return implode(' ', $parts);
    }

    /**
     * Check if late for morning session
     */
    public function isLateMorning()
    {
        $schedule = AttendanceSchedule::where('user_id', $this->user_id)
                                      ->where('is_active', true)
                                      ->first();
        
        if (!$schedule || !$this->morning_check_in) {
            return false;
        }

        $checkInTime = Carbon::parse($this->morning_check_in);
        $scheduledTime = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $schedule->scheduled_check_in_morining);
        $lateThreshold = $scheduledTime->addMinutes($schedule->late_allowed_min);

        return $checkInTime->gt($lateThreshold);
    }

    /**
     * Check if late for afternoon session
     */
    public function isLateAfternoon()
    {
        $schedule = AttendanceSchedule::where('user_id', $this->user_id)
                                      ->where('is_active', true)
                                      ->first();
        
        if (!$schedule || !$this->afternoon_check_in) {
            return false;
        }

        $checkInTime = Carbon::parse($this->afternoon_check_in);
        $scheduledTime = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $schedule->scheduled_check_in_afternoon);
        $lateThreshold = $scheduledTime->addMinutes($schedule->late_allowed_min);

        return $checkInTime->gt($lateThreshold);
    }

    /**
     * Check if late for any session
     */
    public function isLate()
    {
        return $this->isLateMorning() || $this->isLateAfternoon();
    }

    /**
     * Check if user checked in for morning session
     */
    public function hasMorningCheckIn()
    {
        return !is_null($this->morning_check_in);
    }

    /**
     * Check if user checked out for morning session
     */
    public function hasMorningCheckOut()
    {
        return !is_null($this->morning_check_out);
    }

    /**
     * Check if user checked in for afternoon session
     */
    public function hasAfternoonCheckIn()
    {
        return !is_null($this->afternoon_check_in);
    }

    /**
     * Check if user checked out for afternoon session
     */
    public function hasAfternoonCheckOut()
    {
        return !is_null($this->afternoon_check_out);
    }

    /**
     * Check if morning session is complete
     */
    public function isMorningSessionComplete()
    {
        return $this->hasMorningCheckIn() && $this->hasMorningCheckOut();
    }

    /**
     * Check if afternoon session is complete
     */
    public function isAfternoonSessionComplete()
    {
        return $this->hasAfternoonCheckIn() && $this->hasAfternoonCheckOut();
    }

    /**
     * Check if full day is complete
     */
    public function isFullDayComplete()
    {
        return $this->isMorningSessionComplete() && $this->isAfternoonSessionComplete();
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('attendance_date', now()->year)
                    ->whereMonth('attendance_date', now()->month);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeComplete($query)
    {
        return $query->whereNotNull('morning_check_in')
                    ->whereNotNull('morning_check_out')
                    ->whereNotNull('afternoon_check_in')
                    ->whereNotNull('afternoon_check_out');
    }

    public function scopeIncomplete($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('morning_check_in')
              ->orWhereNull('morning_check_out')
              ->orWhereNull('afternoon_check_in')
              ->orWhereNull('afternoon_check_out');
        });
    }

    // Working Days Integration (using cache-based configuration)

    /**
     * Check if a specific date is a working day
     */
    public static function isWorkingDay(Carbon $date)
    {
        $dayName = strtolower($date->format('l')); // e.g., "monday"
        $workingDays = Cache::get('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        
        return in_array($dayName, $workingDays);
    }

    /**
     * Check if today is a working day
     */
    public static function isTodayWorkingDay()
    {
        return self::isWorkingDay(now());
    }

    /**
     * Check if the attendance date is a working day
     */
    public function isAttendanceDateWorkingDay()
    {
        return self::isWorkingDay($this->attendance_date);
    }

    /**
     * Get working hours configuration for a specific date
     */
    public static function getWorkingHoursForDate(Carbon $date)
    {
        $dayName = strtolower($date->format('l'));
        $workingDaysConfig = Cache::get('working_days_config', []);
        
        if (!isset($workingDaysConfig[$dayName]) || !$workingDaysConfig[$dayName]['is_working']) {
            return null;
        }

        return [
            'morning' => [
                'start' => $workingDaysConfig[$dayName]['morning_start'] ?? '07:30',
                'end' => $workingDaysConfig[$dayName]['morning_end'] ?? '11:30',
            ],
            'afternoon' => [
                'start' => $workingDaysConfig[$dayName]['afternoon_start'] ?? '14:00',
                'end' => $workingDaysConfig[$dayName]['afternoon_end'] ?? '17:30',
            ],
        ];
    }

    /**
     * Get today's working hours
     */
    public static function getTodayWorkingHours()
    {
        return self::getWorkingHoursForDate(now());
    }

    /**
     * Get working hours for this attendance's date
     */
    public function getWorkingHours()
    {
        return self::getWorkingHoursForDate($this->attendance_date);
    }

    /**
     * Check if current time is within working hours
     */
    public static function isWithinWorkingHours(Carbon $time = null)
    {
        $time = $time ?? now();
        
        if (!self::isWorkingDay($time)) {
            return false;
        }

        $hours = self::getWorkingHoursForDate($time);
        if (!$hours) {
            return false;
        }

        $currentTime = $time->format('H:i');
        
        // Check morning session
        if ($currentTime >= $hours['morning']['start'] && $currentTime <= $hours['morning']['end']) {
            return true;
        }
        
        // Check afternoon session
        if ($currentTime >= $hours['afternoon']['start'] && $currentTime <= $hours['afternoon']['end']) {
            return true;
        }
        
        return false;
    }

    /**
     * Get current session (morning or afternoon)
     */
    public static function getCurrentSession(Carbon $time = null)
    {
        $time = $time ?? now();
        
        if (!self::isWorkingDay($time)) {
            return null;
        }

        $hours = self::getWorkingHoursForDate($time);
        if (!$hours) {
            return null;
        }

        $currentTime = $time->format('H:i');
        
        // Check morning session
        if ($currentTime >= $hours['morning']['start'] && $currentTime <= $hours['morning']['end']) {
            return 'morning';
        }
        
        // Check afternoon session  
        if ($currentTime >= $hours['afternoon']['start'] && $currentTime <= $hours['afternoon']['end']) {
            return 'afternoon';
        }
        
        // Between sessions or outside hours
        if ($currentTime < $hours['morning']['start']) {
            return 'before_work';
        } elseif ($currentTime > $hours['morning']['end'] && $currentTime < $hours['afternoon']['start']) {
            return 'break_time';
        } else {
            return 'after_work';
        }
    }

    /**
     * Scope: Only working days
     */
    public function scopeWorkingDaysOnly($query)
    {
        $workingDays = Cache::get('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        
        return $query->where(function($q) use ($workingDays) {
            foreach ($workingDays as $day) {
                $dayNumber = array_search($day, ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
                $q->orWhereRaw('DAYOFWEEK(attendance_date) = ?', [$dayNumber + 1]);
            }
        });
    }

    /**
     * Scope: Only non-working days
     */
    public function scopeNonWorkingDaysOnly($query)
    {
        $workingDays = Cache::get('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        $allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $nonWorkingDays = array_diff($allDays, $workingDays);
        
        return $query->where(function($q) use ($nonWorkingDays) {
            foreach ($nonWorkingDays as $day) {
                $dayNumber = array_search($day, ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
                $q->orWhereRaw('DAYOFWEEK(attendance_date) = ?', [$dayNumber + 1]);
            }
        });
    }

    /**
     * Get next working day from a given date
     */
    public static function getNextWorkingDay(Carbon $date = null)
    {
        $date = $date ? $date->copy() : now();
        $maxAttempts = 14; // Check up to 2 weeks ahead
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $date->addDay();
            
            if (self::isWorkingDay($date)) {
                return $date;
            }
        }
        
        return now()->addDay();
    }

    /**
     * Count working days in date range
     */
    public static function countWorkingDaysInRange(Carbon $startDate, Carbon $endDate)
    {
        $count = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if (self::isWorkingDay($current)) {
                $count++;
            }
            $current->addDay();
        }
        
        return $count;
    }

    /**
     * Get all configured working days
     */
    public static function getConfiguredWorkingDays()
    {
        return Cache::get('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
    }

    /**
     * Get formatted working days list
     */
    public static function getFormattedWorkingDaysList()
    {
        $days = self::getConfiguredWorkingDays();
        $formatted = array_map('ucfirst', $days);
        
        if (empty($formatted)) {
            return 'No working days configured';
        }
        
        if (count($formatted) === 7) {
            return 'All days';
        }
        
        return implode(', ', $formatted);
    }

    /**
     * Check and mark attendance as absent based on rules
     * Rules:
     * 1. Morning check-in after 9:00 AM → Mark morning as absent
     * 2. Morning check-out before 11:00 AM → Mark morning as absent
     * 3. No morning check-out by 2:00 PM → Mark morning as absent
     * 4. Afternoon check-in after 3:00 PM → Mark afternoon as absent
     * 5. Afternoon check-out before 5:00 PM → Mark afternoon as absent
     * 6. No afternoon check-out by 5:30 PM → Mark afternoon as absent
     * 7. Morning check-in without check-out (after 2:00 PM) → Mark morning as absent (nullify both)
     * 8. Afternoon check-in without check-out (after 5:30 PM) → Mark afternoon as absent (nullify both)
     */
    public function checkAndMarkAbsent()
    {
        $changes = [];
        $now = now();
        $currentTime = $now->format('H:i');
        
        // Morning Session Rules
        if ($this->morning_check_in) {
            $morningCheckIn = Carbon::parse($this->morning_check_in);
            
            // Rule 1: If check-in is after 9:00 AM → Mark as absent
            if ($morningCheckIn->format('H:i') > '09:00') {
                $this->morning_check_in = null;
                $this->morning_check_out = null;
                $changes[] = 'Morning check-in after 9:00 AM';
            }
            // Rule 3 & 7: If no morning check-out and afternoon session has started (after 2:00 PM) → Mark morning as absent
            elseif (!$this->morning_check_out && $currentTime >= '14:00') {
                $this->morning_check_in = null;
                $this->morning_check_out = null;
                $changes[] = 'Morning check-in without check-out';
            }
        }
        
        if ($this->morning_check_out && $this->morning_check_in) {
            $morningCheckOut = Carbon::parse($this->morning_check_out);
            
            // Rule 2: If check-out is before 11:00 AM → Mark as absent
            if ($morningCheckOut->format('H:i') < '11:00') {
                $this->morning_check_in = null;
                $this->morning_check_out = null;
                $changes[] = 'Morning check-out before 11:00 AM';
            }
        }
        
        // Afternoon Session Rules
        if ($this->afternoon_check_in) {
            $afternoonCheckIn = Carbon::parse($this->afternoon_check_in);
            
            // Rule 4: If check-in is after 3:00 PM (15:00) → Mark as absent
            if ($afternoonCheckIn->format('H:i') > '15:00') {
                $this->afternoon_check_in = null;
                $this->afternoon_check_out = null;
                $changes[] = 'Afternoon check-in after 3:00 PM';
            }
            // Rule 6 & 8: If no afternoon check-out and it's past end of work day (after 5:30 PM) → Mark afternoon as absent
            elseif (!$this->afternoon_check_out && $currentTime >= '17:30') {
                $this->afternoon_check_in = null;
                $this->afternoon_check_out = null;
                $changes[] = 'Afternoon check-in without check-out';
            }
        }
        
        if ($this->afternoon_check_out && $this->afternoon_check_in) {
            $afternoonCheckOut = Carbon::parse($this->afternoon_check_out);
            
            // Rule 5: If check-out is before 5:00 PM (17:00) → Mark as absent
            if ($afternoonCheckOut->format('H:i') < '17:00') {
                $this->afternoon_check_in = null;
                $this->afternoon_check_out = null;
                $changes[] = 'Afternoon check-out before 5:00 PM';
            }
        }
        
        // Update status if any changes were made
        if (!empty($changes)) {
            // Check if both sessions are now null
            if (!$this->morning_check_in && !$this->afternoon_check_in) {
                $this->status = 'absent';
                $this->absent_note = 'Automatically marked absent: ' . implode(', ', $changes);
            } elseif (!$this->morning_check_in || !$this->afternoon_check_in) {
                $this->status = 'half_day';
                $this->absent_note = 'Half day: ' . implode(', ', $changes);
            }
            
            // Recalculate work hours
            $this->calculateWorkHours();
            $this->save();
        }
        
        return $changes;
    }

    /**
     * Auto-check for missing check-outs and mark as absent
     * Should be called via scheduled task
     */
    public static function autoCheckMissingCheckouts()
    {
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');
        
        // Check for missing morning check-outs (after 2:00 PM)
        if ($currentTime >= '14:00') {
            Attendance::whereDate('attendance_date', $today)
                      ->whereNotNull('morning_check_in')
                      ->whereNull('morning_check_out')
                      ->each(function ($attendance) {
                          $attendance->checkAndMarkAbsent();
                      });
        }
        
        // Check for missing afternoon check-outs (after 5:30 PM)
        if ($currentTime >= '17:30') {
            Attendance::whereDate('attendance_date', $today)
                      ->whereNotNull('afternoon_check_in')
                      ->whereNull('afternoon_check_out')
                      ->each(function ($attendance) {
                          $attendance->checkAndMarkAbsent();
                      });
        }
    }
}
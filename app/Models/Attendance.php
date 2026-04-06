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
        'mission_id',
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

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    // ============================================
    // MISSION ATTENDANCE METHODS
    // ============================================

    /**
     * Calculate work hours for mission attendance
     * Mission attendance uses only morning_check_in and morning_check_out for start/end times
     * No separate sessions - just total hours for the day
     */
   public function calculateMissionWorkHours()
    {
        // If mission is pending or rejected, mark as absent with 0 hours
        if (!$this->mission_id || !$this->mission || !$this->mission->isApproved()) {
            $this->work_hours = 0;
            $this->status = 'absent'; // 🔥 Mark as absent until approved
            $this->save();
            return;
        }

        // Calculate total hours from check-in to check-out (for approved missions)
        if ($this->morning_check_in && $this->morning_check_out) {
            $checkIn = Carbon::parse($this->morning_check_in);
            $checkOut = Carbon::parse($this->morning_check_out);
            $totalMinutes = $checkOut->diffInMinutes($checkIn);

            // Convert to decimal hours
            $this->work_hours = round($totalMinutes / 60, 2);
            $this->status = 'on_time'; // 🔥 Mark as on_time when approved
            $this->save();
        } else {
            $this->work_hours = 0;
            $this->status = 'absent';
            $this->save();
        }
    }


    /**
     * Check if this is mission attendance
     */
    public function isMissionAttendance()
    {
        return !is_null($this->mission_id);
    }

    /**
     * Get mission check-in time
     */
    public function getMissionCheckInAttribute()
    {
        if (!$this->mission_id) {
            return null;
        }
        return $this->morning_check_in;
    }

    /**
     * Get mission check-out time
     */
    public function getMissionCheckOutAttribute()
    {
        if (!$this->mission_id) {
            return null;
        }
        return $this->morning_check_out;
    }

    /**
     * Check if mission is approved
     */
    public function isMissionApproved()
    {
        if (!$this->mission_id || !$this->mission) {
            return false;
        }
        return $this->mission->isApproved();
    }

    /**
     * Check if mission is pending
     */
    public function isMissionPending()
    {
        if (!$this->mission_id || !$this->mission) {
            return false;
        }
        return $this->mission->isPending();
    }

    // ============================================
    // WORK HOURS CALCULATION (REGULAR + MISSION)
    // ============================================

    /**
     * Calculate total work hours from both morning and afternoon sessions
     * OR from mission total hours if mission attendance
     */
    public function calculateWorkHours()
    {
        // If this is mission attendance, use mission calculation
        if ($this->mission_id) {
            return $this->calculateMissionWorkHours();
        }

        // Regular attendance calculation
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
        // For mission attendance, return 0 if not approved
        if ($this->mission_id) {
            return $this->isMissionApproved() ? $this->work_hours : 0;
        }

        // Regular attendance
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
        // Mission attendance has no afternoon session
        if ($this->mission_id) {
            return 0;
        }

        if (!$this->afternoon_check_in || !$this->afternoon_check_out) {
            return 0;
        }

        $afternoonIn = Carbon::parse($this->afternoon_check_in);
        $afternoonOut = Carbon::parse($this->afternoon_check_out);
        $totalMinutes = $afternoonOut->diffInMinutes($afternoonIn);

        return round($totalMinutes / 60, 2);
    }

    // ============================================
    // FORMATTED HOURS ATTRIBUTES
    // ============================================

    /**
     * Get formatted work hours as "Xh Ym" (e.g., "8h 30m")
     */
    public function getFormattedWorkHoursAttribute()
    {
        // If mission is not approved, show 0 hours (marked as absent)
        if ($this->mission_id && !$this->isMissionApproved()) {
            return '0h'; // 🔥 Show 0 hours for pending/rejected missions
        }

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
        // For mission attendance, return 0 hours if not approved
        if ($this->mission_id) {
            if (!$this->isMissionApproved()) {
                return '0h'; // 🔥 Show 0 hours for pending/rejected missions
            }
            return $this->formatted_work_hours;
        }

        // Regular attendance
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
        // For mission attendance, show N/A (no separate afternoon session)
        if ($this->mission_id) {
            return '—';
        }

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
        // For mission, return 0 if not approved
        if ($this->mission_id && !$this->isMissionApproved()) {
            return 0;
        }

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
        // For mission, return 0 if not approved
        if ($this->mission_id && !$this->isMissionApproved()) {
            return 0;
        }

        if (!$this->work_hours) {
            return 0;
        }

        $totalMinutes = $this->work_hours * 60;
        return $totalMinutes % 60;
    }

    /**
     * Get total work duration as human-readable string
     */
    public function getWorkDurationAttribute()
    {
        // For mission, show "Absent" if not approved
        if ($this->mission_id && !$this->isMissionApproved()) {
            return 'Absent (Pending Approval)'; // 🔥 Show absent for pending missions
        }

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


    // ============================================
    // CHECK-IN/OUT STATUS METHODS
    // ============================================

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
        // Mission attendance only needs morning session
        if ($this->mission_id) {
            return $this->isMorningSessionComplete();
        }

        return $this->isMorningSessionComplete() && $this->isAfternoonSessionComplete();
    }

    // ============================================
    // LATE CHECK METHODS
    // ============================================

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

    // ============================================
    // SCOPES
    // ============================================

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

    /**
     * Scope for mission attendances only
     */
    public function scopeMissionOnly($query)
    {
        return $query->whereNotNull('mission_id');
    }

    /**
     * Scope for regular attendances only
     */
    public function scopeRegularOnly($query)
    {
        return $query->whereNull('mission_id');
    }

    /**
     * Scope for approved mission attendances
     */
    public function scopeApprovedMissions($query)
    {
        return $query->whereHas('mission', function($q) {
            $q->where('status', 'approved');
        });
    }

    /**
     * Scope for pending mission attendances
     */
    public function scopePendingMissions($query)
    {
        return $query->whereHas('mission', function($q) {
            $q->where('status', 'pending');
        });
    }

    // ============================================
    // WORKING DAYS METHODS
    // ============================================

    /**
     * Check if a specific date is a working day
     */
    public static function isWorkingDay(Carbon $date)
    {
        $dayName = strtolower($date->format('l'));
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
        
        if ($currentTime >= $hours['morning']['start'] && $currentTime <= $hours['morning']['end']) {
            return true;
        }
        
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
        
        if ($currentTime >= $hours['morning']['start'] && $currentTime <= $hours['morning']['end']) {
            return 'morning';
        }
        
        if ($currentTime >= $hours['afternoon']['start'] && $currentTime <= $hours['afternoon']['end']) {
            return 'afternoon';
        }
        
        if ($currentTime < $hours['morning']['start']) {
            return 'before_work';
        } elseif ($currentTime > $hours['morning']['end'] && $currentTime < $hours['afternoon']['start']) {
            return 'break_time';
        } else {
            return 'after_work';
        }
    }

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
        $maxAttempts = 14;
        
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

    // ============================================
    // ABSENT MARKING METHODS
    // ============================================

    /**
     * Check and mark attendance as absent based on rules
     */
    public function checkAndMarkAbsent()
    {
        // Skip for mission attendance
        if ($this->mission_id) {
            return [];
        }

        $changes = [];
        $now = now();
        $currentTime = $now->format('H:i');
        
        // Morning Session Rules
        if ($this->morning_check_in) {
            $morningCheckIn = Carbon::parse($this->morning_check_in);
            
            if ($morningCheckIn->format('H:i') > '09:00') {
                $this->morning_check_in = null;
                $this->morning_check_out = null;
                $changes[] = 'Morning check-in after 9:00 AM';
            }
            elseif (!$this->morning_check_out && $currentTime >= '14:00') {
                $this->morning_check_in = null;
                $this->morning_check_out = null;
                $changes[] = 'Morning check-in without check-out';
            }
        }
        
        if ($this->morning_check_out && $this->morning_check_in) {
            $morningCheckOut = Carbon::parse($this->morning_check_out);
            
            if ($morningCheckOut->format('H:i') < '11:00') {
                $this->morning_check_in = null;
                $this->morning_check_out = null;
                $changes[] = 'Morning check-out before 11:00 AM';
            }
        }
        
        // Afternoon Session Rules
        if ($this->afternoon_check_in) {
            $afternoonCheckIn = Carbon::parse($this->afternoon_check_in);
            
            if ($afternoonCheckIn->format('H:i') > '15:00') {
                $this->afternoon_check_in = null;
                $this->afternoon_check_out = null;
                $changes[] = 'Afternoon check-in after 3:00 PM';
            }
            elseif (!$this->afternoon_check_out && $currentTime >= '17:30') {
                $this->afternoon_check_in = null;
                $this->afternoon_check_out = null;
                $changes[] = 'Afternoon check-in without check-out';
            }
        }
        
        if ($this->afternoon_check_out && $this->afternoon_check_in) {
            $afternoonCheckOut = Carbon::parse($this->afternoon_check_out);
            
            if ($afternoonCheckOut->format('H:i') < '17:00') {
                $this->afternoon_check_in = null;
                $this->afternoon_check_out = null;
                $changes[] = 'Afternoon check-out before 5:00 PM';
            }
        }
        
        if (!empty($changes)) {
            if (!$this->morning_check_in && !$this->afternoon_check_in) {
                $this->status = 'absent';
                $this->absent_note = 'Automatically marked absent: ' . implode(', ', $changes);
            } elseif (!$this->morning_check_in || !$this->afternoon_check_in) {
                $this->status = 'half_day';
                $this->absent_note = 'Half day: ' . implode(', ', $changes);
            }
            
            $this->calculateWorkHours();
            $this->save();
        }
        
        return $changes;
    }

    /**
     * Auto-check for missing check-outs and mark as absent
     */
    public static function autoCheckMissingCheckouts()
    {
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');
        
        if ($currentTime >= '14:00') {
            Attendance::whereDate('attendance_date', $today)
                      ->whereNull('mission_id') // Skip mission attendance
                      ->whereNotNull('morning_check_in')
                      ->whereNull('morning_check_out')
                      ->each(function ($attendance) {
                          $attendance->checkAndMarkAbsent();
                      });
        }
        
        if ($currentTime >= '17:30') {
            Attendance::whereDate('attendance_date', $today)
                      ->whereNull('mission_id') // Skip mission attendance
                      ->whereNotNull('afternoon_check_in')
                      ->whereNull('afternoon_check_out')
                      ->each(function ($attendance) {
                          $attendance->checkAndMarkAbsent();
                      });
        }
    }
}
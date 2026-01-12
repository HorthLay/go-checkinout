<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'check_in',
        'check_out',
        'longitude',
        'latitude',
        'status',
        'work_hours',
        'absent_note',
        'note',
        'office_location_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'work_hours' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officeLocation()
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    /**
     * Calculate work hours with decimal precision (includes minutes)
     * Stores total hours as decimal (e.g., 8.5 hours = 8 hours 30 minutes)
     */
    public function calculateWorkHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            
            // Calculate total minutes worked
            $totalMinutes = $checkOut->diffInMinutes($checkIn);
            
            // Convert to decimal hours (e.g., 510 minutes = 8.5 hours)
            $this->work_hours = round($totalMinutes / 60, 2);
            
            $this->save();
        }
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

    // Check if late
    public function isLate()
    {
        $schedule = AttendanceSchedule::where('user_id', $this->user_id)
                                      ->where('is_active', true)
                                      ->first();
        
        if (!$schedule || !$this->check_in) {
            return false;
        }

        $checkInTime = Carbon::parse($this->check_in);
        $scheduledTime = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $schedule->scheduled_check_in);
        $lateThreshold = $scheduledTime->addMinutes($schedule->late_allowed_min);

        return $checkInTime->gt($lateThreshold);
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
}
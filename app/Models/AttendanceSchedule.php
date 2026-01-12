<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'late_allowed_min',
        'scheduled_check_in_morining',
        'scheduled_check_out_morining',
        'scheduled_check_in_afternoon',
        'scheduled_check_out_afternoon',
        'is_active'
    ];

    protected $casts = [
        'scheduled_check_in_morining' => 'string',
        'scheduled_check_out_morining' => 'string',
        'scheduled_check_in_afternoon' => 'string',
        'scheduled_check_out_afternoon' => 'string',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get total scheduled work hours per day
     */
    public function getTotalScheduledHoursAttribute()
    {
        $morningIn = Carbon::parse($this->scheduled_check_in_morining);
        $morningOut = Carbon::parse($this->scheduled_check_out_morining);
        $afternoonIn = Carbon::parse($this->scheduled_check_in_afternoon);
        $afternoonOut = Carbon::parse($this->scheduled_check_out_afternoon);

        $morningMinutes = $morningOut->diffInMinutes($morningIn);
        $afternoonMinutes = $afternoonOut->diffInMinutes($afternoonIn);

        return round(($morningMinutes + $afternoonMinutes) / 60, 2);
    }

    /**
     * Get morning session scheduled hours
     */
    public function getMorningScheduledHoursAttribute()
    {
        $morningIn = Carbon::parse($this->scheduled_check_in_morining);
        $morningOut = Carbon::parse($this->scheduled_check_out_morining);

        $morningMinutes = $morningOut->diffInMinutes($morningIn);

        return round($morningMinutes / 60, 2);
    }

    /**
     * Get afternoon session scheduled hours
     */
    public function getAfternoonScheduledHoursAttribute()
    {
        $afternoonIn = Carbon::parse($this->scheduled_check_in_afternoon);
        $afternoonOut = Carbon::parse($this->scheduled_check_out_afternoon);

        $afternoonMinutes = $afternoonOut->diffInMinutes($afternoonIn);

        return round($afternoonMinutes / 60, 2);
    }

    /**
     * Get formatted total scheduled hours
     */
    public function getFormattedTotalScheduledHoursAttribute()
    {
        $hours = $this->total_scheduled_hours;
        $totalMinutes = $hours * 60;
        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;

        if ($m > 0) {
            return "{$h}h {$m}m";
        }

        return "{$h}h";
    }

    /**
     * Check if currently within morning session time
     */
    public function isCurrentlyMorningSession()
    {
        $now = now()->format('H:i:s');
        return $now >= $this->scheduled_check_in_morining 
            && $now <= $this->scheduled_check_out_morining;
    }

    /**
     * Check if currently within afternoon session time
     */
    public function isCurrentlyAfternoonSession()
    {
        $now = now()->format('H:i:s');
        return $now >= $this->scheduled_check_in_afternoon 
            && $now <= $this->scheduled_check_out_afternoon;
    }

    /**
     * Get current active session (morning/afternoon/none)
     */
    public function getCurrentSessionAttribute()
    {
        if ($this->isCurrentlyMorningSession()) {
            return 'morning';
        }
        
        if ($this->isCurrentlyAfternoonSession()) {
            return 'afternoon';
        }

        return 'none';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
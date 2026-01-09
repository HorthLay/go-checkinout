<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
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
        'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWorkHoursAttribute($value)
    {
        if ($this->check_in && $this->check_out) {
            $hours = Carbon::parse($this->check_in)
                    ->diffInMinutes(Carbon::parse($this->check_out)) / 60;
            return round($hours, 2);
        }
        return $value;
    }

        public function calculateWorkHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $this->work_hours = $checkOut->diffInHours($checkIn, true);
            $this->save();
        }
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

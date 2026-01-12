<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceOffDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'off_date',
        'reason',
    ];

    protected $casts = [
        'off_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if date is off day for user
    public static function isOffDay($userId, $date)
    {
        return self::where('user_id', $userId)
                  ->whereDate('off_date', $date)
                  ->exists();
    }

    // Model events
    protected static function boot()
    {
        parent::boot();

        // When day-off is created
        static::created(function ($dayOff) {
            // Check if user already checked in/out on this day
            $attendance = \App\Models\Attendance::where('user_id', $dayOff->user_id)
                                                ->whereDate('attendance_date', $dayOff->off_date)
                                                ->first();
            
            if ($attendance) {
                // User already has attendance record (checked in/out)
                if ($attendance->check_in || $attendance->check_out) {
                    // Mark as leave since they showed up
                    $attendance->update([
                        'status' => 'leave',
                        'absent_note' => $dayOff->reason,
                        'note' => 'Day off (with attendance): ' . $dayOff->reason,
                    ]);
                } else {
                    // No check-in/out, mark as absent
                    $attendance->update([
                        'status' => 'absent',
                        'absent_note' => $dayOff->reason,
                        'note' => 'Day off (absent): ' . $dayOff->reason,
                    ]);
                }
            } else {
                // No attendance record exists, create one with absent status
                \App\Models\Attendance::create([
                    'user_id' => $dayOff->user_id,
                    'attendance_date' => $dayOff->off_date,
                    'check_in' => null,
                    'check_out' => null,
                    'longitude' => null,
                    'latitude' => null,
                    'status' => 'absent',
                    'work_hours' => null,
                    'absent_note' => $dayOff->reason,
                    'note' => 'Day off (absent): ' . $dayOff->reason,
                ]);
            }
        });

        // When day-off is updated
        static::updated(function ($dayOff) {
            $attendance = \App\Models\Attendance::where('user_id', $dayOff->user_id)
                                                ->whereDate('attendance_date', $dayOff->off_date)
                                                ->first();
            
            if ($attendance) {
                // Update based on whether user checked in/out
                if ($attendance->check_in || $attendance->check_out) {
                    $attendance->update([
                        'status' => 'leave',
                        'absent_note' => $dayOff->reason,
                        'note' => 'Day off (with attendance): ' . $dayOff->reason,
                    ]);
                } else {
                    $attendance->update([
                        'status' => 'absent',
                        'absent_note' => $dayOff->reason,
                        'note' => 'Day off (absent): ' . $dayOff->reason,
                    ]);
                }
            }
        });

        // When day-off is deleted
        static::deleted(function ($dayOff) {
            $attendance = \App\Models\Attendance::where('user_id', $dayOff->user_id)
                                                ->whereDate('attendance_date', $dayOff->off_date)
                                                ->first();
            
            // Only delete if it's absent/leave status with no actual check-in
            if ($attendance && !$attendance->check_in && !$attendance->check_out) {
                if (in_array($attendance->status, ['absent', 'leave'])) {
                    $attendance->delete();
                }
            }
        });
    }
}
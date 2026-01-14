<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telegram_id',
        'telegram_chat_id',
        'phone',
        'password',
        'gender',
        'role_type',
        'gender',
        'active',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function attendances()
{
    return $this->hasMany(Attendance::class);
}

public function attendanceSchedule()
{
    return $this->hasOne(AttendanceSchedule::class);
}

public function offDays()
{
    return $this->hasMany(AttendanceOffDay::class);
}

 // Helper methods
    public function isAdmin(): bool
    {
        return $this->role_type === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role_type === 'user';
    }

    // Get today's attendance
    public function todayAttendance()
    {
        return $this->attendances()->whereDate('attendance_date', today())->first();
    }
     // Get image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('users/' . $this->image);
        }
        return null;
    }

    // Check if user has checked in today
    public function hasCheckedInToday(): bool
    {
        return $this->attendances()
                    ->whereDate('attendance_date', today())
                    ->whereNotNull('check_in')
                    ->exists();
    }

    // Check if user has checked out today
    public function hasCheckedOutToday(): bool
    {
        return $this->attendances()
                    ->whereDate('attendance_date', today())
                    ->whereNotNull('check_out')
                    ->exists();
    }
}

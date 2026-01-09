<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSchedule extends Model
{
      protected $fillable = [
        'user_id',
        'scheduled_check_in',
        'scheduled_check_out',
        'late_allowed_min',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

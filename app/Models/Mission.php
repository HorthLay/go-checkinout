<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'longitude',
        'latitude',
        'mission_date',
        'status', // pending, approved, rejected
        'active',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'longitude' => 'decimal:8',
        'latitude' => 'decimal:8',
        'mission_date' => 'date',
        'active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who created the mission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the mission
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the attendance record for this mission
     */
    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    /**
     * Get all attendances for this mission
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Scope to get only active missions
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to get pending missions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved missions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected missions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if mission is approved
     */
public function isPending()
{
    return $this->status === 'pending';
}

public function isApproved()
{
    return $this->status === 'approved';
}

public function isRejected()
{
    return $this->status === 'rejected';
}
    /**
     * Approve the mission and update attendance
     */
  public function approve($adminId)
{
    $this->status = 'approved';
    $this->approved_by = $adminId;
    $this->approved_at = now();
    $this->rejection_reason = null;
    $this->save();

    // 🔥 UPDATE ATTENDANCE STATUS AND WORK HOURS
    if ($this->attendance) {
        $this->attendance->calculateMissionWorkHours();
    }

    return $this;
}

    /**
     * Reject the mission and update attendance
     */
public function reject($adminId, $reason = null)
{
    $this->status = 'rejected';
    $this->approved_by = $adminId;
    $this->approved_at = now();
    $this->rejection_reason = $reason;
    $this->save();

    // 🔥 MARK ATTENDANCE AS ABSENT
    if ($this->attendance) {
        $this->attendance->calculateMissionWorkHours();
    }

    return $this;
}

    /**
     * Get formatted location
     */
    public function getFormattedLocationAttribute()
    {
        return number_format($this->latitude, 6) . ', ' . number_format($this->longitude, 6);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    
    /**
     * Get display title for mission
     */
    public function getDisplayTitleAttribute()
    {
        return 'Mission - ' . $this->user->name . ' - ' . $this->mission_date->format('M d, Y');
    }
}
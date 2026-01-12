<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Get notification icon based on type
     */
   public function getIconAttribute()
{
    return match($this->type) {
        'checkin' => 'login',
        'checkout' => 'logout',
        'late' => 'schedule',
        'absent' => 'cancel',
        'leave' => 'event_available',
        'alert' => 'warning',
        default => 'notifications',
    };
}

    /**
     * Get notification color based on type
     */
   public function getColorAttribute()
{
    return match($this->type) {
        'checkin' => 'green',
        'checkout' => 'blue',
        'late' => 'orange',
        'absent' => 'red',
        'leave' => 'purple',
        'alert' => 'red',
        default => 'gray',
    };
}
}
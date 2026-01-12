<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showDropdown = false;
    public $showOnlyUnread = false;

    // Remove the echo listener - just keep the manual trigger
    protected $listeners = [
        'notificationCreated' => 'loadNotifications',
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $query = Notification::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc');

        if ($this->showOnlyUnread) {
            $query->unread();
        }

        $this->notifications = $query->take(20)->get();
        $this->unreadCount = Notification::where('user_id', Auth::id())
                                       ->unread()
                                       ->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        
        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
                   ->unread()
                   ->update([
                       'is_read' => true,
                       'read_at' => now(),
                   ]);
        
        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    public function deleteAllRead()
    {
        Notification::where('user_id', Auth::id())
                   ->read()
                   ->delete();
        
        $this->loadNotifications();
    }

    public function toggleFilter()
    {
        $this->showOnlyUnread = !$this->showOnlyUnread;
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
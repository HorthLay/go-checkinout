<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public bool $open = false;
    public bool $showOnlyUnread = false;

    protected $listeners = [
        'refreshNotifications' => '$refresh',
    ];

    public function getNotificationsProperty()
    {
        $query = Notification::where('user_id', Auth::id())
            ->latest()
            ->limit(20);

        if ($this->showOnlyUnread) {
            $query->unread();
        }

        return $query->get();
    }

    public function getUnreadCountProperty()
    {
        return Notification::where('user_id', Auth::id())
            ->unread()
            ->count();
    }

    public function toggle()
    {
        $this->open = !$this->open;
    }

    public function close()
    {
        $this->open = false;
    }

    public function toggleFilter()
    {
        $this->showOnlyUnread = !$this->showOnlyUnread;
    }

    public function markAsRead($id)
    {
        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function deleteNotification($id)
    {
        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();
    }

    public function deleteAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->read()
            ->delete();
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}

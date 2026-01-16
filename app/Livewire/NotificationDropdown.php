<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationDropdown extends Component
{
    public bool $open = false;
    public bool $showOnlyUnread = false;
    public ?string $message = null;
    public ?string $messageType = null;

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
        $this->message = null; // Clear message when toggling
    }

    public function close()
    {
        $this->open = false;
        $this->message = null;
    }

    public function toggleFilter()
    {
        $this->showOnlyUnread = !$this->showOnlyUnread;
    }

    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if ($notification) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
                $this->showMessage('Marked as read', 'success');
            }
        } catch (\Exception $e) {
            $this->showMessage('Failed to mark as read', 'error');
        }
    }

    public function markAllAsRead()
    {
        try {
            $count = Notification::where('user_id', Auth::id())
                ->unread()
                ->count();

            if ($count > 0) {
                Notification::where('user_id', Auth::id())
                    ->unread()
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                
                $this->showMessage("Marked {$count} notification(s) as read", 'success');
            } else {
                $this->showMessage('No unread notifications', 'info');
            }
        } catch (\Exception $e) {
            Log::error('Mark All Read Error: ' . $e->getMessage());
            $this->showMessage('Failed to mark all as read', 'error');
        }
    }

    public function deleteNotification($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if ($notification) {
                $notification->delete();
                $this->showMessage('Notification deleted', 'success');
            }
        } catch (\Exception $e) {
            Log::error('Delete Notification Error: ' . $e->getMessage());
            $this->showMessage('Failed to delete notification', 'error');
        }
    }

    public function deleteAllRead()
    {
        try {
            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', true)
                ->count();

            if ($count === 0) {
                $this->showMessage('No read notifications to delete', 'info');
                return;
            }

            Notification::where('user_id', Auth::id())
                ->where('is_read', true)
                ->delete();
            
            $this->showMessage("Successfully deleted {$count} read notification(s)", 'success');
            
        } catch (\Exception $e) {
            Log::error('Delete All Read Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->showMessage('Failed to delete notifications', 'error');
        }
    }

    private function showMessage($message, $type = 'success')
    {
        $this->message = $message;
        $this->messageType = $type;
        
        // Auto-clear message after 3 seconds
        $this->dispatch('message-shown');
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
<div class="relative" wire:key="notification-dropdown">

    <!-- 🔔 Button -->
    <button
        wire:click="toggle"
        class="size-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 relative transition-colors"
    >
        <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">notifications</span>

        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 size-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold shadow-lg">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- ⬇️ Dropdown -->
    @if($open)
        <div
            wire:click.outside="close"
            class="fixed md:absolute inset-x-0 bottom-0 md:inset-auto md:right-0 md:bottom-auto md:top-full mt-0 md:mt-2 w-full md:w-96 bg-white dark:bg-gray-800 rounded-t-2xl md:rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-[85vh] md:max-h-[32rem] flex flex-col"
        >

            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-base md:text-lg text-gray-900 dark:text-white">Notifications</h3>
                    @if($this->unreadCount)
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $this->unreadCount }} unread</p>
                    @endif
                </div>

                <div class="flex gap-2 items-center">
                    <button 
                        wire:click="toggleFilter" 
                        class="text-sm text-primary hover:text-primary-dark font-medium transition-colors"
                    >
                        {{ $showOnlyUnread ? 'Show All' : 'Unread Only' }}
                    </button>

                    <!-- Mobile close button -->
                    <button 
                        wire:click="close" 
                        class="md:hidden ml-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-1"
                    >
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if($message)
                <div class="mx-4 mt-4 p-3 rounded-lg flex items-center gap-2 text-sm
                    {{ $messageType === 'success' ? 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800' : '' }}
                    {{ $messageType === 'error' ? 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800' : '' }}
                    {{ $messageType === 'info' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800' : '' }}
                ">
                    <span class="material-symbols-outlined text-lg">
                        {{ $messageType === 'success' ? 'check_circle' : ($messageType === 'error' ? 'error' : 'info') }}
                    </span>
                    <span class="font-medium">{{ $message }}</span>
                </div>
            @endif

            <!-- Action Buttons -->
            @if($this->unreadCount > 0 || $this->notifications->where('is_read', true)->count() > 0)
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex gap-2 flex-wrap">
                    @if($this->unreadCount > 0)
                        <button 
                            wire:click="markAllAsRead" 
                            class="text-xs px-3 py-1.5 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/30 font-medium transition-colors flex items-center gap-1"
                        >
                            <span class="material-symbols-outlined text-sm">done_all</span>
                            Mark All Read
                        </button>
                    @endif

                    @if($this->notifications->where('is_read', true)->count() > 0)
                        <button 
                            wire:click="deleteAllRead"
                            wire:confirm="Are you sure you want to delete all read notifications?"
                            class="text-xs px-3 py-1.5 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/30 font-medium transition-colors flex items-center gap-1"
                        >
                            <span class="material-symbols-outlined text-sm">delete_sweep</span>
                            Delete Read
                        </button>
                    @endif
                </div>
            @endif

            <!-- Poll ONLY when open -->
            <div wire:poll.30s class="flex-1 overflow-y-auto overscroll-contain">
                @forelse($this->notifications as $notification)
                    <div 
                        class="p-3 md:p-4 border-b border-gray-200 dark:border-gray-700 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                        wire:key="notification-{{ $notification->id }}"
                    >
                        <div class="flex gap-3">

                            <!-- Icon -->
                            <div class="size-10 shrink-0 rounded-full bg-{{ $notification->color }}-100 dark:bg-{{ $notification->color }}-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-{{ $notification->color }}-600 dark:text-{{ $notification->color }}-400 text-xl">
                                    {{ $notification->icon }}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2">
                                    <p class="font-semibold text-sm text-gray-900 dark:text-white">
                                        {{ $notification->title }}
                                    </p>
                                    @if(!$notification->is_read)
                                        <span class="shrink-0 size-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $notification->message }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-1 shrink-0">
                                @if(!$notification->is_read)
                                    <button 
                                        wire:click="markAsRead({{ $notification->id }})"
                                        class="p-1.5 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/20 text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors"
                                        title="Mark as read"
                                    >
                                        <span class="material-symbols-outlined text-lg">done</span>
                                    </button>
                                @endif
                                <button 
                                    wire:click="deleteNotification({{ $notification->id }})"
                                    wire:confirm="Are you sure you want to delete this notification?"
                                    class="p-1.5 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/20 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                                    title="Delete"
                                >
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 md:p-12 text-center">
                        <div class="inline-flex items-center justify-center size-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                            <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-600">notifications_off</span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">
                            {{ $showOnlyUnread ? 'No unread notifications' : 'No notifications' }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            You're all caught up!
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            <div class="p-3 md:p-4 border-t border-gray-200 dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-900/50 rounded-b-2xl md:rounded-b-xl">
                <a 
                    href="{{ route('admin.notifications') }}" 
                    class="text-sm text-primary hover:text-primary-dark font-medium transition-colors inline-flex items-center gap-1"
                >
                    View all notifications
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Mobile backdrop overlay -->
        <div class="md:hidden fixed inset-0 bg-black/50 z-40" wire:click="close"></div>
    @endif

    @script
    <script>
        // Auto-hide message after 3 seconds
        $wire.on('message-shown', () => {
            setTimeout(() => {
                $wire.set('message', null);
            }, 3000);
        });
    </script>
    @endscript
</div>
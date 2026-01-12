<div class="relative" wire:key="notification-dropdown">

    <!-- 🔔 Button -->
    <button
        wire:click="toggle"
        class="size-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 relative"
    >
        <span class="material-symbols-outlined text-gray-600">notifications</span>

        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 size-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- ⬇️ Dropdown -->
    @if($open)
        <div
            wire:click.outside="close"
            class="fixed md:absolute inset-x-0 bottom-0 md:inset-auto md:right-0 md:bottom-auto md:top-full mt-0 md:mt-2 w-full md:w-96 bg-white dark:bg-gray-800 rounded-t-2xl md:rounded-xl shadow-xl border z-50 max-h-[85vh] md:max-h-[32rem] flex flex-col"
        >

            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-base md:text-lg">Notifications</h3>
                    @if($this->unreadCount)
                        <p class="text-xs text-gray-500">{{ $this->unreadCount }} unread</p>
                    @endif
                </div>

                <div class="flex gap-2 items-center">
                    <button wire:click="toggleFilter" class="text-sm text-primary">
                        {{ $showOnlyUnread ? 'All' : 'Unread' }}
                    </button>

                    @if($this->unreadCount)
                        <button wire:click="markAllAsRead" class="text-xs text-primary whitespace-nowrap">
                            Mark all
                        </button>
                    @endif

                    <!-- Mobile close button -->
                    <button wire:click="close" class="md:hidden ml-2 text-gray-500 hover:text-gray-700">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Poll ONLY when open -->
            <div wire:poll.30s class="flex-1 overflow-y-auto overscroll-contain">
                @forelse($this->notifications as $notification)
                    <div class="p-3 md:p-4 border-b {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        <div class="flex gap-3">

                            <!-- Icon -->
                            <div class="size-10 shrink-0 rounded-full bg-{{ $notification->color }}-100 dark:bg-{{ $notification->color }}-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-{{ $notification->color }}-600 text-xl">
                                    {{ $notification->icon }}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm">{{ $notification->title }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2 shrink-0">
                                @if(!$notification->is_read)
                                    <button 
                                        wire:click="markAsRead({{ $notification->id }})"
                                        class="text-gray-600 hover:text-green-600 text-lg"
                                    >
                                        ✓
                                    </button>
                                @endif
                                <button 
                                    wire:click="deleteNotification({{ $notification->id }})" 
                                    class="text-red-500 hover:text-red-700 text-lg"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-8 md:p-6 text-center text-gray-400">
                        {{ $showOnlyUnread ? 'No unread notifications' : 'No notifications' }}
                    </p>
                @endforelse
            </div>

            <!-- Footer -->
            <div class="p-3 md:p-4 border-t text-center bg-gray-50 dark:bg-gray-900/50 rounded-b-2xl md:rounded-b-xl">
                <a href="{{ route('admin.notifications') }}" class="text-sm text-primary font-medium">
                    View all →
                </a>
            </div>
        </div>

        <!-- Mobile backdrop overlay -->
        <div class="md:hidden fixed inset-0 bg-black/50 z-40" wire:click="close"></div>
    @endif
</div>
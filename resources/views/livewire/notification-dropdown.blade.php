<div wire:poll.30s="loadNotifications" class="relative" x-data="{ open: @entangle('showDropdown') }">
    <!-- Notification Button -->
    <button 
        @click="open = !open"
        class="size-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 relative transition-colors"
    >
        <span class="material-symbols-outlined">notifications</span>
        @if($unreadCount > 0)
            <span class="absolute top-2.5 right-2.5 size-2 bg-red-500 border-2 border-white dark:border-gray-900 rounded-full animate-pulse"></span>
            <span class="absolute -top-1 -right-1 size-5 flex items-center justify-center bg-red-500 text-white text-xs font-bold rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div 
        x-show="open"
        x-cloak
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-[32rem] flex flex-col"
    >
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Notifications</h3>
                @if($unreadCount > 0)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $unreadCount }} unread</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <!-- Filter Toggle -->
                <button 
                    wire:click="toggleFilter"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="{{ $showOnlyUnread ? 'Show All' : 'Show Unread Only' }}"
                >
                    <span class="material-symbols-outlined text-lg {{ $showOnlyUnread ? 'text-primary' : 'text-gray-500' }}">
                        filter_list
                    </span>
                </button>

                <!-- Mark All as Read -->
                @if($unreadCount > 0)
                    <button 
                        wire:click="markAllAsRead"
                        class="text-xs text-primary hover:text-primary-dark font-medium"
                    >
                        Mark all read
                    </button>
                @endif

                <!-- Delete All Read -->
                @if(collect($notifications)->where('is_read', true)->count() > 0)
                    <button 
                        wire:click="deleteAllRead"
                        wire:confirm="Are you sure you want to delete all read notifications?"
                        class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                        title="Delete All Read"
                    >
                        <span class="material-symbols-outlined text-lg text-red-600 dark:text-red-400">delete</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="flex-1 overflow-y-auto">
            @forelse($notifications as $notification)
                <div 
                    class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 size-10 rounded-full bg-{{ $notification->color }}-100 dark:bg-{{ $notification->color }}-900/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-{{ $notification->color }}-600 dark:text-{{ $notification->color }}-400 text-lg">
                                {{ $notification->icon }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $notification->title }}
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $notification->message }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-1">
                                    @if(!$notification->is_read)
                                        <button 
                                            wire:click="markAsRead({{ $notification->id }})"
                                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                                            title="Mark as read"
                                        >
                                            <span class="material-symbols-outlined text-sm text-gray-500">done</span>
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="deleteNotification({{ $notification->id }})"
                                        class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/20"
                                        title="Delete"
                                    >
                                        <span class="material-symbols-outlined text-sm text-red-600">close</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Timestamp -->
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>

                            <!-- Additional Data -->
                            @if($notification->data)
                                <div class="mt-2 flex items-center gap-2 text-xs">
                                    @if(isset($notification->data['location']))
                                        <span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                            <span class="material-symbols-outlined text-xs">location_on</span>
                                            {{ $notification->data['location'] }}
                                        </span>
                                    @endif
                                    @if(isset($notification->data['work_hours']))
                                        <span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                            <span class="material-symbols-outlined text-xs">schedule</span>
                                            {{ $notification->data['work_hours'] }}
                                        </span>
                                    @endif
                                    @if(isset($notification->data['actual_distance']))
                                        <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                                            <span class="material-symbols-outlined text-xs">warning</span>
                                            {{ $notification->data['actual_distance'] }}m away
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">notifications_off</span>
                    <p class="text-gray-500 dark:text-gray-400 mt-3">
                        {{ $showOnlyUnread ? 'No unread notifications' : 'No notifications yet' }}
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
            <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-center">
                <a 
                    href="{{ route('admin.notifications') }}" 
                    class="text-sm text-primary hover:text-primary-dark font-medium"
                >
                    View all notifications →
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
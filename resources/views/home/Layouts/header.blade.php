@auth
<div class="flex items-center gap-3 md:gap-4">
    <!-- Search Bar -->
    <div class="relative hidden md:block group">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">search</span>
        </span>
        <input 
            class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-64 transition-all" 
            placeholder="Search..." 
            type="text" 
        />
    </div>

    <!-- Theme Toggle -->
    <button 
        id="theme-toggle" 
        class="size-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 transition-colors"
        aria-label="Toggle theme"
    >
        <span class="material-symbols-outlined dark:hidden">dark_mode</span>
        <span class="material-symbols-outlined hidden dark:block">light_mode</span>
    </button>

    <!-- Notification Dropdown (Admin Only) -->
    @if(Auth::user()->role_type === 'admin')
        @livewire('notification-dropdown')
    @endif

    <!-- Profile Dropdown -->
    <div class="relative" x-data="{ open: false }" @click.away="open = false">
        <button 
            @click="open = !open"
            class="flex items-center gap-3 pl-2 hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg transition-colors"
            aria-label="Toggle profile dropdown"
        >
            @php($user = Auth::user())

            @if($user->image)
                <img
                    src="{{ asset('users/' . $user->image) }}"
                    alt="{{ $user->name }}"
                    class="size-10 rounded-full object-cover shadow-sm"
                >
            @else
                <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif

            <div class="hidden md:block text-left">
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ $user->name }}</p>
                <p class="text-[11px] text-gray-500 font-medium mt-1">{{ ucfirst($user->role_type) }}</p>
            </div>

            <span class="material-symbols-outlined text-gray-500 hidden lg:block">
                <span x-show="!open">expand_more</span>
                <span x-show="open" x-cloak>expand_less</span>
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div 
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-64 bg-surface-light dark:bg-surface-dark rounded-xl shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden"
        >
            <!-- Profile Info -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3 mb-3">
                    @if($user->image)
                        <img
                            src="{{ asset('users/' . $user->image) }}"
                            alt="{{ $user->name }}"
                            class="size-12 rounded-full object-cover shadow-sm"
                        >
                    @else
                        <div class="size-12 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold shadow-sm text-lg">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 font-medium truncate">{{ $user->email }}</p>
                    </div>
                </div>
                
                <!-- Role Badge -->
                <div class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                    {{ $user->role_type === 'admin' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' }}">
                    <span class="material-symbols-outlined text-sm mr-1">
                        {{ $user->role_type === 'admin' ? 'admin_panel_settings' : 'person' }}
                    </span>
                    {{ ucfirst($user->role_type) }}
                </div>
                
                <!-- Telegram Status -->
                @if(!is_null($user->telegram_id))
                    <div class="flex items-center gap-2 mt-2 px-2.5 py-1.5 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                        </svg>
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Connected</span>
                    </div>
                @endif
            </div>

            <!-- Menu Items -->
            <div class="py-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all">
                    <span class="material-symbols-outlined text-xl">person</span>
                    <span class="text-sm font-medium">Profile</span>
                </a>
                
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all">
                    <span class="material-symbols-outlined text-xl">settings</span>
                    <span class="text-sm font-medium">Settings</span>
                </a>

                <!-- Telegram Bind/Unbind -->
                @if(is_null($user->telegram_id))
                    <a href="{{ route('telegram.bind') }}" class="flex items-center gap-3 px-4 py-2.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                        </svg>
                        <span class="text-sm font-medium">Bind Telegram</span>
                    </a>
                @else
                    <a 
                        href="{{ route('telegram.unbind') }}" 
                        class="flex items-center gap-3 px-4 py-2.5 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all"
                        onclick="return confirm('Are you sure you want to unbind your Telegram account? You will stop receiving notifications.')"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                        </svg>
                        <span class="text-sm font-medium">Unbind Telegram</span>
                    </a>
                @endif

                <!-- Divider -->
                <div class="border-t border-gray-100 dark:border-gray-800 my-2"></div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
                        <span class="material-symbols-outlined text-xl">logout</span>
                        <span class="text-sm font-medium">Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth
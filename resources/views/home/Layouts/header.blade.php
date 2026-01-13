  
    @auth
    <div class="flex items-center gap-3 md:gap-4">
        {{-- <div class="relative hidden md:block group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-64 transition-all" placeholder="Search..." type="text" />
        </div> --}}

       

    <livewire:notification-dropdown />

        <div class="relative">
            <button id="profile-toggle" aria-label="Toggle profile dropdown" class="flex items-center gap-3 pl-2 hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg transition-colors">
                <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="hidden md:block">
                    <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-500 font-medium mt-1">{{ ucfirst(Auth::user()->role_type) }}</p>
                </div>
            </button>
            <div id="profile-dropdown" class="hidden absolute top-20 right-0 mt-2 w-64 bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 z-30">
                <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">{{ Auth::user()->email }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">{{ ucfirst(Auth::user()->role_type) }}</p>
                    
                    {{-- Telegram Status in Dropdown --}}
                    @if(!is_null(Auth::user()->telegram_id))
                        <div class="flex items-center gap-2 mt-2 px-2 py-1 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                            </svg>
                            <span class="text-xs text-green-600 dark:text-green-400 font-medium">Telegram Connected</span>
                        </div>
                    @endif
                </div>
                <div class="py-2">
                    {{-- <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all">
                        <span class="material-symbols-outlined">person</span>
                        <span>Profile</span>
                    </a> --}}
                      @if(Auth::user()->role_type === 'admin')
                      <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all">
                          <span class="material-symbols-outlined">settings</span>
                          <span>Settings</span>
                      </a>
                    @endif
                    
                    {{-- Telegram Bind/Unbind in Dropdown --}}
                    @if(is_null(Auth::user()->telegram_id))
                        <a href="{{ route('telegram.bind') }}" class="flex items-center gap-3 px-4 py-2.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                            </svg>
                            <span>Bind Telegram</span>
                        </a>
                    @else
                        <a href="{{ route('telegram.unbind') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all"
                           onclick="return confirm('Are you sure you want to unbind your Telegram account?')">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                            </svg>
                            <span>Unbind Telegram</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
                            <span class="material-symbols-outlined">logout</span>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endauth
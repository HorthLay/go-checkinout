<div>
    <div class="relative" x-data="{ open: @entangle('showDropdown') }" @click.away="open = false">
        <!-- Bell Icon with Badge -->
        <button wire:click="toggleDropdown" 
                @click="playSound('click')"
                class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all duration-200 active:scale-95 min-h-[44px] min-w-[44px] flex items-center justify-center"
                title="Mission Alerts">
            <span class="material-symbols-outlined text-2xl">
                target
            </span>
            
            @if($pendingCount > 0)
            <!-- Animated Badge -->
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="mission-alert-admin-badge relative inline-flex rounded-full h-5 w-5 bg-red-500 text-white text-xs font-bold items-center justify-center shadow-lg">
                    {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                </span>
            </span>
            @endif
        </button>

        <!-- Dropdown - Mobile First, Responsive -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed md:absolute top-16 md:top-auto left-0 right-0 md:left-auto md:right-0 md:mt-2 
                    w-screen md:w-96 md:max-w-[calc(100vw-2rem)] 
                    bg-surface-light dark:bg-surface-dark 
                    md:rounded-2xl rounded-b-2xl 
                    shadow-2xl border-t md:border border-gray-100 dark:border-gray-800 
                    z-[100] overflow-hidden"
             style="display: none;">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary to-blue-600 px-4 md:px-5 py-3 md:py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="p-1.5 md:p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <span class="material-symbols-outlined text-white text-lg md:text-xl">assignment</span>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base md:text-lg">Mission Alerts</h3>
                            <p class="text-blue-100 text-[10px] md:text-xs">{{ $pendingCount }} pending approval{{ $pendingCount !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDropdown" 
                            @click="playSound('click')"
                            class="p-2 hover:bg-white/20 rounded-lg transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Mission List -->
            <div class="max-h-[calc(100vh-12rem)] md:max-h-[400px] overflow-y-auto overscroll-contain">
                @forelse($pendingMissions as $mission)
                <a href="{{ route('mission', ['status' => 'pending']) }}" 
                   wire:click="closeDropdown"
                   @click="playSound('click')"
                   class="block px-4 md:px-5 py-3 md:py-4 
                          hover:bg-gray-50 dark:hover:bg-gray-800/50 
                          active:bg-gray-100 dark:active:bg-gray-800 
                          transition-colors border-b border-gray-100 dark:border-gray-800 last:border-0"
                   style="min-height: 44px;">
                    <div class="flex items-start gap-2.5 md:gap-3">
                        <!-- User Avatar -->
                        @if($mission->user->image)
                            <img src="{{ asset('users/' . $mission->user->image) }}" 
                                 alt="{{ $mission->user->name }}"
                                 class="w-9 h-9 md:w-10 md:h-10 rounded-full object-cover ring-2 ring-yellow-400 shrink-0">
                        @else
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-xs md:text-sm ring-2 ring-yellow-400 shrink-0">
                                {{ strtoupper(substr($mission->user->name, 0, 2)) }}
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-0.5 md:mb-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">
                                    {{ $mission->user->name }}
                                </p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] md:text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 shrink-0">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                                    <span class="hidden sm:inline">Pending</span>
                                    <span class="sm:hidden">•</span>
                                </span>
                            </div>
                            <p class="text-[11px] md:text-xs text-gray-600 dark:text-gray-400 mb-1 truncate">
                                Mission on {{ $mission->mission_date->format('M d, Y') }}
                            </p>
                            <div class="flex items-center gap-2 md:gap-3 text-[10px] md:text-xs text-gray-500 dark:text-gray-500">
                                <span class="flex items-center gap-0.5 md:gap-1">
                                    <span class="material-symbols-outlined text-xs md:text-sm">schedule</span>
                                    <span class="hidden sm:inline">{{ $mission->created_at->diffForHumans() }}</span>
                                    <span class="sm:hidden">{{ $mission->created_at->diffForHumans(null, true) }}</span>
                                </span>
                                <span class="flex items-center gap-0.5 md:gap-1">
                                    <span class="material-symbols-outlined text-xs md:text-sm">location_on</span>
                                    <span class="hidden sm:inline">Location</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="px-4 md:px-5 py-8 md:py-12 text-center">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2 md:mb-3">
                        <span class="material-symbols-outlined text-gray-400 text-2xl md:text-3xl">check_circle</span>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium text-sm md:text-base">All caught up!</p>
                    <p class="text-[11px] md:text-xs text-gray-400 dark:text-gray-500 mt-0.5 md:mt-1">No pending missions at the moment</p>
                </div>
                @endforelse
            </div>

            <!-- Footer -->
            @if($pendingCount > 0)
            <div class="bg-gray-50 dark:bg-gray-800/50 px-4 md:px-5 py-2.5 md:py-3 border-t border-gray-100 dark:border-gray-800 sticky bottom-0">
                <a href="{{ route('mission', ['status' => 'pending']) }}" 
                   wire:click="closeDropdown"
                   @click="playSound('click')"
                   class="flex items-center justify-center gap-2 text-xs md:text-sm font-semibold text-primary hover:text-primary-dark active:text-primary-dark transition-colors min-h-[44px]">
                    <span>View All Pending Missions</span>
                    <span class="material-symbols-outlined text-base md:text-lg">arrow_forward</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes pulse-ring {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        /* Prevent body scroll when dropdown is open on mobile */
        @media (max-width: 768px) {
            body:has([x-data*="showDropdown"]) {
                overflow: hidden;
            }
        }

        /* Touch-friendly sizing */
        @media (max-width: 768px) {
            button, a {
                min-height: 44px;
            }
        }

        /* Smooth scrolling for mobile */
        [x-show="open"] > div {
            -webkit-overflow-scrolling: touch;
        }
    </style>

    <script>
        // ============================================
        // ATTENDIFY SOUND EFFECTS SYSTEM (Integrated)
        // ============================================
        
        const AttendifySound = window.AttendifySound || {
            audioContext: null,
            enabled: true,
            volume: 0.3,

            init() {
                document.addEventListener('click', () => {
                    if (!this.audioContext) {
                        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    }
                }, { once: true });

                const savedPreference = localStorage.getItem('attendify-sounds');
                if (savedPreference !== null) {
                    this.enabled = savedPreference === 'true';
                }
            },

            toggle() {
                this.enabled = !this.enabled;
                localStorage.setItem('attendify-sounds', this.enabled);
                if (this.enabled) {
                    this.playSuccess();
                }
                return this.enabled;
            },

            setVolume(vol) {
                this.volume = Math.max(0, Math.min(1, vol));
                localStorage.setItem('attendify-volume', this.volume);
            },

            playTone(frequency, duration = 0.15, type = 'sine', volume = null) {
                if (!this.enabled) return;
                
                try {
                    const ctx = this.audioContext || new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = ctx.createOscillator();
                    const gainNode = ctx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(ctx.destination);

                    oscillator.frequency.value = frequency;
                    oscillator.type = type;

                    const vol = volume !== null ? volume : this.volume;
                    gainNode.gain.setValueAtTime(vol, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);

                    oscillator.start(ctx.currentTime);
                    oscillator.stop(ctx.currentTime + duration);
                } catch (e) {
                    console.log('Audio not available');
                }
            },

            playClick() {
                this.playTone(800, 0.05, 'sine', this.volume * 0.5);
            },

            playSuccess() {
                if (!this.enabled) return;
                this.playTone(523.25, 0.1);
                setTimeout(() => this.playTone(659.25, 0.1), 100);
                setTimeout(() => this.playTone(783.99, 0.15), 200);
            },

            playNotification() {
                if (!this.enabled) return;
                this.playTone(800, 0.15);
                setTimeout(() => this.playTone(1000, 0.2), 150);
            },

            playApproved() {
                if (!this.enabled) return;
                this.playTone(523.25, 0.08);
                setTimeout(() => this.playTone(659.25, 0.08), 60);
                setTimeout(() => this.playTone(783.99, 0.1), 120);
                setTimeout(() => this.playTone(1046.50, 0.2), 180);
            },

            playModalOpen() {
                this.playTone(600, 0.1, 'sine', this.volume * 0.4);
                setTimeout(() => this.playTone(800, 0.1, 'sine', this.volume * 0.3), 80);
            },

            playModalClose() {
                this.playTone(800, 0.08, 'sine', this.volume * 0.3);
                setTimeout(() => this.playTone(600, 0.1, 'sine', this.volume * 0.25), 60);
            }
        };

        // Initialize if not already initialized
        if (!window.AttendifySound) {
            AttendifySound.init();
            window.AttendifySound = AttendifySound;
        }

        // Global function for Alpine.js
        window.playSound = function(type) {
            switch(type) {
                case 'click':
                    AttendifySound.playClick();
                    break;
                case 'notification':
                    AttendifySound.playNotification();
                    break;
                case 'success':
                    AttendifySound.playSuccess();
                    break;
                case 'approved':
                    AttendifySound.playApproved();
                    break;
                case 'modal-open':
                    AttendifySound.playModalOpen();
                    break;
                case 'modal-close':
                    AttendifySound.playModalClose();
                    break;
            }
        };

        // Store previous count to detect new missions
        let previousMissionCount = {{ $pendingCount }};

        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('missionStatusChanged');
            }
        }, 30000);

        // Listen for mission status changes from other components
        window.addEventListener('mission-updated', event => {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('missionStatusChanged');
            }
        });

        // Listen for Livewire updates to detect new missions
        document.addEventListener('livewire:update', () => {
            setTimeout(() => {
                const badgeElement = document.querySelector('.mission-alert-admin-badge');
                if (badgeElement) {
                    const currentCount = parseInt(badgeElement.textContent) || 0;
                    
                    // New mission detected!
                    if (currentCount > previousMissionCount) {
                        AttendifySound.playNotification();
                        
                        // Optional: Show browser notification
                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification('New Mission Alert', {
                                body: `${currentCount - previousMissionCount} new mission${currentCount - previousMissionCount > 1 ? 's' : ''} pending approval`,
                                icon: '/favicon.ico',
                                badge: '/favicon.ico',
                                tag: 'mission-alert',
                                requireInteraction: false
                            });
                        }
                    }
                    
                    previousMissionCount = currentCount;
                }
            }, 100);
        });

        // Request notification permission on page load (optional)
        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(() => {
                Notification.requestPermission();
            }, 5000);
        }

        // Listen for approve/reject events from admin panel
        window.addEventListener('mission-approved', () => {
            AttendifySound.playApproved();
        });

        window.addEventListener('mission-rejected', () => {
            if (AttendifySound.enabled) {
                AttendifySound.playTone(400, 0.15, 'sawtooth', AttendifySound.volume * 0.3);
                setTimeout(() => AttendifySound.playTone(300, 0.2, 'sawtooth', AttendifySound.volume * 0.25), 120);
            }
        });
    </script>
</div>
<!-- Mobile Bottom Navigation Bar -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface-light dark:bg-surface-dark border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 safe-area-bottom">
  <nav class="flex items-center justify-around px-2 h-16 relative">
    
    @if(Auth::user()->role_type === 'admin')
      {{-- Admin Dashboard --}}
      <a href="{{ route('admin.dashboard') }}" 
         class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">dashboard</span>
        <span class="text-[11px] font-medium">Dashboard</span>
      </a>

      
      
      {{-- Log Attendance --}}
      <a href="{{ route('admin.attendance.index') }}" 
         class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full {{ request()->routeIs('admin.attendance*') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">fact_check</span>
        <span class="text-[11px] font-medium">Attendance</span>
      </a>
    @else
      {{-- User Attendance Log --}}
      <a href="{{ route('home') }}" 
         class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">event_note</span>
        <span class="text-[11px] font-medium">Log</span>
      </a>


      {{-- My Mission --}}
      <a href="{{ route('missions.my') }}" 
         class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full {{ request()->routeIs('missions.my') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">target</span>
        <span class="text-[11px] font-medium">Mission</span>
      </a>
        {{-- Check-In (Center - Small Button) --}}
    <a href="{{ route('checkin') }}" 
       class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full transition-colors active:scale-95">
      <div class="flex items-center justify-center {{ request()->routeIs('checkin') ? 'bg-primary' : 'bg-primary' }} text-white rounded-full size-12 shadow-lg shadow-primary/30">
        <span class="material-symbols-outlined text-[24px]">check_circle</span>
      </div>
      <span class="text-[11px] font-semibold {{ request()->routeIs('checkin') ? 'text-primary' : 'text-gray-700 dark:text-gray-300' }} mt-0.5">Check-In</span>
    </a>
    
      
      {{-- My Schedule --}}
      <a href="{{ route('attendance') }}" 
         class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full {{ request()->routeIs('attendance') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">calendar_today</span>
        <span class="text-[11px] font-medium">Schedule</span>
      </a>
    @endif
    
  
    @if(Auth::user()->role_type === 'admin')
      {{-- Reports --}}
      <a href="{{ route('reports') }}" 
         class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full {{ request()->routeIs('reports') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">bar_chart</span>
        <span class="text-[11px] font-medium">Reports</span>
      </a>
      
      {{-- More Menu --}}
      <button id="mobile-more-toggle" 
              class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full text-gray-500 dark:text-gray-400 transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">menu</span>
        <span class="text-[11px] font-medium">More</span>
      </button>
    @else
      {{-- More Menu (User) --}}
      <button id="mobile-more-toggle" 
              class="flex flex-col items-center justify-center gap-0.5 flex-1 h-full text-gray-500 dark:text-gray-400 transition-colors active:scale-95">
        <span class="material-symbols-outlined text-[22px]">menu</span>
        <span class="text-[11px] font-medium">More</span>
      </button>
    @endif
  </nav>
</div>

<!-- Mobile More Menu (Slide-up Overlay) -->
<div id="mobile-more-menu" class="lg:hidden hidden fixed inset-0 bg-black/50 z-[60] backdrop-blur-sm">
  <div class="absolute bottom-0 left-0 right-0 bg-surface-light dark:bg-surface-dark rounded-t-2xl shadow-2xl max-h-[80vh] overflow-y-auto safe-area-bottom transform transition-transform duration-300">
    
    {{-- Header --}}
    <div class="sticky top-0 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 px-4 py-3 rounded-t-2xl z-10">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-bold text-gray-900 dark:text-white">Menu</h3>
        <button id="mobile-more-close" class="size-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>
    </div>

    {{-- User Info Section --}}
    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
      <div class="flex items-center gap-3">
        @php($user = Auth::user())

        @if($user->image)
          <img
            src="{{ asset('users/' . $user->image) }}"
            alt="{{ $user->name }}"
            class="size-10 rounded-full object-cover shadow-sm"
          >
        @else
          <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
            {{ strtoupper(substr($user->name, 0, 2)) }}
          </div>
        @endif

        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
          <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-primary/10 text-primary mt-0.5">
            {{ ucfirst(Auth::user()->role_type) }}
          </span>
        </div>
      </div>
      
      {{-- Telegram Status --}}
      @if(!is_null(Auth::user()->telegram_id))
        <div class="flex items-center gap-2 mt-2 px-2.5 py-1.5 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
          </svg>
          <span class="text-xs text-green-600 dark:text-green-400 font-medium">Telegram Connected</span>
        </div>
      @endif
    </div>

    {{-- Menu Items --}}
    <nav class="py-3 px-4 space-y-1 pb-safe">
      
      @if(Auth::user()->role_type === 'admin')
        <p class="px-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Management</p>
        
        {{-- QR Code --}}
        <a class="flex items-center gap-3 px-2 py-2.5 rounded-lg {{ request()->routeIs('qrcode') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all active:scale-[0.98]" 
           href="{{ route('qrcode') }}">
          <span class="material-symbols-outlined text-xl">qr_code_2</span>
          <span class="text-sm font-medium">QR Code</span>
        </a>
        
        {{-- Employees --}}
        <a class="flex items-center gap-3 px-2 py-2.5 rounded-lg {{ request()->routeIs('employees*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all active:scale-[0.98]" 
           href="{{ route('employees') }}">
          <span class="material-symbols-outlined text-xl">group</span>
          <span class="text-sm font-medium">Employees</span>
        </a>

        {{-- Mission Check --}}
        <a class="flex items-center gap-3 px-2 py-2.5 rounded-lg {{ request()->routeIs('mission') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all active:scale-[0.98]" 
           href="{{ route('mission') }}">
          <span class="material-symbols-outlined text-xl">task_alt</span>
          <span class="text-sm font-medium">Mission</span>
        </a>

        {{-- Map --}}
        <a class="flex items-center gap-3 px-2 py-2.5 rounded-lg {{ request()->routeIs('map*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all active:scale-[0.98]" 
           href="{{ route('map.created') }}">
          <span class="material-symbols-outlined text-xl">place</span>
          <span class="text-sm font-medium">Map</span>
        </a>
        
        <p class="px-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 mt-3">System</p>
        
        {{-- Settings --}}
        <a class="flex items-center gap-3 px-2 py-2.5 rounded-lg {{ request()->routeIs('settings') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all active:scale-[0.98]" 
           href="{{ route('settings') }}">
          <span class="material-symbols-outlined text-xl">settings</span>
          <span class="text-sm font-medium">Settings</span>
        </a>
      @endif
      
      {{-- Support (For All Users) --}}
      <a class="flex items-center gap-3 px-2 py-2.5 rounded-lg {{ request()->routeIs('support') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all active:scale-[0.98]" 
         href="{{ route('support') }}">
        <span class="material-symbols-outlined text-xl">help</span>
        <span class="text-sm font-medium">Support</span>
      </a>
      
      {{-- Divider --}}
      <div class="border-t border-gray-100 dark:border-gray-800 my-2"></div>
      
      {{-- Telegram Bind/Unbind --}}
      @if(is_null(Auth::user()->telegram_id))
        <a href="{{ route('telegram.bind') }}" 
           class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all active:scale-[0.98]">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
          </svg>
          <span class="text-sm font-medium">Bind Telegram</span>
        </a>
      @else
        <a href="{{ route('telegram.unbind') }}" 
           class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all active:scale-[0.98]"
           onclick="return confirm('Are you sure you want to unbind your Telegram account?')">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
          </svg>
          <span class="text-sm font-medium">Unbind Telegram</span>
        </a>
      @endif
      
      {{-- Logout --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-2 py-2.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all active:scale-[0.98]">
          <span class="material-symbols-outlined text-xl">logout</span>
          <span class="text-sm font-medium">Sign Out</span>
        </button>
      </form>
    </nav>
  </div>
</div>

<style>
  /* Safe area bottom for devices with notches */
  .safe-area-bottom {
    padding-bottom: env(safe-area-inset-bottom);
  }
  
  .pb-safe {
    padding-bottom: calc(5rem + env(safe-area-inset-bottom));
  }
  
  /* Add bottom padding to main content to account for bottom nav */
  @media (max-width: 1024px) {
    main, .main-content {
      padding-bottom: calc(5rem + env(safe-area-inset-bottom)) !important;
      margin-bottom: 0 !important;
    }
    
    /* Ensure body doesn't have conflicting padding */
    body {
      padding-bottom: 0 !important;
    }
  }
  
  /* Slide-up animation for more menu */
  #mobile-more-menu > div {
    transform: translateY(100%);
  }
  
  #mobile-more-menu:not(.hidden) > div {
    animation: slideUp 0.3s ease-out forwards;
  }
  
  @keyframes slideUp {
    to {
      transform: translateY(0);
    }
  }

  /* Prevent scroll bounce on iOS */
  body {
    overscroll-behavior-y: none;
  }
</style>

<script>
  // Mobile More Menu Toggle
  const moreToggle = document.getElementById('mobile-more-toggle');
  const moreMenu = document.getElementById('mobile-more-menu');
  const moreClose = document.getElementById('mobile-more-close');

  if (moreToggle && moreMenu) {
    moreToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      moreMenu.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    });
  }

  if (moreClose && moreMenu) {
    moreClose.addEventListener('click', () => {
      moreMenu.classList.add('hidden');
      document.body.style.overflow = '';
    });
  }

  // Close when clicking overlay
  if (moreMenu) {
    moreMenu.addEventListener('click', (e) => {
      if (e.target === moreMenu) {
        moreMenu.classList.add('hidden');
        document.body.style.overflow = '';
      }
    });
  }

  // Close when pressing Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && moreMenu && !moreMenu.classList.contains('hidden')) {
      moreMenu.classList.add('hidden');
      document.body.style.overflow = '';
    }
  });
</script>
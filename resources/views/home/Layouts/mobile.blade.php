<!-- Mobile Menu -->
<div id="mobile-menu" class="lg:hidden hidden absolute top-16 md:top-20 left-0 right-0 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shadow-lg z-30 max-h-[calc(100vh-4rem)] md:max-h-[calc(100vh-5rem)] overflow-y-auto">
  {{-- User Info Section --}}
  <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
    <div class="flex items-center gap-3">
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

      <div>
        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary/10 text-primary mt-1">
          {{ ucfirst(Auth::user()->role_type) }}
        </span>
      </div>
    </div>
    
    {{-- Telegram Status --}}
    @if(!is_null(Auth::user()->telegram_id))
      <div class="flex items-center gap-2 mt-3 px-3 py-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
        </svg>
        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Telegram Connected</span>
      </div>
    @endif
  </div>

  <nav class="py-4 px-6 space-y-2">
    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu</p>
    
    @if(Auth::user()->role_type === 'admin')
      {{-- Admin Dashboard --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('admin.dashboard') }}">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="font-medium">Dashboard</span>
      </a>
    @else
      {{-- User Attendance Log --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('home') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('home') }}">
        <span class="material-symbols-outlined">event_note</span>
        <span class="font-medium">Attendance Log</span>
      </a>
    @endif
    
    {{-- Check-In --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('checkin') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
       href="{{ route('checkin') }}">
      <span class="material-symbols-outlined">check_circle</span>
      <span class="font-medium">Check-In</span>
    </a>
    
    {{-- Attendance (User) / Log Attendance (Admin) --}}
    @if(Auth::user()->role_type === 'admin')
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.attendance*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('admin.attendance.index') }}">
        <span class="material-symbols-outlined">fact_check</span>
        <span class="font-medium">Log Attendance</span>
      </a>
    @else
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('attendance') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('attendance') }}">
        <span class="material-symbols-outlined">how_to_reg</span>
        <span class="font-medium">My Schedule</span>
      </a>
    @endif
    
    {{-- QR Code (Admin Only) --}}
    @if(Auth::user()->role_type === 'admin')
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('qrcode') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('qrcode') }}">
        <span class="material-symbols-outlined">qr_code_2</span>
        <span class="font-medium">QR Code</span>
      </a>
      
      {{-- Reports --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('reports') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('reports') }}">
        <span class="material-symbols-outlined">bar_chart</span>
        <span class="font-medium">Reports</span>
      </a>
    @endif
    
    {{-- Admin Only Section --}}
    @if(Auth::user()->role_type === 'admin')
      <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4">Management</p>
      
      {{-- Employees --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('employees*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('employees') }}">
        <span class="material-symbols-outlined">group</span>
        <span class="font-medium">Employees</span>
      </a>

         {{-- Employees --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('map*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
         href="{{ route('map.created') }}">
        <span class="material-symbols-outlined">place</span>
        <span class="font-medium">Map</span>
      </a>
    @endif
    
    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4">System</p>
       @if(Auth::user()->role_type === 'admin')
    {{-- Settings --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('settings') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
       href="{{ route('settings') }}">
      <span class="material-symbols-outlined">settings</span>
      <span class="font-medium">Settings</span>
    </a>
    @endif
    {{-- Support --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('support') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all" 
       href="{{ route('support') }}">
      <span class="material-symbols-outlined">help</span>
      <span class="font-medium">Support</span>
    </a>
    
    {{-- Divider --}}
    <div class="border-t border-gray-100 dark:border-gray-800 my-4"></div>
    
    {{-- Telegram Bind/Unbind --}}
    @if(is_null(Auth::user()->telegram_id))
      <a href="{{ route('telegram.bind') }}" 
         class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
        </svg>
        <span class="font-medium">Bind Telegram</span>
      </a>
    @else
      <a href="{{ route('telegram.unbind') }}" 
         class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all"
         onclick="return confirm('Are you sure you want to unbind your Telegram account?')">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
        </svg>
        <span class="font-medium">Unbind Telegram</span>
      </a>
    @endif
    
    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
        <span class="material-symbols-outlined">logout</span>
        <span class="font-medium">Sign Out</span>
      </button>
    </form>
  </nav>
</div>
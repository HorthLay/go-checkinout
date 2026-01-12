<aside class="w-64 bg-surface-light dark:bg-surface-dark border-r border-gray-200 dark:border-gray-800 flex-col hidden lg:flex z-20 shadow-sm">
  <div class="h-20 flex items-center px-6 border-b border-gray-100 dark:border-gray-800 gap-3">
    <div class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
      <span class="material-symbols-outlined text-2xl">qr_code_scanner</span>
    </div>
    <div>
      <span class="font-bold text-lg tracking-tight block leading-none">Attendify</span>
      <span class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Portal</span>
    </div>
  </div>
  
  <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
    <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2">Menu</p>
    
    @if(Auth::user()->role_type === 'admin')
      {{-- Admin Dashboard --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('admin.dashboard') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('admin.dashboard') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">dashboard</span>
        <span class="font-medium {{ request()->routeIs('admin.dashboard') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Dashboard</span>
      </a>
    @else
      {{-- User Attendance Log --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('home') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('home') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('home') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">event_note</span>
        <span class="font-medium {{ request()->routeIs('home') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Attendance Log</span>
      </a>
    @endif
    
    {{-- Check-In --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('checkin') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('checkin') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('checkin') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">check_circle</span>
      <span class="font-medium {{ request()->routeIs('checkin') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Check-In</span>
    </a>
    
    {{-- Attendance (User) / Log Attendance (Admin) --}}
    @if(Auth::user()->role_type === 'admin')
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.attendance*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('admin.attendance.index') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('admin.attendance*') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">fact_check</span>
        <span class="font-medium {{ request()->routeIs('admin.attendance*') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Log Attendance</span>
      </a>
    @else
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('attendance') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('attendance') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('attendance') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">how_to_reg</span>
        <span class="font-medium {{ request()->routeIs('attendance') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">My Schedule</span>
      </a>
    @endif
    
    {{-- QR Code (Admin Only) --}}
    @if(Auth::user()->role_type === 'admin')
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('qrcode') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('qrcode') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('qrcode') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">qr_code_2</span>
        <span class="font-medium {{ request()->routeIs('qrcode') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">QR Code</span>
      </a>
      {{-- Reports --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('reports') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('reports') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('reports') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">bar_chart</span>
        <span class="font-medium {{ request()->routeIs('reports') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Reports</span>
      </a>
    @endif
    
    
    {{-- Admin Only Section --}}
    @if(Auth::user()->role_type === 'admin')
      <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-8">Management</p>
      
      {{-- Employees --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('employees*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('employees') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('employees*') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">group</span>
        <span class="font-medium {{ request()->routeIs('employees*') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Employees</span>
      </a>

        {{-- Map --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('map*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('map.created') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('map*') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">place</span>
        <span class="font-medium {{ request()->routeIs('map*') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">map</span>
      </a>
    @endif
    
    <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-8">System</p>
    
    {{-- Settings --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('settings') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('settings') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('settings') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">settings</span>
      <span class="font-medium {{ request()->routeIs('settings') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Settings</span>
    </a>
    
    {{-- Support --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('support') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('support') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('support') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">help</span>
      <span class="font-medium {{ request()->routeIs('support') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Support</span>
    </a>
  </nav>
  
  <div class="p-4 border-t border-gray-100 dark:border-gray-800">
    {{-- Telegram Bind/Unbind --}}
    @if(is_null(Auth::user()->telegram_id))
      <a href="{{ route('telegram.bind') }}" 
         class="flex items-center gap-3 px-3 py-2.5 mb-2 rounded-xl text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
        </svg>
        <span class="font-medium text-sm">Bind Telegram</span>
      </a>
    @else
      <div class="flex items-center gap-2 px-3 py-2 mb-2 bg-green-50 dark:bg-green-900/20 rounded-xl">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
        </svg>
        <span class="text-xs text-green-600 dark:text-green-400 font-medium flex-1">Connected</span>
        <a href="{{ route('telegram.unbind') }}" 
           class="text-xs text-red-600 hover:text-red-700"
           onclick="return confirm('Are you sure you want to unbind your Telegram account?')">
          Unbind
        </a>
      </div>
    @endif
    
    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
        <span class="material-symbols-outlined">logout</span>
        <span class="font-medium">Sign Out</span>
      </button>
    </form>
  </div>
</aside>
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
    
    {{-- Dashboard --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('home') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('home') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('home') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">dashboard</span>
      <span class="font-medium {{ request()->routeIs('home') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Dashboard</span>
    </a>
    
    {{-- Check-In --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('checkin') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('checkin') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('checkin') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">check_circle</span>
      <span class="font-medium {{ request()->routeIs('checkin') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Check-In</span>
    </a>
    
    {{-- Attendance --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('attendance') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('attendance') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('attendance') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">how_to_reg</span>
      <span class="font-medium {{ request()->routeIs('attendance') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Attendance</span>
    </a>
    
    {{-- QR Code --}}
      @if(Auth::user()->role_type === 'admin')
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('qrcode') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('qrcode') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('qrcode') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">qr_code_2</span>
      <span class="font-medium {{ request()->routeIs('qrcode') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">QR Code</span>
    </a>
    @endif
    
    {{-- Reports --}}
    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('reports') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
       href="{{ route('reports') }}">
      <span class="material-symbols-outlined {{ request()->routeIs('reports') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">bar_chart</span>
      <span class="font-medium {{ request()->routeIs('reports') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Reports</span>
    </a>
    
    {{-- Admin Only Section --}}
    @if(Auth::user()->role_type === 'admin')
      <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-8">Management</p>
      
      {{-- Log Attendance (Admin Only) --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.attendance*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('admin.attendance.index') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('admin.attendance*') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">event_note</span>
        <span class="font-medium {{ request()->routeIs('admin.attendance*') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Log Attendance</span>
      </a>
      
      {{-- Employees --}}
      <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('employees*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-all group" 
         href="{{ route('employees') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('employees*') ? '' : 'text-gray-500 group-hover:text-primary' }} transition-colors">group</span>
        <span class="font-medium {{ request()->routeIs('employees*') ? '' : 'group-hover:text-gray-900 dark:group-hover:text-white' }} transition-colors">Employees</span>
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
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
        <span class="material-symbols-outlined">logout</span>
        <span class="font-medium">Sign Out</span>
      </button>
    </form>
  </div>
</aside>
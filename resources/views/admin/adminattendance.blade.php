<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Log Attendance - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
      rel="preload"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'"
    />
    <noscript>
      <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
      />
    </noscript>
    <link
      rel="preload"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'"
    />
    <noscript>
      <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
      />
    </noscript>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#135bec",
              "primary-dark": "#0f4bc0",
              "background-light": "#f6f6f8",
              "background-dark": "#101622",
              "surface-light": "#ffffff",
              "surface-dark": "#1e2430",
            },
            fontFamily: {
              display: ["Inter", "sans-serif"],
            },
          },
        },
      };
    </script>
    <style>
      body {
        font-family: "Inter", sans-serif;
      }
      .no-scrollbar::-webkit-scrollbar {
        display: none;
      }
      .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
      }

      .modal {
        transition: opacity 0.3s ease;
      }
      .modal-content {
        transition: transform 0.3s ease;
        transform: scale(0.9);
      }
      .modal:not(.hidden) .modal-content {
        transform: scale(1);
      }

      #mobile-menu,
      #profile-dropdown {
        transition: transform 0.3s ease, opacity 0.3s ease;
        transform: translateY(-10px);
        opacity: 0;
      }
      #mobile-menu:not(.hidden),
      #profile-dropdown:not(.hidden) {
        transform: translateY(0);
        opacity: 1;
      }

      @media (max-width: 768px) {
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
      }
    </style>
  </head>
  <body
    class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden"
  >
    <!-- Sidebar -->
    @include('home.Layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="h-16 md:h-20 flex items-center justify-between px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10">
        <div class="flex items-center gap-3 lg:hidden">
          <button id="menu-toggle" aria-label="Toggle menu" class="text-gray-500 hover:text-gray-900 dark:hover:text-white p-2">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <span class="font-bold text-base md:text-lg">Log Attendance</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Log Attendance</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage employee schedules and day-offs</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Success/Error Messages -->
        @if(session('success'))
          <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
          </div>
        @endif

        @if(session('error'))
          <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
          </div>
        @endif

        <!-- Tabs -->
        <div class="mb-6">
          <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex gap-4" aria-label="Tabs">
              <button 
                onclick="switchTab('schedules')" 
                id="tab-schedules"
                class="tab-button px-4 py-3 text-sm font-medium border-b-2 {{ $activeTab === 'schedules' ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} transition-colors"
              >
                <span class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-lg">schedule</span>
                  <span>Schedules</span>
                </span>
              </button>
              <button 
                onclick="switchTab('dayoffs')" 
                id="tab-dayoffs"
                class="tab-button px-4 py-3 text-sm font-medium border-b-2 {{ $activeTab === 'dayoffs' ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
              >
                <span class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-lg">event_available</span>
                  <span>Day Offs</span>
                </span>
              </button>
              <button 
                onclick="switchTab('records')" 
                id="tab-records"
                class="tab-button px-4 py-3 text-sm font-medium border-b-2 {{ $activeTab === 'records' ? 'border-primary text-primary' : 'border-transparent text-gray-500' }} hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
              >
                <span class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-lg">history</span>
                  <span>All Records</span>
                </span>
              </button>
            </nav>
          </div>
        </div>

        <!-- Schedules Tab -->
        <div id="content-schedules" class="tab-content {{ $activeTab === 'schedules' ? '' : 'hidden' }}">
          <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">Employee Work Schedules</h2>
              <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                  </span>
                  <input 
                    type="text" 
                    id="searchSchedules"
                    placeholder="Search employees..." 
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-full sm:w-64 transition-all"
                  />
                </div>
                <button 
                  onclick="openScheduleModal()"
                  class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors shadow-sm"
                >
                  <span class="material-symbols-outlined">add</span>
                  <span>Set Schedule</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Schedules Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" id="schedulesGrid">
            @forelse($users as $user)
              <div class="schedule-card bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email ?? '') }}">
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center gap-3">
                    @if($user->image)
                      <img src="{{ asset('users/' . $user->image) }}" alt="{{ $user->name }}" class="size-10 rounded-full object-cover">
                    @else
                      <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                      </div>
                    @endif
                    <div>
                      <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $user->name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                  </div>
                </div>

                @if($user->attendanceSchedule)
                  <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600 dark:text-gray-400">Check-In:</span>
                      <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($user->attendanceSchedule->scheduled_check_in)->format('h:i A') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600 dark:text-gray-400">Check-Out:</span>
                      <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($user->attendanceSchedule->scheduled_check_out)->format('h:i A') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-600 dark:text-gray-400">Late Tolerance:</span>
                      <span class="font-medium text-gray-900 dark:text-white">{{ $user->attendanceSchedule->late_allowed_min }} mins</span>
                    </div>
                  </div>
                  <button 
                    onclick="editSchedule({{ $user->id }}, '{{ $user->name }}', '{{ \Carbon\Carbon::parse($user->attendanceSchedule->scheduled_check_in)->format('H:i') }}', '{{ \Carbon\Carbon::parse($user->attendanceSchedule->scheduled_check_out)->format('H:i') }}', {{ $user->attendanceSchedule->late_allowed_min }})"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                  >
                    <span class="material-symbols-outlined text-lg">edit</span>
                    <span>Update Schedule</span>
                  </button>
                @else
                  <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mb-3 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">No schedule set</p>
                  </div>
                  <button 
                    onclick="editSchedule({{ $user->id }}, '{{ $user->name }}', '09:00', '17:00', 10)"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-medium transition-colors"
                  >
                    <span class="material-symbols-outlined text-lg">add</span>
                    <span>Set Schedule</span>
                  </button>
                @endif
              </div>
            @empty
              <div class="col-span-full text-center py-12">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">person_off</span>
                <p class="text-gray-500 dark:text-gray-400">No employees found</p>
              </div>
            @endforelse
          </div>

          <!-- No Results Message -->
          <div id="noScheduleResults" class="hidden text-center py-12">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">search_off</span>
            <p class="text-gray-500 dark:text-gray-400">No employees found matching your search</p>
          </div>

          <!-- Pagination -->
          @if($users->hasPages())
            <div class="mt-6">
              <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Showing <span class="font-semibold">{{ $users->firstItem() }}</span> to <span class="font-semibold">{{ $users->lastItem() }}</span> of <span class="font-semibold">{{ $users->total() }}</span> employees
                </p>
                <div class="flex gap-2">
                  @if ($users->onFirstPage())
                    <span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">Previous</span>
                  @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Previous</a>
                  @endif

                  @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if ($page == $users->currentPage())
                      <span class="px-4 py-2 bg-primary text-white rounded-lg font-medium">{{ $page }}</span>
                    @else
                      <a href="{{ $url }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">{{ $page }}</a>
                    @endif
                  @endforeach

                  @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Next</a>
                  @else
                    <span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">Next</span>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>

        <!-- Day Offs Tab -->
        <div id="content-dayoffs" class="tab-content {{ $activeTab === 'dayoffs' ? '' : 'hidden' }}">
          <div class="mb-6 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Employee Day Offs</h2>
            <button 
              onclick="openDayOffModal()"
              class="flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium transition-colors shadow-sm"
            >
              <span class="material-symbols-outlined">add</span>
              <span>Add Day Off</span>
            </button>
          </div>

          <!-- Day Offs Table -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                  <tr>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reason</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested</th>
                    <th class="text-right py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                  @forelse($dayOffs as $dayOff)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                      <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                          @if($dayOff->user->image)
                            <img src="{{ asset('users/' . $dayOff->user->image) }}" alt="{{ $dayOff->user->name }}" class="size-8 rounded-full object-cover">
                          @else
                            <div class="size-8 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-xs">
                              {{ strtoupper(substr($dayOff->user->name, 0, 2)) }}
                            </div>
                          @endif
                          <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $dayOff->user->name }}</span>
                        </div>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($dayOff->off_date)->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($dayOff->off_date)->format('l') }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dayOff->reason }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $dayOff->created_at->diffForHumans() }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <div class="flex items-center justify-end gap-2">
                          <form method="POST" action="{{ route('admin.attendance.dayoff.delete', $dayOff->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this day off?')">
                            @csrf
                            @method('DELETE')
                            <button 
                              type="submit"
                              class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" 
                              title="Delete">
                              <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                          <span class="material-symbols-outlined text-5xl text-gray-300">event_busy</span>
                          <p class="text-gray-500 dark:text-gray-400">No day offs found</p>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($dayOffs->hasPages())
              <div class="p-6 border-t border-gray-100 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold">{{ $dayOffs->firstItem() }}</span> to <span class="font-semibold">{{ $dayOffs->lastItem() }}</span> of <span class="font-semibold">{{ $dayOffs->total() }}</span> day offs
                  </p>
                  <div class="flex gap-2">
                    @if ($dayOffs->onFirstPage())
                      <span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                      <a href="{{ $dayOffs->previousPageUrl() }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Previous</a>
                    @endif

                    @foreach ($dayOffs->getUrlRange(1, $dayOffs->lastPage()) as $page => $url)
                      @if ($page == $dayOffs->currentPage())
                        <span class="px-4 py-2 bg-primary text-white rounded-lg font-medium">{{ $page }}</span>
                      @else
                        <a href="{{ $url }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">{{ $page }}</a>
                      @endif
                    @endforeach

                    @if ($dayOffs->hasMorePages())
                      <a href="{{ $dayOffs->nextPageUrl() }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Next</a>
                    @else
                      <span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">Next</span>
                    @endif
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- All Records Tab -->
        <div id="content-records" class="tab-content {{ $activeTab === 'records' ? '' : 'hidden' }}">
          <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">All Attendance Records</h2>
              
              <!-- Filters -->
              <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                  </span>
                  <input 
                    type="text" 
                    id="searchRecords"
                    placeholder="Search employee..." 
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-full sm:w-64 transition-all"
                  />
                </div>

                <!-- Date Filter -->
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                  </span>
                  <input 
                    type="date" 
                    id="filterDate"
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-full sm:w-auto transition-all"
                  />
                </div>

                <!-- Quick Date Filters -->
                <div class="flex gap-2">
                  <button 
                    onclick="filterByDate('today')"
                    class="px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                  >
                    Today
                  </button>
                  <button 
                    onclick="filterByDate('clear')"
                    class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                  >
                    Clear
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Attendance Records Table -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto" id="recordsTableContainer">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                  <tr>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check-In</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check-Out</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800" id="attendanceTableBody">
                  @forelse($attendances as $attendance)
                    <tr class="attendance-row hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" 
                        data-name="{{ strtolower($attendance->user->name) }}"
                        data-email="{{ strtolower($attendance->user->email ?? '') }}"
                        data-date="{{ $attendance->attendance_date->format('Y-m-d') }}"
                        data-status="{{ $attendance->status }}">
                      <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                          @if($attendance->user->image)
                            <img src="{{ asset('users/' . $attendance->user->image) }}" alt="{{ $attendance->user->name }}" class="size-8 rounded-full object-cover">
                          @else
                            <div class="size-8 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-xs">
                              {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                            </div>
                          @endif
                          <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->user->name }}</span>
                        </div>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->attendance_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->attendance_date->format('l') }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '—' }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '—' }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->formatted_work_hours}}</p>
                      </td>
                      <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                          {{ $attendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
                          {{ $attendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                          {{ $attendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                          {{ $attendance->status === 'leave' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
                        ">
                          {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                        </span>
                      </td>
                    </tr>
                  @empty
                    <tr id="noRecordsDefault">
                      <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                          <span class="material-symbols-outlined text-5xl text-gray-300">event_busy</span>
                          <p class="text-gray-500 dark:text-gray-400">No attendance records found</p>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- No Results Message (for filtered results) -->
            <div id="noRecordsFiltered" class="hidden py-12 text-center">
              <div class="flex flex-col items-center gap-3">
                <span class="material-symbols-outlined text-5xl text-gray-300">search_off</span>
                <p class="text-gray-500 dark:text-gray-400">No records found matching your filters</p>
                <button 
                  onclick="clearAllFilters()"
                  class="mt-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-medium transition-colors"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Records Summary -->
          <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm">
            <p class="text-gray-600 dark:text-gray-400">
              Showing <span id="visibleCount" class="font-semibold text-gray-900 dark:text-white">{{ $attendances->count() }}</span> 
              of <span id="totalCount" class="font-semibold text-gray-900 dark:text-white">{{ $attendances->count() }}</span> records
            </p>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg text-xs font-medium">
                <span id="onTimeCount">{{ $attendances->where('status', 'on_time')->count() }}</span> On Time
              </span>
              <span class="px-3 py-1 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-lg text-xs font-medium">
                <span id="lateCount">{{ $attendances->where('status', 'late')->count() }}</span> Late
              </span>
              <span class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-xs font-medium">
                <span id="absentCount">{{ $attendances->where('status', 'absent')->count() }}</span> Absent
              </span>
              <span class="px-3 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg text-xs font-medium">
                <span id="leaveCount">{{ $attendances->where('status', 'leave')->count() }}</span> Leave
              </span>
            </div>
          </div>

          <!-- Pagination -->
          @if($attendances->hasPages())
            <div class="mt-6">
              <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Page {{ $attendances->currentPage() }} of {{ $attendances->lastPage() }}
                </p>
                <div class="flex gap-2">
                  @if ($attendances->onFirstPage())
                    <span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">Previous</span>
                  @else
                    <a href="{{ $attendances->previousPageUrl() }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Previous</a>
                  @endif

                  @foreach ($attendances->getUrlRange(max(1, $attendances->currentPage() - 2), min($attendances->lastPage(), $attendances->currentPage() + 2)) as $page => $url)
                    @if ($page == $attendances->currentPage())
                      <span class="px-4 py-2 bg-primary text-white rounded-lg font-medium">{{ $page }}</span>
                    @else
                      <a href="{{ $url }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">{{ $page }}</a>
                    @endif
                  @endforeach

                  @if ($attendances->hasMorePages())
                    <a href="{{ $attendances->nextPageUrl() }}" class="px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Next</a>
                  @else
                    <span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">Next</span>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>
      </main>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="modal-content bg-surface-light dark:bg-surface-dark rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 sticky top-0 bg-surface-light dark:bg-surface-dark z-10">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="scheduleModalTitle">Set Work Schedule</h3>
            <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
        </div>
        <form method="POST" action="{{ route('admin.attendance.schedule.store') }}" class="p-6 space-y-4">
          @csrf
          <input type="hidden" name="user_id_select" id="schedule_user_id_hidden">
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee</label>
            
            <div class="relative mb-2">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <span class="material-symbols-outlined text-[20px]">search</span>
              </span>
              <input 
                type="text" 
                id="schedule_employee_search"
                placeholder="Search employee by name..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                autocomplete="off"
              />
            </div>

            <div class="border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 max-h-60 overflow-y-auto">
              <div id="schedule_employee_list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($users as $user)
                  <div class="employee-option p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                       data-id="{{ $user->id }}" 
                       data-name="{{ strtolower($user->name) }}"
                       data-email="{{ strtolower($user->email ?? '') }}"
                       onclick="selectScheduleEmployee({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">
                    <div class="flex items-center gap-3">
                      @if($user->image)
                        <img src="{{ asset('users/' . $user->image) }}" alt="{{ $user->name }}" class="size-8 rounded-full object-cover">
                      @else
                        <div class="size-8 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-xs">
                          {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                      @endif
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div id="schedule_no_results" class="hidden p-6 text-center">
                <span class="material-symbols-outlined text-3xl text-gray-300">search_off</span>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No employees found</p>
              </div>
            </div>

            <div id="schedule_selected_display" class="hidden mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div id="schedule_selected_avatar"></div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="schedule_selected_name"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="schedule_selected_email"></p>
                  </div>
                </div>
                <button type="button" onclick="clearScheduleSelection()" class="text-gray-400 hover:text-red-600">
                  <span class="material-symbols-outlined">close</span>
                </button>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-In Time</label>
            <input type="time" name="scheduled_check_in" id="schedule_check_in" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-Out Time</label>
            <input type="time" name="scheduled_check_out" id="schedule_check_out" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Late Tolerance (minutes)</label>
            <input type="number" name="late_allowed_min" id="schedule_late_min" min="0" max="60" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
          </div>

          <div class="flex gap-3 pt-4">
            <button type="button" onclick="closeScheduleModal()" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors">
              Save Schedule
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Day Off Modal -->
    <div id="dayOffModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="modal-content bg-surface-light dark:bg-surface-dark rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 sticky top-0 bg-surface-light dark:bg-surface-dark z-10">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Day Off</h3>
            <button onclick="closeDayOffModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
        </div>
        <form method="POST" action="{{ route('admin.attendance.dayoff.store') }}" class="p-6 space-y-4">
          @csrf
          <input type="hidden" name="user_id" id="dayoff_user_id_hidden">
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee</label>
            
            <div class="relative mb-2">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <span class="material-symbols-outlined text-[20px]">search</span>
              </span>
              <input 
                type="text" 
                id="dayoff_employee_search"
                placeholder="Search employee by name..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                autocomplete="off"
              />
            </div>

            <div class="border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 max-h-60 overflow-y-auto">
              <div id="dayoff_employee_list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($users as $user)
                  <div class="dayoff-option p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                       data-id="{{ $user->id }}" 
                       data-name="{{ strtolower($user->name) }}"
                       data-email="{{ strtolower($user->email ?? '') }}"
                       onclick="selectDayOffEmployee({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->image }}')">
                    <div class="flex items-center gap-3">
                      @if($user->image)
                        <img src="{{ asset('users/' . $user->image) }}" alt="{{ $user->name }}" class="size-8 rounded-full object-cover">
                      @else
                        <div class="size-8 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-xs">
                          {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                      @endif
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div id="dayoff_no_results" class="hidden p-6 text-center">
                <span class="material-symbols-outlined text-3xl text-gray-300">search_off</span>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No employees found</p>
              </div>
            </div>

            <div id="dayoff_selected_display" class="hidden mt-3 p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div id="dayoff_selected_avatar"></div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="dayoff_selected_name"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="dayoff_selected_email"></p>
                  </div>
                </div>
                <button type="button" onclick="clearDayOffSelection()" class="text-gray-400 hover:text-red-600">
                  <span class="material-symbols-outlined">close</span>
                </button>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
            <input type="date" name="off_date" min="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
            <textarea name="reason" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Enter reason for day off..."></textarea>
          </div>

          <div class="flex gap-3 pt-4">
            <button type="button" onclick="closeDayOffModal()" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium transition-colors">
              Add Day Off
            </button>
          </div>
        </form>
      </div>
    </div>

    <script>
      // Mobile Menu & Profile Dropdown
      const menuToggle = document.getElementById("menu-toggle");
      const mobileMenu = document.getElementById("mobile-menu");
      const profileToggle = document.getElementById("profile-toggle");
      const profileDropdown = document.getElementById("profile-dropdown");

      if (menuToggle && mobileMenu) {
        menuToggle.addEventListener("click", (e) => {
          e.stopPropagation();
          mobileMenu.classList.toggle("hidden");
        });
      }

      if (profileToggle && profileDropdown) {
        profileToggle.addEventListener("click", (e) => {
          e.stopPropagation();
          profileDropdown.classList.toggle("hidden");
        });
      }

      document.addEventListener("click", (e) => {
        if (profileToggle && profileDropdown && !profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
          profileDropdown.classList.add("hidden");
        }
        if (menuToggle && mobileMenu && !menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
          mobileMenu.classList.add("hidden");
        }
      });

      // Maintain active tab on page load
      document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || '{{ $activeTab }}';
        
        if (activeTab) {
          switchTab(activeTab);
        }
      });

      // Tab Switching
      function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(content => {
          content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-button').forEach(button => {
          button.classList.remove('border-primary', 'text-primary');
          button.classList.add('border-transparent', 'text-gray-500');
        });

        document.getElementById(`content-${tab}`).classList.remove('hidden');

        const activeTab = document.getElementById(`tab-${tab}`);
        activeTab.classList.add('border-primary', 'text-primary');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        
        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
      }

      // Schedule Search
      const searchSchedules = document.getElementById('searchSchedules');
      if (searchSchedules) {
        searchSchedules.addEventListener('input', function(e) {
          const searchTerm = e.target.value.toLowerCase();
          const cards = document.querySelectorAll('.schedule-card');
          const noResults = document.getElementById('noScheduleResults');
          let visibleCount = 0;

          cards.forEach(card => {
            const name = card.dataset.name;
            const email = card.dataset.email;
            
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
              card.style.display = '';
              visibleCount++;
            } else {
              card.style.display = 'none';
            }
          });

          if (visibleCount === 0 && searchTerm !== '') {
            noResults.classList.remove('hidden');
          } else {
            noResults.classList.add('hidden');
          }
        });
      }

      // Schedule Modal Functions
      function openScheduleModal() {
        document.getElementById('scheduleModalTitle').textContent = 'Set Work Schedule';
        document.getElementById('schedule_check_in').value = '09:00';
        document.getElementById('schedule_check_out').value = '17:00';
        document.getElementById('schedule_late_min').value = '10';
        clearScheduleSelection();
        document.getElementById('schedule_employee_search').value = '';
        filterScheduleEmployees();
        document.getElementById('scheduleModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function editSchedule(userId, userName, checkIn, checkOut, lateMins) {
        document.getElementById('scheduleModalTitle').textContent = `Update Schedule - ${userName}`;
        document.getElementById('schedule_user_id_hidden').value = userId;
        document.getElementById('schedule_check_in').value = checkIn;
        document.getElementById('schedule_check_out').value = checkOut;
        document.getElementById('schedule_late_min').value = lateMins;
        
        const options = document.querySelectorAll('.employee-option');
        options.forEach(opt => {
          if (opt.dataset.id == userId) {
            opt.click();
          }
        });
        
        document.getElementById('scheduleModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
      }

      function selectScheduleEmployee(id, name, email) {
        document.getElementById('schedule_user_id_hidden').value = id;
        document.getElementById('schedule_selected_name').textContent = name;
        document.getElementById('schedule_selected_email').textContent = email;
        
        const option = document.querySelector(`.employee-option[data-id="${id}"]`);
        const avatar = option.querySelector('img, div').cloneNode(true);
        document.getElementById('schedule_selected_avatar').innerHTML = '';
        document.getElementById('schedule_selected_avatar').appendChild(avatar);
        
        document.getElementById('schedule_selected_display').classList.remove('hidden');
        document.getElementById('schedule_employee_list').parentElement.classList.add('hidden');
        document.getElementById('schedule_employee_search').parentElement.classList.add('hidden');
      }

      function clearScheduleSelection() {
        document.getElementById('schedule_user_id_hidden').value = '';
        document.getElementById('schedule_selected_display').classList.add('hidden');
        document.getElementById('schedule_employee_list').parentElement.classList.remove('hidden');
        document.getElementById('schedule_employee_search').parentElement.classList.remove('hidden');
      }

      const scheduleSearch = document.getElementById('schedule_employee_search');
      if (scheduleSearch) {
        scheduleSearch.addEventListener('input', filterScheduleEmployees);
      }

      function filterScheduleEmployees() {
        const searchTerm = document.getElementById('schedule_employee_search').value.toLowerCase();
        const options = document.querySelectorAll('.employee-option');
        const noResults = document.getElementById('schedule_no_results');
        let visibleCount = 0;

        options.forEach(option => {
          const name = option.dataset.name;
          const email = option.dataset.email;
          
          if (name.includes(searchTerm) || email.includes(searchTerm)) {
            option.style.display = '';
            visibleCount++;
          } else {
            option.style.display = 'none';
          }
        });

        if (visibleCount === 0) {
          noResults.classList.remove('hidden');
          document.getElementById('schedule_employee_list').classList.add('hidden');
        } else {
          noResults.classList.add('hidden');
          document.getElementById('schedule_employee_list').classList.remove('hidden');
        }
      }

      // Day Off Modal Functions
      function openDayOffModal() {
        clearDayOffSelection();
        document.getElementById('dayoff_employee_search').value = '';
        filterDayOffEmployees();
        document.getElementById('dayOffModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function closeDayOffModal() {
        document.getElementById('dayOffModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
      }

      function selectDayOffEmployee(id, name, email, image) {
        document.getElementById('dayoff_user_id_hidden').value = id;
        document.getElementById('dayoff_selected_name').textContent = name;
        document.getElementById('dayoff_selected_email').textContent = email;
        
        const option = document.querySelector(`.dayoff-option[data-id="${id}"]`);
        const avatar = option.querySelector('img, div').cloneNode(true);
        document.getElementById('dayoff_selected_avatar').innerHTML = '';
        document.getElementById('dayoff_selected_avatar').appendChild(avatar);
        
        document.getElementById('dayoff_selected_display').classList.remove('hidden');
        document.getElementById('dayoff_employee_list').parentElement.classList.add('hidden');
        document.getElementById('dayoff_employee_search').parentElement.classList.add('hidden');
      }

      function clearDayOffSelection() {
        document.getElementById('dayoff_user_id_hidden').value = '';
        document.getElementById('dayoff_selected_display').classList.add('hidden');
        document.getElementById('dayoff_employee_list').parentElement.classList.remove('hidden');
        document.getElementById('dayoff_employee_search').parentElement.classList.remove('hidden');
      }

      const dayoffSearch = document.getElementById('dayoff_employee_search');
      if (dayoffSearch) {
        dayoffSearch.addEventListener('input', filterDayOffEmployees);
      }

      function filterDayOffEmployees() {
        const searchTerm = document.getElementById('dayoff_employee_search').value.toLowerCase();
        const options = document.querySelectorAll('.dayoff-option');
        const noResults = document.getElementById('dayoff_no_results');
        let visibleCount = 0;

        options.forEach(option => {
          const name = option.dataset.name;
          const email = option.dataset.email;
          
          if (name.includes(searchTerm) || email.includes(searchTerm)) {
            option.style.display = '';
            visibleCount++;
          } else {
            option.style.display = 'none';
          }
        });

        if (visibleCount === 0) {
          noResults.classList.remove('hidden');
          document.getElementById('dayoff_employee_list').classList.add('hidden');
        } else {
          noResults.classList.add('hidden');
          document.getElementById('dayoff_employee_list').classList.remove('hidden');
        }
      }

      // Attendance Records Search and Filter
      const searchRecords = document.getElementById('searchRecords');
      const filterDate = document.getElementById('filterDate');

      if (searchRecords) {
        searchRecords.addEventListener('input', filterAttendanceRecords);
      }

      if (filterDate) {
        filterDate.addEventListener('change', filterAttendanceRecords);
      }

      function filterAttendanceRecords() {
        const searchTerm = document.getElementById('searchRecords').value.toLowerCase();
        const selectedDate = document.getElementById('filterDate').value;
        const rows = document.querySelectorAll('.attendance-row');
        const noResultsFiltered = document.getElementById('noRecordsFiltered');
        const tableContainer = document.getElementById('recordsTableContainer');
        
        let visibleCount = 0;
        let onTimeCount = 0;
        let lateCount = 0;
        let absentCount = 0;
        let leaveCount = 0;

        rows.forEach(row => {
          const name = row.dataset.name;
          const email = row.dataset.email;
          const rowDate = row.dataset.date;
          const status = row.dataset.status;
          
          const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
          const matchesDate = !selectedDate || rowDate === selectedDate;
          
          if (matchesSearch && matchesDate) {
            row.style.display = '';
            visibleCount++;
            
            if (status === 'on_time') onTimeCount++;
            else if (status === 'late') lateCount++;
            else if (status === 'absent') absentCount++;
            else if (status === 'leave') leaveCount++;
          } else {
            row.style.display = 'none';
          }
        });

        document.getElementById('visibleCount').textContent = visibleCount;
        document.getElementById('onTimeCount').textContent = onTimeCount;
        document.getElementById('lateCount').textContent = lateCount;
        document.getElementById('absentCount').textContent = absentCount;
        document.getElementById('leaveCount').textContent = leaveCount;

        if (visibleCount === 0) {
          noResultsFiltered.classList.remove('hidden');
          tableContainer.classList.add('hidden');
        } else {
          noResultsFiltered.classList.add('hidden');
          tableContainer.classList.remove('hidden');
        }
      }

      function filterByDate(type) {
        const filterDate = document.getElementById('filterDate');
        const today = new Date();
        
        if (type === 'today') {
          const year = today.getFullYear();
          const month = String(today.getMonth() + 1).padStart(2, '0');
          const day = String(today.getDate()).padStart(2, '0');
          filterDate.value = `${year}-${month}-${day}`;
        } else if (type === 'clear') {
          filterDate.value = '';
        }
        
        filterAttendanceRecords();
      }

      function clearAllFilters() {
        document.getElementById('searchRecords').value = '';
        document.getElementById('filterDate').value = '';
        filterAttendanceRecords();
      }

      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeScheduleModal();
          closeDayOffModal();
        }
      });
    </script>
  </body>
</html>
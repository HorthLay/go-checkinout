<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Attendance Log - Attendify</title>
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
          <span class="font-bold text-base md:text-lg">Attendance Log</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Attendance Log</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">View your attendance history</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
          <!-- Total Present -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Present</span>
              <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg md:text-xl">check_circle</span>
            </div>
            <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPresent }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This month</p>
          </div>

          <!-- Total Late -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Late</span>
              <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-lg md:text-xl">schedule</span>
            </div>
            <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $totalLate }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This month</p>
          </div>

          <!-- Total Absent -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Absent</span>
              <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-lg md:text-xl">cancel</span>
            </div>
            <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAbsent }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This month</p>
          </div>

          <!-- Total Leave -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Leave</span>
              <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-lg md:text-xl">event_available</span>
            </div>
            <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $totalLeave }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This month</p>
          </div>
        </div>

        <!-- Current Schedule Card (if exists) -->
        @if($schedule)
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary">schedule</span>
              </div>
              <div class="min-w-0">
                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white truncate">My Work Schedule</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Your current working hours</p>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
              <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 md:p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-In Time</p>
                <p class="text-base md:text-lg font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($schedule->scheduled_check_in)->format('h:i A') }}</p>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 md:p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-Out Time</p>
                <p class="text-base md:text-lg font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($schedule->scheduled_check_out)->format('h:i A') }}</p>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 md:p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Late Tolerance</p>
                <p class="text-base md:text-lg font-bold text-gray-900 dark:text-white">{{ $schedule->late_allowed_min }} min</p>
              </div>
            </div>
          </div>
        @endif

        <!-- Today's Status -->
        @if($todayAttendance)
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
              <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">today</span>
                </div>
                <div class="min-w-0">
                  <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Today's Attendance</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ now()->format('l, F j, Y') }}</p>
                </div>
              </div>
              <span class="inline-flex items-center px-2.5 py-1.5 md:px-3 rounded-lg text-xs md:text-sm font-medium shrink-0 self-start sm:self-auto
                {{ $todayAttendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
                {{ $todayAttendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                {{ $todayAttendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                {{ $todayAttendance->status === 'leave' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
              ">
                {{ ucfirst(str_replace('_', ' ', $todayAttendance->status)) }}
              </span>
            </div>
            <div class="grid grid-cols-3 gap-3 md:gap-4">
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-In</p>
                <p class="text-sm md:text-base font-semibold text-gray-900 dark:text-white truncate">
                  {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('h:i A') : '—' }}
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-Out</p>
                <p class="text-sm md:text-base font-semibold text-gray-900 dark:text-white truncate">
                  {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('h:i A') : '—' }}
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Hours</p>
                <p class="text-sm md:text-base font-semibold text-gray-900 dark:text-white truncate">
                  {{ $todayAttendance->formatted_work_hours ?? '—' }}
                </p>
              </div>
            </div>
            @if($todayAttendance->note)
              <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Note</p>
                <p class="text-sm text-gray-900 dark:text-white break-words">{{ $todayAttendance->note }}</p>
              </div>
            @endif
          </div>
        @elseif($hasDayOffToday)
          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4 md:p-6 mb-6">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-2xl md:text-3xl shrink-0">event_available</span>
              <div class="min-w-0">
                <h3 class="text-base md:text-lg font-bold text-purple-900 dark:text-purple-100">Today is your Day Off</h3>
                <p class="text-sm text-purple-700 dark:text-purple-300">Enjoy your day!</p>
              </div>
            </div>
          </div>
        @else
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 md:p-6 mb-6">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl md:text-3xl shrink-0">info</span>
              <div class="min-w-0">
                <h3 class="text-base md:text-lg font-bold text-blue-900 dark:text-blue-100">No attendance recorded today</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">Please check in when you arrive</p>
              </div>
            </div>
          </div>
        @endif

        <!-- Attendance History -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-800">
            <div class="flex flex-col gap-4">
              <div>
                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Attendance History</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Your attendance records</p>
              </div>

              <!-- Filter Controls -->
              <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1 sm:flex-initial">
                  <input
                    type="date"
                    id="filterDate"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                    value="{{ request('date') }}"
                  />
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">calendar_today</span>
                </div>

                <div class="flex gap-2">
                  <button onclick="filterByDate('today')" class="flex-1 sm:flex-initial px-4 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">
                    Today
                  </button>
                  <button onclick="clearDateFilter()" class="flex-1 sm:flex-initial px-4 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">
                    Clear
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Desktop Table -->
          <div id="recordsTableContainer" class="hidden md:block overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <tr>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check-In</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check-Out</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($monthlyAttendance as $attendance)
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors attendance-row"
                      data-date="{{ $attendance->attendance_date->format('Y-m-d') }}"
                      data-status="{{ $attendance->status }}">
                    <td class="py-4 px-6">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->attendance_date->format('M d, Y') }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->attendance_date->format('l') }}</p>
                    </td>
                    <td class="py-4 px-6">
                      <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '—' }}</p>
                    </td>
                    <td class="py-4 px-6">
                      <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '—' }}</p>
                    </td>
                    <td class="py-4 px-6">
                      <p class="text-sm text-gray-900 dark:text-white">{{ $attendance->formatted_work_hours ?? '—' }}</p>
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
                  <tr>
                    <td colspan="5" class="py-12 text-center">
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

          <!-- Mobile Cards -->
          <div id="mobileRecordsContainer" class="md:hidden divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($monthlyAttendance as $attendance)
              <div class="p-4 attendance-row"
                   data-date="{{ $attendance->attendance_date->format('Y-m-d') }}"
                   data-status="{{ $attendance->status }}">
                <div class="flex items-start justify-between gap-2 mb-3">
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $attendance->attendance_date->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->attendance_date->format('l') }}</p>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium shrink-0
                    {{ $attendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
                    {{ $attendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                    {{ $attendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                    {{ $attendance->status === 'leave' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
                  ">
                    {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                  </span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-In</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-Out</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Hours</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $attendance->formatted_work_hours ?? '—' }}</p>
                  </div>
                </div>
              </div>
            @empty
              <div class="py-12 text-center">
                <div class="flex flex-col items-center gap-3">
                  <span class="material-symbols-outlined text-5xl text-gray-300">event_busy</span>
                  <p class="text-sm text-gray-500 dark:text-gray-400">No attendance records found</p>
                </div>
              </div>
            @endforelse
          </div>

          <!-- No Results Message (hidden by default) -->
          <div id="noRecordsFiltered" class="hidden py-12 text-center px-4">
            <div class="flex flex-col items-center gap-3">
              <span class="material-symbols-outlined text-4xl md:text-5xl text-gray-300">search_off</span>
              <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">No records found for selected date</p>
              <button onclick="clearDateFilter()" class="mt-2 px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl text-sm font-medium transition-colors">
                Clear Filter
              </button>
            </div>
          </div>

          <!-- Pagination -->
          @if($monthlyAttendance->hasPages())
            <div class="p-4 md:p-6 border-t border-gray-100 dark:border-gray-800">
              <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 text-center sm:text-left">
                  Showing <span class="font-semibold">{{ $monthlyAttendance->firstItem() }}</span> to <span class="font-semibold">{{ $monthlyAttendance->lastItem() }}</span> of <span class="font-semibold">{{ $monthlyAttendance->total() }}</span> records
                </p>
                <div class="flex flex-wrap gap-2 justify-center">
                  @if ($monthlyAttendance->onFirstPage())
                    <span class="px-3 md:px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed text-sm">Previous</span>
                  @else
                    <a href="{{ $monthlyAttendance->previousPageUrl() }}" class="px-3 md:px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm">Previous</a>
                  @endif

                  <div class="flex gap-1 md:gap-2">
                    @foreach ($monthlyAttendance->getUrlRange(max(1, $monthlyAttendance->currentPage() - 1), min($monthlyAttendance->lastPage(), $monthlyAttendance->currentPage() + 1)) as $page => $url)
                      @if ($page == $monthlyAttendance->currentPage())
                        <span class="px-3 md:px-4 py-2 bg-primary text-white rounded-lg font-medium text-sm">{{ $page }}</span>
                      @else
                        <a href="{{ $url }}" class="px-3 md:px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm">{{ $page }}</a>
                      @endif
                    @endforeach
                  </div>

                  @if ($monthlyAttendance->hasMorePages())
                    <a href="{{ $monthlyAttendance->nextPageUrl() }}" class="px-3 md:px-4 py-2 bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm">Next</a>
                  @else
                    <span class="px-3 md:px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed text-sm">Next</span>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>
      </main>
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

      // Date Filter Functions
      const filterDate = document.getElementById('filterDate');
      
      if (filterDate) {
        filterDate.addEventListener('change', filterAttendanceRecords);
      }

      function filterAttendanceRecords() {
        const selectedDate = document.getElementById('filterDate').value;
        const rows = document.querySelectorAll('.attendance-row');
        const noResultsFiltered = document.getElementById('noRecordsFiltered');
        const tableContainer = document.getElementById('recordsTableContainer');
        const mobileContainer = document.getElementById('mobileRecordsContainer');
        
        let visibleCount = 0;

        rows.forEach(row => {
          const rowDate = row.dataset.date;
          const matchesDate = !selectedDate || rowDate === selectedDate;
          
          if (matchesDate) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });

        if (visibleCount === 0) {
          noResultsFiltered.classList.remove('hidden');
          tableContainer.classList.add('hidden');
          mobileContainer.classList.add('hidden');
        } else {
          noResultsFiltered.classList.add('hidden');
          tableContainer.classList.remove('hidden');
          mobileContainer.classList.remove('hidden');
        }
      }

      function filterByDate(type) {
        const filterDateInput = document.getElementById('filterDate');
        const today = new Date();
        
        if (type === 'today') {
          const year = today.getFullYear();
          const month = String(today.getMonth() + 1).padStart(2, '0');
          const day = String(today.getDate()).padStart(2, '0');
          filterDateInput.value = `${year}-${month}-${day}`;
        }
        
        filterAttendanceRecords();
      }

      function clearDateFilter() {
        document.getElementById('filterDate').value = '';
        filterAttendanceRecords();
      }
    </script>
  </body>
</html>
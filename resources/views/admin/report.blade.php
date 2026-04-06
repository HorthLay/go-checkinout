<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Attendance Reports - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    
    <!-- Inter font for English -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" /></noscript>
    
    <!-- Noto Sans Khmer font -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap" /></noscript>
    
    <!-- Material Symbols -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" /></noscript>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
              display: ["Inter", "Noto Sans Khmer", "sans-serif"],
              khmer: ["Noto Sans Khmer", "sans-serif"],
              inter: ["Inter", "sans-serif"],
            },
          },
        },
      };
    </script>
    <style>
      body {
        font-family: "Inter", "Noto Sans Khmer", sans-serif;
      }
      
      .khmer-text {
        font-family: "Noto Sans Khmer", sans-serif;
        line-height: 1.8;
      }
      
      .mixed-text {
        font-family: "Inter", "Noto Sans Khmer", sans-serif;
      }
      
      .no-scrollbar::-webkit-scrollbar {
        display: none;
      }
      .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
      }

      .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
      }

      .attendance-row {
        animation: fadeInUp 0.5s ease-out backwards;
      }
      
      .attendance-row:nth-child(n) { animation-delay: calc(0.05s * var(--row-index, 0)); }

      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .stats-card {
        animation: scaleIn 0.5s ease-out backwards;
      }
      
      .stats-card:nth-child(1) { animation-delay: 0.1s; }
      .stats-card:nth-child(2) { animation-delay: 0.2s; }
      .stats-card:nth-child(3) { animation-delay: 0.3s; }
      .stats-card:nth-child(4) { animation-delay: 0.4s; }

      @keyframes scaleIn {
        from {
          opacity: 0;
          transform: scale(0.9);
        }
        to {
          opacity: 1;
          transform: scale(1);
        }
      }

      .pagination-button {
        transition: all 0.2s;
      }
      .pagination-button:hover:not(:disabled) {
        transform: translateY(-2px);
      }

      @media (max-width: 768px) {
        .chart-container {
          height: 220px;
        }
        
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
        
        .hide-mobile {
          display: none;
        }
        
        .stats-card {
          padding: 12px !important;
        }
        
        .filter-grid {
          grid-template-columns: 1fr !important;
        }
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

      @media print {
        body {
          background: white !important;
        }
        
        * {
          font-family: "Inter", "Noto Sans Khmer", sans-serif !important;
        }
        
        .no-print {
          display: none !important;
        }
        
        #sidebar,
        header,
        #mobile-menu {
          display: none !important;
        }
        
        .flex-1 {
          margin: 0 !important;
          padding: 20px !important;
        }
      }
    </style>
    @livewireStyles
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div id="sidebar" class="no-print">
      @include('home.Layouts.sidebar')
    </div>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="h-16 md:h-20 flex items-center justify-between px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10 no-print">
        <div class="flex items-center gap-3 lg:hidden">
          <span class="font-bold text-base md:text-lg">Reports</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Attendance Reports</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Generate and export attendance reports</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      <div class="no-print">
        @include('home.Layouts.mobile')
      </div>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-3 md:p-6 lg:p-10">
        <!-- Filters Section -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-6 mb-4 md:mb-6 no-print">
          <h2 class="text-base md:text-lg font-bold text-gray-900 dark:text-white mb-3 md:mb-4">Report Filters</h2>
          
          <form method="GET" action="{{ route('reports') }}" class="filter-grid grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
            <!-- Start Date -->
            <div>
              <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 md:mb-2">Start Date</label>
              <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg md:rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" />
            </div>

            <!-- End Date -->
            <div>
              <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 md:mb-2">End Date</label>
              <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg md:rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" />
            </div>

            <!-- User Filter -->
            <div>
              <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 md:mb-2">Employee</label>
              <select name="user_id" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg md:rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                <option value="">All Employees</option>
                @foreach($allUsers as $user)
                  <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2 action-buttons">
              <button type="submit" class="flex-1 px-3 md:px-4 py-2 text-sm bg-primary hover:bg-primary-dark text-white rounded-lg md:rounded-xl font-medium transition-colors flex items-center justify-center gap-1.5 md:gap-2">
                <span class="material-symbols-outlined text-base md:text-lg">filter_alt</span>
                <span class="text-xs md:text-sm">Apply</span>
              </button>
              
              <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="px-3 md:px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg md:rounded-xl font-medium transition-colors flex items-center justify-center gap-1.5 md:gap-2">
                <span class="material-symbols-outlined text-base md:text-lg">print</span>
                <span class="hidden sm:inline text-xs md:text-sm">Print</span>
              </a>
            </div>
          </form>
          
          <!-- Quick Filters -->
          <div class="flex flex-wrap gap-1.5 md:gap-2 mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('reports', array_merge(request()->except(['start_date', 'end_date']), ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')])) }}" class="compact-mobile px-3 md:px-4 py-1.5 md:py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md md:rounded-lg text-xs md:text-sm font-medium transition-colors">
              Today
            </a>
            <a href="{{ route('reports', array_merge(request()->except(['start_date', 'end_date']), ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')])) }}" class="compact-mobile px-3 md:px-4 py-1.5 md:py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md md:rounded-lg text-xs md:text-sm font-medium transition-colors">
              This Month
            </a>
            <a href="{{ route('reports', array_merge(request()->except(['start_date', 'end_date']), ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')])) }}" class="compact-mobile px-3 md:px-4 py-1.5 md:py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md md:rounded-lg text-xs md:text-sm font-medium transition-colors">
              Last Month
            </a>
            <a href="{{ route('reports', array_merge(request()->except(['start_date', 'end_date']), ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d')])) }}" class="compact-mobile px-3 md:px-4 py-1.5 md:py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md md:rounded-lg text-xs md:text-sm font-medium transition-colors">
              This Year
            </a>
            <a href="{{ route('reports') }}" class="compact-mobile px-3 md:px-4 py-1.5 md:py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md md:rounded-lg text-xs md:text-sm font-medium transition-colors">
              Reset
            </a>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
          <!-- Total Present -->
          <div class="stats-card bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-1 md:mb-2">
              <span class="text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Present</span>
              <span class="material-symbols-outlined text-base md:text-xl text-green-600 dark:text-green-400">check_circle</span>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_present'] }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">Days</p>
          </div>

          <!-- Total Absent -->
          <div class="stats-card bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-1 md:mb-2">
              <span class="text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Absent</span>
              <span class="material-symbols-outlined text-base md:text-xl text-red-600 dark:text-red-400">cancel</span>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_absent'] }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">Days</p>
          </div>

          <!-- Total Leave -->
          <div class="stats-card bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-1 md:mb-2">
              <span class="text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Leave</span>
              <span class="material-symbols-outlined text-base md:text-xl text-purple-600 dark:text-purple-400">event_available</span>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_leave'] ?? 0 }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">Days</p>
          </div>

          <!-- Total Hours -->
          <div class="stats-card bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
            <div class="flex items-center justify-between mb-1 md:mb-2">
              <span class="text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Hours</span>
              <span class="material-symbols-outlined text-base md:text-xl text-blue-600 dark:text-blue-400">timer</span>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_hours'], 1) }}</p>
            <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">Hours</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
          <!-- Top 5 Performers -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-800">
              <div class="flex items-center gap-2 md:gap-3">
                <div class="size-8 md:size-10 rounded-lg md:rounded-xl bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                  <span class="material-symbols-outlined text-base md:text-xl text-yellow-600 dark:text-yellow-400">emoji_events</span>
                </div>
                <div>
                  <h3 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white">Top 5 Performers</h3>
                  <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400">By total work hours</p>
                </div>
              </div>
            </div>

            <div class="p-4 md:p-6">
              <div class="space-y-2 md:space-y-4">
                @forelse($topUsers as $index => $topUser)
                  <div class="flex items-center gap-2 md:gap-4">
                    <div class="flex items-center justify-center size-8 md:size-10 rounded-full font-bold text-white text-xs md:text-base shrink-0
                      {{ $index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600' : '' }}
                      {{ $index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500' : '' }}
                      {{ $index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-600' : '' }}
                      {{ $index > 2 ? 'bg-gradient-to-br from-blue-400 to-blue-600' : '' }}
                    ">
                      {{ $index + 1 }}
                    </div>

                    <div class="flex-1 min-w-0">
                      <p class="text-xs md:text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $topUser->name }}</p>
                      <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 truncate hidden sm:block">{{ $topUser->email }}</p>
                    </div>

                    <div class="text-right">
                      <p class="text-xs md:text-sm font-bold text-gray-900 dark:text-white whitespace-nowrap">
                        @php
                          $hours = floor($topUser->total_hours ?? 0);
                          $minutes = (($topUser->total_hours ?? 0) - $hours) * 60;
                        @endphp
                        {{ $hours }}h {{ round($minutes) }}m
                      </p>
                      <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400">{{ $topUser->total_days ?? 0 }} days</p>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-6 md:py-8">
                    <span class="material-symbols-outlined text-3xl md:text-5xl text-gray-300">emoji_events</span>
                    <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-2">No data available</p>
                  </div>
                @endforelse
              </div>
            </div>
          </div>

          <!-- Daily Attendance Chart -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-800">
              <div class="flex items-center gap-2 md:gap-3">
                <div class="size-8 md:size-10 rounded-lg md:rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                  <span class="material-symbols-outlined text-base md:text-xl text-blue-600 dark:text-blue-400">insights</span>
                </div>
                <div>
                  <h3 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white">Attendance Trend</h3>
                  <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400">Daily attendance overview</p>
                </div>
              </div>
            </div>

            <div class="p-4 md:p-6">
              <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Records Table -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
              <div>
                <h3 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white">Detailed Attendance Records</h3>
                <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">
                  Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} records
                </p>
              </div>

              <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('reports.export-csv', request()->all()) }}" class="no-print flex-1 sm:flex-none px-3 md:px-4 py-2 text-xs md:text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg md:rounded-xl font-medium transition-colors flex items-center justify-center gap-1.5 md:gap-2">
                  <span class="material-symbols-outlined text-base md:text-lg">download</span>
                  <span class="hidden md:inline">Export CSV</span>
                  <span class="md:hidden">CSV</span>
                </a>

                <button onclick="exportToCSV()" class="no-print flex-1 sm:flex-none px-3 md:px-4 py-2 text-xs md:text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg md:rounded-xl font-medium transition-colors flex items-center justify-center gap-1.5 md:gap-2">
                  <span class="material-symbols-outlined text-base md:text-lg">file_download</span>
                  <span class="hidden sm:inline">Quick Export</span>
                  <span class="sm:hidden">Quick</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
              <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <tr>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">Employee</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">Date</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap hide-mobile">Morning</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap hide-mobile">Afternoon</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">Hours</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">Status Morning</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">Status Afternoon</th>
                  <th class="text-left py-2 md:py-4 px-3 md:px-6 text-[10px] md:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap hide-mobile">Mission</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($attendances as $index => $attendance)
                  <tr class="attendance-row hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" style="--row-index: {{ $index }}">
                    <!-- Employee -->
                    <td class="py-2 md:py-4 px-3 md:px-6">
                      <p class="text-xs md:text-sm font-medium text-gray-900 dark:text-white truncate max-w-[120px] md:max-w-none">
                        {{ $attendance->user->name }}
                      </p>
                      <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 truncate max-w-[120px] md:max-w-none hidden sm:block">
                        {{ $attendance->user->email }}
                      </p>
                    </td>

                    <!-- Date -->
                    <td class="py-2 md:py-4 px-3 md:px-6 whitespace-nowrap">
                      <p class="text-xs md:text-sm font-medium text-gray-900 dark:text-white">
                        {{ $attendance->attendance_date->format('M d') }}
                      </p>
                      <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 hidden md:block">
                        {{ $attendance->attendance_date->format('l') }}
                      </p>
                    </td>

                    <!-- Morning -->
                    <td class="py-2 md:py-4 px-3 md:px-6 hide-mobile">
                      <div class="text-[10px] md:text-xs space-y-0.5 md:space-y-1">
                        <p><span class="text-gray-500">In:</span> {{ $attendance->morning_check_in?->format('h:i A') ?? '—' }}</p>
                        <p><span class="text-gray-500">Out:</span> {{ $attendance->morning_check_out?->format('h:i A') ?? '—' }}</p>
                      </div>
                    </td>

                    <!-- Afternoon -->
                    <td class="py-2 md:py-4 px-3 md:px-6 hide-mobile">
                      <div class="text-[10px] md:text-xs space-y-0.5 md:space-y-1">
                        <p><span class="text-gray-500">In:</span> {{ $attendance->afternoon_check_in?->format('h:i A') ?? '—' }}</p>
                        <p><span class="text-gray-500">Out:</span> {{ $attendance->afternoon_check_out?->format('h:i A') ?? '—' }}</p>
                      </div>
                    </td>

                    <!-- Hours -->
                    <td class="py-2 md:py-4 px-3 md:px-6 whitespace-nowrap">
                      <p class="text-xs md:text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $attendance->formatted_work_hours ?? '—' }}
                      </p>
                      <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 hidden md:block">
                        M: {{ $attendance->formatted_morning_hours }} | A: {{ $attendance->formatted_afternoon_hours }}
                      </p>
                    </td>

                    <!-- Status Morning -->
                    <td class="py-2 md:py-4 px-3 md:px-6">
                      <span class="inline-flex items-center px-2 md:px-2.5 py-0.5 md:py-1 rounded-md md:rounded-lg text-[10px] md:text-xs font-medium whitespace-nowrap
                        {{ $attendance->morning_work_hours === null || $attendance->morning_work_hours == 0 
                           ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' 
                           : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' }}
                      ">
                        {{ $attendance->morning_work_hours === null || $attendance->morning_work_hours == 0 ? 'អវត្តមាន' : 'វត្តមាន' }}
                      </span>
                    </td>

                    <!-- Status Afternoon -->
                    <td class="py-2 md:py-4 px-3 md:px-6">
                      <span class="inline-flex items-center px-2 md:px-2.5 py-0.5 md:py-1 rounded-md md:rounded-lg text-[10px] md:text-xs font-medium whitespace-nowrap
                        {{ $attendance->afternoon_work_hours === null || $attendance->afternoon_work_hours == 0 
                           ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' 
                           : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' }}
                      ">
                        {{ $attendance->afternoon_work_hours === null || $attendance->afternoon_work_hours == 0 ? 'អវត្តមាន' : 'វត្តមាន' }}
                      </span>
                    </td>

                    <!-- Mission -->
                    <td class="py-2 md:py-4 px-3 md:px-6 hide-mobile">
                      @if(isset($attendance->day_missions) && $attendance->day_missions->count() > 0)
                        @php
                          $mission = $attendance->day_missions->first();
                        @endphp
                        <div class="space-y-1">
                          <div>
                            @if($mission->status === 'approved')
                              <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] md:text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                                បេសកកម្ម
                              </span>
                            @elseif($mission->status === 'pending')
                              <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] md:text-xs font-medium bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                                ពិនិត្យ
                              </span>
                            @else
                              <span class="text-gray-400 text-[10px] md:text-xs">--</span>
                            @endif
                          </div>
                          
                          <p class="text-[10px] md:text-xs text-gray-600 dark:text-gray-300">
                            <span class="text-gray-500">Check-in:</span>
                            @if($mission->check_in_time)
                              {{ \Carbon\Carbon::parse($mission->check_in_time)->format('h:i A') }}
                            @else
                              {{ $mission->created_at->format('h:i A') }}
                            @endif
                          </p>
                          
                          @if($attendance->day_missions->count() > 1)
                            <p class="text-[10px] text-gray-500">+{{ $attendance->day_missions->count() - 1 }} more</p>
                          @endif
                        </div>
                      @else
                        <span class="text-gray-400 text-[10px] md:text-xs">--</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="py-8 md:py-12 text-center">
                      <div class="flex flex-col items-center gap-2 md:gap-3">
                        <span class="material-symbols-outlined text-3xl md:text-5xl text-gray-300">search_off</span>
                        <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">No attendance records found</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          @if($attendances->hasPages())
            <div class="p-3 md:p-4 border-t border-gray-100 dark:border-gray-800 no-print">
              <div class="flex flex-col sm:flex-row items-center justify-between gap-3 md:gap-4">
                <div class="text-xs md:text-sm text-gray-600 dark:text-gray-400">
                  Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $attendances->firstItem() }}</span> to 
                  <span class="font-semibold text-gray-900 dark:text-white">{{ $attendances->lastItem() }}</span> of 
                  <span class="font-semibold text-gray-900 dark:text-white">{{ $attendances->total() }}</span> results
                </div>

                <div class="flex items-center gap-1 md:gap-2">
                  @if ($attendances->onFirstPage())
                    <button disabled class="pagination-button px-2 md:px-3 py-1.5 md:py-2 rounded-md md:rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed">
                      <span class="material-symbols-outlined text-base md:text-lg">chevron_left</span>
                    </button>
                  @else
                    <a href="{{ $attendances->appends(request()->except('page'))->previousPageUrl() }}" class="pagination-button px-2 md:px-3 py-1.5 md:py-2 rounded-md md:rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                      <span class="material-symbols-outlined text-base md:text-lg">chevron_left</span>
                    </a>
                  @endif

                  <div class="hidden sm:flex items-center gap-1 md:gap-2">
                    @foreach ($attendances->appends(request()->except('page'))->getUrlRange(max(1, $attendances->currentPage() - 1), min($attendances->lastPage(), $attendances->currentPage() + 1)) as $page => $url)
                      @if ($page == $attendances->currentPage())
                        <button class="pagination-button px-3 md:px-4 py-1.5 md:py-2 rounded-md md:rounded-lg bg-primary text-white text-xs md:text-sm font-medium shadow-sm">
                          {{ $page }}
                        </button>
                      @else
                        <a href="{{ $url }}" class="pagination-button px-3 md:px-4 py-1.5 md:py-2 rounded-md md:rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs md:text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                          {{ $page }}
                        </a>
                      @endif
                    @endforeach
                  </div>

                  <div class="sm:hidden px-2 md:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300">
                    Page {{ $attendances->currentPage() }} of {{ $attendances->lastPage() }}
                  </div>

                  @if ($attendances->hasMorePages())
                    <a href="{{ $attendances->appends(request()->except('page'))->nextPageUrl() }}" class="pagination-button px-2 md:px-3 py-1.5 md:py-2 rounded-md md:rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                      <span class="material-symbols-outlined text-base md:text-lg">chevron_right</span>
                    </a>
                  @else
                    <button disabled class="pagination-button px-2 md:px-3 py-1.5 md:py-2 rounded-md md:rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed">
                      <span class="material-symbols-outlined text-base md:text-lg">chevron_right</span>
                    </button>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>
      </main>
    </div>

    @livewireScripts
  
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

      // Attendance Chart
      const attendanceCtx = document.getElementById('attendanceChart');
      if (attendanceCtx) {
        const dailySummary = @json($dailySummary);
        
        new Chart(attendanceCtx, {
          type: 'line',
          data: {
            labels: dailySummary.map(day => {
              const date = new Date(day.date);
              return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            datasets: [
              {
                label: 'Present',
                data: dailySummary.map(day => day.present),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
              },
              {
                label: 'Late',
                data: dailySummary.map(day => day.late),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
              },
              {
                label: 'Absent',
                data: dailySummary.map(day => day.absent),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: true,
                position: 'bottom'
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      }

      // Export to CSV
      function exportToCSV() {
        const attendances = @json($attendances->items());
        
        let csv = 'Employee,Day,Date,Morning In,Morning Out,Afternoon In,Afternoon Out,Morning Status,Afternoon Status,Mission Status,Mission Check-In\n';
        
        attendances.forEach(att => {
          let dateStr = att.attendance_date.date || att.attendance_date;
          const dateObj = new Date(dateStr);
          const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
          const dateFormatted = dateObj.toLocaleDateString('en-US');
          
          const formatTime = (timeData) => {
            if (!timeData) return '-';
            let timeStr = timeData.date || timeData;
            const time = new Date(timeStr);
            return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
          };
          
          const morningStatus = (att.morning_work_hours === null || att.morning_work_hours == 0) ? 'អវត្តមាន' : 'វត្តមាន';
          const afternoonStatus = (att.afternoon_work_hours === null || att.afternoon_work_hours == 0) ? 'អវត្តមាន' : 'វត្តមាន';
          
          let missionStatus = '--';
          let missionCheckIn = '--';
          
          if (att.day_missions && att.day_missions.length > 0) {
            const mission = att.day_missions[0];
            
            if (mission.status === 'approved') {
              missionStatus = 'បេសកកម្ម';
            } else if (mission.status === 'pending') {
              missionStatus = 'ពិនិត្យ';
            } else {
              missionStatus = '--';
            }
            
            if (mission.check_in_time) {
              missionCheckIn = formatTime(mission.check_in_time);
            } else if (mission.created_at) {
              missionCheckIn = formatTime(mission.created_at);
            }
          }
          
          csv += `"${att.user.name}",`;
          csv += `"${dayName}",`;
          csv += `"${dateFormatted}",`;
          csv += `"${formatTime(att.morning_check_in)}",`;
          csv += `"${formatTime(att.morning_check_out)}",`;
          csv += `"${formatTime(att.afternoon_check_in)}",`;
          csv += `"${formatTime(att.afternoon_check_out)}",`;
          csv += `"${morningStatus}",`;
          csv += `"${afternoonStatus}",`;
          csv += `"${missionStatus}",`;
          csv += `"${missionCheckIn}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `attendance-report-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
      }
    </script>
  </body>
</html>
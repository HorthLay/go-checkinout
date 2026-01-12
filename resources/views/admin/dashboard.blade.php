<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard - Attendify</title>
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

      .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      }

      .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
      }

      @media (max-width: 768px) {
        .chart-container {
          height: 220px;
        }
        
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
      }

      [x-cloak] { 
        display: none !important; 
      }
    </style>
    @livewireStyles
  </head>
  <body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('home.Layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="h-16 md:h-20 flex items-center justify-between px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10">
        <div class="flex items-center gap-3 lg:hidden">
          <button id="menu-toggle" aria-label="Toggle menu" class="text-gray-500 hover:text-gray-900 dark:hover:text-white p-2">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <span class="font-bold text-base md:text-lg">Dashboard</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Welcome back, {{ Auth::user()->name }}</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Overview Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <!-- Total Employees -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs md:text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Employees</span>
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl md:text-3xl">groups</span>
            </div>
            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['total_employees'] }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Total registered</p>
          </div>

          <!-- Present Today -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs md:text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Present</span>
              <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-2xl md:text-3xl">check_circle</span>
            </div>
            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['total_present_today'] }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
          </div>

          <!-- Late Today -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs md:text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Late</span>
              <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-2xl md:text-3xl">schedule</span>
            </div>
            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['total_late_today'] }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
          </div>

          <!-- Total Hours This Month -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs md:text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Hours</span>
              <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-2xl md:text-3xl">timer</span>
            </div>
            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($stats['total_hours_month'], 0) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">This month</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Top 5 Performers -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/10 dark:to-orange-900/10">
              <div class="flex items-center gap-3">
                <div class="size-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                  <span class="material-symbols-outlined text-white text-2xl">emoji_events</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top 5 Performers</h3>
                  <p class="text-xs text-gray-600 dark:text-gray-400">By total work hours this month</p>
                </div>
              </div>
            </div>

            <div class="p-6">
              <div class="space-y-4">
                @forelse($topPerformers as $index => $performer)
                  <div class="flex items-center gap-4 p-4 rounded-xl {{ $index === 0 ? 'bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-gray-50 dark:bg-gray-800/50' }}">
                    <div class="flex items-center justify-center size-12 md:size-14 rounded-full font-bold text-white shrink-0 text-lg
                      {{ $index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-lg' : '' }}
                      {{ $index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500' : '' }}
                      {{ $index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-600' : '' }}
                      {{ $index > 2 ? 'bg-gradient-to-br from-blue-400 to-blue-600' : '' }}
                    ">
                      @if($index === 0) 🥇
                      @elseif($index === 1) 🥈
                      @elseif($index === 2) 🥉
                      @else {{ $index + 1 }}
                      @endif
                    </div>

                    <div class="flex-1 min-w-0">
                      <p class="text-sm md:text-base font-bold text-gray-900 dark:text-white truncate">{{ $performer->name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $performer->email }}</p>
                      <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-green-600 dark:text-green-400">✓ {{ $performer->present_days }} days</span>
                        @if($performer->late_days > 0)
                          <span class="text-xs text-orange-600 dark:text-orange-400">⚠ {{ $performer->late_days }} late</span>
                        @endif
                      </div>
                    </div>

                    <div class="text-right">
                      <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                        @php
                          $hours = floor($performer->total_hours ?? 0);
                          $minutes = (($performer->total_hours ?? 0) - $hours) * 60;
                        @endphp
                        {{ $hours }}h {{ round($minutes) }}m
                      </p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $performer->present_days > 0 ? number_format(($performer->total_hours ?? 0) / $performer->present_days, 1) : '0' }}h avg
                      </p>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-12">
                    <span class="material-symbols-outlined text-6xl text-gray-300">emoji_events</span>
                    <p class="text-gray-500 dark:text-gray-400 mt-3">No attendance data yet</p>
                  </div>
                @endforelse
              </div>
            </div>
          </div>

          <!-- Attendance Trend Chart -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800">
              <div class="flex items-center gap-3">
                <div class="size-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">insights</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Attendance Trend</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Last 7 days overview</p>
                </div>
              </div>
            </div>

            <div class="p-6">
              <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Status Distribution -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800">
              <div class="flex items-center gap-3">
                <div class="size-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                  <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-2xl">pie_chart</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Status Distribution</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400">This month breakdown</p>
                </div>
              </div>
            </div>

            <div class="p-6">
              <div class="chart-container">
                <canvas id="statusChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Recent Check-Ins -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="size-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-2xl">schedule</span>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Latest check-ins</p>
                  </div>
                </div>
                <a href="{{ route('admin.attendance.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium">View All →</a>
              </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-96 overflow-y-auto">
              @forelse($recentAttendance as $attendance)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                  <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold shrink-0">
                      {{ substr($attendance->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $attendance->user->name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->officeLocation->name ?? 'Unknown' }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->check_in->format('h:i A') }}</p>
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        {{ $attendance->status === 'on_time' ? 'bg-green-50 text-green-600' : '' }}
                        {{ $attendance->status === 'late' ? 'bg-orange-50 text-orange-600' : '' }}
                      ">
                        {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                      </span>
                    </div>
                  </div>
                </div>
              @empty
                <div class="p-12 text-center">
                  <span class="material-symbols-outlined text-5xl text-gray-300">schedule</span>
                  <p class="text-gray-500 dark:text-gray-400 mt-2">No recent activity</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>

        <!-- Today's Summary -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
              <div class="size-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-2xl">today</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Today's Summary</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
              <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $todaySummary['total_checked_in'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Checked In</p>
              </div>
              <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $todaySummary['on_time'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">On Time</p>
              </div>
              <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $todaySummary['late'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Late</p>
              </div>
              <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $todaySummary['total_checked_out'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Checked Out</p>
              </div>
              <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/20 rounded-xl">
                <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $todaySummary['pending'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Pending</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- CORRECT ORDER: Livewire Scripts FIRST -->
    @livewireScripts
    
    <!-- Alpine.js AFTER Livewire -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Scripts LAST -->
    <script>
      // Wait for DOM to be ready
      document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Toggle
        const menuToggle = document.getElementById("menu-toggle");
        const mobileMenu = document.getElementById("mobile-menu");

        if (menuToggle && mobileMenu) {
          menuToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            mobileMenu.classList.toggle("hidden");
          });
          
          // Close on click outside
          document.addEventListener("click", (e) => {
            if (mobileMenu && !menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
              mobileMenu.classList.add("hidden");
            }
          });
        }

        // Attendance Trend Chart
        const last7Days = @json($last7Days);
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
          new Chart(attendanceCtx, {
            type: 'line',
            data: {
              labels: last7Days.map(day => day.full_date),
              datasets: [
                {
                  label: 'Present',
                  data: last7Days.map(day => day.present),
                  borderColor: '#10b981',
                  backgroundColor: 'rgba(16, 185, 129, 0.1)',
                  borderWidth: 2,
                  fill: true,
                  tension: 0.4
                },
                {
                  label: 'Late',
                  data: last7Days.map(day => day.late),
                  borderColor: '#f59e0b',
                  backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
                  position: 'top'
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: { stepSize: 1 }
                }
              }
            }
          });
        }

        // Status Distribution Chart
        const statusDistribution = @json($statusDistribution);
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
          new Chart(statusCtx, {
            type: 'doughnut',
            data: {
              labels: ['On Time', 'Late', 'Absent', 'Leave'],
              datasets: [{
                data: [
                  statusDistribution.on_time,
                  statusDistribution.late,
                  statusDistribution.absent,
                  statusDistribution.leave
                ],
                backgroundColor: [
                  '#10b981',
                  '#f59e0b',
                  '#ef4444',
                  '#8b5cf6'
                ],
                borderWidth: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                  }
                }
              }
            }
          });
        }

        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        
        if (themeToggle) {
          // Get saved theme or default to light
          const currentTheme = localStorage.getItem('theme') || 'light';
          htmlElement.classList.toggle('dark', currentTheme === 'dark');
          
          themeToggle.addEventListener('click', () => {
            const isDark = htmlElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
          });
        }
      });
    </script>
  </body>
</html>
<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard - Location Tracker</title>
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

      /* Card hover effect */
      .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      }

      /* Chart container */
      .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
      }

      /* Mobile responsive adjustments */
      @media (max-width: 768px) {
        .chart-container {
          height: 220px;
        }
        
        /* Reduce padding on mobile */
        .mobile-padding {
          padding-left: 1rem !important;
          padding-right: 1rem !important;
        }

        /* Stat cards on mobile */
        .stat-card {
          padding: 1rem !important;
        }

        .stat-card h3 {
          font-size: 1.5rem !important;
        }

        /* Table responsive */
        table {
          font-size: 0.75rem;
        }

        table th,
        table td {
          padding: 0.5rem !important;
        }

        /* Hide some columns on mobile */
        .hide-mobile {
          display: none;
        }
      }

      @media (max-width: 640px) {
        .chart-container {
          height: 200px;
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

      /* Prevent horizontal scroll */
      body {
        overflow-x: hidden;
      }

      /* Better touch targets */
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
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
          <!-- Total Check-ins -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3 md:mb-4">
              <div class="p-2 md:p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg md:rounded-xl">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-xl md:text-2xl">location_on</span>
              </div>
              <span class="text-[10px] md:text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md md:rounded-lg">+12%</span>
            </div>
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-0.5 md:mb-1">1,247</h3>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Total Check-ins</p>
          </div>

          <!-- This Month -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3 md:mb-4">
              <div class="p-2 md:p-3 bg-green-50 dark:bg-green-900/20 rounded-lg md:rounded-xl">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-xl md:text-2xl">calendar_today</span>
              </div>
              <span class="text-[10px] md:text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md md:rounded-lg">+8%</span>
            </div>
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-0.5 md:mb-1">342</h3>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">This Month</p>
          </div>

          <!-- Active Locations -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3 md:mb-4">
              <div class="p-2 md:p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg md:rounded-xl">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-xl md:text-2xl">pin_drop</span>
              </div>
              <span class="text-[10px] md:text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md md:rounded-lg">--</span>
            </div>
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-0.5 md:mb-1">28</h3>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Active Locations</p>
          </div>

          <!-- Average Time -->
          <div class="stat-card bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3 md:mb-4">
              <div class="p-2 md:p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg md:rounded-xl">
                <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-xl md:text-2xl">schedule</span>
              </div>
              <span class="text-[10px] md:text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md md:rounded-lg">-3%</span>
            </div>
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-0.5 md:mb-1">2.4h</h3>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Avg. Duration</p>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
          <!-- Check-ins Chart -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 md:mb-6">
              <div>
                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Check-ins Overview</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Last 7 days activity</p>
              </div>
              <select class="text-xs md:text-sm border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg px-2 md:px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-primary/20">
                <option>Last 7 days</option>
                <option>Last 30 days</option>
                <option>Last 3 months</option>
              </select>
            </div>
            <div class="chart-container">
              <canvas id="checkinsChart"></canvas>
            </div>
          </div>

          <!-- Location Distribution -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-4 md:mb-6">
              <div>
                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Top Locations</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Most visited places</p>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="locationsChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl md:rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4 md:mb-6">
            <div>
              <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Your latest check-ins</p>
            </div>
            <a href="#" class="text-xs md:text-sm font-medium text-primary hover:text-primary-dark transition-colors">View All</a>
          </div>

          <!-- Mobile Card View -->
          <div class="block md:hidden space-y-3">
            <div class="border border-gray-100 dark:border-gray-800 rounded-lg p-3">
              <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                  <div class="p-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-lg">business</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Main Office</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">123 Business St.</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                  Completed
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>Jan 09, 2026 • 09:00 AM</span>
                <span class="font-medium">3h 45m</span>
              </div>
            </div>

            <div class="border border-gray-100 dark:border-gray-800 rounded-lg p-3">
              <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                  <div class="p-1.5 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-lg">store</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Client Site A</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">456 Market Rd.</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                  Completed
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>Jan 08, 2026 • 02:30 PM</span>
                <span class="font-medium">2h 15m</span>
              </div>
            </div>

            <div class="border border-gray-100 dark:border-gray-800 rounded-lg p-3">
              <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                  <div class="p-1.5 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-lg">home</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Home Office</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">789 Home Ave.</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                  In Progress
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>Jan 08, 2026 • 08:00 AM</span>
                <span class="font-medium">5h 30m</span>
              </div>
            </div>
          </div>

          <!-- Desktop Table View -->
          <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                  <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date & Time</th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                  <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                  <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                      <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-xl">business</span>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Main Office</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">123 Business St.</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-4">
                    <p class="text-sm text-gray-900 dark:text-white">Jan 09, 2026</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">09:00 AM</p>
                  </td>
                  <td class="py-4 px-4">
                    <p class="text-sm text-gray-900 dark:text-white">3h 45m</p>
                  </td>
                  <td class="py-4 px-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                      Completed
                    </span>
                  </td>
                  <td class="py-4 px-4 text-right">
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                      <span class="material-symbols-outlined text-xl">more_vert</span>
                    </button>
                  </td>
                </tr>

                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                  <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                      <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-xl">store</span>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Client Site A</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">456 Market Rd.</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-4">
                    <p class="text-sm text-gray-900 dark:text-white">Jan 08, 2026</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">02:30 PM</p>
                  </td>
                  <td class="py-4 px-4">
                    <p class="text-sm text-gray-900 dark:text-white">2h 15m</p>
                  </td>
                  <td class="py-4 px-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                      Completed
                    </span>
                  </td>
                  <td class="py-4 px-4 text-right">
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                      <span class="material-symbols-outlined text-xl">more_vert</span>
                    </button>
                  </td>
                </tr>

                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                  <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                      <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-xl">home</span>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Home Office</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">789 Home Ave.</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-4">
                    <p class="text-sm text-gray-900 dark:text-white">Jan 08, 2026</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">08:00 AM</p>
                  </td>
                  <td class="py-4 px-4">
                    <p class="text-sm text-gray-900 dark:text-white">5h 30m</p>
                  </td>
                  <td class="py-4 px-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                      In Progress
                    </span>
                  </td>
                  <td class="py-4 px-4 text-right">
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                      <span class="material-symbols-outlined text-xl">more_vert</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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

      // Check-ins Chart
      const checkinsCtx = document.getElementById('checkinsChart');
      if (checkinsCtx) {
        new Chart(checkinsCtx, {
          type: 'line',
          data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
              label: 'Check-ins',
              data: [45, 52, 38, 65, 48, 55, 42],
              borderColor: '#135bec',
              backgroundColor: 'rgba(19, 91, 236, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointRadius: 4,
              pointBackgroundColor: '#135bec',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                  color: '#6b7280',
                  font: {
                    size: window.innerWidth < 768 ? 10 : 12
                  }
                }
              },
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  color: '#6b7280',
                  font: {
                    size: window.innerWidth < 768 ? 10 : 12
                  }
                }
              }
            }
          }
        });
      }

      // Locations Chart
      const locationsCtx = document.getElementById('locationsChart');
      if (locationsCtx) {
        new Chart(locationsCtx, {
          type: 'doughnut',
          data: {
            labels: ['Main Office', 'Client Site A', 'Home Office', 'Client Site B', 'Others'],
            datasets: [{
              data: [35, 25, 20, 12, 8],
              backgroundColor: [
                '#135bec',
                '#8b5cf6',
                '#f59e0b',
                '#10b981',
                '#6b7280'
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
                  padding: window.innerWidth < 768 ? 10 : 15,
                  color: '#6b7280',
                  usePointStyle: true,
                  pointStyle: 'circle',
                  font: {
                    size: window.innerWidth < 768 ? 10 : 12
                  }
                }
              }
            }
          }
        });
      }
    </script>
  </body>
</html>
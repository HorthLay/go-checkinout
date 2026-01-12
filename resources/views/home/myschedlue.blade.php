<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Schedule - Attendify</title>
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
    @livewireStyles
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
          <span class="font-bold text-base md:text-lg">My Schedule</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Schedule</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">View your work schedule and upcoming day-offs</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Work Schedule Section -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden mb-6">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
              <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">schedule</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Work Schedule</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Your current working hours</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            @if($schedule)
              <!-- Morning Session -->
              <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                  <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">wb_sunny</span>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Morning Session</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="size-10 rounded-lg bg-green-600 dark:bg-green-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">login</span>
                      </div>
                      <p class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wider">Check-In</p>
                    </div>
                    <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ \Carbon\Carbon::parse($schedule->scheduled_check_in_morining)->format('h:i A') }}</p>
                  </div>

                  <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="size-10 rounded-lg bg-red-600 dark:bg-red-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">logout</span>
                      </div>
                      <p class="text-xs font-semibold text-red-700 dark:text-red-300 uppercase tracking-wider">Check-Out</p>
                    </div>
                    <p class="text-3xl font-bold text-red-900 dark:text-red-100">{{ \Carbon\Carbon::parse($schedule->scheduled_check_out_morining)->format('h:i A') }}</p>
                  </div>
                </div>

                @php
                  $morningIn = \Carbon\Carbon::parse($schedule->scheduled_check_in_morining);
                  $morningOut = \Carbon\Carbon::parse($schedule->scheduled_check_out_morining);
                  $morningHours = $morningOut->diffInHours($morningIn);
                  $morningMinutes = $morningOut->diffInMinutes($morningIn) % 60;
                @endphp

                <div class="mt-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-3">
                  <div class="flex items-center justify-between">
                    <p class="text-sm text-blue-700 dark:text-blue-300">Expected Work Hours</p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $morningHours }}h {{ $morningMinutes }}m</p>
                  </div>
                </div>
              </div>

              <!-- Afternoon Session -->
              <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                  <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">wb_twilight</span>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">Afternoon Session</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="size-10 rounded-lg bg-green-600 dark:bg-green-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">login</span>
                      </div>
                      <p class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wider">Check-In</p>
                    </div>
                    <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ \Carbon\Carbon::parse($schedule->scheduled_check_in_afternoon)->format('h:i A') }}</p>
                  </div>

                  <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="size-10 rounded-lg bg-red-600 dark:bg-red-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">logout</span>
                      </div>
                      <p class="text-xs font-semibold text-red-700 dark:text-red-300 uppercase tracking-wider">Check-Out</p>
                    </div>
                    <p class="text-3xl font-bold text-red-900 dark:text-red-100">{{ \Carbon\Carbon::parse($schedule->scheduled_check_out_afternoon)->format('h:i A') }}</p>
                  </div>
                </div>

                @php
                  $afternoonIn = \Carbon\Carbon::parse($schedule->scheduled_check_in_afternoon);
                  $afternoonOut = \Carbon\Carbon::parse($schedule->scheduled_check_out_afternoon);
                  $afternoonHours = $afternoonOut->diffInHours($afternoonIn);
                  $afternoonMinutes = $afternoonOut->diffInMinutes($afternoonIn) % 60;
                @endphp

                <div class="mt-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-3">
                  <div class="flex items-center justify-between">
                    <p class="text-sm text-blue-700 dark:text-blue-300">Expected Work Hours</p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $afternoonHours }}h {{ $afternoonMinutes }}m</p>
                  </div>
                </div>
              </div>

              <!-- Late Tolerance -->
              <div class="mb-6">
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="size-10 rounded-lg bg-orange-600 dark:bg-orange-500 flex items-center justify-center">
                      <span class="material-symbols-outlined text-white text-xl">timer</span>
                    </div>
                    <p class="text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase tracking-wider">Late Tolerance</p>
                  </div>
                  <p class="text-3xl font-bold text-orange-900 dark:text-orange-100">{{ $schedule->late_allowed_min }} <span class="text-xl">min</span></p>
                </div>
              </div>

              <!-- Schedule Info Card -->
              <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 mt-0.5">info</span>
                  <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Schedule Information</p>
                    <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                      <p>
                        <strong>Morning:</strong> {{ \Carbon\Carbon::parse($schedule->scheduled_check_in_morining)->format('h:i A') }} to {{ \Carbon\Carbon::parse($schedule->scheduled_check_out_morining)->format('h:i A') }}
                      </p>
                      <p>
                        <strong>Afternoon:</strong> {{ \Carbon\Carbon::parse($schedule->scheduled_check_in_afternoon)->format('h:i A') }} to {{ \Carbon\Carbon::parse($schedule->scheduled_check_out_afternoon)->format('h:i A') }}
                      </p>
                      <p class="pt-2 border-t border-blue-200 dark:border-blue-800">
                        You have <strong>{{ $schedule->late_allowed_min }} minutes</strong> grace period. Arriving after the scheduled time plus grace period will be marked as late.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Working Hours Calculation -->
              @php
                $totalMinutes = $morningOut->diffInMinutes($morningIn) + $afternoonOut->diffInMinutes($afternoonIn);
                $totalHours = floor($totalMinutes / 60);
                $totalMins = $totalMinutes % 60;
              @endphp

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Daily Work Hours</p>
                  <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalHours }}h {{ $totalMins }}m</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Morning: {{ $morningHours }}h {{ $morningMinutes }}m + Afternoon: {{ $afternoonHours }}h {{ $afternoonMinutes }}m</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Schedule Status</p>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium">
                    <span class="size-2 bg-green-600 dark:bg-green-400 rounded-full"></span>
                    Active
                  </span>
                </div>
              </div>
            @else
              <div class="text-center py-16">
                <div class="inline-flex items-center justify-center size-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                  <span class="material-symbols-outlined text-5xl text-gray-400">schedule</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Schedule Set</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                  You don't have a work schedule assigned yet. Please contact your administrator to set up your working hours.
                </p>
              </div>
            @endif
          </div>
        </div>

        <!-- Upcoming Day-Offs Section -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
              <div class="size-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-2xl">event_available</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Upcoming Day-Offs</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Your scheduled days off</p>
              </div>
            </div>
          </div>

          @php
            $dayOffs = \App\Models\AttendanceOffDay::where('user_id', Auth::id())
                                                   ->where('off_date', '>=', today())
                                                   ->orderBy('off_date', 'asc')
                                                   ->get();
          @endphp

          @if($dayOffs->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                  <tr>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Day</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reason</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Days Until</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                  @foreach($dayOffs as $dayOff)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                      <td class="py-4 px-6">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $dayOff->off_date->format('M d, Y') }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dayOff->off_date->format('l') }}</p>
                      </td>
                      <td class="py-4 px-6">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $dayOff->reason }}</p>
                      </td>
                      <td class="py-4 px-6">
                        @if($dayOff->off_date->isToday())
                          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300">
                            Today
                          </span>
                        @elseif($dayOff->off_date->isTomorrow())
                          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300">
                            Tomorrow
                          </span>
                        @else
                          <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dayOff->off_date->diffForHumans() }}</p>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-800">
              @foreach($dayOffs as $dayOff)
                <div class="p-4">
                  <div class="flex items-start gap-3">
                    <div class="size-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center shrink-0">
                      <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">event</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                          <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $dayOff->off_date->format('M d, Y') }}</p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">{{ $dayOff->off_date->format('l') }}</p>
                        </div>
                        @if($dayOff->off_date->isToday())
                          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 shrink-0">
                            Today
                          </span>
                        @elseif($dayOff->off_date->isTomorrow())
                          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 shrink-0">
                            Tomorrow
                          </span>
                        @endif
                      </div>
                      <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $dayOff->reason }}</p>
                      @if(!$dayOff->off_date->isToday() && !$dayOff->off_date->isTomorrow())
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $dayOff->off_date->diffForHumans() }}</p>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="p-12 text-center">
              <div class="inline-flex items-center justify-center size-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <span class="material-symbols-outlined text-5xl text-gray-400">event_busy</span>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Upcoming Day-Offs</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                You don't have any scheduled day-offs coming up. Contact your administrator if you need to request time off.
              </p>
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
    </script>
  </body>
</html>
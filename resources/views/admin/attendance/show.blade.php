<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>View Attendance - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" /></noscript>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" /></noscript>
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
            fontFamily: { display: ["Inter", "sans-serif"] },
          },
        },
      };
    </script>
    <style>
      body { font-family: "Inter", sans-serif; }
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      #mobile-menu, #profile-dropdown { transition: transform 0.3s ease, opacity 0.3s ease; transform: translateY(-10px); opacity: 0; }
      #mobile-menu:not(.hidden), #profile-dropdown:not(.hidden) { transform: translateY(0); opacity: 1; }
      @media (max-width: 768px) { button, a { min-height: 44px; min-width: 44px; } }
      @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
      }
    </style>
  </head>
  <body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <div class="no-print">
      @include('home.Layouts.sidebar')
    </div>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="h-16 md:h-20 flex items-center justify-between px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10 no-print">
        <div class="flex items-center gap-3 lg:hidden">
          <button id="menu-toggle" aria-label="Toggle menu" class="text-gray-500 hover:text-gray-900 dark:hover:text-white p-2">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <span class="font-bold text-base md:text-lg">View Attendance</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">View Attendance Details</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Complete attendance record information</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      <div class="no-print">
        @include('home.Layouts.mobile')
      </div>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        
        <!-- Back Button -->
        <div class="mb-6 no-print">
          <a href="{{ route('attendance', ['tab' => 'records']) }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="text-sm font-medium">Back to Records</span>
          </a>
        </div>

        <!-- Employee Info Card -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
              @if($attendance->user->image)
                <img src="{{ asset('users/' . $attendance->user->image) }}" alt="{{ $attendance->user->name }}" class="size-16 rounded-full object-cover">
              @else
                <div class="size-16 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                  {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                </div>
              @endif
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendance->user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->user->email }}</p>
                @if($attendance->user->phone)
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->user->phone }}</p>
                @endif
              </div>
            </div>
            <div class="flex gap-2 no-print">
              <a href="{{ route('admin.attendance.edit', $attendance->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">edit</span>
                <span>Edit</span>
              </a>
              <button onclick="window.print()" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">print</span>
                <span>Print</span>
              </button>
            </div>
          </div>

          <!-- Status Badge -->
          <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
              {{ $attendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
              {{ $attendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
              {{ $attendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
              {{ $attendance->status === 'leave' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
            ">
              {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          
          <!-- Date & Time Card -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">calendar_today</span>
              </div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Date & Time</h3>
            </div>
            <div class="space-y-3">
              <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm text-gray-600 dark:text-gray-400">Date</span>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->attendance_date->format('F j, Y') }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->attendance_date->format('l') }}</p>
                </div>
              </div>
              <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Day of Week</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->attendance_date->format('l') }}</span>
              </div>
            </div>
          </div>

          <!-- Work Hours Summary Card -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="size-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">schedule</span>
              </div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Work Hours Summary</h3>
            </div>
            <div class="space-y-3">
              <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Hours</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $attendance->formatted_work_hours ?? '0h 0m' }}</span>
              </div>
              <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm text-gray-600 dark:text-gray-400">🌞 Morning Hours</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->formatted_morning_hours ?? '—' }}</span>
              </div>
              <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">🌅 Afternoon Hours</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->formatted_afternoon_hours ?? '—' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Morning Session Card -->
        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/10 dark:to-orange-900/10 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 mb-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="size-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
              <span class="material-symbols-outlined text-2xl text-yellow-600 dark:text-yellow-400">wb_sunny</span>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Morning Session</h3>
              <p class="text-xs text-gray-600 dark:text-gray-400">7:30 AM - 11:30 AM</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Check-In</span>
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">login</span>
              </div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $attendance->morning_check_in ? $attendance->morning_check_in->format('h:i A') : '—' }}
              </p>
              @if($attendance->morning_check_in)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ $attendance->morning_check_in->diffForHumans() }}
                </p>
              @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Check-Out</span>
                <span class="material-symbols-outlined text-red-600 dark:text-red-400">logout</span>
              </div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $attendance->morning_check_out ? $attendance->morning_check_out->format('h:i A') : '—' }}
              </p>
              @if($attendance->morning_check_out)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ $attendance->morning_check_out->diffForHumans() }}
                </p>
              @endif
            </div>
          </div>

          <div class="mt-4 flex items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Session Status:</span>
            @if($attendance->isMorningSessionComplete())
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                ✅ Complete
              </span>
            @else
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                ⏳ Incomplete
              </span>
            @endif
          </div>
        </div>

        <!-- Afternoon Session Card -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10 border border-orange-200 dark:border-orange-800 rounded-xl p-6 mb-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="size-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
              <span class="material-symbols-outlined text-2xl text-orange-600 dark:text-orange-400">wb_twilight</span>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Afternoon Session</h3>
              <p class="text-xs text-gray-600 dark:text-gray-400">2:00 PM - 5:30 PM</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Check-In</span>
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">login</span>
              </div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $attendance->afternoon_check_in ? $attendance->afternoon_check_in->format('h:i A') : '—' }}
              </p>
              @if($attendance->afternoon_check_in)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ $attendance->afternoon_check_in->diffForHumans() }}
                </p>
              @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Check-Out</span>
                <span class="material-symbols-outlined text-red-600 dark:text-red-400">logout</span>
              </div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $attendance->afternoon_check_out ? $attendance->afternoon_check_out->format('h:i A') : '—' }}
              </p>
              @if($attendance->afternoon_check_out)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ $attendance->afternoon_check_out->diffForHumans() }}
                </p>
              @endif
            </div>
          </div>

          <div class="mt-4 flex items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Session Status:</span>
            @if($attendance->isAfternoonSessionComplete())
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                ✅ Complete
              </span>
            @else
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                ⏳ Incomplete
              </span>
            @endif
          </div>
        </div>

        <!-- Location Info Card -->
        @if($officeLocation && $attendance->latitude && $attendance->longitude)
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="size-10 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">location_on</span>
              </div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Location Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Office</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $officeLocation->name }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Distance</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ round($officeLocation->calculateDistance($attendance->latitude, $attendance->longitude)) }}m
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Coordinates</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ $attendance->latitude }}, {{ $attendance->longitude }}
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Allowed Radius</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $officeLocation->radius }}m</p>
              </div>
            </div>
          </div>
        @endif

        <!-- Notes Card -->
        @if($attendance->note)
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="size-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">note</span>
              </div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Notes</h3>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $attendance->note }}</p>
          </div>
        @endif>

        <!-- Metadata Card -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="size-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
              <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">info</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Record Information</h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->updated_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Record ID</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">#{{ $attendance->id }}</p>
            </div>
          </div>
        </div>

      </main>
    </div>

    <script>
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
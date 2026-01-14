<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $employee->name }} - Employee Details</title>
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
    @livewireStyles
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

      .info-card {
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
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

      /* Better touch targets */
      @media (max-width: 768px) {
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
      }

      /* Gender badge animation */
      .gender-badge {
        transition: all 0.2s ease;
      }
      .gender-badge:hover {
        transform: scale(1.05);
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
          <span class="font-bold text-base md:text-lg">Employee Details</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Employee Details</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">View employee information</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('employees') }}" class="hover:text-primary transition-colors">Employees</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-gray-900 dark:text-white font-medium">{{ $employee->name }}</span>
          </div>
          <div class="flex gap-2">
            <a 
              href="{{ route('employees.edit', $employee->id) }}" 
              class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors shadow-sm"
            >
              <span class="material-symbols-outlined text-lg">edit</span>
              <span class="hidden sm:inline">Edit</span>
            </a>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Employee Profile Card -->
          <div class="lg:col-span-1">
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6 sticky top-6">
              <div class="flex flex-col items-center text-center">
                @if($employee->image)
                  <img src="{{ asset('users/' . $employee->image) }}" alt="{{ $employee->name }}" class="size-32 rounded-full object-cover shadow-xl mb-4">
                @else
                  <div class="size-32 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-4xl shadow-xl mb-4">
                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                  </div>
                @endif
                
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $employee->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $employee->email ?? 'No email provided' }}</p>
                
                <div class="flex gap-2 mb-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $employee->role_type === 'admin' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' }}">
                    {{ ucfirst($employee->role_type) }}
                  </span>
                  <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $employee->active ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
                    {{ $employee->active ? 'Active' : 'Inactive' }}
                  </span>
                </div>

                <div class="w-full pt-6 border-t border-gray-100 dark:border-gray-800 space-y-4">
                  <div class="flex items-center gap-3 text-sm">
                    <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <span class="material-symbols-outlined text-gray-400">badge</span>
                    </div>
                    <div class="text-left flex-1">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Employee ID</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">#{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-3 text-sm">
                    <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <span class="material-symbols-outlined text-gray-400">
                        {{ $employee->gender === 'male' ? 'male' : 'female' }}
                      </span>
                    </div>
                    <div class="text-left flex-1">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Gender</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($employee->gender ?? 'N/A') }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-3 text-sm">
                    <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <span class="material-symbols-outlined text-gray-400">phone</span>
                    </div>
                    <div class="text-left flex-1">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Phone Number</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->phone ?? 'Not provided' }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-3 text-sm">
                    <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <span class="material-symbols-outlined text-gray-400">calendar_today</span>
                    </div>
                    <div class="text-left flex-1">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Joined Date</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->created_at->format('M d, Y') }}</p>
                    </div>
                  </div>

                  @if($employee->telegram_id)
                    <div class="flex items-center gap-3 text-sm">
                      <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161l-1.84 8.673c-.137.645-.503.804-.997.5l-2.756-2.031-1.327 1.277c-.147.147-.27.27-.552.27l.197-2.8 5.102-4.61c.222-.197-.048-.308-.345-.11l-6.304 3.97-2.715-.848c-.59-.184-.602-.59.125-.873l10.606-4.088c.493-.178.925.11.763.872z"/>
                        </svg>
                      </div>
                      <div class="text-left flex-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Telegram</p>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Connected</p>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Details Section -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Account Information -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">account_circle</span>
                Account Information
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full Name</label>
                  <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $employee->name }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email Address</label>
                  <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $employee->email ?? 'Not provided' }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone Number</label>
                  <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $employee->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gender</label>
                  <p class="text-sm font-medium mt-1">
                    <span class="gender-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $employee->gender === 'male' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400' }}">
                      <span class="material-symbols-outlined text-base">
                        {{ $employee->gender === 'male' ? 'male' : 'female' }}
                      </span>
                      {{ ucfirst($employee->gender ?? 'N/A') }}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User Role</label>
                  <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ ucfirst($employee->role_type) }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Status</label>
                  <p class="text-sm font-medium mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $employee->active ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
                      {{ $employee->active ? 'Active' : 'Inactive' }}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member Since</label>
                  <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $employee->created_at->format('F d, Y') }}</p>
                </div>
              </div>
            </div>

            <!-- Attendance Statistics (if available) -->
            @php
              $totalAttendance = $employee->attendances()->count();
              $monthlyAttendance = $employee->attendances()->whereMonth('attendance_date', now()->month)->count();
              $onTimeCount = $employee->attendances()->where('status', 'on_time')->count();
              $lateCount = $employee->attendances()->where('status', 'late')->count();
            @endphp

            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">bar_chart</span>
                Attendance Overview
              </h3>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="info-card bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                      <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">calendar_month</span>
                    </div>
                    <div>
                      <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalAttendance }}</p>
                      <p class="text-xs text-blue-600/70 dark:text-blue-400/70">Total Days</p>
                    </div>
                  </div>
                </div>

                <div class="info-card bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-100 dark:border-green-800">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                      <span class="material-symbols-outlined text-green-600 dark:text-green-400">event_available</span>
                    </div>
                    <div>
                      <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $monthlyAttendance }}</p>
                      <p class="text-xs text-green-600/70 dark:text-green-400/70">This Month</p>
                    </div>
                  </div>
                </div>

                <div class="info-card bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4 border border-purple-100 dark:border-purple-800">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                      <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">check_circle</span>
                    </div>
                    <div>
                      <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $onTimeCount }}</p>
                      <p class="text-xs text-purple-600/70 dark:text-purple-400/70">On Time</p>
                    </div>
                  </div>
                </div>

                <div class="info-card bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 border border-orange-100 dark:border-orange-800">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-800 rounded-lg">
                      <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">schedule</span>
                    </div>
                    <div>
                      <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $lateCount }}</p>
                      <p class="text-xs text-orange-600/70 dark:text-orange-400/70">Late</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            @php
              $recentAttendance = $employee->attendances()->latest()->limit(5)->get();
            @endphp

            @if($recentAttendance->count() > 0)
              <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                  <span class="material-symbols-outlined">history</span>
                  Recent Attendance
                </h3>
                <div class="space-y-3">
                  @foreach($recentAttendance as $attendance)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <div class="flex items-center gap-3">
                        <div class="p-2 bg-white dark:bg-gray-700 rounded-lg">
                          <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">calendar_today</span>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $attendance->attendance_date->format('M d, Y') }}</p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">
                            Check-in: {{ $attendance->check_in ? $attendance->check_in->format('h:i A') : 'N/A' }}
                          </p>
                        </div>
                      </div>
                      <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium 
                        {{ $attendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
                        {{ $attendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                        {{ $attendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                        {{ $attendance->status === 'leave' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}
                      ">
                        {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                      </span>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
              <a 
                href="{{ route('employees') }}" 
                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
              >
                <span class="material-symbols-outlined">arrow_back</span>
                <span>Back to Employees</span>
              </a>
              <a 
                href="{{ route('employees.edit', $employee->id) }}" 
                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors shadow-sm"
              >
                <span class="material-symbols-outlined">edit</span>
                <span>Edit Employee</span>
              </a>
            </div>
          </div>
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
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Settings - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    
    <!-- Inter font for English -->
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
    
    <!-- Noto Sans Khmer font -->
    <link
      rel="preload"
      href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'"
    />
    <noscript>
      <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap"
      />
    </noscript>
    
    <!-- Material Symbols -->
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
      
      /* Khmer text styling */
      .khmer-text {
        font-family: "Noto Sans Khmer", sans-serif;
        line-height: 1.8;
      }
      
      /* Mixed content (English + Khmer) */
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

      .setting-card {
        transition: all 0.2s;
      }
      .setting-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
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
          <span class="font-bold text-base md:text-lg">Settings</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">System Settings</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure your attendance system</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Success/Error Messages -->
        @if(session('success'))
          <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
          </div>
        @endif

        @if(session('error'))
          <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-red-600 dark:text-red-400">error</span>
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
          </div>
        @endif

        <!-- Settings Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
          <button onclick="showTab('general')" id="tab-general" class="tab-button px-4 py-3 text-sm font-medium border-b-2 border-primary text-primary">
            General
          </button>
          <button onclick="showTab('schedule')" id="tab-schedule" class="tab-button px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-white">
            Schedule
          </button>
          <button onclick="showTab('notifications')" id="tab-notifications" class="tab-button px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-white">
            Notifications
          </button>
          <button onclick="showTab('advanced')" id="tab-advanced" class="tab-button px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-white">
            Advanced
          </button>
        </div>

        <!-- General Settings Tab -->
        <div id="content-general" class="tab-content">
          <!-- Company Information -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="size-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">business</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Company Information</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Basic information about your organization</p>
              </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
              @csrf
              @method('PUT')
              <input type="hidden" name="section" value="company">

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                  <input
                    type="text"
                    name="company_name"
                    value="{{ config('app.name') }}"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Your Company Name"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Zone</label>
                  <select
                    name="timezone"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                  >
                    <option value="Asia/Phnom_Penh">Asia/Phnom Penh (GMT+7)</option>
                    <option value="Asia/Bangkok">Asia/Bangkok (GMT+7)</option>
                    <option value="Asia/Ho_Chi_Minh">Asia/Ho Chi Minh (GMT+7)</option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Address</label>
                  <textarea
                    name="company_address"
                    rows="3"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                    placeholder="Enter your company address"
                  ></textarea>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">save</span>
                  Save Changes
                </button>
              </div>
            </form>
          </div>

          <!-- Office Location Settings -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="size-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">location_on</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Office Location</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Set default office location and radius</p>
              </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
              @csrf
              @method('PUT')
              <input type="hidden" name="section" value="location">

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Name</label>
                  <input
                    type="text"
                    name="location_name"
                    value="Main Office"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in Radius (meters)</label>
                  <input
                    type="number"
                    name="radius"
                    value="100"
                    min="10"
                    max="1000"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Latitude</label>
                  <input
                    type="text"
                    name="latitude"
                    value="10.635982"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="10.635982"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Longitude</label>
                  <input
                    type="text"
                    name="longitude"
                    value="103.515688"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="103.515688"
                  />
                </div>
              </div>

              <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex gap-3">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">info</span>
                  <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-semibold mb-1">Location Tips:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                      <li>Use Google Maps to find accurate coordinates</li>
                      <li>Set radius based on your office building size</li>
                      <li>Recommended radius: 50-200 meters</li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">save</span>
                  Save Location
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Schedule Settings Tab -->
        <div id="content-schedule" class="tab-content hidden">
          <!-- Work Schedule Configuration -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="size-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">schedule</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Default Work Schedule</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Set default morning and afternoon session times</p>
              </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
              @csrf
              @method('PUT')
              <input type="hidden" name="section" value="schedule">

              <!-- Morning Session -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/10 dark:to-orange-900/10">
                <div class="flex items-center gap-2 mb-4">
                  <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">wb_sunny</span>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Morning Session</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-In Time</label>
                    <input
                      type="time"
                      name="morning_check_in"
                      value="07:30"
                      class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-Out Time</label>
                    <input
                      type="time"
                      name="morning_check_out"
                      value="11:30"
                      class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                </div>
              </div>

              <!-- Afternoon Session -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10">
                <div class="flex items-center gap-2 mb-4">
                  <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">wb_twilight</span>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Afternoon Session</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-In Time</label>
                    <input
                      type="time"
                      name="afternoon_check_in"
                      value="14:00"
                      class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-Out Time</label>
                    <input
                      type="time"
                      name="afternoon_check_out"
                      value="17:30"
                      class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                </div>
              </div>

              <!-- Late Tolerance -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Late Tolerance (minutes)</label>
                <input
                  type="number"
                  name="late_allowed_min"
                  value="10"
                  min="0"
                  max="60"
                  class="w-full md:w-64 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Grace period before marking as late</p>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">save</span>
                  Save Schedule
                </button>
              </div>
            </form>
          </div>

          <!-- Working Days -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-3">
                <div class="size-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                  <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">calendar_month</span>
                </div>
                <div>
                  <h2 class="text-lg font-bold text-gray-900 dark:text-white">Working Days</h2>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Configure working days and their schedules</p>
                </div>
              </div>
              <button
                onclick="selectPreset('weekdays')"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors"
              >
                Mon-Fri Preset
              </button>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
              @csrf
              @method('PUT')
              <input type="hidden" name="section" value="working_days">

              <!-- Day Selection with Individual Schedules -->
              <div class="space-y-3">
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $index => $day)
                  @php
                    $dayLower = strtolower($day);
                    $isWeekend = in_array($day, ['Saturday', 'Sunday']);
                    
                    // Get saved configuration for this day
                    $savedConfig = isset($workingDaysConfig[$dayLower]) ? $workingDaysConfig[$dayLower] : null;
                    $isWorkingDay = $savedConfig ? $savedConfig['is_working'] : !$isWeekend;
                    $morningStart = $savedConfig['morning_start'] ?? '07:30';
                    $morningEnd = $savedConfig['morning_end'] ?? '11:30';
                    $afternoonStart = $savedConfig['afternoon_start'] ?? '14:00';
                    $afternoonEnd = $savedConfig['afternoon_end'] ?? '17:30';
                  @endphp
                  
                  <div class="border-2 rounded-xl transition-all {{ $isWeekend ? 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900' }}" id="day-{{ $dayLower }}">
                    <!-- Day Header -->
                    <div class="p-4 flex items-center gap-4">
                      <label class="flex items-center gap-3 cursor-pointer flex-1">
                        <input
                          type="checkbox"
                          name="working_days[]"
                          value="{{ $dayLower }}"
                          {{ $isWorkingDay ? 'checked' : '' }}
                          class="size-5 text-primary border-gray-300 rounded focus:ring-primary day-checkbox"
                          onchange="toggleDaySchedule('{{ $dayLower }}')"
                        />
                        <div class="flex items-center gap-3">
                          <div class="size-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($day, 0, 3) }}
                          </div>
                          <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $day }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 day-status-{{ $dayLower }}">
                              {{ $isWorkingDay ? 'Working Day' : 'Day Off' }}
                            </p>
                          </div>
                        </div>
                      </label>

                      <button
                        type="button"
                        onclick="toggleScheduleDetails('{{ $dayLower }}')"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors schedule-toggle-{{ $dayLower }} {{ !$isWorkingDay ? 'hidden' : '' }}"
                      >
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">expand_more</span>
                      </button>
                    </div>

                    <!-- Day Schedule Details (Collapsible) -->
                    <div class="schedule-details-{{ $dayLower }} {{ !$isWorkingDay ? 'hidden' : '' }} border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Morning Session -->
                        <div>
                          <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400 text-sm">wb_sunny</span>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Morning Session</span>
                          </div>
                          <div class="grid grid-cols-2 gap-2">
                            <div>
                              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Start</label>
                              <input
                                type="time"
                                name="{{ $dayLower }}_morning_start"
                                value="{{ $morningStart }}"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              />
                            </div>
                            <div>
                              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">End</label>
                              <input
                                type="time"
                                name="{{ $dayLower }}_morning_end"
                                value="{{ $morningEnd }}"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              />
                            </div>
                          </div>
                        </div>

                        <!-- Afternoon Session -->
                        <div>
                          <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-sm">wb_twilight</span>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Afternoon Session</span>
                          </div>
                          <div class="grid grid-cols-2 gap-2">
                            <div>
                              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Start</label>
                              <input
                                type="time"
                                name="{{ $dayLower }}_afternoon_start"
                                value="{{ $afternoonStart }}"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              />
                            </div>
                            <div>
                              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">End</label>
                              <input
                                type="time"
                                name="{{ $dayLower }}_afternoon_end"
                                value="{{ $afternoonEnd }}"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              <!-- Quick Presets -->
              <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-start gap-3">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">info</span>
                  <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Quick Presets:</p>
                    <div class="flex flex-wrap gap-2">
                      <button
                        type="button"
                        onclick="selectPreset('weekdays')"
                        class="px-3 py-1.5 bg-white dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium transition-colors"
                      >
                        Mon-Fri
                      </button>
                      <button
                        type="button"
                        onclick="selectPreset('all')"
                        class="px-3 py-1.5 bg-white dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium transition-colors"
                      >
                        All Days
                      </button>
                      <button
                        type="button"
                        onclick="selectPreset('custom')"
                        class="px-3 py-1.5 bg-white dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium transition-colors"
                      >
                        Mon-Sat
                      </button>
                      <button
                        type="button"
                        onclick="selectPreset('none')"
                        class="px-3 py-1.5 bg-white dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium transition-colors"
                      >
                        Clear All
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">save</span>
                  Save Working Days
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Notifications Settings Tab -->
        <div id="content-notifications" class="tab-content hidden">
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="size-12 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600 dark:text-red-400">notifications</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Telegram Notifications</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Configure Telegram bot settings</p>
              </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
              @csrf
              @method('PUT')
              <input type="hidden" name="section" value="notifications">

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telegram Bot Token</label>
                <input
                  type="text"
                  name="telegram_bot_token"
                  value="{{ env('TELEGRAM_BOT_TOKEN', '') }}"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent font-mono text-sm"
                  placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz"
                />
              </div>

              <div class="space-y-3">
                <label class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Check-in Notifications</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Notify when employees check in</p>
                  </div>
                  <input type="checkbox" name="notify_checkin" checked class="size-5 text-primary rounded focus:ring-primary" />
                </label>

                <label class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Check-out Notifications</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Notify when employees check out</p>
                  </div>
                  <input type="checkbox" name="notify_checkout" checked class="size-5 text-primary rounded focus:ring-primary" />
                </label>

                <label class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Late Arrival Alerts</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Alert when employees arrive late</p>
                  </div>
                  <input type="checkbox" name="notify_late" checked class="size-5 text-primary rounded focus:ring-primary" />
                </label>

                <label class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Location Alerts</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Alert when check-in is outside allowed radius</p>
                  </div>
                  <input type="checkbox" name="notify_location" checked class="size-5 text-primary rounded focus:ring-primary" />
                </label>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">save</span>
                  Save Settings
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div id="content-advanced" class="tab-content hidden">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- System Maintenance -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                  <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">settings</span>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">System Maintenance</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Manage system data</p>
                </div>
              </div>

              <div class="space-y-3">
                <form action="{{ route('settings.export') }}" method="POST">
                  @csrf
                  <button type="submit" class="w-full p-3 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-xl text-left transition-colors">
                    <div class="flex items-center gap-3">
                      <span class="material-symbols-outlined text-blue-600">download</span>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Export Data</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Download all attendance records as CSV</p>
                      </div>
                    </div>
                  </button>
                </form>

                <form action="{{ route('settings.clear-cache') }}" method="POST">
                  @csrf
                  <button type="submit" class="w-full p-3 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-xl text-left transition-colors">
                    <div class="flex items-center gap-3">
                      <span class="material-symbols-outlined text-orange-600">refresh</span>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Clear Cache</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Clear system cache and optimize</p>
                      </div>
                    </div>
                  </button>
                </form>
              </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-xl p-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                  <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                </div>
                <div>
                  <h3 class="text-base font-bold text-red-900 dark:text-red-100">Danger Zone</h3>
                  <p class="text-xs text-red-700 dark:text-red-300">Irreversible actions</p>
                </div>
              </div>

              <div class="space-y-3">
                <form action="{{ route('settings.reset-data') }}" method="POST" onsubmit="return confirmReset()">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="w-full p-3 bg-white dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-xl text-left transition-colors">
                    <div class="flex items-center gap-3">
                      <span class="material-symbols-outlined text-red-600">delete_forever</span>
                      <div>
                        <p class="text-sm font-medium text-red-900 dark:text-red-100">Reset All Data</p>
                        <p class="text-xs text-red-700 dark:text-red-300">Delete all attendance records permanently</p>
                      </div>
                    </div>
                  </button>
                </form>
              </div>
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

      // Tab Navigation
      function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
          content.classList.add('hidden');
        });

        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
          button.classList.remove('border-primary', 'text-primary');
          button.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab content
        document.getElementById(`content-${tabName}`).classList.remove('hidden');

        // Add active state to selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.add('border-primary', 'text-primary');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
      }

      // Confirm reset data action
      function confirmReset() {
        const confirmation = prompt('This will permanently delete ALL attendance records. Type "DELETE" to confirm:');
        if (confirmation === 'DELETE') {
          return confirm('Are you absolutely sure? This action cannot be undone!');
        }
        return false;
      }

      // Working Days Functions
      function toggleDaySchedule(day) {
        const checkbox = document.querySelector(`input[value="${day}"]`);
        const scheduleToggle = document.querySelector(`.schedule-toggle-${day}`);
        const scheduleDetails = document.querySelector(`.schedule-details-${day}`);
        const statusElement = document.querySelector(`.day-status-${day}`);
        
        if (checkbox.checked) {
          scheduleToggle.classList.remove('hidden');
          scheduleDetails.classList.remove('hidden');
          statusElement.textContent = 'Working Day';
        } else {
          scheduleToggle.classList.add('hidden');
          scheduleDetails.classList.add('hidden');
          statusElement.textContent = 'Day Off';
        }
      }

      function toggleScheduleDetails(day) {
        const details = document.querySelector(`.schedule-details-${day}`);
        const button = document.querySelector(`.schedule-toggle-${day} .material-symbols-outlined`);
        
        details.classList.toggle('hidden');
        button.textContent = details.classList.contains('hidden') ? 'expand_more' : 'expand_less';
      }

      function selectPreset(preset) {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        const customDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        days.forEach(day => {
          const checkbox = document.querySelector(`input[value="${day}"]`);
          
          switch(preset) {
            case 'weekdays':
              checkbox.checked = weekdays.includes(day);
              break;
            case 'all':
              checkbox.checked = true;
              break;
            case 'custom':
              checkbox.checked = customDays.includes(day);
              break;
            case 'none':
              checkbox.checked = false;
              break;
          }
          
          toggleDaySchedule(day);
        });
      }

      // Initialize on page load
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize all day schedules based on checkbox state
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        days.forEach(day => {
          toggleDaySchedule(day);
        });
      });
    </script>
  </body>
</html>
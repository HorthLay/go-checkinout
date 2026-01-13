<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Edit Attendance - Attendify</title>
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
    </style>
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
          <span class="font-bold text-base md:text-lg">Edit Attendance</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Attendance Record</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Update attendance information</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        
        <!-- Back Button -->
        <div class="mb-6">
          <a href="{{ route('admin.attendance.show', $attendance->id) }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="text-sm font-medium">Back to View</span>
          </a>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
          <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-red-600 dark:text-red-400">error</span>
              <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Please correct the following errors:</h3>
                <ul class="list-disc list-inside space-y-1">
                  @foreach($errors->all() as $error)
                    <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}" class="space-y-6">
          @csrf
          @method('PUT')

          <!-- Employee Info Card (Read-only) -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center gap-4">
              @if($attendance->user->image)
                <img src="{{ asset('users/' . $attendance->user->image) }}" alt="{{ $attendance->user->name }}" class="size-16 rounded-full object-cover">
              @else
                <div class="size-16 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                  {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                </div>
              @endif
              <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $attendance->user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->user->email }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ $attendance->attendance_date->format('l, F j, Y') }}
                </p>
              </div>
            </div>
          </div>

          <!-- Morning Session Card -->
          <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/10 dark:to-orange-900/10 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="size-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-yellow-600 dark:text-yellow-400">wb_sunny</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Morning Session</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">7:30 AM - 11:30 AM</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Morning Check-In -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg">login</span>
                    Morning Check-In
                  </span>
                </label>
                <input 
                  type="time" 
                  name="morning_check_in" 
                  value="{{ $attendance->morning_check_in ? $attendance->morning_check_in->format('H:i') : '' }}"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty if not checked in</p>
              </div>

              <!-- Morning Check-Out -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-lg">logout</span>
                    Morning Check-Out
                  </span>
                </label>
                <input 
                  type="time" 
                  name="morning_check_out" 
                  value="{{ $attendance->morning_check_out ? $attendance->morning_check_out->format('H:i') : '' }}"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty if not checked out</p>
              </div>
            </div>
          </div>

          <!-- Afternoon Session Card -->
          <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10 border border-orange-200 dark:border-orange-800 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="size-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-orange-600 dark:text-orange-400">wb_twilight</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Afternoon Session</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">2:00 PM - 5:30 PM</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Afternoon Check-In -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg">login</span>
                    Afternoon Check-In
                  </span>
                </label>
                <input 
                  type="time" 
                  name="afternoon_check_in" 
                  value="{{ $attendance->afternoon_check_in ? $attendance->afternoon_check_in->format('H:i') : '' }}"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty if not checked in</p>
              </div>

              <!-- Afternoon Check-Out -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-lg">logout</span>
                    Afternoon Check-Out
                  </span>
                </label>
                <input 
                  type="time" 
                  name="afternoon_check_out" 
                  value="{{ $attendance->afternoon_check_out ? $attendance->afternoon_check_out->format('H:i') : '' }}"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty if not checked out</p>
              </div>
            </div>
          </div>

          <!-- Status & Notes Card -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Additional Information</h3>
            
            <div class="space-y-4">
              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">info</span>
                    Attendance Status
                  </span>
                </label>
                <select 
                  name="status" 
                  required
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
                  <option value="on_time" {{ $attendance->status === 'on_time' ? 'selected' : '' }}>On Time</option>
                  <option value="late" {{ $attendance->status === 'late' ? 'selected' : '' }}>Late</option>
                  <option value="absent" {{ $attendance->status === 'absent' ? 'selected' : '' }}>Absent</option>
                  <option value="leave" {{ $attendance->status === 'leave' ? 'selected' : '' }}>Leave</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select the attendance status for this record</p>
              </div>

              <!-- Notes -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">note</span>
                    Notes (Optional)
                  </span>
                </label>
                <textarea 
                  name="note" 
                  rows="4"
                  maxlength="500"
                  placeholder="Add any additional notes or comments about this attendance record..."
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                >{{ old('note', $attendance->note) }}</textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 500 characters</p>
              </div>
            </div>
          </div>

          <!-- Current Values Info Box -->
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">info</span>
              <div class="flex-1">
                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Important Information</h4>
                <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1">
                  <li>• Work hours will be automatically recalculated based on your changes</li>
                  <li>• Leave time fields empty if the employee hasn't checked in/out for that session</li>
                  <li>• Changes will be saved with the current date ({{ $attendance->attendance_date->format('Y-m-d') }})</li>
                  <li>• All times should be in 24-hour format (HH:MM)</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.attendance.show', $attendance->id) }}" class="flex-1 px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-center">
              Cancel
            </a>
            <button type="submit" class="flex-1 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
              <span class="material-symbols-outlined">save</span>
              <span>Save Changes</span>
            </button>
          </div>
        </form>

        <!-- Delete Section -->
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
          <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
            <div class="flex items-start gap-4">
              <div class="size-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl text-red-600 dark:text-red-400">warning</span>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-red-900 dark:text-red-100 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-800 dark:text-red-200 mb-4">
                  Deleting this attendance record is permanent and cannot be undone. All associated data will be permanently removed.
                </p>
                <form method="POST" action="{{ route('admin.attendance.delete', $attendance->id) }}" onsubmit="return confirm('Are you absolutely sure you want to delete this attendance record? This action cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">delete</span>
                    <span>Delete Attendance Record</span>
                  </button>
                </form>
              </div>
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

      // Character count for notes
      const noteTextarea = document.querySelector('textarea[name="note"]');
      if (noteTextarea) {
        const maxLength = noteTextarea.getAttribute('maxlength');
        const counterDiv = document.createElement('div');
        counterDiv.className = 'text-xs text-gray-500 dark:text-gray-400 text-right mt-1';
        noteTextarea.parentElement.appendChild(counterDiv);
        
        function updateCounter() {
          const current = noteTextarea.value.length;
          counterDiv.textContent = `${current}/${maxLength} characters`;
        }
        
        noteTextarea.addEventListener('input', updateCounter);
        updateCounter();
      }
    </script>
  </body>
</html>
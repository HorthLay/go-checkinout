<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Notifications - Attendify</title>
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
          <span class="font-bold text-base md:text-lg">Notifications</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage your notifications</p>
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
            <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
          </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total</span>
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">notifications</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">All notifications</p>
          </div>

          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Unread</span>
              <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">mark_email_unread</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['unread'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Needs attention</p>
          </div>

          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Today</span>
              <span class="material-symbols-outlined text-green-600 dark:text-green-400">today</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['today'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Received today</p>
          </div>
        </div>

        <!-- Filters & Actions -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4 md:p-6 mb-6">
          <form method="GET" action="{{ route('admin.notifications') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- Search -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <span class="material-symbols-outlined text-lg">search</span>
                  </span>
                  <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search notifications..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                </div>
              </div>

              <!-- Type Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select
                  name="type"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                  <option value="">All Types</option>
                  <option value="checkin" {{ request('type') === 'checkin' ? 'selected' : '' }}>Check-In</option>
                  <option value="checkout" {{ request('type') === 'checkout' ? 'selected' : '' }}>Check-Out</option>
                  <option value="late" {{ request('type') === 'late' ? 'selected' : '' }}>Late</option>
                  <option value="alert" {{ request('type') === 'alert' ? 'selected' : '' }}>Alert</option>
                  <option value="absent" {{ request('type') === 'absent' ? 'selected' : '' }}>Absent</option>
                  <option value="leave" {{ request('type') === 'leave' ? 'selected' : '' }}>Leave</option>
                </select>
              </div>

              <!-- Status Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select
                  name="status"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                  <option value="">All Status</option>
                  <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                  <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Date From -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                <input
                  type="date"
                  name="date_from"
                  value="{{ request('date_from') }}"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                />
              </div>

              <!-- Date To -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                <input
                  type="date"
                  name="date_to"
                  value="{{ request('date_to') }}"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                />
              </div>

              <!-- Filter Buttons -->
              <div class="flex items-end gap-2">
                <button
                  type="submit"
                  class="flex-1 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors flex items-center justify-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">filter_alt</span>
                  <span>Apply</span>
                </button>
                
                 <a href="{{ route('admin.notifications') }}"
                  class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors"
                >
                  Clear
                </a>
              </div>
            </div>
          </form>

          <!-- Bulk Actions -->
          @if($stats['unread'] > 0 || $notifications->where('is_read', true)->count() > 0)
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
              @if($stats['unread'] > 0)
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                  @csrf
                  <button
                    type="submit"
                    class="px-4 py-2 bg-blue-100 dark:bg-blue-900/20 hover:bg-blue-200 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-xl text-sm font-medium transition-colors flex items-center gap-2"
                  >
                    <span class="material-symbols-outlined text-lg">done_all</span>
                    <span>Mark All as Read</span>
                  </button>
                </form>
              @endif

              @if($notifications->where('is_read', true)->count() > 0)
                <form 
                  method="POST" 
                  action="{{ route('admin.notifications.delete-all-read') }}"
                  onsubmit="return confirm('Are you sure you want to delete all read notifications?')"
                >
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="px-4 py-2 bg-red-100 dark:bg-red-900/20 hover:bg-red-200 dark:hover:bg-red-900/30 text-red-700 dark:text-red-300 rounded-xl text-sm font-medium transition-colors flex items-center gap-2"
                  >
                    <span class="material-symbols-outlined text-lg">delete_sweep</span>
                    <span>Delete All Read</span>
                  </button>
                </form>
              @endif
            </div>
          @endif
        </div>

        <!-- Notifications List -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Notifications</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notifications->total() }} total notifications</p>
          </div>

          <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($notifications as $notification)
              <div class="p-4 md:p-6 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex items-start gap-4">
                  <!-- Icon -->
                  <div class="flex-shrink-0 size-12 rounded-full bg-{{ $notification->color }}-100 dark:bg-{{ $notification->color }}-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-{{ $notification->color }}-600 dark:text-{{ $notification->color }}-400 text-xl">
                      {{ $notification->icon }}
                    </span>
                  </div>

                  <!-- Content -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4 mb-2">
                      <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white">
                          {{ $notification->title }}
                          @if(!$notification->is_read)
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                              New
                            </span>
                          @endif
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                          {{ $notification->message }}
                        </p>
                      </div>

                      <!-- Actions -->
                      <div class="flex items-center gap-1">
                        @if(!$notification->is_read)
                          <form method="POST" action="{{ route('admin.notifications.mark-read', $notification->id) }}">
                            @csrf
                            <button
                              type="submit"
                              class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700"
                              title="Mark as read"
                            >
                              <span class="material-symbols-outlined text-lg text-gray-500">done</span>
                            </button>
                          </form>
                        @endif
                        <form 
                          method="POST" 
                          action="{{ route('admin.notifications.delete', $notification->id) }}"
                          onsubmit="return confirm('Are you sure you want to delete this notification?')"
                        >
                          @csrf
                          @method('DELETE')
                          <button
                            type="submit"
                            class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/20"
                            title="Delete"
                          >
                            <span class="material-symbols-outlined text-lg text-red-600">delete</span>
                          </button>
                        </form>
                      </div>
                    </div>

                    <!-- Metadata -->
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                      <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        {{ $notification->created_at->format('M d, Y - h:i A') }}
                      </span>
                      <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">access_time</span>
                        {{ $notification->created_at->diffForHumans() }}
                      </span>
                      @if(isset($notification->data['employee_name']))
                        <span class="flex items-center gap-1">
                          <span class="material-symbols-outlined text-sm">person</span>
                          {{ $notification->data['employee_name'] }}
                        </span>
                      @endif
                      @if(isset($notification->data['location']))
                        <span class="flex items-center gap-1">
                          <span class="material-symbols-outlined text-sm">location_on</span>
                          {{ $notification->data['location'] }}
                        </span>
                      @endif
                    </div>

                    <!-- Additional Data -->
                    @if($notification->data)
                      <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                          @if(isset($notification->data['time']))
                            <div>
                              <span class="text-gray-500 dark:text-gray-400">Time</span>
                              <p class="font-semibold text-gray-900 dark:text-white">{{ $notification->data['time'] }}</p>
                            </div>
                          @endif
                          @if(isset($notification->data['work_hours']))
                            <div>
                              <span class="text-gray-500 dark:text-gray-400">Work Hours</span>
                              <p class="font-semibold text-gray-900 dark:text-white">{{ $notification->data['work_hours'] }}</p>
                            </div>
                          @endif
                          @if(isset($notification->data['status']))
                            <div>
                              <span class="text-gray-500 dark:text-gray-400">Status</span>
                              <p class="font-semibold text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $notification->data['status']) }}</p>
                            </div>
                          @endif
                          @if(isset($notification->data['actual_distance']))
                            <div>
                              <span class="text-gray-500 dark:text-gray-400">Distance</span>
                              <p class="font-semibold text-red-600 dark:text-red-400">{{ $notification->data['actual_distance'] }}m</p>
                            </div>
                          @endif
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @empty
              <div class="p-12 text-center">
                <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">notifications_off</span>
                <p class="text-gray-500 dark:text-gray-400 mt-3">No notifications found</p>
                @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                  <a href="{{ route('admin.notifications') }}" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium">
                    Clear Filters
                  </a>
                @endif
              </div>
            @endforelse
          </div>

          <!-- Pagination -->
          @if($notifications->hasPages())
            <div class="p-4 md:p-6 border-t border-gray-100 dark:border-gray-800">
              {{ $notifications->links() }}
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
    </script>
  </body>
</html>
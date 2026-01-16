<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Manage Locations - Attendify</title>
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
          <span class="font-bold text-base md:text-lg">Office Locations</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Office Locations</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage office locations and check-in zones</p>
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

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Office Locations</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $locations->total() }} total locations</p>
          </div>
          
           <a href="{{ route('map.create') }}"
            class="flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-semibold transition-colors shadow-lg"
          >
            <span class="material-symbols-outlined">add_location</span>
            <span>Add New Location</span>
          </a>
        </div>

        <!-- Locations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @forelse($locations as $location)
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-md hover:shadow-xl transition-shadow">
              <!-- Location Header -->
              <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-primary/5 to-blue-600/5">
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $location->name }}</h3>
                    @if($location->address)
                      <p class="text-sm text-gray-600 dark:text-gray-400">{{ $location->address }}</p>
                    @endif
                  </div>
                  <div class="flex items-center gap-1">
                    @if($location->is_active)
                      <span class="px-2 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg text-xs font-medium">Active</span>
                    @else
                      <span class="px-2 py-1 bg-gray-50 dark:bg-gray-900/20 text-gray-600 dark:text-gray-400 rounded-lg text-xs font-medium">Inactive</span>
                    @endif
                  </div>
                </div>
              </div>

              <!-- Location Details -->
              <div class="p-6">
                <div class="space-y-3 mb-4">
                  <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-gray-400 text-lg">location_on</span>
                    <div class="flex-1">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Coordinates</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $location->latitude }}, {{ $location->longitude }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-gray-400 text-lg">straighten</span>
                    <div class="flex-1">
                      <p class="text-xs text-gray-500 dark:text-gray-400">Check-in Radius</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $location->radius }} meters</p>
                    </div>
                  </div>

                  @if($location->description)
                    <div class="flex items-start gap-3">
                      <span class="material-symbols-outlined text-gray-400 text-lg">description</span>
                      <div class="flex-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Description</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($location->description, 60) }}</p>
                      </div>
                    </div>
                  @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                  
                   <a href="{{ route('map.edit', $location->id) }}"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                  >
                    <span class="material-symbols-outlined text-lg">edit</span>
                    <span class="text-sm font-medium">Edit</span>
                  </a>

                  <form action="{{ route('map.toggle', $location->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button
                      type="submit"
                      class="w-full flex items-center justify-center gap-2 px-4 py-2 {{ $location->is_active ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/30' : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30' }} rounded-lg transition-colors"
                    >
                      <span class="material-symbols-outlined text-lg">{{ $location->is_active ? 'visibility_off' : 'visibility' }}</span>
                      <span class="text-sm font-medium">{{ $location->is_active ? 'Disable' : 'Enable' }}</span>
                    </button>
                  </form>

                  <button
                    onclick="confirmDelete({{ $location->id }})"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                  >
                    <span class="material-symbols-outlined text-lg">delete</span>
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="col-span-full">
              <div class="text-center py-16 bg-surface-light dark:bg-surface-dark rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-700 mb-4">location_off</span>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No locations yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first office location</p>
                
                <a  href="{{ route('map.create') }}"
                  class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-semibold transition-colors"
                >
                  <span class="material-symbols-outlined">add_location</span>
                  <span>Add Location</span>
                </a>
              </div>
            </div>
          @endforelse
        </div>

        <!-- Pagination -->
        @if($locations->hasPages())
          <div class="mt-8">
            {{ $locations->links() }}
          </div>
        @endif
      </main>
    </div>

    <!-- Delete Confirmation Form -->
    <form id="delete-form" method="POST" style="display: none;">
      @csrf
      @method('DELETE')
    </form>

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

      function confirmDelete(locationId) {
        if (confirm('Are you sure you want to delete this location? This action cannot be undone.')) {
          const form = document.getElementById('delete-form');
          form.action = `/admin/map/${locationId}`;
          form.submit();
        }
      }
    </script>
  </body>
</html>
@php
    use App\Models\Mission;
    use Illuminate\Support\Facades\Auth;
    
    $missions = Mission::with('attendance')
        ->where('user_id', Auth::id())
        ->orderBy('mission_date', 'desc')
        ->paginate(15);
    
    $totalMissions = $missions->total();
    $pendingCount = Mission::where('user_id', Auth::id())->where('status', 'pending')->count();
    $approvedCount = Mission::where('user_id', Auth::id())->where('status', 'approved')->count();
@endphp

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Missions - Attendify</title>
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

      /* Pulse animation for pending badges */
      @keyframes pulse-ring {
        0% {
          box-shadow: 0 0 0 0 rgba(250, 204, 21, 0.4);
        }
        70% {
          box-shadow: 0 0 0 6px rgba(250, 204, 21, 0);
        }
        100% {
          box-shadow: 0 0 0 0 rgba(250, 204, 21, 0);
        }
      }

      .animate-pulse-ring {
        animation: pulse-ring 2s ease-out infinite;
      }

      /* Fade in animation for cards */
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

      .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
      }

      /* Staggered animation for cards */
      .mission-row {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
      }

      .mission-row:nth-child(1) { animation-delay: 0.05s; }
      .mission-row:nth-child(2) { animation-delay: 0.1s; }
      .mission-row:nth-child(3) { animation-delay: 0.15s; }
      .mission-row:nth-child(4) { animation-delay: 0.2s; }
      .mission-row:nth-child(5) { animation-delay: 0.25s; }
      .mission-row:nth-child(6) { animation-delay: 0.3s; }
      .mission-row:nth-child(7) { animation-delay: 0.35s; }
      .mission-row:nth-child(8) { animation-delay: 0.4s; }
      .mission-row:nth-child(9) { animation-delay: 0.45s; }
      .mission-row:nth-child(10) { animation-delay: 0.5s; }

      /* Smooth transitions */
      .mission-row {
        transition: all 0.3s ease;
      }

      /* Mobile optimization */
      @media (max-width: 1024px) {
        main {
          padding-bottom: calc(6rem + env(safe-area-inset-bottom)) !important;
        }
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
          <span class="font-bold text-base md:text-lg">My Missions</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Mission History</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Track your field work attendance</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-start gap-3">
          <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-xl shrink-0">
            check_circle
          </span>
          <div>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
          </div>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex items-start gap-3">
          <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-xl shrink-0">
            error
          </span>
          <div>
            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
          </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <!-- Total Missions -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-6 border border-gray-100 dark:border-gray-800 hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300 animate-fade-in-up">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 font-medium">Total Missions</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalMissions }}</p>
              </div>
              <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl text-blue-600 dark:text-blue-400">
                  assignment
                </span>
              </div>
            </div>
          </div>

          <!-- Pending -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-6 border border-gray-100 dark:border-gray-800 hover:shadow-lg hover:border-yellow-200 dark:hover:border-yellow-800 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 font-medium">Pending</p>
                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</p>
              </div>
              <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center animate-pulse-ring">
                <span class="material-symbols-outlined text-3xl text-yellow-600 dark:text-yellow-400">
                  schedule
                </span>
              </div>
            </div>
          </div>

          <!-- Approved -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-6 border border-gray-100 dark:border-gray-800 hover:shadow-lg hover:border-green-200 dark:hover:border-green-800 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 font-medium">Approved</p>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $approvedCount }}</p>
              </div>
              <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl text-green-600 dark:text-green-400">
                  check_circle
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm animate-fade-in-up" style="animation-delay: 0.3s;">
          <div class="flex flex-col gap-4">
            <!-- Mobile: Stack vertically -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Filter by Date</label>
                <input type="date" id="filterDate" 
                       class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200">
              </div>
              
              <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Filter by Status</label>
                <select id="filterStatus" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200">
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>

            <div class="flex justify-end">
              <button onclick="clearFilters()" 
                      class="px-5 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl transition-all duration-200 flex items-center gap-2 font-semibold active:scale-95 shadow-sm">
                <span class="material-symbols-outlined text-xl">
                  clear
                </span>
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Desktop Table -->
        <div id="recordsTableContainer" class="hidden lg:block bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Mission Date
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Check-in Time
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Location
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Work Hours
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($missions as $mission)
                <tr class="mission-row hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150" 
                    data-date="{{ $mission->mission_date->format('Y-m-d') }}"
                    data-status="{{ $mission->status }}">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-gray-400 text-xl">
                        calendar_today
                      </span>
                      <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $mission->mission_date->format('M d, Y') }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-gray-400 text-xl">
                        schedule
                      </span>
                      <span class="text-sm text-gray-600 dark:text-gray-300">
                        {{ $mission->created_at->format('h:i A') }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <button onclick="openMap({{ $mission->latitude }}, {{ $mission->longitude }})" 
                            class="flex items-center gap-2 text-sm text-primary hover:text-primary-dark transition-colors">
                      <span class="material-symbols-outlined text-xl">
                        location_on
                      </span>
                      <span>View Map</span>
                    </button>
                  </td>
                  <td class="px-6 py-4">
                    @if($mission->attendance && $mission->isApproved())
                      <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $mission->attendance->formatted_work_hours }}
                      </span>
                    @else
                      <span class="text-sm text-gray-400">—</span>
                    @endif
                  </td>
                  <td class="px-6 py-4">
                    @if($mission->status === 'pending')
                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                        Pending
                      </span>
                    @elseif($mission->status === 'approved')
                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Approved
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                        <span class="material-symbols-outlined text-sm">cancel</span>
                        Rejected
                      </span>
                    @endif
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button onclick="viewDetails({{ $mission->id }})" 
                         class="p-2 text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-xl">
                          visibility
                        </span>
                      </button>
                      @if($mission->status === 'pending')
                      <button onclick="cancelMission({{ $mission->id }})" 
                              class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-xl">
                          delete
                        </span>
                      </button>
                      @endif
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                      <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">
                        assignment
                      </span>
                      <p class="text-gray-500 dark:text-gray-400">No missions found</p>
                    </div>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile Cards -->
        <div id="mobileRecordsContainer" class="lg:hidden space-y-4">
          @forelse($missions as $mission)
          <div class="mission-row bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-md transition-all duration-300" 
               data-date="{{ $mission->mission_date->format('Y-m-d') }}"
               data-status="{{ $mission->status }}">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center gap-2.5">
                <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                  <span class="material-symbols-outlined text-gray-600 dark:text-gray-400 text-xl">
                    calendar_today
                  </span>
                </div>
                <div>
                  <span class="font-bold text-gray-900 dark:text-white text-base block">
                    {{ $mission->mission_date->format('M d, Y') }}
                  </span>
                  <span class="text-xs text-gray-500 dark:text-gray-400">
                    Mission Date
                  </span>
                </div>
              </div>
              @if($mission->status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 shadow-sm">
                  <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                  Pending
                </span>
              @elseif($mission->status === 'approved')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 shadow-sm">
                  <span class="material-symbols-outlined text-sm">check_circle</span>
                  Approved
                </span>
              @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 shadow-sm">
                  <span class="material-symbols-outlined text-sm">cancel</span>
                  Rejected
                </span>
              @endif
            </div>

            <!-- Details -->
            <div class="space-y-3 mb-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl p-4">
              <div class="flex items-center gap-3 text-sm">
                <div class="p-1.5 bg-white dark:bg-gray-800 rounded-lg">
                  <span class="material-symbols-outlined text-gray-500 dark:text-gray-400 text-lg">
                    schedule
                  </span>
                </div>
                <div>
                  <span class="text-xs text-gray-500 dark:text-gray-400 block">Check-in Time</span>
                  <span class="text-gray-900 dark:text-white font-medium">
                    {{ $mission->created_at->format('h:i A') }}
                  </span>
                </div>
              </div>
              
              <div class="flex items-center gap-3 text-sm">
                <div class="p-1.5 bg-white dark:bg-gray-800 rounded-lg">
                  <span class="material-symbols-outlined text-gray-500 dark:text-gray-400 text-lg">
                    timer
                  </span>
                </div>
                <div>
                  <span class="text-xs text-gray-500 dark:text-gray-400 block">Work Hours</span>
                  @if($mission->attendance && $mission->isApproved())
                    <span class="font-bold text-gray-900 dark:text-white">{{ $mission->attendance->formatted_work_hours }}</span>
                  @else
                    <span class="text-gray-400">Pending Approval</span>
                  @endif
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
              <button onclick="openMap({{ $mission->latitude }}, {{ $mission->longitude }})" 
                      class="flex-1 px-4 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 active:scale-95 shadow-sm hover:shadow-md">
                <span class="material-symbols-outlined text-lg">
                  location_on
                </span>
                Location
              </button>
              
              <button onclick="viewDetails({{ $mission->id }})" 
                      class="p-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl transition-all duration-200 active:scale-95 shadow-sm">
                <span class="material-symbols-outlined text-xl">
                  visibility
                </span>
              </button>
              
              @if($mission->status === 'pending')
              <button onclick="cancelMission({{ $mission->id }})" 
                      class="p-3 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl transition-all duration-200 active:scale-95 shadow-sm">
                <span class="material-symbols-outlined text-xl">
                  delete
                </span>
              </button>
              @endif
            </div>
          </div>
          @empty
          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 mb-3 block">
              assignment
            </span>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No missions found</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Check back later or create a new mission</p>
          </div>
          @endforelse
        </div>

        <!-- No Results After Filter -->
        <div id="noRecordsFiltered" class="hidden bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-12 text-center">
          <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 mb-3 block">
            search_off
          </span>
          <p class="text-gray-500 dark:text-gray-400 mb-2 font-medium">No missions match your filters</p>
          <button onclick="clearFilters()" class="text-primary hover:text-primary-dark text-sm font-medium">
            Clear filters
          </button>
        </div>

        <!-- Pagination -->
        @if($missions->hasPages())
        <div class="mt-6">
          {{ $missions->links() }}
        </div>
        @endif

      </main>
    </div>

    <!-- Delete Mission Form (Hidden) -->
    <form id="deleteMissionForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- View Mission Details Modal -->
    <div id="viewMissionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeViewModal()">
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 px-6 py-4 rounded-t-2xl flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Mission Details</h3>
                <button onclick="closeViewModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="viewMissionContent" class="p-6"></div>
        </div>
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

      // Filter Functions
      const filterDate = document.getElementById('filterDate');
      const filterStatus = document.getElementById('filterStatus');
      
      if (filterDate) {
        filterDate.addEventListener('change', filterMissions);
      }
      
      if (filterStatus) {
        filterStatus.addEventListener('change', filterMissions);
      }

      function filterMissions() {
        const selectedDate = document.getElementById('filterDate').value;
        const selectedStatus = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('.mission-row');
        const noResultsFiltered = document.getElementById('noRecordsFiltered');
        const tableContainer = document.getElementById('recordsTableContainer');
        const mobileContainer = document.getElementById('mobileRecordsContainer');
        
        let visibleCount = 0;
        let hasRecords = false;

        // Check if there are any records at all
        rows.forEach(row => {
          hasRecords = true;
          const rowDate = row.dataset.date;
          const rowStatus = row.dataset.status;
          
          const matchesDate = !selectedDate || rowDate === selectedDate;
          const matchesStatus = !selectedStatus || rowStatus === selectedStatus;
          
          if (matchesDate && matchesStatus) {
            row.style.display = '';
            row.style.opacity = '0';
            row.style.animation = 'none';
            
            // Trigger reflow
            void row.offsetWidth;
            
            row.style.animation = 'fadeInUp 0.5s ease-out forwards';
            row.style.animationDelay = (visibleCount * 0.05) + 's';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });

        // Show/hide appropriate containers
        if (hasRecords && visibleCount === 0) {
          noResultsFiltered.classList.remove('hidden');
          if (window.innerWidth >= 1024) {
            tableContainer.querySelector('table tbody').style.display = 'none';
          }
          const emptyStates = document.querySelectorAll('.mission-row');
          emptyStates.forEach(state => {
            if (state.querySelector('.text-6xl')) {
              state.style.display = 'none';
            }
          });
        } else if (!hasRecords) {
          noResultsFiltered.classList.add('hidden');
          // Show empty state
        } else {
          noResultsFiltered.classList.add('hidden');
          if (window.innerWidth >= 1024) {
            tableContainer.querySelector('table tbody').style.display = '';
          }
        }
      }

      function clearFilters() {
        document.getElementById('filterDate').value = '';
        document.getElementById('filterStatus').value = '';
        filterMissions();
      }

      // Open Map Function
      function openMap(lat, lng) {
        const url = `https://www.google.com/maps?q=${lat},${lng}`;
        window.open(url, '_blank');
      }

      // View Details Function with Modal
      function viewDetails(missionId) {
        fetch(`/mission/details/${missionId}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('viewMissionContent').innerHTML = `
              <div class="space-y-6">
                <!-- Mission Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                      <div class="p-2 bg-blue-600 dark:bg-blue-500 rounded-lg">
                        <span class="material-symbols-outlined text-white text-xl">calendar_today</span>
                      </div>
                      <div>
                        <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wider">Mission Date</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-100">${data.mission_date}</p>
                      </div>
                    </div>
                  </div>

                  <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-800 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                      <div class="p-2 bg-purple-600 dark:bg-purple-500 rounded-lg">
                        <span class="material-symbols-outlined text-white text-xl">schedule</span>
                      </div>
                      <div>
                        <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold uppercase tracking-wider">Check-in Time</p>
                        <p class="text-lg font-bold text-purple-900 dark:text-purple-100">${data.check_in_time}</p>
                      </div>
                    </div>
                  </div>

                  <div class="p-5 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                      <div class="p-2 bg-green-600 dark:bg-green-500 rounded-lg">
                        <span class="material-symbols-outlined text-white text-xl">timer</span>
                      </div>
                      <div>
                        <p class="text-xs text-green-600 dark:text-green-400 font-semibold uppercase tracking-wider">Work Hours</p>
                        <p class="text-lg font-bold text-green-900 dark:text-green-100">${data.work_hours || 'Pending Approval'}</p>
                      </div>
                    </div>
                  </div>

                  <div class="p-5 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                      <div class="p-2 bg-amber-600 dark:bg-amber-500 rounded-lg">
                        <span class="material-symbols-outlined text-white text-xl">info</span>
                      </div>
                      <div>
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider">Status</p>
                        <div class="mt-1">${data.status_badge}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Location Section -->
                <div class="p-5 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-xl">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="p-2 bg-red-600 dark:bg-red-500 rounded-lg">
                        <span class="material-symbols-outlined text-white text-2xl">location_on</span>
                      </div>
                      <div>
                        <p class="text-xs text-red-600 dark:text-red-400 font-semibold uppercase tracking-wider mb-1">Location</p>
                        <p class="text-sm text-red-700 dark:text-red-300 font-mono">${data.latitude}, ${data.longitude}</p>
                      </div>
                    </div>
                    <button onclick="openMap(${data.latitude_raw}, ${data.longitude_raw})" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2 shadow-md hover:shadow-lg active:scale-95">
                      <span class="material-symbols-outlined text-lg">map</span>
                      Open Map
                    </button>
                  </div>
                </div>

                ${data.rejection_reason ? `
                  <div class="p-5 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-400 rounded-xl">
                    <div class="flex items-start gap-3">
                      <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-2xl">error</span>
                      <div>
                        <p class="text-sm font-bold text-red-900 dark:text-red-100 mb-1">Rejection Reason</p>
                        <p class="text-sm text-red-700 dark:text-red-300">${data.rejection_reason}</p>
                      </div>
                    </div>
                  </div>
                ` : ''}

                <!-- Timeline Info -->
                <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                  <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">history</span>
                    Timeline
                  </h4>
                  <div class="space-y-3">
                    <div class="flex items-start gap-3">
                      <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                      <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${data.created_at}</p>
                      </div>
                    </div>
                    ${data.updated_at !== data.created_at ? `
                      <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div>
                          <p class="text-xs text-gray-500 dark:text-gray-400">Last Updated</p>
                          <p class="text-sm font-medium text-gray-900 dark:text-white">${data.updated_at}</p>
                        </div>
                      </div>
                    ` : ''}
                  </div>
                </div>
              </div>
            `;
            document.getElementById('viewMissionModal').classList.remove('hidden');
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Failed to load mission details. Please try again.');
          });
      }

      // Close View Modal
      function closeViewModal() {
        document.getElementById('viewMissionModal').classList.add('hidden');
      }

      // Cancel Mission Function
      function cancelMission(missionId) {
        if (confirm('Are you sure you want to cancel this mission?')) {
          // Create form dynamically to submit DELETE request
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '/mission/cancel/' + missionId;
          
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = '{{ csrf_token() }}';
          
          const methodInput = document.createElement('input');
          methodInput.type = 'hidden';
          methodInput.name = '_method';
          methodInput.value = 'DELETE';
          
          form.appendChild(csrfInput);
          form.appendChild(methodInput);
          document.body.appendChild(form);
          form.submit();
        }
      }
    </script>
</body>
</html>
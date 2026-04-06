<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Mission Management - Attendify</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
      
      .khmer-text {
        font-family: "Noto Sans Khmer", sans-serif;
        line-height: 1.8;
      }
      
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

      @media (max-width: 1024px) {
        main {
          padding-bottom: calc(6rem + env(safe-area-inset-bottom)) !important;
        }
      }

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
        animation: fadeInUp 0.5s ease-out;
      }

      .modal-backdrop {
        backdrop-filter: blur(4px);
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
          <span class="font-bold text-base md:text-lg">Mission Management</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Mission Management</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage and approve field work missions</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div id="successMessage" class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 flex items-start gap-3 animate-fade-in-up">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-xl shrink-0">check_circle</span>
            <div class="flex-1">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-700">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div id="errorMessage" class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3 animate-fade-in-up">
            <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-xl shrink-0">error</span>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-700">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-symbols-outlined text-2xl text-blue-600 dark:text-blue-400">assignment</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Total Missions</p>
            </div>

            <!-- Pending -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 border border-yellow-200 dark:border-yellow-800 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-symbols-outlined text-2xl text-yellow-600 dark:text-yellow-400">schedule</span>
                </div>
                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Pending</p>
            </div>

            <!-- Approved -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 border border-green-200 dark:border-green-800 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-symbols-outlined text-2xl text-green-600 dark:text-green-400">check_circle</span>
                </div>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['approved'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Approved</p>
            </div>

            <!-- Rejected -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 border border-red-200 dark:border-red-800 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-symbols-outlined text-2xl text-red-600 dark:text-red-400">cancel</span>
                </div>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['rejected'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Rejected</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('mission') }}" class="bg-surface-light dark:bg-surface-dark rounded-2xl p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search User</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name..."
                           class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" 
                            class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" 
                           class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">User</label>
                    <select name="user" 
                            class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="mt-4 flex gap-3 justify-end">
                <a href="{{ route('mission') }}" 
                   class="px-5 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl transition-all duration-200 flex items-center gap-2 font-semibold active:scale-95">
                    <span class="material-symbols-outlined text-xl">clear</span>
                    Clear Filters
                </a>
                <button type="submit" 
                        class="px-5 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl transition-all duration-200 flex items-center gap-2 font-semibold active:scale-95">
                    <span class="material-symbols-outlined text-xl">search</span>
                    Apply Filters
                </button>
            </div>
        </form>

        <!-- Desktop Table -->
        <div class="hidden lg:block bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Mission Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Check-in</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($missions as $mission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($mission->user->image)
                                        <img src="{{ asset('users/' . $mission->user->image) }}" 
                                             alt="{{ $mission->user->name }}"
                                             class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                            {{ strtoupper(substr($mission->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $mission->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $mission->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $mission->mission_date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $mission->created_at->format('h:i A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="openMap({{ $mission->latitude }}, {{ $mission->longitude }})" 
                                        class="text-sm text-primary hover:text-primary-dark transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-lg">location_on</span>
                                    View Map
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                @if($mission->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($mission->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        <span class="material-symbols-outlined text-sm">cancel</span>
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="viewMission({{ $mission->id }})" 
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
                                            title="View Details">
                                        <span class="material-symbols-outlined text-xl">visibility</span>
                                    </button>
                                    
                                    @if($mission->status === 'pending')
                                        <button onclick="showApproveModal({{ $mission->id }})" 
                                                class="p-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                                                title="Approve">
                                            <span class="material-symbols-outlined text-xl">check</span>
                                        </button>
                                        
                                        <button onclick="showRejectModal({{ $mission->id }})" 
                                                class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Reject">
                                            <span class="material-symbols-outlined text-xl">close</span>
                                        </button>
                                    @endif
                                    
                                    @if($mission->status !== 'approved')
                                        <form action="{{ route('admin.missions.delete', $mission->id) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this mission?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                    title="Delete">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 block mb-3">assignment</span>
                                <p class="text-gray-500 dark:text-gray-400">No missions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-4">
            @forelse($missions as $mission)
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-5 shadow-sm">
                <!-- User Info -->
                <div class="flex items-center gap-3 mb-4">
                    @if($mission->user->image)
                        <img src="{{ asset('users/' . $mission->user->image) }}" 
                             alt="{{ $mission->user->name }}"
                             class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg">
                            {{ strtoupper(substr($mission->user->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $mission->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $mission->mission_date->format('M d, Y') }}</p>
                    </div>
                    @if($mission->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                            Pending
                        </span>
                    @elseif($mission->status === 'approved')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Approved
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            <span class="material-symbols-outlined text-sm">cancel</span>
                            Rejected
                        </span>
                    @endif
                </div>

                <!-- Details -->
                <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-4 mb-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Check-in Time</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $mission->created_at->format('h:i A') }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button onclick="viewMission({{ $mission->id }})" 
                            class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-sm font-semibold rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                        View
                    </button>
                    
                    @if($mission->status === 'pending')
                        <button onclick="showApproveModal({{ $mission->id }})" 
                                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">check</span>
                            Approve
                        </button>
                        
                        <button onclick="showRejectModal({{ $mission->id }})" 
                                class="p-3 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl transition-all active:scale-95">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-12 text-center">
                <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 block mb-3">assignment</span>
                <p class="text-gray-500 dark:text-gray-400">No missions found</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($missions->hasPages())
        <div class="mt-6">
            {{ $missions->appends(request()->query())->links() }}
        </div>
        @endif

      </main>
    </div>

    <!-- View Mission Modal -->
    <div id="viewModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 modal-backdrop" onclick="closeModal('viewModal')">
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 px-6 py-4 rounded-t-2xl flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Mission Details</h3>
                <button onclick="closeModal('viewModal')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div id="viewModalContent" class="p-6"></div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 modal-backdrop" onclick="closeModal('approveModal')">
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Approve Mission</h3>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to approve this mission?</p>
                    <div id="approveMissionInfo"></div>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-4 flex gap-3">
                    <button type="button" onclick="closeModal('approveModal')" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">check</span>
                        Approve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 modal-backdrop" onclick="closeModal('rejectModal')">
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Reject Mission</h3>
            </div>
            <form id="rejectForm"  method="POST">
                @csrf
                <div class="p-6">
                    <div id="rejectMissionInfo" class="mb-4"></div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                    </div>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-4 flex gap-3">
                    <button type="button" onclick="closeModal('rejectModal')" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">close</span>
                        Reject
                    </button>
                </div>
            </form>
        </div>
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

      // CSRF Token
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      // Open Map
      function openMap(lat, lng) {
        window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
      }

      // Modal Functions
      function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
      }

      // View Mission
      function viewMission(id) {
        fetch(`/admin/missions/${id}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('viewModalContent').innerHTML = `
              <div class="space-y-6">
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                  ${data.user_image ? `<img src="/users/${data.user_image}" alt="${data.user_name}" class="w-16 h-16 rounded-full object-cover">` : 
                    `<div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl">${data.user_initials}</div>`}
                  <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${data.user_name}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">${data.user_email}</p>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Mission Date</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${data.mission_date}</p>
                  </div>
                  <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-in Time</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${data.check_in_time}</p>
                  </div>
                  <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                    ${data.status_badge}
                  </div>
                  <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Work Hours</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${data.work_hours || '—'}</p>
                  </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Location</p>
                  <button onclick="openMap(${data.latitude}, ${data.longitude})" class="text-primary hover:text-primary-dark transition-colors flex items-center gap-2 font-medium">
                    <span class="material-symbols-outlined">location_on</span>
                    ${data.latitude}, ${data.longitude}
                  </button>
                </div>
                ${data.rejection_reason ? `
                  <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <p class="text-xs font-semibold text-red-800 dark:text-red-300 mb-2">Rejection Reason</p>
                    <p class="text-sm text-red-700 dark:text-red-400">${data.rejection_reason}</p>
                  </div>
                ` : ''}
              </div>
            `;
            document.getElementById('viewModal').classList.remove('hidden');
          })
          .catch(error => console.error('Error:', error));
      }

      // Show Approve Modal
      function showApproveModal(id) {
        fetch(`/admin/missions/${id}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('approveMissionInfo').innerHTML = `
              <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                <p class="text-sm"><strong>User:</strong> ${data.user_name}</p>
                <p class="text-sm"><strong>Date:</strong> ${data.mission_date}</p>
              </div>
            `;
            document.getElementById('approveForm').action = `/admin/missions/${id}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
          })
          .catch(error => console.error('Error:', error));
      }

      // Show Reject Modal
      function showRejectModal(id) {
        fetch(`/admin/missions/${id}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('rejectMissionInfo').innerHTML = `
              <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                <p class="text-sm"><strong>User:</strong> ${data.user_name}</p>
                <p class="text-sm"><strong>Date:</strong> ${data.mission_date}</p>
              </div>
            `;
            document.getElementById('rejectForm').action = `/admin/missions/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
          })
          .catch(error => console.error('Error:', error));
      }

      // Auto-hide success/error messages after 5 seconds
      setTimeout(() => {
        const successMsg = document.getElementById('successMessage');
        const errorMsg = document.getElementById('errorMessage');
        if (successMsg) successMsg.remove();
        if (errorMsg) errorMsg.remove();
      }, 5000);
    </script>
</body>
</html>
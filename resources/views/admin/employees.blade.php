<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Employees - Location Tracker</title>
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

      /* Modal styles with animation */
      .modal {
        transition: opacity 0.3s ease;
      }
      .modal-content {
        transition: transform 0.3s ease;
        transform: scale(0.9);
      }
      .modal:not(.hidden) .modal-content {
        transform: scale(1);
      }

      /* Pagination styles */
      .pagination-button {
        transition: all 0.2s;
      }
      .pagination-button:hover:not(:disabled) {
        transform: translateY(-2px);
      }

      /* Table row animation */
      .employee-row {
        animation: fadeInUp 0.5s ease-out backwards;
      }
      
      .employee-row:nth-child(1) { animation-delay: 0.05s; }
      .employee-row:nth-child(2) { animation-delay: 0.1s; }
      .employee-row:nth-child(3) { animation-delay: 0.15s; }
      .employee-row:nth-child(4) { animation-delay: 0.2s; }
      .employee-row:nth-child(5) { animation-delay: 0.25s; }
      .employee-row:nth-child(6) { animation-delay: 0.3s; }
      .employee-row:nth-child(7) { animation-delay: 0.35s; }
      .employee-row:nth-child(8) { animation-delay: 0.4s; }
      .employee-row:nth-child(9) { animation-delay: 0.45s; }
      .employee-row:nth-child(10) { animation-delay: 0.5s; }

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

      /* Mobile card animation */
      .employee-card {
        animation: fadeInUp 0.5s ease-out backwards;
      }
      
      .employee-card:nth-child(1) { animation-delay: 0.05s; }
      .employee-card:nth-child(2) { animation-delay: 0.1s; }
      .employee-card:nth-child(3) { animation-delay: 0.15s; }
      .employee-card:nth-child(4) { animation-delay: 0.2s; }
      .employee-card:nth-child(5) { animation-delay: 0.25s; }
      .employee-card:nth-child(6) { animation-delay: 0.3s; }
      .employee-card:nth-child(7) { animation-delay: 0.35s; }
      .employee-card:nth-child(8) { animation-delay: 0.4s; }
      .employee-card:nth-child(9) { animation-delay: 0.45s; }
      .employee-card:nth-child(10) { animation-delay: 0.5s; }

      /* Gender badge animation */
      .gender-badge {
        transition: all 0.2s ease;
      }
      .gender-badge:hover {
        transform: scale(1.05);
      }

      /* Mobile responsive */
      @media (max-width: 768px) {
        .hide-mobile {
          display: none;
        }
        table {
          font-size: 0.75rem;
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

      /* Better touch targets */
      @media (max-width: 768px) {
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
      }

      /* Success/Error message animation */
      .alert-message {
        animation: slideInDown 0.5s ease-out;
      }

      @keyframes slideInDown {
        from {
          opacity: 0;
          transform: translateY(-20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      /* Button hover animations */
      .action-button {
        transition: all 0.2s ease;
      }
      .action-button:hover {
        transform: translateY(-1px);
      }
      .action-button:active {
        transform: translateY(0);
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
          <span class="font-bold text-base md:text-lg">Employees</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Employees</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage your team members</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Success/Error Messages -->
        @if(session('success'))
          <div class="alert-message mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
          </div>
        @endif

        @if(session('error'))
          <div class="alert-message mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
          </div>
        @endif

        <!-- Top Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Employee List</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total: {{ $employees->total() }} employees</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <!-- Search Input -->
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <span class="material-symbols-outlined text-[20px]">search</span>
              </span>
              <input 
                type="text" 
                id="searchInput"
                placeholder="Search employees..." 
                class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-full sm:w-64 transition-all"
              />
            </div>
            <!-- Add Employee Button -->
            <button 
              onclick="openAddModal()"
              class="action-button flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors shadow-sm">
              <span class="material-symbols-outlined">add</span>
              <span>Add Employee</span>
            </button>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <tr>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gender</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                  <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
                  <th class="text-right py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800" id="employeeTable">
                @forelse($employees as $employee)
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors employee-row" data-name="{{ strtolower($employee->name) }}" data-email="{{ strtolower($employee->email ?? '') }}">
                    <td class="py-4 px-6">
                      <div class="flex items-center gap-3">
                        @if($employee->image)
                          <img src="{{ asset('users/' . $employee->image) }}" alt="{{ $employee->name }}" class="size-10 rounded-full object-cover shadow-sm">
                        @else
                          <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                          </div>
                        @endif
                        <div>
                          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->name }}</p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="py-4 px-6">
                      <p class="text-sm text-gray-900 dark:text-white">{{ filled($employee->email) ? $employee->email : '----' }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->phone ?? 'No phone' }}</p>
                    </td>
                    <td class="py-4 px-6">
                      <span class="gender-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $employee->gender === 'male' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400' }}">
                        <span class="material-symbols-outlined text-base">
                          {{ $employee->gender === 'male' ? 'male' : 'female' }}
                        </span>
                        {{ ucfirst($employee->gender ?? 'N/A') }}
                      </span>
                    </td>
                    <td class="py-4 px-6">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $employee->role_type === 'admin' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' }}">
                        {{ ucfirst($employee->role_type) }}
                      </span>
                    </td>
                    <td class="py-4 px-6">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $employee->active ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
                        {{ $employee->active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td class="py-4 px-6">
                      <p class="text-sm text-gray-900 dark:text-white">{{ $employee->created_at->format('M d, Y') }}</p>
                    </td>
                    <td class="py-4 px-6">
                      <div class="flex items-center justify-end gap-2">
                        <button 
                          onclick="viewEmployee({{ $employee->id }})"
                          class="action-button p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" 
                          title="View">
                          <span class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                        <button 
                          onclick="editEmployee({{ $employee->id }})"
                          class="action-button p-2 text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors" 
                          title="Edit">
                          <span class="material-symbols-outlined text-xl">edit</span>
                        </button>
                        <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                          @csrf
                          @method('DELETE')
                          <button 
                            type="submit"
                            class="action-button p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" 
                            title="Delete">
                            <span class="material-symbols-outlined text-xl">delete</span>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="py-12 text-center">
                      <div class="flex flex-col items-center gap-3">
                        <span class="material-symbols-outlined text-5xl text-gray-300">person_off</span>
                        <p class="text-gray-500 dark:text-gray-400">No employees found</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3" id="mobileEmployeeList">
          @forelse($employees as $employee)
            <div class="employee-card bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4" data-name="{{ strtolower($employee->name) }}" data-email="{{ strtolower($employee->email ?? '') }}">
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                  @if($employee->image)
                    <img src="{{ asset('users/' . $employee->image) }}" alt="{{ $employee->name }}" class="size-12 rounded-full object-cover shadow-sm">
                  @else
                    <div class="size-12 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                      {{ strtoupper(substr($employee->name, 0, 2)) }}
                    </div>
                  @endif
                  <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $employee->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->email ?? 'No email' }}</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium {{ $employee->active ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
                  {{ $employee->active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              
              <div class="flex items-center gap-4 mb-3 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-base">{{ $employee->gender === 'male' ? 'male' : 'female' }}</span>
                  {{ ucfirst($employee->gender ?? 'N/A') }}
                </span>
                <span class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-base">badge</span>
                  {{ ucfirst($employee->role_type) }}
                </span>
                <span class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-base">calendar_today</span>
                  {{ $employee->created_at->format('M d, Y') }}
                </span>
              </div>

              <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                <button 
                  onclick="viewEmployee({{ $employee->id }})"
                  class="action-button flex-1 flex items-center justify-center gap-2 px-3 py-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors text-sm font-medium">
                  <span class="material-symbols-outlined text-lg">visibility</span>
                  <span>View</span>
                </button>
                <button 
                  onclick="editEmployee({{ $employee->id }})"
                  class="action-button flex-1 flex items-center justify-center gap-2 px-3 py-2 text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors text-sm font-medium">
                  <span class="material-symbols-outlined text-lg">edit</span>
                  <span>Edit</span>
                </button>
                <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                  @csrf
                  @method('DELETE')
                  <button 
                    type="submit"
                    class="action-button w-full flex items-center justify-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors text-sm font-medium">
                    <span class="material-symbols-outlined text-lg">delete</span>
                    <span>Delete</span>
                  </button>
                </form>
              </div>
            </div>
          @empty
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-12 text-center">
              <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">person_off</span>
              <p class="text-gray-500 dark:text-gray-400">No employees found</p>
            </div>
          @endforelse
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
          <div class="mt-6 bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <!-- Results Info -->
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $employees->firstItem() }}</span> to 
                <span class="font-semibold text-gray-900 dark:text-white">{{ $employees->lastItem() }}</span> of 
                <span class="font-semibold text-gray-900 dark:text-white">{{ $employees->total() }}</span> results
              </div>

              <!-- Pagination Buttons -->
              <div class="flex items-center gap-2">
                {{-- Previous Button --}}
                @if ($employees->onFirstPage())
                  <button disabled class="pagination-button px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                  </button>
                @else
                  <a href="{{ $employees->previousPageUrl() }}" class="pagination-button px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                  </a>
                @endif

                {{-- Page Numbers --}}
                <div class="hidden sm:flex items-center gap-2">
                  @foreach ($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                    @if ($page == $employees->currentPage())
                      <button class="pagination-button px-4 py-2 rounded-lg bg-primary text-white font-medium shadow-sm">
                        {{ $page }}
                      </button>
                    @else
                      <a href="{{ $url }}" class="pagination-button px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        {{ $page }}
                      </a>
                    @endif
                  @endforeach
                </div>

                {{-- Mobile: Current Page Display --}}
                <div class="sm:hidden px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                  Page {{ $employees->currentPage() }} of {{ $employees->lastPage() }}
                </div>

                {{-- Next Button --}}
                @if ($employees->hasMorePages())
                  <a href="{{ $employees->nextPageUrl() }}" class="pagination-button px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                  </a>
                @else
                  <button disabled class="pagination-button px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                  </button>
                @endif
              </div>
            </div>
          </div>
        @endif
      </main>
    </div>

 
    <!-- Add Employee Modal -->
    <div id="addModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="modal-content bg-surface-light dark:bg-surface-dark rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Employee</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
        </div>
        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
          @csrf
          
          <!-- Image Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Picture (Optional)</label>
            <div class="flex items-center gap-4">
              <div class="size-20 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-2xl" id="preview-avatar">
                <span class="material-symbols-outlined text-3xl">person</span>
              </div>
              <div class="flex-1">
                <input 
                  type="file" 
                  name="image" 
                  id="image" 
                  accept="image/*"
                  class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer transition-colors"
                  onchange="previewImage(event)"
                >
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG, GIF up to 2MB</p>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Full Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="John Doe">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email (Optional)</label>
            <input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="john@example.com">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone (Optional)</label>
            <input type="tel" name="phone" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="+1234567890">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Password <span class="text-red-500">*</span>
            </label>
            <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="••••••••">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 8 characters</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Role <span class="text-red-500">*</span>
            </label>
            <select name="role_type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Gender <span class="text-red-500">*</span>
            </label>
            <select name="gender" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
            <label for="active" class="text-sm text-gray-700 dark:text-gray-300">Active Employee</label>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="button" onclick="closeAddModal()" class="action-button flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              Cancel
            </button>
            <button type="submit" class="action-button flex-1 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors">
              Add Employee
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

      // Modal Functions
      function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
      }

      function viewEmployee(id) {
        window.location.href = `/employees/${id}`;
      }

      function editEmployee(id) {
        window.location.href = `/employees/${id}/edit`;
      }

      // Image Preview Function
      function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview-avatar');
        
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          
          reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="size-full rounded-full object-cover">`;
          };
          
          reader.readAsDataURL(input.files[0]);
        }
      }

      // Search Functionality
      const searchInput = document.getElementById('searchInput');
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        // Desktop table rows
        const rows = document.querySelectorAll('.employee-row');
        rows.forEach(row => {
          const name = row.dataset.name;
          const email = row.dataset.email;
          if (name.includes(searchTerm) || email.includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });

        // Mobile cards
        const cards = document.querySelectorAll('.employee-card');
        cards.forEach(card => {
          const name = card.dataset.name;
          const email = card.dataset.email;
          if (name.includes(searchTerm) || email.includes(searchTerm)) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });

      // Close modal on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeAddModal();
        }
      });

      // Close modal when clicking outside
      document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeAddModal();
        }
      });
    </script>
  </body>
</html>
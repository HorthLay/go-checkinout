<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Edit Employee - {{ $employee->name }}</title>
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

      /* Better touch targets */
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
          <span class="font-bold text-base md:text-lg">Edit Employee</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Employee</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Update employee information</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
          <a href="{{ route('employees') }}" class="hover:text-primary transition-colors">Employees</a>
          <span class="material-symbols-outlined text-base">chevron_right</span>
          <span class="text-gray-900 dark:text-white font-medium">Edit {{ $employee->name }}</span>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
          <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-red-600 dark:text-red-400">error</span>
              <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</h3>
                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                  @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Employee Info Card -->
          <div class="lg:col-span-1">
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
              <div class="flex flex-col items-center text-center">
                @if($employee->image)
                  <img src="{{ asset('users/' . $employee->image) }}" alt="{{ $employee->name }}" class="size-24 rounded-full object-cover shadow-lg mb-4">
                @else
                  <div class="size-24 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-3xl shadow-lg mb-4">
                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                  </div>
                @endif
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $employee->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $employee->email ?? 'No email' }}</p>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $employee->role_type === 'admin' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' }}">
                  {{ ucfirst($employee->role_type) }}
                </span>
              </div>

              <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 space-y-3">
                <div class="flex items-center gap-3 text-sm">
                  <span class="material-symbols-outlined text-gray-400">badge</span>
                  <span class="text-gray-600 dark:text-gray-400">ID:</span>
                  <span class="text-gray-900 dark:text-white font-medium">#{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                  <span class="material-symbols-outlined text-gray-400">phone</span>
                  <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                  <span class="text-gray-900 dark:text-white font-medium">{{ $employee->phone ?? 'Not provided' }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                  <span class="material-symbols-outlined text-gray-400">calendar_today</span>
                  <span class="text-gray-600 dark:text-gray-400">Joined:</span>
                  <span class="text-gray-900 dark:text-white font-medium">{{ $employee->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                  <span class="material-symbols-outlined text-gray-400">{{ $employee->active ? 'check_circle' : 'cancel' }}</span>
                  <span class="text-gray-600 dark:text-gray-400">Status:</span>
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $employee->active ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
                    {{ $employee->active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Form -->
          <div class="lg:col-span-2">
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-8">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Edit Employee Information</h3>
              
              <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Image Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Picture</label>
                  <div class="flex items-center gap-4">
                    <div id="preview-container">
                      @if($employee->image)
                        <img src="{{ asset('users/' . $employee->image) }}" alt="{{ $employee->name }}" class="size-20 rounded-full object-cover" id="preview-avatar">
                      @else
                        <div class="size-20 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold text-2xl" id="preview-avatar">
                          {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </div>
                      @endif
                    </div>
                    <div class="flex-1">
                      <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer"
                        onchange="previewImage(event)"
                      >
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG, GIF up to 2MB</p>
                      @if($employee->image)
                        <label class="inline-flex items-center gap-1 text-xs text-red-600 dark:text-red-400 mt-2 cursor-pointer">
                          <input type="checkbox" name="remove_image" value="1" class="rounded text-red-600">
                          <span>Remove current image</span>
                        </label>
                      @endif
                    </div>
                  </div>
                  @error('image')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Name -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Full Name <span class="text-red-500">*</span>
                  </label>
                  <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $employee->name) }}"
                    required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors @error('name') border-red-500 @enderror" 
                    placeholder="John Doe"
                  >
                  @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email (Optional)
                  </label>
                  <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $employee->email) }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors @error('email') border-red-500 @enderror" 
                    placeholder="john@example.com"
                  >
                  @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Phone -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Phone (Optional)
                  </label>
                  <input 
                    type="tel" 
                    name="phone" 
                    value="{{ old('phone', $employee->phone) }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors @error('phone') border-red-500 @enderror" 
                    placeholder="+1234567890"
                  >
                  @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Password -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    New Password (Optional)
                  </label>
                  <input 
                    type="password" 
                    name="password" 
                    minlength="8"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors @error('password') border-red-500 @enderror" 
                    placeholder="••••••••"
                  >
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to keep current password. Minimum 8 characters.</p>
                  @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Role -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Role <span class="text-red-500">*</span>
                  </label>
                  <select 
                    name="role_type" 
                    required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors @error('role_type') border-red-500 @enderror"
                  >
                    <option value="user" {{ old('role_type', $employee->role_type) == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role_type', $employee->role_type) == 'admin' ? 'selected' : '' }}>Admin</option>
                  </select>
                  @error('role_type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Status -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Account Status
                  </label>
                  <div class="flex items-center gap-3">
                    <input 
                      type="checkbox" 
                      name="active" 
                      id="active" 
                      value="1" 
                      {{ old('active', $employee->active) ? 'checked' : '' }}
                      class="rounded border-gray-300 text-primary focus:ring-primary w-5 h-5"
                    >
                    <label for="active" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                      Active Employee (Can log in and access the system)
                    </label>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                  <a 
                    href="{{ route('employees') }}" 
                    class="flex-1 flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                  >
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span>Cancel</span>
                  </a>
                  <button 
                    type="submit" 
                    class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors shadow-sm"
                  >
                    <span class="material-symbols-outlined">save</span>
                    <span>Save Changes</span>
                  </button>
                </div>
              </form>

              <!-- Delete Section -->
              <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-800">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Danger Zone</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Permanently delete this employee and all associated data.</p>
                <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button 
                    type="submit" 
                    class="flex items-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                  >
                    <span class="material-symbols-outlined">delete</span>
                    <span>Delete Employee</span>
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

      // Image Preview Function
      function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview-avatar');
        
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          
          reader.onload = function(e) {
            preview.outerHTML = `<img src="${e.target.result}" class="size-20 rounded-full object-cover" id="preview-avatar">`;
          };
          
          reader.readAsDataURL(input.files[0]);
        }
      }
    </script>
     <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </body>
</html>
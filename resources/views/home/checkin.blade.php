<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Check-In - Attendify</title>
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
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
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

      #qr-reader {
        border-radius: 1rem;
        overflow: hidden;
      }

      #qr-reader__dashboard_section_swaplink {
        display: none !important;
      }

      #qr-reader video {
        border-radius: 1rem;
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
          <span class="font-bold text-base md:text-lg">Check-In</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Check-In / Check-Out</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Scan QR code to proceed with location verification</p>
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

        <div class="max-w-4xl mx-auto">
          <!-- Today's Status Card -->
          @if($todayAttendance)
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <div class="size-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">today</span>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Today's Attendance</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                  {{ $todayAttendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
                  {{ $todayAttendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                  {{ $todayAttendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                  {{ $todayAttendance->status === 'leave' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
                ">
                  {{ ucfirst(str_replace('_', ' ', $todayAttendance->status)) }}
                </span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-In</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('h:i A') : '—' }}
                  </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Check-Out</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('h:i A') : '—' }}
                  </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Work Hours</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white">
                  {{ $todayAttendance->formatted_work_hours ?? '—' }}

                  </p>
                </div>
              </div>
            </div>
          @endif

          <!-- Instructions Card -->
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">info</span>
              <div>
                <h3 class="text-base font-bold text-blue-900 dark:text-blue-100 mb-2">How to Check-In/Out</h3>
                <ol class="text-sm text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside">
                  <li>Scan the QR code at your workplace entrance</li>
                  <li>You will be redirected to location verification</li>
                  <li>Confirm your location is within the allowed area</li>
                  <li>Complete your check-in or check-out</li>
                </ol>
              </div>
            </div>
          </div>

          <!-- QR Scanner Section -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800">
              <div class="flex items-center gap-3">
                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center">
                  <span class="material-symbols-outlined text-primary text-2xl">qr_code_scanner</span>
                </div>
                <div>
                  <h2 class="text-lg font-bold text-gray-900 dark:text-white">Scan QR Code</h2>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Point your camera at the QR code</p>
                </div>
              </div>
            </div>

            <div class="p-6">
              <!-- Scanner Container -->
              <div id="qr-reader" class="mb-4 hidden"></div>

              <!-- Scanner Status -->
              <div id="scanner-status" class="text-center py-12">
                <div class="inline-flex items-center justify-center size-24 rounded-full bg-primary/10 mb-4">
                  <span class="material-symbols-outlined text-primary text-6xl">qr_code_scanner</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ready to Scan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Click the button below to activate your camera</p>
                <button onclick="startScanner()" id="start-scan-btn" class="px-8 py-4 bg-primary hover:bg-primary-dark text-white rounded-xl font-semibold transition-colors flex items-center gap-3 mx-auto text-lg">
                  <span class="material-symbols-outlined text-2xl">photo_camera</span>
                  Start Camera
                </button>
              </div>

              <!-- Scanned Data Display -->
              <div id="scanned-data" class="hidden mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                <div class="flex items-start gap-3">
                  <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
                  <div class="flex-1">
                    <p class="text-sm font-semibold text-green-900 dark:text-green-100 mb-1">QR Code Detected!</p>
                    <p class="text-xs text-green-700 dark:text-green-300 mb-3">Redirecting to location verification...</p>
                  </div>
                </div>
              </div>

              <!-- Stop Scanner Button -->
              <button onclick="stopScanner()" id="stop-scan-btn" class="hidden mt-4 w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">stop_circle</span>
                Stop Camera
              </button>
            </div>
          </div>
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

      // QR Scanner
      let html5QrCode = null;

      function startScanner() {
        const qrReader = document.getElementById('qr-reader');
        const scannerStatus = document.getElementById('scanner-status');
        const stopBtn = document.getElementById('stop-scan-btn');

        qrReader.classList.remove('hidden');
        scannerStatus.classList.add('hidden');
        stopBtn.classList.remove('hidden');

        html5QrCode = new Html5Qrcode("qr-reader");
        
        html5QrCode.start(
          { facingMode: "environment" },
          {
            fps: 10,
            qrbox: { width: 250, height: 250 }
          },
          (decodedText, decodedResult) => {
            document.getElementById('scanned-data').classList.remove('hidden');
            
            // Stop scanner
            stopScanner();
            
            // Redirect to map verification with QR data
            setTimeout(() => {
              window.location.href = `{{ route('attendance.verify') }}?qr=${encodeURIComponent(decodedText)}`;
            }, 1500);
          },
          (errorMessage) => {
            // Handle scan errors silently
          }
        ).catch((err) => {
          console.error('Error starting scanner:', err);
          alert('Unable to start camera. Please check camera permissions in your browser settings.');
          stopScanner();
        });
      }

      function stopScanner() {
        if (html5QrCode) {
          html5QrCode.stop().then(() => {
            document.getElementById('qr-reader').classList.add('hidden');
            document.getElementById('scanner-status').classList.remove('hidden');
            document.getElementById('stop-scan-btn').classList.add('hidden');
            html5QrCode = null;
          }).catch((err) => {
            console.error('Error stopping scanner:', err);
          });
        }
      }
    </script>
  </body>
</html>
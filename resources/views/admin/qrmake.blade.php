<!DOCTYPE html>
<html class="light" lang="en">
 <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>QR Code - Attendify</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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

      @media print {
        body * {
          visibility: hidden;
        }
        #qr-design-card, #qr-design-card * {
          visibility: visible;
        }
        #qr-design-card {
          position: absolute;
          left: 50%;
          top: 50%;
          transform: translate(-50%, -50%);
          max-width: 130mm;
          box-shadow: none !important;
        }
        /* Ensure Khmer prints correctly */
        #qr-design-card * {
          font-family: "Inter", "Noto Sans Khmer", sans-serif !important;
        }
        @page {
          size: A5;
          margin: 10mm;
        }
      }

      .qr-design-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
          <span class="font-bold text-base md:text-lg">QR Code</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">QR Code Generator</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Generate QR codes for check-in/check-out</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <div class="max-w-6xl mx-auto">
          <!-- Instruction Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">qr_code_2</span>
                </div>
                <h3 class="font-semibold text-blue-900 dark:text-blue-100">Generate</h3>
              </div>
              <p class="text-sm text-blue-700 dark:text-blue-300">Create a QR code for attendance tracking</p>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                  <span class="material-symbols-outlined text-green-600 dark:text-green-400">download</span>
                </div>
                <h3 class="font-semibold text-green-900 dark:text-green-100">Download</h3>
              </div>
              <p class="text-sm text-green-700 dark:text-green-300">Save QR code with beautiful design</p>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                  <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">qr_code_scanner</span>
                </div>
                <h3 class="font-semibold text-purple-900 dark:text-purple-100">Scan</h3>
              </div>
              <p class="text-sm text-purple-700 dark:text-purple-300">Employees scan to check-in or check-out instantly</p>
            </div>
          </div>

          <!-- QR Code Generator -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Section -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
              <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined">settings</span>
                QR Code Settings
              </h2>

              <div class="space-y-4">
                <!-- QR Code Color -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">QR Code Color</label>
                  <select id="qr-color" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="blue">Blue</option>
                    <option value="purple">Purple</option>
                    <option value="green">Green</option>
                    <option value="red">Red</option>
                    <option value="black">Black</option>
                  </select>
                </div>

                <!-- QR Code Size -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">QR Code Size</label>
                  <select id="qr-size" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="200">Small (200x200)</option>
                    <option value="256" selected>Medium (256x256)</option>
                    <option value="300">Large (300x300)</option>
                  </select>
                </div>

                <!-- Generate Button -->
                <button 
                  onclick="generateQR()"
                  class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-medium transition-colors shadow-sm"
                >
                  <span class="material-symbols-outlined">add_circle</span>
                  <span>Generate QR Code</span>
                </button>
              </div>
            </div>

            <!-- Preview Section -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
              <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined">visibility</span>
                Preview & Download
              </h2>

              <div class="flex flex-col items-center justify-center min-h-[400px]">
                <div id="qr-placeholder" class="text-center">
                  <div class="size-32 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-gray-400">qr_code_2</span>
                  </div>
                  <p class="text-gray-500 dark:text-gray-400 text-sm">Your QR code will appear here</p>
                </div>

                <div id="qr-result" class="hidden w-full">
                  <!-- Designed QR Card for Download -->
                  <div id="qr-design-card" class="bg-white rounded-3xl shadow-2xl p-8 max-w-md mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-6">
                      <div class="inline-flex items-center gap-2 bg-primary/10 px-4 py-2 rounded-full mb-3">
                        <span class="material-symbols-outlined text-primary text-xl">qr_code_scanner</span>
                        <span class="font-bold text-primary">Attendify</span>
                      </div>
                      <h3 class="text-2xl font-bold text-gray-900 mb-1">Attendance Check-In/Out</h3>
                      <p class="text-sm text-gray-600">Scan to record your attendance</p>
                    </div>

                    <!-- QR Code Container -->
                    <div class="bg-white p-6 rounded-2xl shadow-inner mb-6 flex justify-center">
                      <div id="qrcode"></div>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-4 mb-4">
                      <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">info</span>
                        How to Use
                      </p>
                      <ol class="text-xs text-gray-600 space-y-1 list-decimal list-inside">
                        <li>Open your camera or QR scanner app</li>
                        <li>Point at this QR code</li>
                        <li>Tap the notification to check-in/out</li>
                      </ol>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-200">
                      <span id="qr-date"></span>
                      <span>Powered by Attendify</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div id="action-buttons" class="hidden space-y-3 mt-6">
                <button 
                  onclick="downloadDesignedQR()"
                  class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all shadow-lg"
                >
                  <span class="material-symbols-outlined">download</span>
                  <span>Download with Design</span>
                </button>

                <button 
                  onclick="downloadSimpleQR()"
                  class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors"
                >
                  <span class="material-symbols-outlined">qr_code</span>
                  <span>Download QR Only</span>
                </button>

                <button 
                  onclick="printQR()"
                  class="w-full flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                  <span class="material-symbols-outlined">print</span>
                  <span>Print QR Code</span>
                </button>

                <button 
                  onclick="resetQR()"
                  class="w-full flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                  <span class="material-symbols-outlined">refresh</span>
                  <span>Generate New</span>
                </button>
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

      // QR Code Generation
      let currentQRCode = null;

      // Color mapping
      const colorMap = {
        blue: '#135bec',
        purple: '#8b5cf6',
        green: '#10b981',
        red: '#ef4444',
        black: '#000000'
      };

      function generateQR() {
        const size = parseInt(document.getElementById('qr-size').value);
        const color = document.getElementById('qr-color').value;

        // Clear previous QR code
        document.getElementById('qrcode').innerHTML = '';

        // Generate QR data - simple URL redirect
        const qrData = window.location.origin + '/attendance/checkinout';

        // Create QR code
        currentQRCode = new QRCode(document.getElementById('qrcode'), {
          text: qrData,
          width: size,
          height: size,
          colorDark: colorMap[color],
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });

        // Update UI
        document.getElementById('qr-placeholder').classList.add('hidden');
        document.getElementById('qr-result').classList.remove('hidden');
        document.getElementById('action-buttons').classList.remove('hidden');
        
        document.getElementById('qr-date').textContent = new Date().toLocaleDateString('en-US', { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric' 
        });
      }

      function downloadDesignedQR() {
        const card = document.getElementById('qr-design-card');
        if (!card) {
          alert('Please generate a QR code first');
          return;
        }

        html2canvas(card, {
          backgroundColor: '#ffffff',
          scale: 2,
          logging: false,
          useCORS: true
        }).then(canvas => {
          const link = document.createElement('a');
          link.download = `attendify-qrcode-designed.png`;
          link.href = canvas.toDataURL('image/png');
          link.click();
        });
      }

      function downloadSimpleQR() {
        const canvas = document.querySelector('#qrcode canvas');
        if (!canvas) {
          alert('Please generate a QR code first');
          return;
        }

        const link = document.createElement('a');
        link.download = `attendify-qrcode.png`;
        link.href = canvas.toDataURL();
        link.click();
      }

      function printQR() {
        window.print();
      }

      function resetQR() {
        document.getElementById('qr-placeholder').classList.remove('hidden');
        document.getElementById('qr-result').classList.add('hidden');
        document.getElementById('action-buttons').classList.add('hidden');
        document.getElementById('qrcode').innerHTML = '';
        currentQRCode = null;
      }
    </script>
  </body>
</html>
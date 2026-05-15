<!DOCTYPE html>
<html class="light" lang="en">
 <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" name="viewport" />
    <title>Check-In - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />

    <!-- Inter font for English -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" /></noscript>

    <!-- Noto Sans Khmer font -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap" /></noscript>

    <!-- Material Symbols -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" /></noscript>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
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
      body { font-family: "Inter", "Noto Sans Khmer", sans-serif; }
      .khmer-text { font-family: "Noto Sans Khmer", sans-serif; line-height: 1.8; }
      .mixed-text { font-family: "Inter", "Noto Sans Khmer", sans-serif; }
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

      #mobile-menu, #profile-dropdown {
        transition: transform 0.3s ease, opacity 0.3s ease;
        transform: translateY(-10px);
        opacity: 0;
      }
      #mobile-menu:not(.hidden), #profile-dropdown:not(.hidden) {
        transform: translateY(0);
        opacity: 1;
      }

      button, a, .clickable {
        min-height: 44px;
        min-width: 44px;
        touch-action: manipulation;
      }

      #qr-reader { border-radius: 0.75rem; overflow: hidden; max-width: 100%; }
      #qr-reader__dashboard_section_swaplink { display: none !important; }
      #qr-reader video { border-radius: 0.75rem; width: 100% !important; height: auto !important; max-height: 400px; }
      @media (max-width: 640px) { #qr-reader video { max-height: 300px; } }

      .upload-area {
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .upload-area.dragover { border-color: #135bec; background-color: rgba(19, 91, 236, 0.05); }
      .dark .upload-area { border-color: #4b5563; }
      .dark .upload-area.dragover { border-color: #135bec; background-color: rgba(19, 91, 236, 0.1); }

      .tab-btn { color: #6b7280; transition: all 0.2s ease; }
      .dark .tab-btn { color: #9ca3af; }
      .tab-btn.active { background-color: #135bec; color: white; }

      .sound-toggle-btn { transition: all 0.2s ease; }
      .sound-toggle-btn.muted { color: #ef4444; }
      .sound-toggle-btn:not(.muted) { color: #135bec; }

      @media (max-width: 640px) {
        .mobile-compact { padding: 1rem !important; }
        .mobile-text-sm { font-size: 0.875rem !important; }
        .mobile-icon-sm { font-size: 1.25rem !important; }
      }

      @media (max-width: 640px) { input[type="file"] { font-size: 16px; } }

      .tab-content { animation: fadeIn 0.3s ease-in; }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

      #uploaded-image { max-height: 250px; width: auto; margin: 0 auto; display: block; }
      @media (max-width: 640px) { #uploaded-image { max-height: 200px; } }

      /* Time status badges */
      .time-badge {
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        line-height: 1;
        white-space: nowrap;
      }
    </style>

    @livewireStyles
</head>
  <body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('home.Layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="h-14 sm:h-16 md:h-20 flex items-center justify-between px-3 sm:px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10">
        <div class="flex items-center gap-2 sm:gap-3 lg:hidden">
          <span class="font-bold text-sm sm:text-base md:text-lg">Check-In</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Check-In / Check-Out</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Scan QR code or upload image to proceed</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 lg:p-10">
        <!-- Hidden flash data for sound triggers -->
        <div id="flash-data"
             data-success="@if(session('success')){{ session('success') }}@endif"
             data-error="@if(session('error')){{ session('error') }}@endif"
             class="hidden"></div>

        <!-- Success/Error Messages -->
        @if(session('success'))
          <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg sm:rounded-xl flex items-start sm:items-center gap-2 sm:gap-3">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-xl sm:text-2xl">check_circle</span>
            <p class="text-xs sm:text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
          </div>
        @endif

        @if(session('error'))
          <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg sm:rounded-xl flex items-start sm:items-center gap-2 sm:gap-3">
            <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-xl sm:text-2xl">error</span>
            <p class="text-xs sm:text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
          </div>
        @endif

        <div class="max-w-4xl mx-auto">
          <!-- Toolbar: Sound Toggle -->
          <div class="mb-3 sm:mb-4 flex items-center justify-end">
            <button type="button" id="sound-toggle" onclick="SoundManager.toggleMute()"
              class="sound-toggle-btn inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-xs sm:text-sm font-medium"
              aria-label="Toggle notification sound" title="Toggle notification sound">
              <span id="sound-icon" class="material-symbols-outlined text-lg sm:text-xl">volume_up</span>
              <span id="sound-label" class="hidden sm:inline">Sound On</span>
            </button>
          </div>

          <!-- Tabs -->
          <div class="mb-4 sm:mb-6">
            <div class="bg-surface-light dark:bg-surface-dark rounded-lg sm:rounded-xl border border-gray-100 dark:border-gray-800 p-1 flex gap-1 w-full">
              <button onclick="switchTab('scan')" id="tab-scan"
                class="tab-btn active flex-1 px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm md:text-base font-medium transition-all">
                <span class="flex items-center justify-center gap-1.5 sm:gap-2">
                  <span class="material-symbols-outlined text-base sm:text-lg">qr_code_scanner</span>
                  <span>Scan</span>
                </span>
              </button>
              <button onclick="switchTab('upload')" id="tab-upload"
                class="tab-btn flex-1 px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm md:text-base font-medium transition-all">
                <span class="flex items-center justify-center gap-1.5 sm:gap-2">
                  <span class="material-symbols-outlined text-base sm:text-lg">upload</span>
                  <span>Upload</span>
                </span>
              </button>
            </div>
          </div>

          <!-- Scan Tab -->
          <div id="scan-tab" class="tab-content">
            <div class="bg-surface-light dark:bg-surface-dark rounded-lg sm:rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
              <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 sm:gap-3">
                  <div class="size-10 sm:size-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-xl sm:text-2xl">qr_code_scanner</span>
                  </div>
                  <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Scan QR Code</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Point camera at QR</p>
                  </div>
                </div>
              </div>

              <div class="p-4 sm:p-6">
                <div id="qr-reader" class="mb-4 hidden"></div>

                <div id="scanner-status" class="text-center py-8 sm:py-12">
                  <div class="inline-flex items-center justify-center size-20 sm:size-24 rounded-full bg-primary/10 mb-3 sm:mb-4">
                    <span class="material-symbols-outlined text-primary text-5xl sm:text-6xl">qr_code_scanner</span>
                  </div>
                  <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-2">Ready to Scan</h3>
                  <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 px-4">Tap below to start camera</p>
                  <button onclick="startScanner()" id="start-scan-btn" class="px-6 sm:px-8 py-3 sm:py-4 bg-primary hover:bg-primary-dark text-white rounded-lg sm:rounded-xl font-semibold transition-colors flex items-center gap-2 sm:gap-3 mx-auto text-base sm:text-lg">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">photo_camera</span>
                    <span>Start Camera</span>
                  </button>
                </div>

                <div id="scanned-data" class="hidden mt-3 sm:mt-4 p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg sm:rounded-xl">
                  <div class="flex items-start gap-2 sm:gap-3">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg sm:text-xl">check_circle</span>
                    <div class="flex-1">
                      <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-100 mb-1">QR Code Detected!</p>
                      <p class="text-xs text-green-700 dark:text-green-300">Redirecting...</p>
                    </div>
                  </div>
                </div>

                <button onclick="stopScanner()" id="stop-scan-btn" class="hidden mt-3 sm:mt-4 w-full px-4 sm:px-6 py-2.5 sm:py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg sm:rounded-xl text-sm sm:text-base font-medium transition-colors flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-lg sm:text-xl">stop_circle</span>
                  <span>Stop Camera</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Upload Tab -->
          <div id="upload-tab" class="tab-content hidden">
            <div class="bg-surface-light dark:bg-surface-dark rounded-lg sm:rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
              <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 sm:gap-3">
                  <div class="size-10 sm:size-12 rounded-lg sm:rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-xl sm:text-2xl">upload_file</span>
                  </div>
                  <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Upload QR Image</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Upload QR image</p>
                  </div>
                </div>
              </div>

              <div class="p-4 sm:p-6">
                <div id="upload-area"
                  class="upload-area rounded-lg sm:rounded-xl p-6 sm:p-8 text-center cursor-pointer mb-3 sm:mb-4"
                  onclick="document.getElementById('qr-image-input').click()"
                  ondragover="handleDragOver(event)"
                  ondragleave="handleDragLeave(event)"
                  ondrop="handleDrop(event)">
                  <input type="file" id="qr-image-input" accept="image/*" class="hidden" onchange="handleFileSelect(event)" />

                  <div id="upload-prompt">
                    <div class="size-16 sm:size-20 md:size-24 mx-auto mb-3 sm:mb-4 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl sm:rounded-2xl flex items-center justify-center">
                      <span class="material-symbols-outlined text-3xl sm:text-4xl md:text-5xl text-orange-600 dark:text-orange-400">cloud_upload</span>
                    </div>
                    <p class="text-xs sm:text-sm md:text-base font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Drop QR image here</p>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-2 sm:mb-4">or tap to browse</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG, JPEG</p>
                  </div>

                  <div id="upload-preview" class="hidden">
                    <img id="uploaded-image" class="rounded-lg sm:rounded-xl shadow-lg" alt="Uploaded QR" />
                  </div>
                </div>

                <div id="decode-processing" class="hidden p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg sm:rounded-xl mb-3 sm:mb-4">
                  <div class="flex items-center gap-2 sm:gap-3">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 animate-spin text-lg sm:text-xl">progress_activity</span>
                    <p class="text-xs sm:text-sm text-blue-900 dark:text-blue-100 font-medium">Decoding...</p>
                  </div>
                </div>

                <div id="decode-success" class="hidden p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg sm:rounded-xl mb-3 sm:mb-4">
                  <div class="flex items-start gap-2 sm:gap-3">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg sm:text-xl">check_circle</span>
                    <div class="flex-1">
                      <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-100 mb-1">QR Decoded!</p>
                      <p class="text-xs text-green-700 dark:text-green-300">Redirecting...</p>
                    </div>
                  </div>
                </div>

                <div id="decode-error" class="hidden p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg sm:rounded-xl mb-3 sm:mb-4">
                  <div class="flex items-start gap-2 sm:gap-3">
                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-lg sm:text-xl">error</span>
                    <div class="flex-1">
                      <p class="text-xs sm:text-sm font-semibold text-red-900 dark:text-red-100 mb-1">Decoding Failed</p>
                      <p class="text-xs text-red-700 dark:text-red-300">No QR code found. Try another image.</p>
                    </div>
                  </div>
                </div>

                <button onclick="clearUpload()" id="clear-upload-btn"
                  class="hidden w-full px-4 py-2.5 sm:py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg sm:rounded-xl text-sm sm:text-base font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-lg">close</span>
                  <span>Clear</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Today's Status Card -->
          @if($todayAttendance)
            @php
                // Time-rule checks (matching report / violation rules)
                $mIn  = $todayAttendance->morning_check_in;
                $mOut = $todayAttendance->morning_check_out;
                $aIn  = $todayAttendance->afternoon_check_in;
                $aOut = $todayAttendance->afternoon_check_out;

                // Morning IN: violation if > 09:00
                $mInLate     = $mIn  && $mIn->format('H:i')  > '09:00';
                // Morning OUT: early < 11:00, late > 12:00
                $mOutEarly   = $mOut && $mOut->format('H:i') < '11:00';
                $mOutLate    = $mOut && $mOut->format('H:i') > '12:00';
                // Afternoon IN: violation if > 15:00
                $aInLate     = $aIn  && $aIn->format('H:i')  > '15:00';
                // Afternoon OUT: early < 17:00, late > 18:00
                $aOutEarly   = $aOut && $aOut->format('H:i') < '17:00';
                $aOutLate    = $aOut && $aOut->format('H:i') > '18:00';

                // Badge style helpers
                $okBadge    = 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-800';
                $lateBadge  = 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800';
                $earlyBadge = 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800';
                $lateOutBadge = 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800';
                $aLateOutBadge = 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 border-pink-200 dark:border-pink-800';

                // Text color helpers
                $okText    = 'text-green-600 dark:text-green-400';
                $badText   = 'text-red-600 dark:text-red-400';
                $earlyText = 'text-orange-600 dark:text-orange-400';
                $lateOutText = 'text-yellow-700 dark:text-yellow-400';
                $aLateOutText = 'text-pink-600 dark:text-pink-400';
            @endphp

            <div class="bg-surface-light dark:bg-surface-dark rounded-lg sm:rounded-xl border border-gray-100 dark:border-gray-800 p-4 sm:p-6 mt-4 sm:mt-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-4">
                <div class="flex items-center gap-2 sm:gap-3">
                  <div class="size-10 sm:size-12 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-xl sm:text-2xl">today</span>
                  </div>
                  <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Today's Attendance</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ now()->format('D, M j, Y') }}</p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg text-xs sm:text-sm font-medium self-start sm:self-auto
                  {{ $todayAttendance->status === 'on_time' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}
                  {{ $todayAttendance->status === 'late' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}
                  {{ $todayAttendance->status === 'absent' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                  {{ $todayAttendance->status === 'leave' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}
                ">
                  {{ ucfirst(str_replace('_', ' ', $todayAttendance->status)) }}
                </span>
              </div>

              <!-- Morning and Afternoon Sessions -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-3 sm:mb-4">
                <!-- Morning Session -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg sm:rounded-xl p-3 sm:p-4">
                  <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400 text-lg sm:text-xl">wb_sunny</span>
                      <h4 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Morning</h4>
                    </div>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">In ≤ 9:00 · Out 11:00–12:00</span>
                  </div>
                  <div class="space-y-1.5 sm:space-y-2">
                    <!-- Morning Check-In -->
                    <div class="flex items-center justify-between">
                      <span class="text-xs text-gray-500 dark:text-gray-400">Check-In:</span>
                      <div class="flex items-center gap-1.5">
                        @if($mIn)
                          <span class="text-xs sm:text-sm font-semibold {{ $mInLate ? $badText : $okText }}">
                            {{ $mIn->format('h:i A') }}
                          </span>
                          <span class="time-badge {{ $mInLate ? $lateBadge : $okBadge }}">
                            {{ $mInLate ? 'Late' : 'On Time' }}
                          </span>
                        @else
                          <span class="text-xs sm:text-sm font-semibold text-gray-400">—</span>
                        @endif
                      </div>
                    </div>

                    <!-- Morning Check-Out -->
                    <div class="flex items-center justify-between">
                      <span class="text-xs text-gray-500 dark:text-gray-400">Check-Out:</span>
                      <div class="flex items-center gap-1.5">
                        @if($mOut)
                          @php
                            $mOutTextColor = $mOutEarly ? $earlyText : ($mOutLate ? $lateOutText : $okText);
                            $mOutBadgeStyle = $mOutEarly ? $earlyBadge : ($mOutLate ? $lateOutBadge : $okBadge);
                            $mOutLabel = $mOutEarly ? 'Early' : ($mOutLate ? 'Late' : 'On Time');
                          @endphp
                          <span class="text-xs sm:text-sm font-semibold {{ $mOutTextColor }}">
                            {{ $mOut->format('h:i A') }}
                          </span>
                          <span class="time-badge {{ $mOutBadgeStyle }}">{{ $mOutLabel }}</span>
                        @else
                          <span class="text-xs sm:text-sm font-semibold text-gray-400">—</span>
                        @endif
                      </div>
                    </div>

                    <div class="pt-1.5 sm:pt-2 border-t border-gray-200 dark:border-gray-700">
                      <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Hours:</span>
                        <span class="text-xs sm:text-sm font-bold text-blue-600 dark:text-blue-400">
                          {{ $todayAttendance->formatted_morning_hours }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Afternoon Session -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg sm:rounded-xl p-3 sm:p-4">
                  <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-lg sm:text-xl">wb_twilight</span>
                      <h4 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Afternoon</h4>
                    </div>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">In ≤ 3:00 · Out 5:00–6:00</span>
                  </div>
                  <div class="space-y-1.5 sm:space-y-2">
                    <!-- Afternoon Check-In -->
                    <div class="flex items-center justify-between">
                      <span class="text-xs text-gray-500 dark:text-gray-400">Check-In:</span>
                      <div class="flex items-center gap-1.5">
                        @if($aIn)
                          <span class="text-xs sm:text-sm font-semibold {{ $aInLate ? $badText : $okText }}">
                            {{ $aIn->format('h:i A') }}
                          </span>
                          <span class="time-badge {{ $aInLate ? $lateBadge : $okBadge }}">
                            {{ $aInLate ? 'Late' : 'On Time' }}
                          </span>
                        @else
                          <span class="text-xs sm:text-sm font-semibold text-gray-400">—</span>
                        @endif
                      </div>
                    </div>

                    <!-- Afternoon Check-Out -->
                    <div class="flex items-center justify-between">
                      <span class="text-xs text-gray-500 dark:text-gray-400">Check-Out:</span>
                      <div class="flex items-center gap-1.5">
                        @if($aOut)
                          @php
                            $aOutTextColor = $aOutEarly ? $badText : ($aOutLate ? $aLateOutText : $okText);
                            $aOutBadgeStyle = $aOutEarly ? $lateBadge : ($aOutLate ? $aLateOutBadge : $okBadge);
                            $aOutLabel = $aOutEarly ? 'Early' : ($aOutLate ? 'Late' : 'On Time');
                          @endphp
                          <span class="text-xs sm:text-sm font-semibold {{ $aOutTextColor }}">
                            {{ $aOut->format('h:i A') }}
                          </span>
                          <span class="time-badge {{ $aOutBadgeStyle }}">{{ $aOutLabel }}</span>
                        @else
                          <span class="text-xs sm:text-sm font-semibold text-gray-400">—</span>
                        @endif
                      </div>
                    </div>

                    <div class="pt-1.5 sm:pt-2 border-t border-gray-200 dark:border-gray-700">
                      <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Hours:</span>
                        <span class="text-xs sm:text-sm font-bold text-blue-600 dark:text-blue-400">
                          {{ $todayAttendance->formatted_afternoon_hours }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Work Hours -->
              <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg sm:rounded-xl p-3 sm:p-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-lg sm:text-xl">timer</span>
                    <span class="text-xs sm:text-sm font-semibold text-blue-900 dark:text-blue-100">Total Hours</span>
                  </div>
                  <span class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $todayAttendance->formatted_work_hours ?? '—' }}
                  </span>
                </div>
              </div>

              <!-- Violation summary (only if any) -->
              @if($mInLate || $mOutEarly || $mOutLate || $aInLate || $aOutEarly || $aOutLate)
                <div class="mt-3 sm:mt-4 p-3 sm:p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg sm:rounded-xl">
                  <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-lg sm:text-xl shrink-0">warning</span>
                    <div class="flex-1">
                      <p class="text-xs sm:text-sm font-semibold text-amber-900 dark:text-amber-100 mb-1">Time Rule Violations Today</p>
                      <ul class="text-[11px] sm:text-xs text-amber-700 dark:text-amber-300 space-y-0.5 list-disc list-inside">
                        @if($mInLate)    <li>Morning Check-In after 09:00 AM</li>            @endif
                        @if($mOutEarly)  <li>Morning Check-Out before 11:00 AM</li>          @endif
                        @if($mOutLate)   <li>Morning Check-Out after 12:00 PM</li>           @endif
                        @if($aInLate)    <li>Afternoon Check-In after 03:00 PM</li>          @endif
                        @if($aOutEarly)  <li>Afternoon Check-Out before 05:00 PM</li>        @endif
                        @if($aOutLate)   <li>Afternoon Check-Out after 06:00 PM</li>         @endif
                      </ul>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          @endif

          <!-- Instructions Card -->
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg sm:rounded-xl p-4 sm:p-6 mt-4 sm:mt-6">
            <div class="flex items-start gap-2 sm:gap-3">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-xl sm:text-2xl shrink-0">info</span>
              <div>
                <h3 class="text-sm sm:text-base font-bold text-blue-900 dark:text-blue-100 mb-2">How to Check-In/Out</h3>
                <ol class="text-xs sm:text-sm text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside mb-3">
                  <li>Scan QR code or upload image</li>
                  <li>Verify your location</li>
                  <li>Confirm within allowed area</li>
                  <li>Complete check-in/out</li>
                </ol>

                <!-- Allowed time windows reference -->
                <div class="pt-3 border-t border-blue-200 dark:border-blue-800">
                  <p class="text-xs sm:text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1.5">Allowed Time Windows</p>
                  <ul class="text-[11px] sm:text-xs text-blue-700 dark:text-blue-300 space-y-0.5">
                    <li>🌞 Morning Check-In — on or before <span class="font-semibold">09:00 AM</span></li>
                    <li>🌞 Morning Check-Out — <span class="font-semibold">11:00 AM – 12:00 PM</span></li>
                    <li>🌅 Afternoon Check-In — on or before <span class="font-semibold">03:00 PM</span></li>
                    <li>🌅 Afternoon Check-Out — <span class="font-semibold">05:00 PM – 06:00 PM</span></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
@livewireScripts
  <script>
      // ================== SOUND MANAGER ==================
      const SoundManager = {
        STORAGE_KEY: 'attendify_sound_muted',
        isMuted: false,
        unlocked: false,
        sounds: {},

        init() {
          this.isMuted = localStorage.getItem(this.STORAGE_KEY) === 'true';

          this.sounds = {
            checkin:       new Audio("{{ asset('sounds/scan-checkin.mp3') }}"),
            checkout:      new Audio("{{ asset('sounds/scan-checkout.mp3') }}"),
            errorCheckin:  new Audio("{{ asset('sounds/error-scan-checkin.mp3') }}"),
            errorCheckout: new Audio("{{ asset('sounds/error-scan-checkout.mp3') }}"),
          };
          Object.values(this.sounds).forEach(a => { a.preload = 'auto'; a.volume = 0.8; });

          this.updateUI();

          const unlock = () => {
            if (this.unlocked) return;
            this.unlocked = true;
            Object.values(this.sounds).forEach(a => {
              a.play().then(() => { a.pause(); a.currentTime = 0; }).catch(() => {});
            });
            document.removeEventListener('click', unlock);
            document.removeEventListener('touchstart', unlock);
          };
          document.addEventListener('click', unlock);
          document.addEventListener('touchstart', unlock);
        },

        play(name) {
          if (this.isMuted) return;
          const audio = this.sounds[name];
          if (!audio) return;
          try {
            audio.currentTime = 0;
            audio.play().catch(err => console.log('Audio play blocked:', err));
          } catch (e) {
            console.log('Audio error:', e);
          }
        },

        playFromMessage(message, isError = false) {
          if (!message) return;
          const lower = message.toLowerCase();
          const isCheckout = lower.includes('check-out') || lower.includes('check out') || lower.includes('checkout');

          if (isError) {
            this.play(isCheckout ? 'errorCheckout' : 'errorCheckin');
          } else {
            this.play(isCheckout ? 'checkout' : 'checkin');
          }
        },

        toggleMute() {
          this.isMuted = !this.isMuted;
          localStorage.setItem(this.STORAGE_KEY, this.isMuted);
          this.updateUI();
          if (!this.isMuted) {
            this.play('checkin');
          }
        },

        updateUI() {
          const btn = document.getElementById('sound-toggle');
          const icon = document.getElementById('sound-icon');
          const label = document.getElementById('sound-label');
          if (!btn || !icon) return;

          if (this.isMuted) {
            btn.classList.add('muted');
            icon.textContent = 'volume_off';
            if (label) label.textContent = 'Sound Off';
            btn.setAttribute('aria-label', 'Unmute notification sounds');
            btn.setAttribute('title', 'Sounds are muted — tap to enable');
          } else {
            btn.classList.remove('muted');
            icon.textContent = 'volume_up';
            if (label) label.textContent = 'Sound On';
            btn.setAttribute('aria-label', 'Mute notification sounds');
            btn.setAttribute('title', 'Tap to mute notification sounds');
          }
        }
      };

      document.addEventListener('DOMContentLoaded', () => {
        SoundManager.init();

        const flash = document.getElementById('flash-data');
        if (flash) {
          const success = flash.dataset.success;
          const error   = flash.dataset.error;

          if (success) {
            setTimeout(() => SoundManager.playFromMessage(success, false), 250);
          } else if (error) {
            setTimeout(() => SoundManager.playFromMessage(error, true), 250);
          }
        }
      });

      // ================== Mobile Menu & Profile Dropdown ==================
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

      // ================== Tab Switching ==================
      function switchTab(tab) {
        const scanTab = document.getElementById('scan-tab');
        const uploadTab = document.getElementById('upload-tab');
        const tabScanBtn = document.getElementById('tab-scan');
        const tabUploadBtn = document.getElementById('tab-upload');

        if (tab === 'scan') {
          scanTab.classList.remove('hidden');
          uploadTab.classList.add('hidden');
          tabScanBtn.classList.add('active');
          tabUploadBtn.classList.remove('active');
        } else {
          uploadTab.classList.remove('hidden');
          scanTab.classList.add('hidden');
          tabUploadBtn.classList.add('active');
          tabScanBtn.classList.remove('active');

          if (html5QrCode) {
            stopScanner();
          }
        }
      }

      // ================== QR Scanner ==================
      let html5QrCode = null;

      function startScanner() {
        const qrReader = document.getElementById('qr-reader');
        const scannerStatus = document.getElementById('scanner-status');
        const stopBtn = document.getElementById('stop-scan-btn');

        qrReader.classList.remove('hidden');
        scannerStatus.classList.add('hidden');
        stopBtn.classList.remove('hidden');

        html5QrCode = new Html5Qrcode("qr-reader");

        const config = {
          fps: 10,
          qrbox: function(viewfinderWidth, viewfinderHeight) {
            const minDimension = Math.min(viewfinderWidth, viewfinderHeight);
            const qrboxSize = Math.floor(minDimension * 0.7);
            return {
              width: Math.min(qrboxSize, 250),
              height: Math.min(qrboxSize, 250)
            };
          },
          aspectRatio: 1.0
        };

        html5QrCode.start(
          { facingMode: "environment" },
          config,
          (decodedText) => { handleQRCode(decodedText); },
          () => { /* silent */ }
        ).catch((err) => {
          console.error('Error starting scanner:', err);
          alert('Unable to start camera. Please check camera permissions.');
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

      function handleQRCode(qrData) {
        const attendancePattern = /attendance/i;
        const missionPattern = /mission/i;

        let targetRoute = null;
        let qrType = '';

        if (attendancePattern.test(qrData)) {
          targetRoute = `{{ route('attendance.verify') }}?qr=${encodeURIComponent(qrData)}`;
          qrType = 'Attendance';
        } else if (missionPattern.test(qrData)) {
          targetRoute = `{{ route('attendance.mission') }}?qr=${encodeURIComponent(qrData)}`;
          qrType = 'Mission';
        } else {
          showInvalidQRError();
          stopScanner();
          return;
        }

        document.getElementById('scanned-data').classList.remove('hidden');
        document.getElementById('scanned-data').innerHTML = `
          <div class="flex items-start gap-2 sm:gap-3">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg sm:text-xl">check_circle</span>
            <div class="flex-1">
              <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-100 mb-1">${qrType} QR Detected!</p>
              <p class="text-xs text-green-700 dark:text-green-300">Redirecting...</p>
            </div>
          </div>
        `;

        stopScanner();

        setTimeout(() => {
          window.location.href = targetRoute;
        }, 1500);
      }

      function showInvalidQRError() {
        const scannedData = document.getElementById('scanned-data');
        scannedData.classList.remove('hidden');
        scannedData.className = 'mt-3 sm:mt-4 p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg sm:rounded-xl';
        scannedData.innerHTML = `
          <div class="flex items-start gap-2 sm:gap-3">
            <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-lg sm:text-xl">error</span>
            <div class="flex-1">
              <p class="text-xs sm:text-sm font-semibold text-red-900 dark:text-red-100 mb-1">Invalid QR Code</p>
              <p class="text-xs text-red-700 dark:text-red-300">Not valid for attendance or mission. Please scan correct QR.</p>
            </div>
          </div>
        `;

        setTimeout(() => {
          scannedData.classList.add('hidden');
          scannedData.className = 'hidden mt-3 sm:mt-4 p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg sm:rounded-xl';
        }, 3000);
      }

      // ================== Upload & Decode ==================
      let uploadedFile = null;

      function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('upload-area').classList.add('dragover');
      }

      function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('upload-area').classList.remove('dragover');
      }

      function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('upload-area').classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
          handleFile(files[0]);
        }
      }

      function handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
          handleFile(file);
        }
      }

      function handleFile(file) {
        if (!file.type.startsWith('image/')) {
          alert('Please upload an image file');
          return;
        }

        uploadedFile = file;

        const reader = new FileReader();
        reader.onload = (e) => {
          document.getElementById('uploaded-image').src = e.target.result;
          document.getElementById('upload-prompt').classList.add('hidden');
          document.getElementById('upload-preview').classList.remove('hidden');
          document.getElementById('clear-upload-btn').classList.remove('hidden');

          decodeUploadedQR();
        };
        reader.readAsDataURL(file);
      }

      function decodeUploadedQR() {
        const img = document.getElementById('uploaded-image');

        document.getElementById('decode-processing').classList.remove('hidden');
        document.getElementById('decode-success').classList.add('hidden');
        document.getElementById('decode-error').classList.add('hidden');

        img.onload = () => {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');

          canvas.width = img.naturalWidth;
          canvas.height = img.naturalHeight;
          ctx.drawImage(img, 0, 0);

          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
          const code = jsQR(imageData.data, imageData.width, imageData.height);

          document.getElementById('decode-processing').classList.add('hidden');

          if (code) {
            handleUploadedQRCode(code.data);
          } else {
            document.getElementById('decode-success').classList.add('hidden');
            document.getElementById('decode-error').classList.remove('hidden');
          }
        };
      }

      function handleUploadedQRCode(qrData) {
        const attendancePattern = /attendance/i;
        const missionPattern = /mission/i;

        let targetRoute = null;
        let qrType = '';

        if (attendancePattern.test(qrData)) {
          targetRoute = `{{ route('attendance.verify') }}?qr=${encodeURIComponent(qrData)}`;
          qrType = 'Attendance';
        } else if (missionPattern.test(qrData)) {
          targetRoute = `{{ route('attendance.mission') }}?qr=${encodeURIComponent(qrData)}`;
          qrType = 'Mission';
        } else {
          document.getElementById('decode-error').classList.remove('hidden');
          document.getElementById('decode-error').innerHTML = `
            <div class="flex items-start gap-2 sm:gap-3">
              <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-lg sm:text-xl">error</span>
              <div class="flex-1">
                <p class="text-xs sm:text-sm font-semibold text-red-900 dark:text-red-100 mb-1">Invalid QR Code</p>
                <p class="text-xs text-red-700 dark:text-red-300">Not valid for attendance or mission. Please upload correct QR.</p>
              </div>
            </div>
          `;
          return;
        }

        document.getElementById('decode-success').classList.remove('hidden');
        document.getElementById('decode-error').classList.add('hidden');
        document.getElementById('decode-success').innerHTML = `
          <div class="flex items-start gap-2 sm:gap-3">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-lg sm:text-xl">check_circle</span>
            <div class="flex-1">
              <p class="text-xs sm:text-sm font-semibold text-green-900 dark:text-green-100 mb-1">${qrType} QR Decoded!</p>
              <p class="text-xs text-green-700 dark:text-green-300">Redirecting...</p>
            </div>
          </div>
        `;

        setTimeout(() => {
          window.location.href = targetRoute;
        }, 1500);
      }

      function clearUpload() {
        uploadedFile = null;
        document.getElementById('qr-image-input').value = '';
        document.getElementById('upload-prompt').classList.remove('hidden');
        document.getElementById('upload-preview').classList.add('hidden');
        document.getElementById('clear-upload-btn').classList.add('hidden');
        document.getElementById('decode-processing').classList.add('hidden');
        document.getElementById('decode-success').classList.add('hidden');
        document.getElementById('decode-error').classList.add('hidden');
      }

      // Prevent zoom on double-tap for iOS
      let lastTouchEnd = 0;
      document.addEventListener('touchend', function (event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
          event.preventDefault();
        }
        lastTouchEnd = now;
      }, false);
    </script>
  </body>
</html>
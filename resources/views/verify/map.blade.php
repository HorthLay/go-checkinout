<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Verify Location - Attendify</title>
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
      
      #map {
        height: 450px;
        width: 100%;
        border-radius: 1rem;
        z-index: 1;
      }

      @media (max-width: 768px) {
        #map {
          height: 350px;
        }
        
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
      }

      .custom-div-icon {
        background: none;
        border: none;
      }

      .leaflet-popup-content-wrapper {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }

      .leaflet-popup-tip {
        box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1);
      }

      @keyframes pulse {
        0%, 100% {
          transform: scale(1);
          opacity: 1;
        }
        50% {
          transform: scale(1.1);
          opacity: 0.8;
        }
      }
    </style>
  </head>
  <body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display">
    <div class="min-h-screen py-6 px-4 flex items-center justify-center">
      <div class="w-full max-w-3xl">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center size-20 rounded-full bg-gradient-to-br from-primary to-blue-600 mb-4 shadow-lg shadow-primary/25">
            <span class="material-symbols-outlined text-white text-5xl">location_on</span>
          </div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Verify Your Location</h1>
          <p class="text-base text-gray-600 dark:text-gray-400 max-w-md mx-auto">Make sure you're within the allowed area to complete your attendance</p>
        </div>

        <!-- Current Session Info -->
        @php
            $currentHour = now()->format('H:i:s');
            $isMorningTime = $currentHour >= '07:00:00' && $currentHour < '12:00:00';
            $isAfternoonTime = $currentHour >= '13:00:00' && $currentHour < '18:00:00';
            
            $suggestedSession = $isMorningTime ? 'morning' : ($isAfternoonTime ? 'afternoon' : 'morning');
            $sessionIcon = $suggestedSession === 'morning' ? 'wb_sunny' : 'wb_twilight';
            $sessionColor = $suggestedSession === 'morning' ? 'yellow' : 'orange';
        @endphp

        <!-- Session Selector -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6 mb-6 shadow-lg">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">schedule</span>
            Select Session
          </h3>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Morning Session -->
            <label class="relative cursor-pointer">
              <input type="radio" name="session" value="morning" 
                     class="peer sr-only" 
                     {{ $suggestedSession === 'morning' ? 'checked' : '' }}
                     onchange="updateSessionUI('morning')">
              <div class="p-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/20 hover:border-yellow-300">
                <div class="flex items-center gap-3 mb-2">
                  <span class="material-symbols-outlined text-2xl text-yellow-600 dark:text-yellow-400">wb_sunny</span>
                  <span class="font-bold text-gray-900 dark:text-white">Morning Session</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">07:30 AM - 11:30 AM</p>
                @if($todayAttendance && $todayAttendance->morning_check_in)
                  <div class="mt-2 flex items-center gap-1 text-xs">
                    <span class="text-green-600 dark:text-green-400">✓ Checked In: {{ $todayAttendance->morning_check_in->format('h:i A') }}</span>
                  </div>
                @endif
                @if($todayAttendance && $todayAttendance->morning_check_out)
                  <div class="mt-1 flex items-center gap-1 text-xs">
                    <span class="text-blue-600 dark:text-blue-400">✓ Checked Out: {{ $todayAttendance->morning_check_out->format('h:i A') }}</span>
                  </div>
                @endif
              </div>
            </label>

            <!-- Afternoon Session -->
            <label class="relative cursor-pointer">
              <input type="radio" name="session" value="afternoon" 
                     class="peer sr-only"
                     {{ $suggestedSession === 'afternoon' ? 'checked' : '' }}
                     onchange="updateSessionUI('afternoon')">
              <div class="p-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 hover:border-orange-300">
                <div class="flex items-center gap-3 mb-2">
                  <span class="material-symbols-outlined text-2xl text-orange-600 dark:text-orange-400">wb_twilight</span>
                  <span class="font-bold text-gray-900 dark:text-white">Afternoon Session</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">02:00 PM - 05:30 PM</p>
                @if($todayAttendance && $todayAttendance->afternoon_check_in)
                  <div class="mt-2 flex items-center gap-1 text-xs">
                    <span class="text-green-600 dark:text-green-400">✓ Checked In: {{ $todayAttendance->afternoon_check_in->format('h:i A') }}</span>
                  </div>
                @endif
                @if($todayAttendance && $todayAttendance->afternoon_check_out)
                  <div class="mt-1 flex items-center gap-1 text-xs">
                    <span class="text-blue-600 dark:text-blue-400">✓ Checked Out: {{ $todayAttendance->afternoon_check_out->format('h:i A') }}</span>
                  </div>
                @endif
              </div>
            </label>
          </div>
        </div>

        <!-- Map Card -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-xl mb-6">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-primary/5 to-blue-600/5">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Location Verification</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Blue circle shows the allowed check-in zone</p>
              </div>
              <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">map</span>
              </div>
            </div>

            <!-- Map Controls -->
            <div class="flex flex-wrap gap-2">
              <button
                onclick="getUserLocation()"
                id="find-me-btn"
                class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-medium transition-colors shadow-md"
              >
                <span class="material-symbols-outlined text-lg">my_location</span>
                <span>Find Me</span>
              </button>

              <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm">
                <span class="material-symbols-outlined text-gray-500 text-lg">corporate_fare</span>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Office: {{ $officeLocation->name ?? 'Main Office' }}</span>
              </div>

              <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-sm">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-lg">straighten</span>
                <span class="text-blue-700 dark:text-blue-300 font-medium">Radius: {{ $officeLocation->radius ?? 100 }}m</span>
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <div id="map" class="mb-6 shadow-md"></div>

            <!-- Location Status -->
            <div id="location-loading" class="p-5 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-2 border-blue-200 dark:border-blue-800 rounded-xl">
              <div class="flex items-center gap-4">
                <div class="relative">
                  <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-3xl animate-spin">progress_activity</span>
                </div>
                <div>
                  <p class="text-base font-semibold text-blue-900 dark:text-blue-100 mb-1">Detecting Location...</p>
                  <p class="text-sm text-blue-700 dark:text-blue-300">Please wait while we verify your position</p>
                </div>
              </div>
            </div>

            <div id="location-success" class="hidden p-5 bg-gradient-to-r from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-800/20 border-2 border-green-200 dark:border-green-800 rounded-xl">
              <div class="flex items-start gap-4">
                <div class="size-12 rounded-full bg-green-500 dark:bg-green-600 flex items-center justify-center shrink-0 shadow-lg shadow-green-500/25">
                  <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                </div>
                <div class="flex-1">
                  <p class="text-base font-bold text-green-900 dark:text-green-100 mb-1">✓ Location Verified!</p>
                  <p class="text-sm text-green-700 dark:text-green-300 mb-2">You are within the authorized check-in area</p>
                  <div class="flex items-center gap-2 text-xs text-green-600 dark:text-green-400">
                    <span class="material-symbols-outlined text-base">straighten</span>
                    <span id="distance-text" class="font-medium"></span>
                  </div>
                </div>
              </div>
            </div>

            <div id="location-error" class="hidden p-5 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-2 border-red-200 dark:border-red-800 rounded-xl">
              <div class="flex items-start gap-4">
                <div class="size-12 rounded-full bg-red-500 dark:bg-red-600 flex items-center justify-center shrink-0 shadow-lg shadow-red-500/25">
                  <span class="material-symbols-outlined text-white text-2xl">error</span>
                </div>
                <div class="flex-1">
                  <p class="text-base font-bold text-red-900 dark:text-red-100 mb-1">Location Error</p>
                  <p class="text-sm text-red-700 dark:text-red-300 mb-2" id="error-message">Please enable location services in your browser settings</p>
                  <button
                    onclick="getUserLocation()"
                    class="mt-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors"
                  >
                    Try Again
                  </button>
                </div>
              </div>
            </div>

            <div id="location-outside" class="hidden p-5 bg-gradient-to-r from-orange-50 to-amber-100 dark:from-orange-900/20 dark:to-amber-800/20 border-2 border-orange-200 dark:border-orange-800 rounded-xl">
              <div class="flex items-start gap-4">
                <div class="size-12 rounded-full bg-orange-500 dark:bg-orange-600 flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/25">
                  <span class="material-symbols-outlined text-white text-2xl">warning</span>
                </div>
                <div class="flex-1">
                  <p class="text-base font-bold text-orange-900 dark:text-orange-100 mb-1">Outside Authorized Zone</p>
                  <p class="text-sm text-orange-700 dark:text-orange-300 mb-2">You must be within the designated area to complete check-in</p>
                  <div class="flex items-center gap-2 text-xs text-orange-600 dark:text-orange-400 mb-3">
                    <span class="material-symbols-outlined text-base">near_me</span>
                    <span id="distance-outside-text" class="font-medium"></span>
                  </div>
                  <button
                    onclick="getUserLocation()"
                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium transition-colors"
                  >
                    Refresh Location
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form id="attendance-form" method="POST" action="{{ route('attendance.submit') }}">
          @csrf
          <input type="hidden" name="latitude" id="latitude">
          <input type="hidden" name="longitude" id="longitude">
          <input type="hidden" name="action" id="action">
          <input type="hidden" name="session" id="session-input" value="{{ $suggestedSession }}">

          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6 mb-6 shadow-lg">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
              <span class="material-symbols-outlined text-lg text-gray-500">note</span>
              Note (Optional)
            </label>
            <textarea
              name="note"
              id="note"
              rows="3"
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none text-gray-900 dark:text-white placeholder-gray-400"
              placeholder="Add any additional notes about this attendance..."
            ></textarea>
          </div>

          <!-- Action Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="action-buttons">
            @php
                $canCheckInMorning = !$todayAttendance || !$todayAttendance->morning_check_in;
                $canCheckOutMorning = $todayAttendance && $todayAttendance->morning_check_in && !$todayAttendance->morning_check_out;
                $canCheckInAfternoon = !$todayAttendance || !$todayAttendance->afternoon_check_in;
                $canCheckOutAfternoon = $todayAttendance && $todayAttendance->afternoon_check_in && !$todayAttendance->afternoon_check_out;
            @endphp

            <button
              type="button"
              onclick="performCheckIn()"
              id="checkin-btn"
              disabled
              class="group relative px-8 py-5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl disabled:shadow-none flex items-center justify-center gap-3 text-lg overflow-hidden"
            >
              <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
              <span class="material-symbols-outlined text-3xl relative z-10">login</span>
              <span class="relative z-10" id="checkin-btn-text">Check In</span>
            </button>

            <button
              type="button"
              onclick="performCheckOut()"
              id="checkout-btn"
              disabled
              class="group relative px-8 py-5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl disabled:shadow-none flex items-center justify-center gap-3 text-lg overflow-hidden"
            >
              <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
              <span class="material-symbols-outlined text-3xl relative z-10">logout</span>
              <span class="relative z-10" id="checkout-btn-text">Check Out</span>
            </button>

            <a href="{{ route('checkin') }}"
              class="px-8 py-5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3 text-lg sm:col-span-2"
            >
              <span class="material-symbols-outlined text-3xl">arrow_back</span>
              <span>Back</span>
            </a>
          </div>
        </form>

        <!-- Info Footer -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-xl">
          <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-xl">info</span>
            <p class="text-xs text-blue-700 dark:text-blue-300">
              <strong>Privacy Note:</strong> Your location is only used to verify you're within the office area. Location data is not shared with third parties.
            </p>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Configuration - Get from backend
      const OFFICE_LAT = {{ $officeLocation->latitude ?? 10.635982 }};
      const OFFICE_LNG = {{ $officeLocation->longitude ?? 103.515688 }};
      const ALLOWED_RADIUS = {{ $officeLocation->radius ?? 20 }};
      const OFFICE_NAME = "{{ $officeLocation->name ?? 'Office' }}";

      // Session availability from backend
      const CAN_CHECKIN_MORNING = {{ $canCheckInMorning ? 'true' : 'false' }};
      const CAN_CHECKOUT_MORNING = {{ $canCheckOutMorning ? 'true' : 'false' }};
      const CAN_CHECKIN_AFTERNOON = {{ $canCheckInAfternoon ? 'true' : 'false' }};
      const CAN_CHECKOUT_AFTERNOON = {{ $canCheckOutAfternoon ? 'true' : 'false' }};

      let map, marker, circle, userLat, userLng;
      let currentSession = '{{ $suggestedSession }}';
      let locationVerified = false;

      // Initialize map
      function initMap() {
        map = L.map('map').setView([OFFICE_LAT, OFFICE_LNG], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
          maxZoom: 19
        }).addTo(map);

        // Add office location marker with custom popup
        const officeMarker = L.marker([OFFICE_LAT, OFFICE_LNG], {
          icon: L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: linear-gradient(135deg, #135bec 0%, #1e40af 100%); width: 36px; height: 36px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 10px rgba(19, 91, 236, 0.4); display: flex; align-items: center; justify-center;">
                     <span style="color: white; font-size: 20px;" class="material-symbols-outlined">corporate_fare</span>
                   </div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 18]
          })
        }).addTo(map);

        officeMarker.bindPopup(`
          <div style="padding: 8px; text-align: center;">
            <strong style="color: #135bec; font-size: 14px;">${OFFICE_NAME}</strong><br>
            <span style="color: #666; font-size: 12px;">Allowed radius: ${ALLOWED_RADIUS}m</span>
          </div>
        `);

        // Add allowed radius circle
        circle = L.circle([OFFICE_LAT, OFFICE_LNG], {
          color: '#135bec',
          fillColor: '#135bec',
          fillOpacity: 0.15,
          weight: 2,
          radius: ALLOWED_RADIUS
        }).addTo(map);

        // Get user location
        getUserLocation();
        
        // Update UI for initial session
        updateSessionUI(currentSession);
      }

      function updateSessionUI(session) {
        currentSession = session;
        document.getElementById('session-input').value = session;
        
        const checkinBtn = document.getElementById('checkin-btn');
        const checkoutBtn = document.getElementById('checkout-btn');
        const checkinText = document.getElementById('checkin-btn-text');
        const checkoutText = document.getElementById('checkout-btn-text');
        
        if (session === 'morning') {
          checkinText.textContent = '🌞 Morning Check In';
          checkoutText.textContent = '🌞 Morning Check Out';
          
          // Update button states based on session availability
          if (locationVerified) {
            checkinBtn.disabled = !CAN_CHECKIN_MORNING;
            checkoutBtn.disabled = !CAN_CHECKOUT_MORNING;
          }
        } else {
          checkinText.textContent = '🌅 Afternoon Check In';
          checkoutText.textContent = '🌅 Afternoon Check Out';
          
          if (locationVerified) {
            checkinBtn.disabled = !CAN_CHECKIN_AFTERNOON;
            checkoutBtn.disabled = !CAN_CHECKOUT_AFTERNOON;
          }
        }
      }

      function getUserLocation() {
        if (!navigator.geolocation) {
          showError('Geolocation is not supported by your browser');
          return;
        }

        // Show loading state
        document.getElementById('location-loading').classList.remove('hidden');
        document.getElementById('location-success').classList.add('hidden');
        document.getElementById('location-error').classList.add('hidden');
        document.getElementById('location-outside').classList.add('hidden');

        // Disable find me button
        const findMeBtn = document.getElementById('find-me-btn');
        findMeBtn.disabled = true;
        findMeBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span><span>Locating...</span>';

        navigator.geolocation.getCurrentPosition(
          (position) => {
            userLat = position.coords.latitude;
            userLng = position.coords.longitude;

            document.getElementById('latitude').value = userLat;
            document.getElementById('longitude').value = userLng;

            // Remove old marker if exists
            if (marker) {
              map.removeLayer(marker);
            }

            // Add user marker with custom popup
            marker = L.marker([userLat, userLng], {
              icon: L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.4);">
                         <div style="width: 10px; height: 10px; background: white; border-radius: 50%; margin: 6px auto;"></div>
                       </div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 14]
              })
            }).addTo(map);

            marker.bindPopup(`
              <div style="padding: 8px; text-align: center;">
                <strong style="color: #22c55e; font-size: 14px;">Your Location</strong><br>
                <span style="color: #666; font-size: 11px;">${userLat.toFixed(6)}, ${userLng.toFixed(6)}</span>
              </div>
            `);

            // Update map view to show both locations
            const bounds = L.latLngBounds([[OFFICE_LAT, OFFICE_LNG], [userLat, userLng]]);
            map.fitBounds(bounds, { padding: [80, 80], maxZoom: 17 });

            // Check distance
            checkDistance();

            // Re-enable find me button
            findMeBtn.disabled = false;
            findMeBtn.innerHTML = '<span class="material-symbols-outlined text-lg">my_location</span><span>Find Me</span>';
          },
          (error) => {
            let errorMsg = 'Unable to get your location';
            if (error.code === error.PERMISSION_DENIED) {
              errorMsg = 'Location permission denied. Please enable location services in your browser settings.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
              errorMsg = 'Location information is currently unavailable. Please try again.';
            } else if (error.code === error.TIMEOUT) {
              errorMsg = 'Location request timed out. Please try again.';
            }
            showError(errorMsg);

            // Re-enable find me button
            findMeBtn.disabled = false;
            findMeBtn.innerHTML = '<span class="material-symbols-outlined text-lg">my_location</span><span>Find Me</span>';
          },
          {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
          }
        );
      }

      function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth's radius in meters
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c; // Distance in meters
      }

      function checkDistance() {
        const distance = calculateDistance(userLat, userLng, OFFICE_LAT, OFFICE_LNG);
        
        document.getElementById('location-loading').classList.add('hidden');

        if (distance <= ALLOWED_RADIUS) {
          locationVerified = true;
          document.getElementById('location-success').classList.remove('hidden');
          document.getElementById('distance-text').textContent = `Distance: ${Math.round(distance)}m from office`;
          
          // Update button states based on current session
          updateSessionUI(currentSession);
        } else {
          locationVerified = false;
          document.getElementById('location-outside').classList.remove('hidden');
          document.getElementById('distance-outside-text').textContent = `You are ${Math.round(distance)}m away (${Math.round(distance - ALLOWED_RADIUS)}m outside allowed area)`;
          
          // Disable both buttons
          document.getElementById('checkin-btn').disabled = true;
          document.getElementById('checkout-btn').disabled = true;
        }
      }

      function showError(message) {
        locationVerified = false;
        document.getElementById('location-loading').classList.add('hidden');
        document.getElementById('location-error').classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
        
        // Disable both buttons
        document.getElementById('checkin-btn').disabled = true;
        document.getElementById('checkout-btn').disabled = true;
      }

      function performCheckIn() {
        document.getElementById('action').value = 'checkin';
        document.getElementById('attendance-form').submit();
      }

      function performCheckOut() {
        document.getElementById('action').value = 'checkout';
        document.getElementById('attendance-form').submit();
      }

      // Initialize map when page loads
      document.addEventListener('DOMContentLoaded', initMap);
    </script>
  </body>
</html>
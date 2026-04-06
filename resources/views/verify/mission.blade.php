<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" name="viewport" />
    <title>Mission Attendance - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
    
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#135bec",
              "primary-dark": "#0f4bc0",
            },
            fontFamily: {
              display: ["Inter", "Noto Sans Khmer", "sans-serif"],
            },
          },
        },
      };
    </script>
    <style>
      body {
        font-family: "Inter", "Noto Sans Khmer", sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }

      /* Mobile-first touch targets */
      button, a, .clickable {
        min-height: 44px;
        min-width: 44px;
        touch-action: manipulation;
      }

      /* Map container responsive */
      #map {
        width: 100%;
        touch-action: pan-x pan-y;
      }

      /* Custom marker style */
      .custom-marker {
        background: transparent;
        border: none;
      }

      /* Leaflet popup mobile optimization */
      .leaflet-popup-content-wrapper {
        border-radius: 0.75rem;
      }

      .leaflet-popup-content {
        margin: 0.75rem;
      }

      /* Prevent zoom on input focus for iOS */
      @media (max-width: 640px) {
        input, select, textarea {
          font-size: 16px;
        }
      }

      /* Loading animation */
      @keyframes pulse-ring {
        0% {
          transform: scale(0.95);
          opacity: 1;
        }
        50% {
          transform: scale(1.05);
          opacity: 0.5;
        }
        100% {
          transform: scale(0.95);
          opacity: 1;
        }
      }

      .animate-pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      }

      /* Improve readability on mobile */
      @media (max-width: 640px) {
        .text-balance {
          text-wrap: balance;
        }
      }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 md:py-8">
        <!-- Header - Mobile Optimized -->
        <div class="text-center mb-4 sm:mb-6 md:mb-8">
            <div class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 px-4 sm:px-6 py-2 sm:py-3 rounded-full shadow-lg mb-3 sm:mb-4">
                <span class="material-symbols-outlined text-blue-500 text-xl sm:text-2xl">location_on</span>
                <span class="font-bold text-base sm:text-xl text-gray-900 dark:text-white">Attendify</span>
            </div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2 px-4">Mission Check-In</h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Checking in from field location</p>
        </div>

        <!-- Alert Info - Mobile Optimized -->
        <div class="max-w-2xl mx-auto mb-4 sm:mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg sm:rounded-xl p-3 sm:p-4">
                <div class="flex gap-2 sm:gap-3">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-lg sm:text-xl mt-0.5 shrink-0">info</span>
                    <div>
                        <h3 class="font-semibold text-sm sm:text-base text-blue-900 dark:text-blue-100 mb-1">Mission Mode</h3>
                        <p class="text-xs sm:text-sm text-blue-700 dark:text-blue-300 leading-relaxed">
                            You're checking in from a field location. Your location will be recorded and tracked for the entire mission duration. Admin approval required.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form - Mobile Optimized -->
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8">
            <form action="{{ route('attendance.mission.store') }}" method="POST" id="mission-form">
                @csrf

                <!-- Location Info with Map -->
                <div class="mb-4 sm:mb-6">
                    <!-- Location Status -->
                    <div class="p-3 sm:p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-t-lg sm:rounded-t-xl border-x border-t border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-2 sm:gap-3">
                            <div class="p-1.5 sm:p-2 bg-blue-100 dark:bg-blue-800 rounded-lg shrink-0">
                                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-xl sm:text-2xl">my_location</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm sm:text-base text-blue-900 dark:text-blue-100 mb-2">Your Current Location</h4>
                                <div id="location-status" class="text-xs sm:text-sm text-blue-700 dark:text-blue-300 mb-2">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="animate-pulse text-base sm:text-lg">●</span>
                                        <span>Detecting location...</span>
                                    </span>
                                </div>
                                <div id="location-coords" class="hidden bg-white dark:bg-gray-900 rounded-lg p-2 sm:p-3 space-y-1">
                                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-sm shrink-0">place</span>
                                        <span class="font-medium">Lat:</span>
                                        <span class="font-mono truncate" id="lat-display"></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-sm shrink-0">place</span>
                                        <span class="font-medium">Lng:</span>
                                        <span class="font-mono truncate" id="lng-display"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map View - Responsive Height -->
                    <div id="map" class="h-48 sm:h-56 md:h-64 lg:h-80 rounded-b-lg sm:rounded-b-xl border-x border-b border-blue-200 dark:border-blue-800 bg-gray-100 dark:bg-gray-800"></div>
                </div>

                <!-- Hidden Location Fields -->
                <input type="hidden" name="latitude" id="latitude" />
                <input type="hidden" name="longitude" id="longitude" />

                <!-- Info Box - Mobile Optimized -->
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg sm:rounded-xl border border-purple-200 dark:border-purple-800">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-lg sm:text-xl shrink-0">schedule</span>
                        <div class="text-xs sm:text-sm text-purple-700 dark:text-purple-300">
                            <p class="font-semibold mb-1">All-Day Tracking</p>
                            <p class="leading-relaxed">Mission attendance tracks your total work hours for the entire day, without separate morning/afternoon sessions.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button - Mobile Optimized -->
                <button 
                    type="submit"
                    id="submit-btn"
                    disabled
                    class="w-full flex items-center justify-center gap-2 px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-lg sm:rounded-xl font-semibold transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:from-gray-400 disabled:to-gray-500 active:scale-95"
                >
                    <span class="material-symbols-outlined text-xl sm:text-2xl">send</span>
                    <span class="text-base sm:text-lg">Check-In to Mission</span>
                </button>

                <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-3 sm:mt-4 px-2">
                    ⏳ Pending admin approval • 📍 Location will be recorded
                </p>
            </form>
        </div>

        <!-- Additional Info - Mobile Optimized -->
        <div class="max-w-2xl mx-auto mt-4 sm:mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
                <h4 class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white mb-2 sm:mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-400 text-lg sm:text-xl">help</span>
                    <span>What happens next?</span>
                </h4>
                <div class="space-y-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start gap-2">
                        <span class="text-blue-500 font-semibold shrink-0 mt-0.5">1.</span>
                        <p class="leading-relaxed">Your location is captured and mission attendance is created</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-500 font-semibold shrink-0 mt-0.5">2.</span>
                        <p class="leading-relaxed">Status shows as "Pending Approval" until admin reviews</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-500 font-semibold shrink-0 mt-0.5">3.</span>
                        <p class="leading-relaxed">Once approved, your total work hours will be calculated for the day</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-500 font-semibold shrink-0 mt-0.5">4.</span>
                        <p class="leading-relaxed">Check-out will be automatic at end of day or you can manually check-out</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Link - Mobile Optimized -->
        <div class="text-center mt-4 sm:mt-6">
            <a href="{{ url('/checkin') }}" class="inline-flex items-center gap-2 text-sm sm:text-base text-gray-600 dark:text-gray-400 hover:text-primary transition-colors px-4 py-2 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50">
                <span class="material-symbols-outlined text-lg sm:text-xl">arrow_back</span>
                <span>Back to Check-In</span>
            </a>
        </div>
    </div>

    <script>
        // Initialize map
        let map = null;
        let marker = null;
        let accuracyCircle = null;

        function initMap() {
            // Initialize map centered on default location
            map = L.map('map', {
                center: [11.5564, 104.9282], // Default: Phnom Penh
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: true,
                touchZoom: true,
                dragging: true,
                tap: true
            });

            // Position zoom controls for mobile
            if (window.innerWidth < 640) {
                map.zoomControl.setPosition('bottomright');
            }

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19,
                minZoom: 3
            }).addTo(map);

            // Add a placeholder marker
            marker = L.marker([11.5564, 104.9282], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background: #135bec; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                })
            }).addTo(map);

            // Invalidate size for proper rendering
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }

        // Update map with user location
        function updateMapLocation(lat, lng, accuracy = 50) {
            if (map && marker) {
                const latlng = [lat, lng];
                
                // Update marker position
                marker.setLatLng(latlng);
                
                // Center map on location with appropriate zoom
                const zoom = window.innerWidth < 640 ? 15 : 16;
                map.setView(latlng, zoom);

                // Remove old accuracy circle if exists
                if (accuracyCircle) {
                    map.removeLayer(accuracyCircle);
                }

                // Add circle to show accuracy
                accuracyCircle = L.circle(latlng, {
                    color: '#135bec',
                    fillColor: '#135bec',
                    fillOpacity: 0.15,
                    weight: 2,
                    radius: Math.max(accuracy, 30) // Use actual accuracy or minimum 30m
                }).addTo(map);

                // Add popup with responsive content
                const isMobile = window.innerWidth < 640;
                marker.bindPopup(`
                    <div class="text-center" style="min-width: ${isMobile ? '120px' : '160px'}">
                        <p class="font-semibold text-xs sm:text-sm mb-1">📍 Your Location</p>
                        <p class="text-xs text-gray-600">${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                        ${accuracy ? `<p class="text-xs text-gray-500 mt-1">±${Math.round(accuracy)}m</p>` : ''}
                    </div>
                `, {
                    closeButton: !isMobile,
                    autoClose: false,
                    className: 'location-popup'
                }).openPopup();
            }
        }

        // Get user's location
        function getLocation() {
            const statusEl = document.getElementById('location-status');
            
            if (!navigator.geolocation) {
                statusEl.innerHTML = `
                    <span class="inline-flex items-center gap-2 text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <span class="text-xs sm:text-sm">Geolocation not supported</span>
                    </span>
                `;
                return;
            }

            // Show loading state
            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-2">
                    <span class="animate-pulse-ring text-blue-600 dark:text-blue-400 text-base sm:text-lg">●</span>
                    <span class="text-xs sm:text-sm">Getting your location...</span>
                </span>
            `;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    
                    // Update hidden fields
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    
                    // Update display
                    document.getElementById('lat-display').textContent = lat.toFixed(6);
                    document.getElementById('lng-display').textContent = lng.toFixed(6);
                    
                    // Update status
                    statusEl.innerHTML = `
                        <span class="inline-flex items-center gap-2 text-green-600 dark:text-green-400">
                            <span class="material-symbols-outlined text-base sm:text-lg">check_circle</span>
                            <span class="font-semibold text-xs sm:text-sm">Location detected!</span>
                        </span>
                    `;
                    document.getElementById('location-coords').classList.remove('hidden');
                    
                    // Update map
                    updateMapLocation(lat, lng, accuracy);
                    
                    // Enable submit button with animation
                    const submitBtn = document.getElementById('submit-btn');
                    submitBtn.disabled = false;
                    submitBtn.classList.add('animate-pulse');
                    setTimeout(() => {
                        submitBtn.classList.remove('animate-pulse');
                    }, 1000);
                },
                (error) => {
                    let errorMsg = 'Failed to get location. ';
                    let errorDetail = '';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = 'Location permission denied';
                            errorDetail = 'Please enable location in your browser settings';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = 'Location unavailable';
                            errorDetail = 'Please check your GPS/network';
                            break;
                        case error.TIMEOUT:
                            errorMsg = 'Location request timeout';
                            errorDetail = 'Please try again';
                            break;
                        default:
                            errorMsg = 'Unknown error';
                            errorDetail = 'Please try again or contact support';
                    }
                    
                    statusEl.innerHTML = `
                        <div>
                            <span class="inline-flex items-center gap-2 text-red-600 dark:text-red-400 mb-1">
                                <span class="material-symbols-outlined text-base sm:text-lg">error</span>
                                <span class="font-semibold text-xs sm:text-sm">${errorMsg}</span>
                            </span>
                            ${errorDetail ? `<p class="text-xs text-red-500 dark:text-red-400 ml-6">${errorDetail}</p>` : ''}
                        </div>
                    `;
                    console.error('Location error:', error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }

        // Initialize on page load
        window.addEventListener('load', function() {
            initMap();
            // Small delay to ensure map is ready
            setTimeout(() => {
                getLocation();
            }, 300);
        });

        // Handle orientation change on mobile
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                }
            }, 200);
        });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                }
            }, 200);
        });

        // Form validation
        document.getElementById('mission-form').addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            
            if (!lat || !lng) {
                e.preventDefault();
                alert('Please wait for location to be detected');
                return false;
            }

            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="material-symbols-outlined text-xl sm:text-2xl animate-spin">progress_activity</span>
                <span class="text-base sm:text-lg">Submitting...</span>
            `;
        });

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
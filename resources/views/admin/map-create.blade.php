<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Create Location - Attendify</title>
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

      #map {
        height: 450px;
        width: 100%;
        border-radius: 1rem;
        cursor: crosshair;
      }

      .custom-div-icon {
        background: none;
        border: none;
      }

      @media (max-width: 768px) {
        #map {
          height: 350px;
        }
      }
    </style>
</head>
  <body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display">
    <div class="min-h-screen py-6 px-4">
      <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          
         <a   href="{{ route('map.created') }}"
            class="inline-flex items-center gap-2 text-primary hover:text-primary-dark mb-4 transition-colors"
          >
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="text-sm font-medium">Back to Locations</span>
          </a>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">Create New Location</h1>
          <p class="text-gray-600 dark:text-gray-400">Set up a new office location with check-in area</p>
        </div>

        <form action="{{ route('map.store') }}" method="POST">
          @csrf

          <!-- Location Details Card -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6 mb-6 shadow-lg">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Location Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Location Name -->
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Location Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  name="name"
                  value="{{ old('name') }}"
                  required
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-gray-900 dark:text-white"
                  placeholder="e.g., Main Office, Branch Office"
                />
                @error('name')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Address -->
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Address
                </label>
                <input
                  type="text"
                  name="address"
                  value="{{ old('address') }}"
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-gray-900 dark:text-white"
                  placeholder="Full address"
                />
                @error('address')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Description -->
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Description
                </label>
                <textarea
                  name="description"
                  rows="3"
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none text-gray-900 dark:text-white"
                  placeholder="Additional information about this location"
                >{{ old('description') }}</textarea>
                @error('description')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>
          </div>

          <!-- Map Configuration Card -->
          <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 p-6 mb-6 shadow-lg">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Map Configuration</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Click on the map to set the office location</p>

            <div id="map" class="mb-6"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Latitude -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Latitude <span class="text-red-500">*</span>
                </label>
                <input
                  type="number"
                  name="latitude"
                  id="latitude"
                  step="0.000001"
                  value="{{ old('latitude', '10.635982') }}"
                  required
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-gray-900 dark:text-white"
                  placeholder="0.000000"
                />
                @error('latitude')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Longitude -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Longitude <span class="text-red-500">*</span>
                </label>
                <input
                  type="number"
                  name="longitude"
                  id="longitude"
                  step="0.000001"
                  value="{{ old('longitude', '103.515688') }}"
                  required
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-gray-900 dark:text-white"
                  placeholder="0.000000"
                />
                @error('longitude')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Radius -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Radius (meters) <span class="text-red-500">*</span>
                </label>
                <input
                  type="number"
                  name="radius"
                  id="radius"
                  min="10"
                  max="1000"
                  value="{{ old('radius', '20') }}"
                  required
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-gray-900 dark:text-white"
                  placeholder="20"
                />
                @error('radius')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <!-- Active Status -->
            <div class="mt-6">
              <label class="flex items-center gap-3 cursor-pointer">
                <input
                  type="checkbox"
                  name="is_active"
                  value="1"
                  checked
                  class="w-5 h-5 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active (Allow check-ins at this location)</span>
              </label>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex flex-col sm:flex-row gap-4">
            <button
              type="submit"
              class="flex-1 px-8 py-4 bg-gradient-to-r from-primary to-blue-600 hover:from-primary-dark hover:to-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined">save</span>
              <span>Create Location</span>
            </button>

            
           <a  href="{{ route('map.created') }}"
              class="px-8 py-4 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl font-bold transition-all flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined">cancel</span>
              <span>Cancel</span>
            </a>
          </div>
        </form>
      </div>
    </div>

    <script>
      let map, marker, circle;
      const latInput = document.getElementById('latitude');
      const lngInput = document.getElementById('longitude');
      const radiusInput = document.getElementById('radius');

      // Initialize map
      function initMap() {
        const initialLat = parseFloat(latInput.value) || 10.635982;
        const initialLng = parseFloat(lngInput.value) || 103.515688;
        const initialRadius = parseInt(radiusInput.value) || 20;

        map = L.map('map').setView([initialLat, initialLng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
          maxZoom: 19
        }).addTo(map);

        // Add initial marker
        updateMarker(initialLat, initialLng, initialRadius);

        // Click to set location
        map.on('click', function(e) {
          const lat = e.latlng.lat;
          const lng = e.latlng.lng;
          const radius = parseInt(radiusInput.value) || 20;

          latInput.value = lat.toFixed(6);
          lngInput.value = lng.toFixed(6);

          updateMarker(lat, lng, radius);
        });

        // Update circle when radius changes
        radiusInput.addEventListener('input', function() {
          const lat = parseFloat(latInput.value);
          const lng = parseFloat(lngInput.value);
          const radius = parseInt(this.value) || 20;

          updateMarker(lat, lng, radius);
        });
      }

      function updateMarker(lat, lng, radius) {
        // Remove existing marker and circle
        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);

        // Add new marker
        marker = L.marker([lat, lng], {
          icon: L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: linear-gradient(135deg, #135bec 0%, #1e40af 100%); width: 36px; height: 36px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 10px rgba(19, 91, 236, 0.4); display: flex; align-items: center; justify-center;">
                     <span style="color: white; font-size: 20px;" class="material-symbols-outlined">corporate_fare</span>
                   </div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 18]
          })
        }).addTo(map);

        // Add radius circle
        circle = L.circle([lat, lng], {
          color: '#135bec',
          fillColor: '#135bec',
          fillOpacity: 0.15,
          weight: 2,
          radius: radius
        }).addTo(map);

        // Center map
        map.setView([lat, lng]);
      }

      // Initialize on load
      document.addEventListener('DOMContentLoaded', initMap);
    </script>
  </body>
</html>
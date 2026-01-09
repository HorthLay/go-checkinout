<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Attendance Tracker</title>
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
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

      #map {
        height: 100%;
        width: 100%;
        min-height: 400px;
      }

      /* Mobile responsive */
      @media (max-width: 1024px) {
        .main-layout-container {
          flex-direction: column !important;
        }
        #map {
          height: 45vh !important;
          min-height: 300px !important;
        }
        .nearby-aside {
          width: 100% !important;
          border-left: none !important;
          border-top: 1px solid rgba(0, 0, 0, 0.1);
          display: block !important;
          max-height: 40vh;
          overflow-y: auto;
        }
        .top-bar-section {
          flex-direction: column !important;
          gap: 0.75rem !important;
          align-items: stretch !important;
        }
        .top-bar-section h2 {
          font-size: 1.125rem !important;
        }
        .top-bar-section button {
          width: 100%;
          justify-content: center;
        }
      }

      @media (max-width: 768px) {
        #map {
          height: 40vh !important;
          min-height: 250px !important;
        }
        .nearby-aside {
          max-height: 45vh;
          padding: 1rem !important;
        }
        header h1 {
          font-size: 1rem !important;
        }
        header p {
          font-size: 0.625rem !important;
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

      /* Success animation */
      @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
      }
      .success-animate {
        animation: successPulse 0.5s ease-in-out;
      }

      /* Button styles */
      button {
        min-height: 44px;
        min-width: 44px;
      }

      /* Ensure proper scrolling on mobile */
      @media (max-width: 1024px) {
        main {
          overflow-y: auto !important;
        }
      }
    </style>
  </head>
  <body
    class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden"
  >
    <!-- Sidebar -->
    <aside
      class="w-64 bg-surface-light dark:bg-surface-dark border-r border-gray-200 dark:border-gray-800 flex-col hidden lg:flex z-20 shadow-sm"
    >
      <div
        class="h-20 flex items-center px-6 border-b border-gray-100 dark:border-gray-800 gap-3"
      >
        <div
          class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center"
        >
          <span class="material-symbols-outlined text-2xl">qr_code_scanner</span>
        </div>
        <div>
          <span class="font-bold text-lg tracking-tight block leading-none">Attendify</span>
          <span class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Portal</span>
        </div>
      </div>
      <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2">Menu</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group" href="#">
          <span class="material-symbols-outlined text-gray-500 group-hover:text-primary transition-colors">dashboard</span>
          <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-primary text-white shadow-md shadow-primary/25 transition-all" href="#">
          <span class="material-symbols-outlined">check_circle</span>
          <span class="font-medium">Check-In</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group" href="#">
          <span class="material-symbols-outlined text-gray-500 group-hover:text-primary transition-colors">how_to_reg</span>
          <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Attendance</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group" href="#">
          <span class="material-symbols-outlined">bar_chart</span>
          <span class="font-medium">Reports</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group" href="#">
          <span class="material-symbols-outlined text-gray-500 group-hover:text-primary transition-colors">group</span>
          <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Employees</span>
        </a>
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-8">System</p>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group" href="#">
          <span class="material-symbols-outlined text-gray-500 group-hover:text-primary transition-colors">settings</span>
          <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Settings</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group" href="#">
          <span class="material-symbols-outlined text-gray-500 group-hover:text-primary transition-colors">help</span>
          <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Support</span>
        </a>
      </nav>
      <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors" href="#">
          <span class="material-symbols-outlined">logout</span>
          <span class="font-medium">Sign Out</span>
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
      <!-- Header -->
      <header class="h-20 flex items-center justify-between px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10">
        <div class="flex items-center gap-4 lg:hidden">
          <button id="menu-toggle" aria-label="Toggle menu" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <span class="font-bold text-lg">Check-In</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Location Tracker</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Welcome back, Alex</p>
        </div>
        <div class="flex items-center gap-3 md:gap-4">
          <div class="relative hidden md:block group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-primary transition-colors">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-64 transition-all" placeholder="Search..." type="text" />
          </div>
          <button class="size-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 relative transition-colors">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2.5 right-2.5 size-2 bg-red-500 border-2 border-white dark:border-gray-900 rounded-full"></span>
          </button>
          <div class="relative">
            <button id="profile-toggle" aria-label="Toggle profile dropdown" class="flex items-center gap-3 pl-2 hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg transition-colors">
              <div class="size-10 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-bold shadow-sm">AM</div>
              <div class="hidden md:block">
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">Alex Morgan</p>
                <p class="text-[11px] text-gray-500 font-medium mt-1">Administrator</p>
              </div>
            </button>
            <div id="profile-dropdown" class="hidden absolute top-20 right-0 mt-2 w-64 bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 z-30">
              <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                <p class="text-sm font-bold text-gray-900 dark:text-white">Alex Morgan</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Administrator</p>
              </div>
              <div class="py-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all">
                  <span class="material-symbols-outlined">person</span>
                  <span>Profile</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all">
                  <span class="material-symbols-outlined">settings</span>
                  <span>Settings</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
                  <span class="material-symbols-outlined">logout</span>
                  <span>Sign Out</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Mobile Menu -->
      <div id="mobile-menu" class="lg:hidden hidden absolute top-20 left-0 right-0 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shadow-lg z-30">
        <nav class="py-4 px-6 space-y-2">
          <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all" href="#">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
          </a>
          <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-primary text-white" href="#">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="font-medium">Check-In</span>
          </a>
          <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all" href="#">
            <span class="material-symbols-outlined">how_to_reg</span>
            <span class="font-medium">Attendance</span>
          </a>
        </nav>
      </div>

      <!-- Main Content Area -->
      <main class="flex-1 flex flex-col h-full overflow-hidden">
        <div class="p-4 lg:p-6 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 flex justify-between items-center top-bar-section">
          <div>
            <h2 class="text-lg lg:text-xl font-bold">Location Tracker</h2>
            <p class="text-xs text-gray-500">Verify check-in zones</p>
          </div>
          <button onclick="getCurrentLocation()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined text-sm">my_location</span>
            <span class="font-bold text-sm">Find Me</span>
          </button>
        </div>

        <div class="flex-1 flex overflow-hidden main-layout-container">
          <div id="map" class="flex-1 z-0"></div>

          <aside class="w-80 bg-surface-light dark:bg-surface-dark border-l border-gray-100 dark:border-gray-800 overflow-y-auto hidden lg:block p-6 nearby-aside">
            <h3 class="font-bold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">location_searching</span>
              Nearby Check-ins
            </h3>
            <div class="space-y-3" id="location-list">
              <p class="text-sm text-gray-500">Click "Find Me" to see nearby zones.</p>
            </div>
          </aside>
        </div>
      </main>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white dark:bg-surface-dark rounded-2xl p-8 max-w-md mx-4 shadow-2xl success-animate">
        <div class="text-center">
          <div class="size-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-5xl text-green-600">check_circle</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Check-In Successful!</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-1" id="success-location"></p>
          <p class="text-sm text-gray-500" id="success-time"></p>
          <button onclick="closeSuccessModal()" class="mt-6 w-full px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-dark transition-all font-bold">
            Done
          </button>
        </div>
      </div>
    </div>

    <script>
      const checkInLocations = [
        { name: "Main Office", lat: 10.628899, lng: 103.510511, radius: 50 },
        { name: "Branch Office", lat: 10.630000, lng: 103.512000, radius: 50 },
      ];

      let map = L.map("map").setView([10.628899, 103.510511], 15);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
      }).addTo(map);

      let marker;
      let locationCircles = [];
      let checkedInLocations = {};

      function drawCheckInZones() {
        locationCircles.forEach((circle) => map.removeLayer(circle));
        locationCircles = [];

        checkInLocations.forEach((location) => {
          const circle = L.circle([location.lat, location.lng], {
            color: "#135bec",
            fillColor: "#135bec",
            fillOpacity: 0.15,
            radius: location.radius,
          }).addTo(map);

          circle.bindPopup(`<b>${location.name}</b><br>Zone: ${location.radius}m`);
          locationCircles.push(circle);
        });
      }

      drawCheckInZones();

      function checkIn(locationName) {
        const now = new Date();
        checkedInLocations[locationName] = now;
        
        document.getElementById('success-location').textContent = locationName;
        document.getElementById('success-time').textContent = now.toLocaleString();
        document.getElementById('success-modal').classList.remove('hidden');
        
        setTimeout(() => {
          getCurrentLocation();
        }, 100);
      }

      function closeSuccessModal() {
        document.getElementById('success-modal').classList.add('hidden');
      }

      function getCurrentLocation() {
        if ("geolocation" in navigator) {
          navigator.geolocation.getCurrentPosition((position) => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            map.setView([userLat, userLng], 16);

            if (marker) marker.setLatLng([userLat, userLng]);
            else {
              marker = L.marker([userLat, userLng], {
                icon: L.divIcon({
                  className: 'custom-marker',
                  html: '<div style="background: #135bec; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
                  iconSize: [20, 20]
                })
              }).addTo(map);
            }

            const list = document.getElementById("location-list");
            list.innerHTML = "";

            checkInLocations.forEach((loc) => {
              const distance = map.distance([userLat, userLng], [loc.lat, loc.lng]).toFixed(0);
              const inRange = distance <= loc.radius;
              const alreadyCheckedIn = checkedInLocations[loc.name];

              let statusBadge = '';
              let buttonHtml = '';
              
              if (alreadyCheckedIn) {
                statusBadge = `<div class="mt-2 flex items-center gap-1 text-xs text-green-600 font-bold">
                  <span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span>
                  Checked In
                </div>`;
              } else if (inRange) {
                statusBadge = `<div class="mt-2 text-xs text-green-600 font-bold">● IN RANGE</div>`;
                buttonHtml = `<button onclick="checkIn('${loc.name}')" class="mt-3 w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-bold text-sm shadow-lg">
                  Check In Now
                </button>`;
              } else {
                statusBadge = `<div class="mt-2 text-xs text-red-500 font-bold">● OUT OF ZONE</div>`;
              }

              list.innerHTML += `
                <div class="p-4 rounded-xl border ${
                  alreadyCheckedIn ? "bg-green-50 border-green-300 dark:bg-green-900/20 dark:border-green-700" :
                  inRange ? "bg-green-50 border-green-200 dark:bg-green-900/10 dark:border-green-600" : 
                  "bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                } transition-all">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-lg ${
                          alreadyCheckedIn ? "text-green-600" :
                          inRange ? "text-green-600" : "text-gray-400"
                        }">
                          ${alreadyCheckedIn ? "task_alt" : "location_on"}
                        </span>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">${loc.name}</span>
                      </div>
                      <p class="text-lg font-bold dark:text-white">${distance}m away</p>
                      ${statusBadge}
                    </div>
                  </div>
                  ${buttonHtml}
                </div>`;
            });
          }, (error) => {
            alert("Could not get your location. Please enable location services.");
          });
        } else {
          alert("Geolocation is not supported by your browser.");
        }
      }

      window.addEventListener("resize", () => {
        map.invalidateSize();
      });

      // Initial map resize
      setTimeout(() => {
        map.invalidateSize();
      }, 100);

      // Mobile Menu & Profile Dropdown
      const menuToggle = document.getElementById("menu-toggle");
      const mobileMenu = document.getElementById("mobile-menu");
      const profileToggle = document.getElementById("profile-toggle");
      const profileDropdown = document.getElementById("profile-dropdown");

      menuToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        mobileMenu.classList.toggle("hidden");
      });

      profileToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle("hidden");
      });

      document.addEventListener("click", (e) => {
        if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
          profileDropdown.classList.add("hidden");
        }
        if (!menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
          mobileMenu.classList.add("hidden");
        }
      });
    </script>
  </body>
</html>
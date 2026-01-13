<!DOCTYPE html>
<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Support & Help - Attendify</title>
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

      @media (max-width: 768px) {
        button, a {
          min-height: 44px;
          min-width: 44px;
        }
      }

      .faq-item {
        transition: all 0.3s ease;
      }
      .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
      }
      .faq-item.active .faq-answer {
        max-height: 500px;
      }
      .faq-item.active .faq-icon {
        transform: rotate(180deg);
      }
      .faq-icon {
        transition: transform 0.3s ease;
      }
    </style>
    @livewireStyles
  </head>
  <body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('home.Layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="h-16 md:h-20 flex items-center justify-between px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10">
        <div class="flex items-center gap-3 lg:hidden">
          <button id="menu-toggle" aria-label="Toggle menu" class="text-gray-500 hover:text-gray-900 dark:hover:text-white p-2">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <span class="font-bold text-base md:text-lg">Support</span>
        </div>
        <div class="hidden lg:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Support & Help Center</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Find answers and get assistance</p>
        </div>
        @include('home.Layouts.header')
      </header>

      <!-- Mobile Menu -->
      @include('home.Layouts.mobile')

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-10">
        <!-- Quick Help Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <!-- Getting Started -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="scrollToSection('getting-started')">
            <div class="size-12 rounded-xl bg-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-white text-2xl">rocket_launch</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Getting Started</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Learn the basics and set up your account</p>
          </div>

          <!-- User Guide -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="scrollToSection('user-guide')">
            <div class="size-12 rounded-xl bg-green-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-white text-2xl">menu_book</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">User Guide</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Step-by-step instructions for common tasks</p>
          </div>

          <!-- Contact Support -->
          <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="scrollToSection('contact')">
            <div class="size-12 rounded-xl bg-purple-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-white text-2xl">support_agent</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Contact Support</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Get in touch with our support team</p>
          </div>
        </div>

        <!-- Getting Started Section -->
        <div id="getting-started" class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">rocket_launch</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">Getting Started</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Quick introduction to Attendify</p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex gap-4">
              <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 mt-1">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">1</span>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Welcome to Attendify</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Attendify is a comprehensive attendance management system that helps track employee check-ins, check-outs, and work hours with morning and afternoon session support.</p>
              </div>
            </div>

            <div class="flex gap-4">
              <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 mt-1">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">2</span>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">System Requirements</h3>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                  <li>Modern web browser (Chrome, Firefox, Safari, Edge)</li>
                  <li>Mobile device with GPS capability for location verification</li>
                  <li>Internet connection</li>
                  <li>Telegram account (optional, for notifications)</li>
                </ul>
              </div>
            </div>

            <div class="flex gap-4">
              <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 mt-1">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">3</span>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">First Time Setup</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">When you first log in:</p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                  <li>Complete your profile information</li>
                  <li>Review your work schedule</li>
                  <li>Enable browser location permissions</li>
                  <li>Link your Telegram account (optional)</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- User Guide Section -->
        <div id="user-guide" class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
              <span class="material-symbols-outlined text-green-600 dark:text-green-400">menu_book</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">User Guide</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">How to use key features</p>
            </div>
          </div>

          <!-- Check-In Guide -->
          <div class="mb-6 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/10 dark:to-orange-900/10 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <div class="flex items-center gap-2 mb-3">
              <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">wb_sunny</span>
              <h3 class="font-bold text-gray-900 dark:text-white">How to Check In (Morning/Afternoon)</h3>
            </div>
            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
              <li>Navigate to the Dashboard or Check-In page</li>
              <li>Click the "Check In" button or scan the office QR code</li>
              <li>Allow location access when prompted</li>
              <li>Select your session (Morning 🌞 or Afternoon 🌅)</li>
              <li>Verify your location on the map</li>
              <li>Click "Confirm Check-In"</li>
              <li>You'll receive a confirmation with your status (On Time / Late)</li>
            </ol>
            <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
              <p class="text-xs text-gray-600 dark:text-gray-400"><strong>Note:</strong> You must be within the office radius to check in. Morning session: 7:30 AM - 11:30 AM | Afternoon session: 2:00 PM - 5:30 PM</p>
            </div>
          </div>

          <!-- Check-Out Guide -->
          <div class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10 border border-orange-200 dark:border-orange-800 rounded-lg">
            <div class="flex items-center gap-2 mb-3">
              <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">wb_twilight</span>
              <h3 class="font-bold text-gray-900 dark:text-white">How to Check Out</h3>
            </div>
            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
              <li>Go to the Dashboard or Check-In page</li>
              <li>Click the "Check Out" button</li>
              <li>Select the session you're checking out from</li>
              <li>Verify your location</li>
              <li>Click "Confirm Check-Out"</li>
              <li>Your work hours for the session will be calculated automatically</li>
            </ol>
          </div>

          <!-- View Attendance Guide -->
          <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center gap-2 mb-3">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">calendar_today</span>
              <h3 class="font-bold text-gray-900 dark:text-white">Viewing Your Attendance</h3>
            </div>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc list-inside">
              <li><strong>Dashboard:</strong> View today's attendance and quick stats</li>
              <li><strong>Attendance Log:</strong> See your complete attendance history with filters</li>
              <li><strong>My Schedule:</strong> Check your assigned work schedule and working days</li>
              <li><strong>Reports:</strong> Download attendance reports for any date range</li>
            </ul>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
              <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">help</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">Frequently Asked Questions</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Common questions and answers</p>
            </div>
          </div>

          <div class="space-y-3">
            <!-- FAQ Item 1 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">What if I forget to check in?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Contact your administrator immediately. They can manually mark your attendance or adjust your record. It's important to check in on time whenever possible to maintain accurate records.</p>
              </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">What happens if I'm outside the office radius?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">The system will prevent you from checking in if you're outside the designated office radius. You must be within the allowed distance to successfully check in. If you believe this is an error, contact your administrator.</p>
              </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">How are work hours calculated?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Work hours are calculated based on the time difference between your check-in and check-out for each session (morning and afternoon). The system automatically adds both sessions to give you your total daily work hours.</p>
              </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">Can I check in/out multiple times?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">You can check in once for the morning session and once for the afternoon session per day. Once you've checked in for a session, you cannot check in again until you check out. Each session is tracked separately.</p>
              </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">What does "late" status mean?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">You're marked as "late" if you check in after your scheduled time plus the tolerance period (usually 10 minutes). For example, if your morning session starts at 7:30 AM with 10 minutes tolerance, checking in after 7:40 AM will mark you as late.</p>
              </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">How do I enable Telegram notifications?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Contact your administrator to link your Telegram account. Once linked, you'll receive automatic notifications for check-ins, check-outs, and daily summaries in both English and Khmer.</p>
              </div>
            </div>

            <!-- FAQ Item 7 -->
            <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg">
              <button onclick="toggleFAQ(this)" class="w-full p-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg">
                <span class="font-semibold text-gray-900 dark:text-white">What are working days?</span>
                <span class="material-symbols-outlined text-gray-400 faq-icon">expand_more</span>
              </button>
              <div class="faq-answer px-4 pb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Working days are the days you're expected to check in, typically Monday through Friday. Your administrator configures these days, and each day can have different morning and afternoon session times. You can view your working days in the "My Schedule" section.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Support Section -->
        <div id="contact" class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
              <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">support_agent</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">Contact Support</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Get help from our support team</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary transition-colors">
              <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">email</span>
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Email Support</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Send us an email</p>
              <a href="mailto:support@attendify.com" class="text-sm text-primary hover:underline">support@attendify.com</a>
            </div>

            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary transition-colors">
              <div class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">phone</span>
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Phone Support</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Call us</p>
              <a href="tel:+855123456789" class="text-sm text-primary hover:underline">+855 12 345 6789</a>
            </div>

            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary transition-colors">
              <div class="size-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">chat</span>
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Telegram</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Message us</p>
              <a href="https://t.me/attendify_support" class="text-sm text-primary hover:underline">@attendify_support</a>
            </div>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex gap-3">
              <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">schedule</span>
              <div>
                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">Support Hours</p>
                <p class="text-sm text-blue-700 dark:text-blue-300">Monday - Friday: 8:00 AM - 6:00 PM</p>
                <p class="text-sm text-blue-700 dark:text-blue-300">Saturday: 9:00 AM - 1:00 PM</p>
                <p class="text-sm text-blue-700 dark:text-blue-300">Sunday: Closed</p>
              </div>
            </div>
          </div>
        </div>

        <!-- System Status -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-6 mt-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="size-3 rounded-full bg-green-500 animate-pulse"></div>
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">System Status: Operational</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">All systems running normally</p>
              </div>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Last updated: {{ now()->format('Y-m-d H:i') }}</span>
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

      // FAQ Toggle
      function toggleFAQ(button) {
        const faqItem = button.closest('.faq-item');
        const allFAQs = document.querySelectorAll('.faq-item');
        
        // Close all other FAQs
        allFAQs.forEach(item => {
          if (item !== faqItem) {
            item.classList.remove('active');
          }
        });
        
        // Toggle current FAQ
        faqItem.classList.toggle('active');
      }

      // Smooth scroll to section
      function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    </script>
  </body>
</html>
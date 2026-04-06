<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $success ? 'Mission Check-in Success' : 'Mission Check-in Failed' }} - Attendify</title>
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

      /* Animation for success/error icons */
      @keyframes scaleIn {
        0% {
          transform: scale(0);
          opacity: 0;
        }
        50% {
          transform: scale(1.1);
        }
        100% {
          transform: scale(1);
          opacity: 1;
        }
      }

      @keyframes slideUp {
        from {
          transform: translateY(20px);
          opacity: 0;
        }
        to {
          transform: translateY(0);
          opacity: 1;
        }
      }

      .animate-scale-in {
        animation: scaleIn 0.5s ease-out;
      }

      .animate-slide-up {
        animation: slideUp 0.6s ease-out;
      }

      /* Pulse animation for pending status */
      @keyframes pulse-soft {
        0%, 100% {
          opacity: 1;
        }
        50% {
          opacity: 0.7;
        }
      }

      .animate-pulse-soft {
        animation: pulse-soft 2s ease-in-out infinite;
      }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md animate-slide-up">
        <!-- Success State -->
        @if($success)
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-xl p-8 text-center border border-gray-100 dark:border-gray-800">
                <!-- Success Icon -->
                <div class="flex justify-center mb-6">
                    @if(isset($isPending) && $isPending)
                        <!-- Pending Icon -->
                        <div class="w-20 h-20 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center animate-scale-in">
                            <span class="material-symbols-outlined text-5xl text-yellow-600 dark:text-yellow-400 animate-pulse-soft">
                                schedule
                            </span>
                        </div>
                    @else
                        <!-- Success Icon -->
                        <div class="w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center animate-scale-in">
                            <span class="material-symbols-outlined text-5xl text-green-600 dark:text-green-400">
                                check_circle
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Message -->
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $message }}
                </h1>
                
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $details }}
                </p>

                <!-- Mission Details Card -->
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 mb-6 text-left space-y-3">
                    <!-- Date -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <span class="material-symbols-outlined text-xl">
                                calendar_today
                            </span>
                            <span class="text-sm font-medium">Mission Date</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $missionDate }}
                        </span>
                    </div>

                    <!-- Check-in Time -->
                    @if(isset($checkInTime))
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <span class="material-symbols-outlined text-xl">
                                schedule
                            </span>
                            <span class="text-sm font-medium">Check-in Time</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $checkInTime }}
                        </span>
                    </div>
                    @endif

                    <!-- Status -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <span class="material-symbols-outlined text-xl">
                                info
                            </span>
                            <span class="text-sm font-medium">Status</span>
                        </div>
                        @if(isset($isPending) && $isPending)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                                Pending Approval
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Approved
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Info Alert for Pending -->
                @if(isset($isPending) && $isPending)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6 text-left">
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400 text-xl shrink-0">
                            info
                        </span>
                        <div class="text-sm text-yellow-800 dark:text-yellow-300">
                            <p class="font-semibold mb-1">Awaiting Admin Approval</p>
                            <p class="text-yellow-700 dark:text-yellow-400">
                                Your mission check-in has been submitted. An administrator will review and approve your attendance shortly. You'll be notified once it's approved.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <a href="{{ route('missions.my') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-xl">
                            list_alt
                        </span>
                        View My Missions
                    </a>
                    
                    <a href="{{ route('home') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-xl">
                            home
                        </span>
                        Back to Home
                    </a>
                </div>
            </div>
        @else
            <!-- Error State -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-xl p-8 text-center border border-gray-100 dark:border-gray-800">
                <!-- Error Icon -->
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center animate-scale-in">
                        <span class="material-symbols-outlined text-5xl text-red-600 dark:text-red-400">
                            cancel
                        </span>
                    </div>
                </div>

                <!-- Message -->
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $message }}
                </h1>
                
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $details }}
                </p>

                <!-- Error Details Card -->
                @if(isset($missionDate))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6 text-left">
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-xl shrink-0">
                            error
                        </span>
                        <div class="text-sm text-red-800 dark:text-red-300">
                            <p class="font-semibold mb-1">Already Checked In</p>
                            <p class="text-red-700 dark:text-red-400 mb-2">
                                You have already checked in for mission work on <strong>{{ $missionDate }}</strong>
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-500">
                                Each user can only submit one mission check-in per day.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <a href="{{ route('missions.my') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-xl">
                            list_alt
                        </span>
                        View My Missions
                    </a>
                    
                    <a href="{{ route('home') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-xl">
                            home
                        </span>
                        Back to Home
                    </a>
                </div>
            </div>
        @endif

        <!-- Footer Note -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Need help? Contact your administrator
            </p>
        </div>
    </div>

</body>
</html>
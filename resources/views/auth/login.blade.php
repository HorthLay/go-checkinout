<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ចូលប្រព័ន្ធវត្តមាន - Attendance Login</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&family=Nokora:wght@100;300;400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                        },
                        secondary: {
                            50: '#fafafa',
                            100: '#f5f5f5',
                        },
                        neutral: {
                            50: '#fafafa',
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            600: '#525252',
                            700: '#404040',
                            900: '#171717',
                        }
                    },
                    fontFamily: {
                        khmer: ['Battambang', 'Nokora', 'sans-serif'],
                        sans: ['Poppins', 'sans-serif'],
                    },
                    borderRadius: {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Battambang', sans-serif;
            background: white;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .particle-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.8), rgba(59, 130, 246, 0.2));
            border-radius: 50%;
            opacity: 0.6;
            animation: particleFloat linear infinite;
            box-shadow: 0 0 6px rgba(59, 130, 246, 0.4);
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-100vh) translateX(var(--tx));
                opacity: 0;
            }
        }

        .glass-card {
            background: white;
            border: 2px solid #e0e7ff;
            box-shadow: 0 4px 30px rgba(59, 130, 246, 0.08),
                0 0 0 1px rgba(59, 130, 246, 0.05);
            position: relative;
            z-index: 10;
        }

        .input-field {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
        }

        .input-field:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1),
                0 0 20px rgba(59, 130, 246, 0.15);
            outline: none;
        }

        .input-field.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .input-field.error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .input-field::placeholder {
            color: #94a3b8;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .logo-circle {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .slide-down {
            animation: slideDown 0.6s ease-out;
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        .form-group {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-group:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.4s;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: start;
            gap: 6px;
            animation: slideDown 0.3s ease-out;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: start;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        .error-alert-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            color: #dc2626;
        }

        .eye-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
            color: #64748b;
        }

        .eye-icon-btn:hover {
            background: rgba(59, 130, 246, 0.08);
            color: #3b82f6;
        }

        .checkbox-custom {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            flex-shrink: 0;
        }

        .checkbox-custom:checked {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: #3b82f6;
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.3);
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(4px);
            animation: fadeInUp 0.3s ease-out;
            z-index: 100;
            position: relative;
        }

        .modal-content {
            animation: fadeInUp 0.3s ease-out;
        }

        .link-hover {
            position: relative;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .link-hover::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            transition: width 0.3s ease;
        }

        .link-hover:hover {
            color: #2563eb;
        }

        .link-hover:hover::after {
            width: 100%;
        }

        .divider-text {
            position: relative;
            text-align: center;
            margin: 24px 0;
            color: #94a3b8;
        }

        .divider-text::before,
        .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: calc(50% - 50px);
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        }

        .divider-text::before {
            left: 0;
        }

        .divider-text::after {
            right: 0;
        }

        label {
            font-weight: 500;
            color: #404040;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.8px;
            color: #64748b;
        }

        @media (max-width: 640px) {
            body {
                padding: 12px;
            }

            .glass-card {
                border-radius: 16px;
                padding: 24px;
            }

            .logo-circle {
                width: 64px;
                height: 64px;
                margin-bottom: 16px;
            }

            .logo-circle svg {
                width: 32px;
                height: 32px;
            }

            h1 {
                font-size: 24px;
            }

            p.text-base {
                font-size: 13px;
            }

            .input-field {
                padding: 12px 16px;
                font-size: 14px;
            }

            .btn-primary {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="particle-container" id="particleContainer"></div>

    <div class="w-full max-w-md px-4 sm:px-0 relative z-20">
        <div class="glass-card rounded-2xl p-8 sm:p-10 fade-in-up">
            <!-- Header Section -->
            <div class="text-center mb-10 slide-down">
                <div class="w-20 h-20 mx-auto mb-6 logo-circle rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-11 h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-neutral-900 mb-2 font-khmer">ប្រព័ន្ធវត្តមាន</h1>
                <p class="text-neutral-600 text-sm sm:text-base font-medium">Attendance Management System</p>
            </div>

            <!-- Error Alert (Laravel-style) - Server-side errors -->
            @if ($errors->any())
            <div id="errorAlert" class="error-alert">
                <svg class="error-alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-sm mb-1">Whoops! Something went wrong.</p>
                    <ul class="text-sm space-y-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Success Message -->
            @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <!-- Login Form -->
            <form id="loginForm" action="{{ route('login.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Username Field -->
                <div class="form-group">
                    <label for="login" class="form-label">ឈ្មោះអ្នកប្រើប្រាស់</label>
                    <input type="text" 
                           id="login" 
                           name="login"
                           class="input-field w-full px-4 py-3 rounded-lg focus:ring-0 @error('login') error @enderror"
                           placeholder="បញ្ចូលឈ្មោះផ្ទាល់ខ្លួនរបស់អ្នក" 
                           value="{{ old('login') }}" 
                           required 
                           autofocus>
                    @error('login')
                    <div class="error-message">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">ពាក្យសម្ងាត់</label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="input-field w-full px-4 py-3 pr-12 rounded-lg focus:ring-0 @error('password') error @enderror"
                               placeholder="បញ្ចូលពាក្យសម្ងាត់របស់អ្នក" 
                               required>
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="eye-icon-btn absolute right-2 top-1/2 -translate-y-1/2">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <div class="error-message">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group flex items-center gap-3">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember" 
                           class="checkbox-custom"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-sm text-neutral-600 font-medium cursor-pointer">
                        ចងចាំឈ្មោះរបស់ខ្ញុំ
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="form-group btn-primary w-full py-3.5 sm:py-4 text-white font-semibold rounded-lg text-base">
                    ចូលប្រព័ន្ធ
                </button>
            </form>

            <!-- Divider -->
            <div class="divider-text text-xs font-medium mt-8">ឬ</div>

            <!-- Additional Links -->
            <div class="mt-8 space-y-3 text-center">
                <p class="text-neutral-600 text-sm">
                    មិនទាន់មានគណនីទេ?
                    <a href="#" onclick="showContactPopup(event)" class="link-hover">
                        ទាក់ទងលើកូដផ្សាយ
                    </a>
                </p>
                
                @if (Route::has('password.request'))
                <p class="text-neutral-600 text-sm">
                    <a href="{{ route('password.request') }}" class="link-hover">
                        ភ្លេចពាក្យសម្ងាត់?
                    </a>
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactPopup" class="hidden fixed inset-0 modal-overlay flex items-center justify-center p-4">
        <div class="glass-card modal-content rounded-2xl p-8 max-w-sm w-full">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-neutral-900 mb-2">ទាក់ទងតាម Telegram</h3>
                <p class="text-neutral-600 text-sm mb-6">
                    សូមទាក់ទងលើកូដផ្សាយដើម្បីបង្កើតគណនីថ្មី
                </p>

                <div class="bg-blue-50 rounded-lg p-5 mb-6 border-2 border-blue-100">
                    <p class="text-neutral-600 text-xs uppercase tracking-wide mb-2">Telegram</p>
                    <p class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">
                        @vongsokphol
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="https://t.me/vongsokphol" target="_blank"
                        class="btn-primary flex-1 py-3 inline-block text-center">
                        បើក Telegram
                    </a>
                    <button onclick="closeContactPopup()"
                        class="flex-1 px-6 py-3 border-2 border-neutral-200 text-neutral-700 font-semibold rounded-lg hover:bg-neutral-50 transition-colors duration-200">
                        បិទ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Create particles animation
        function createSnowflakes() {
            const particleContainer = document.getElementById('particleContainer');
            const particleCount = Math.max(40, Math.min(80, Math.floor(window.innerWidth / 15)));

            particleContainer.innerHTML = '';

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const randomLeft = Math.random() * window.innerWidth;
                const randomDelay = Math.random() * 8;
                const randomDuration = 12 + Math.random() * 18;
                const randomSwing = (Math.random() - 0.5) * 150;

                particle.style.left = randomLeft + 'px';
                particle.style.bottom = '-10px';
                particle.style.animation = `particleFloat ${randomDuration}s linear ${randomDelay}s infinite`;
                particle.style.setProperty('--tx', randomSwing + 'px');

                particleContainer.appendChild(particle);
            }
        }

        createSnowflakes();

        window.addEventListener('resize', () => {
            createSnowflakes();
        });

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        // Show contact popup
        function showContactPopup(e) {
            e.preventDefault();
            document.getElementById('contactPopup').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Close contact popup
        function closeContactPopup() {
            document.getElementById('contactPopup').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('contactPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContactPopup();
            }
        });

        // Auto-hide success/error messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.error-alert, .bg-green-50');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

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
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Enhanced Particle System */
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
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat linear infinite;
        }

        .particle.type-1 {
            width: 4px;
            height: 4px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.2));
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.7);
        }

        .particle.type-2 {
            width: 6px;
            height: 6px;
            background: radial-gradient(circle, rgba(147, 197, 253, 0.9), rgba(147, 197, 253, 0.3));
            box-shadow: 0 0 18px rgba(147, 197, 253, 0.6);
        }

        .particle.type-3 {
            width: 3px;
            height: 3px;
            background: radial-gradient(circle, rgba(191, 219, 254, 0.9), rgba(191, 219, 254, 0.2));
            box-shadow: 0 0 10px rgba(191, 219, 254, 0.7);
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 0.8;
                transform: translateY(90vh) translateX(var(--tx)) scale(1);
            }

            90% {
                opacity: 0.8;
            }

            100% {
                transform: translateY(-10vh) translateX(calc(var(--tx) * 2)) scale(0.5);
                opacity: 0;
            }
        }

        /* Animated background gradient */
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(-45deg, #3b82f6, #2563eb, #1d4ed8, #60a5fa);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            z-index: 0;
        }

        /* Glass morphism card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37),
                        0 0 80px 0 rgba(59, 130, 246, 0.15);
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.7s;
        }

        .glass-card:hover::before {
            left: 100%;
        }

        /* Enhanced input fields */
        .input-field {
            background: rgba(248, 250, 252, 0.8);
            border: 2px solid rgba(226, 232, 240, 0.6);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
            position: relative;
        }

        .input-field:focus {
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15),
                        0 4px 20px rgba(59, 130, 246, 0.2);
            outline: none;
            transform: translateY(-2px);
        }

        .input-field.error {
            border-color: #ef4444;
            background: #fef2f2;
            animation: shake 0.5s ease-in-out;
        }

        .input-field.error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .input-field::placeholder {
            color: #94a3b8;
        }

        /* Enhanced button with ripple effect */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            background-size: 200% 200%;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4),
                        0 0 40px rgba(59, 130, 246, 0.2);
            position: relative;
            overflow: hidden;
            animation: buttonPulse 2s ease-in-out infinite;
        }

        @keyframes buttonPulse {
            0%, 100% {
                box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4),
                            0 0 40px rgba(59, 130, 246, 0.2);
            }
            50% {
                box-shadow: 0 6px 30px rgba(59, 130, 246, 0.5),
                            0 0 60px rgba(59, 130, 246, 0.3);
            }
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            background-position: 100% 0;
            box-shadow: 0 8px 35px rgba(59, 130, 246, 0.5),
                        0 0 60px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:active {
            transform: translateY(-1px) scale(0.98);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            animation: none;
        }

        /* Animated logo */
        .logo-icon-container {
            animation: logoFloat 3s ease-in-out infinite;
            position: relative;
        }

        .logo-icon-container::before {
            content: '';
            position: absolute;
            inset: -4px;
            background: linear-gradient(45deg, #3b82f6, #2563eb, #3b82f6);
            border-radius: inherit;
            opacity: 0.3;
            filter: blur(12px);
            animation: logoGlow 2s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes logoGlow {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 0.8;
            }
        }

        /* Enhanced animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-8px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(8px);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .slide-down {
            animation: slideDown 0.6s ease-out;
        }

        .bounce-in {
            animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .form-group {
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }

        /* Error and success messages */
        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: start;
            gap: 6px;
            animation: slideDown 0.3s ease-out;
        }

        .error-alert {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fecaca;
            color: #991b1b;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: start;
            gap: 12px;
            animation: slideDown 0.4s ease-out, pulse 2s ease-in-out infinite;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
            }
            50% {
                box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);
            }
        }

        .success-alert {
            animation: slideDown 0.4s ease-out, successPulse 2s ease-in-out infinite;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.2);
        }

        @keyframes successPulse {
            0%, 100% {
                box-shadow: 0 4px 15px rgba(34, 197, 94, 0.2);
            }
            50% {
                box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
            }
        }

        /* Eye icon button */
        .eye-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #64748b;
        }

        /* Custom checkbox */
        .checkbox-custom {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            flex-shrink: 0;
            position: relative;
        }

        .checkbox-custom:checked {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: #3b82f6;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
            animation: checkboxBounce 0.4s ease;
        }

        .checkbox-custom:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes checkboxBounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Modal overlay */
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
            z-index: 100;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            animation: modalSlideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Link hover effect */
        .link-hover {
            position: relative;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
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
            transform: translateX(2px);
        }

        .link-hover:hover::after {
            width: 100%;
        }

        /* Divider */
        .divider-text {
            position: relative;
            text-align: center;
            margin: 28px 0;
            color: #94a3b8;
            font-weight: 500;
        }

        .divider-text::before,
        .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: calc(50% - 30px);
            height: 2px;
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
        }

        .divider-text::before { left: 0; }
        .divider-text::after { right: 0; }

        /* Labels */
        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.5px;
            color: #475569;
            text-transform: uppercase;
        }

        /* Responsive design */
        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .glass-card {
                border-radius: 20px;
                padding: 28px 24px;
            }

            .size-16 {
                width: 56px;
                height: 56px;
            }

            .size-16 .material-symbols-outlined {
                font-size: 32px;
            }

            h1 {
                font-size: 26px;
            }

            p.text-base {
                font-size: 14px;
            }

            .input-field {
                padding: 13px 16px;
                font-size: 14px;
            }

            .btn-primary {
                padding: 14px 24px;
                font-size: 15px;
            }

            .particle.type-1 { width: 3px; height: 3px; }
            .particle.type-2 { width: 4px; height: 4px; }
            .particle.type-3 { width: 2px; height: 2px; }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .glass-card {
                max-width: 480px;
                margin: 0 auto;
            }
        }

        @media (min-width: 1025px) {
            .glass-card {
                max-width: 500px;
            }
        }

        /* Loading state */
        .btn-primary.loading {
            pointer-events: none;
        }

        .btn-primary.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="particle-container" id="particleContainer"></div>

    <div class="w-full max-w-md px-4 sm:px-0 relative z-20">
        <div class="glass-card rounded-2xl p-8 sm:p-10 fade-in-up">
            <!-- Header Section -->
            <div class="text-center mb-10 bounce-in">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="size-16 rounded-xl bg-primary-500/10 text-primary-600 flex items-center justify-center relative">
                        <span class="material-symbols-outlined text-4xl relative z-10">qr_code_scanner</span>
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-500/20 to-primary-600/20 rounded-xl blur-xl"></div>
                    </div>
                    <div class="text-left">
                        <span class="font-bold text-2xl tracking-tight block leading-none text-neutral-900">Attendify</span>
                        <span class="text-xs text-primary-600 uppercase tracking-wider font-semibold">Portal</span>
                    </div>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-neutral-900 mb-3 font-khmer">ប្រព័ន្ធវត្តមាន</h1>
                <p class="text-neutral-600 text-sm sm:text-base font-medium">Attendance Management System</p>
            </div>

            <!-- Error Alert (Laravel-style) - Server-side errors -->
            @if ($errors->any())
            <div id="errorAlert" class="error-alert">
                <svg class="error-alert-icon w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="success-alert bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 text-green-800 px-4 py-3.5 rounded-xl mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
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
                           class="input-field w-full px-4 py-3.5 rounded-xl focus:ring-0 @error('login') error @enderror"
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
                               class="input-field w-full px-4 py-3.5 pr-12 rounded-xl focus:ring-0 @error('password') error @enderror"
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
                    <label for="remember" class="text-sm text-neutral-600 font-medium cursor-pointer select-none">
                        ចងចាំឈ្មោះរបស់ខ្ញុំ
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit" id="loginBtn"
                    class="form-group btn-primary w-full py-4 text-white font-semibold rounded-xl text-base relative">
                    <span id="btnText">ចូលប្រព័ន្ធ</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="divider-text text-xs">ឬ</div>

            <!-- Additional Links -->
            <div class="mt-8 space-y-4 text-center" style="animation: fadeInUp 0.6s ease-out 0.5s both;">
                <p class="text-neutral-600 text-sm">
                    មិនទាន់មានគណនីទេ?
                    <a href="#" onclick="showContactPopup(event)" class="link-hover">
                        ទាក់ទងលើកូដផ្សាយ
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer Text -->
        <div class="text-center mt-6" style="animation: fadeInUp 0.8s ease-out 0.6s both;">
            <p class="text-white text-xs opacity-80">
                © 2025 Attendance System. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactPopup" class="hidden fixed inset-0 modal-overlay flex items-center justify-center p-4">
        <div class="glass-card modal-content rounded-2xl p-8 max-w-sm w-full">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 3 .97 4.29L2 22l5.71-.97C9 21.64 10.46 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm4.95 13.54c-.21.58-1.21 1.07-1.67 1.11-.46.04-.85.2-2.85-.59-2.41-1.01-3.95-3.46-4.07-3.62-.12-.16-.98-1.3-.98-2.48s.62-1.76.84-2 .48-.3.65-.3c.17 0 .33 0 .48.01.15.01.36-.06.56.43.21.49.7 1.71.76 1.84.06.12.1.27.02.43-.08.16-.12.27-.24.41-.12.14-.25.31-.36.42-.12.12-.24.25-.1.49.14.23.62 1.03 1.33 1.67.91.82 1.68 1.08 1.92 1.2.24.12.38.1.52-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.1.06.58-.15 1.16z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-2">ទាក់ទងតាម Telegram</h3>
                <p class="text-neutral-600 text-sm mb-6">
                    សូមទាក់ទងលើកូដផ្សាយដើម្បីបង្កើតគណនីថ្មី
                </p>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 mb-6 border-2 border-blue-200">
                    <p class="text-neutral-600 text-xs uppercase tracking-wide mb-2 font-semibold">Telegram</p>
                    <p class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">
                        @vongsophal
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="https://t.me/vongsophal" target="_blank"
                        class="btn-primary flex-1 py-3.5 inline-block text-center">
                        បើក Telegram
                    </a>
                    <button onclick="closeContactPopup()"
                        class="flex-1 px-6 py-3.5 border-2 border-neutral-200 text-neutral-700 font-semibold rounded-xl hover:bg-neutral-50 transition-all duration-300 hover:scale-105">
                        បិទ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced particle creation with multiple types
        function createParticles() {
            const particleContainer = document.getElementById('particleContainer');
            const particleCount = Math.max(50, Math.min(100, Math.floor(window.innerWidth / 12)));

            particleContainer.innerHTML = '';

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                const types = ['type-1', 'type-2', 'type-3'];
                const randomType = types[Math.floor(Math.random() * types.length)];
                
                particle.classList.add('particle', randomType);

                const randomLeft = Math.random() * window.innerWidth;
                const randomDelay = Math.random() * 10;
                const randomDuration = 15 + Math.random() * 25;
                const randomSwing = (Math.random() - 0.5) * 200;

                particle.style.left = randomLeft + 'px';
                particle.style.bottom = '-10px';
                particle.style.animation = `particleFloat ${randomDuration}s linear ${randomDelay}s infinite`;
                particle.style.setProperty('--tx', randomSwing + 'px');

                particleContainer.appendChild(particle);
            }
        }

        createParticles();

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                createParticles();
            }, 250);
        });

        // Toggle password visibility with animation
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        // Show contact popup with animation
        function showContactPopup(e) {
            e.preventDefault();
            const popup = document.getElementById('contactPopup');
            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Add entrance animation
            setTimeout(() => {
                popup.style.opacity = '1';
            }, 10);
        }

        // Close contact popup with animation
        function closeContactPopup() {
            const popup = document.getElementById('contactPopup');
            popup.style.opacity = '0';
            setTimeout(() => {
                popup.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('contactPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContactPopup();
            }
        });

        // Enhanced form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            
            btn.classList.add('loading');
            btn.disabled = true;
            btnText.textContent = 'កំពុងចូល...';
        });

        // Auto-hide alerts with fade out animation
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.error-alert, .success-alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });

        // Add input focus animations
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Keyboard accessibility for modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const popup = document.getElementById('contactPopup');
                if (!popup.classList.contains('hidden')) {
                    closeContactPopup();
                }
            }
        });
    </script>
</body>

</html>
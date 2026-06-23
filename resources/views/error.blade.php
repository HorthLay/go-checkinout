<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Failed — Attendify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: #fef2f2;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 1.5rem;
            position: relative; overflow: hidden;
        }

        /* ── Background ── */
        .bg-pattern {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(239,68,68,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(220,38,38,0.04) 0%, transparent 50%),
                radial-gradient(circle at 60% 10%, rgba(248,113,113,0.05) 0%, transparent 40%);
        }

        .bg-dots {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image: radial-gradient(circle, rgba(239,68,68,0.07) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 10%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 10%, transparent 100%);
        }

        /* ── Card ── */
        .card {
            background: #ffffff;
            border: 1px solid rgba(239,68,68,0.1);
            border-radius: 24px;
            padding: 3rem 2.5rem 2.5rem;
            max-width: 440px; width: 100%;
            text-align: center;
            position: relative; z-index: 1;
            box-shadow:
                0 1px 3px rgba(239,68,68,0.06),
                0 8px 32px rgba(239,68,68,0.09),
                0 32px 64px -16px rgba(239,68,68,0.1);
            opacity: 0;
            transform: translateY(24px) scale(0.98);
            animation: cardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.05s forwards;
        }
        @keyframes cardIn { to { opacity: 1; transform: translateY(0) scale(1); } }

        /* Red top bar */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #b91c1c, #dc2626 40%, #ef4444 70%, #f87171);
            border-radius: 24px 24px 0 0;
        }

        /* ── Icon shake ── */
        .icon-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 96px; height: 96px;
            margin-bottom: 1.75rem; z-index: 1; position: relative;
            animation: shake 0.55s cubic-bezier(0.36,0.07,0.19,0.97) 0.25s both;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%       { transform: translateX(-7px) rotate(-2deg); }
            30%       { transform: translateX(7px)  rotate(2deg); }
            45%       { transform: translateX(-5px) rotate(-1deg); }
            60%       { transform: translateX(5px)  rotate(1deg); }
            75%       { transform: translateX(-2px); }
        }

        .icon-circle {
            width: 76px; height: 76px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            display: flex; align-items: center; justify-content: center;
            box-shadow:
                0 4px 20px rgba(239,68,68,0.35),
                0 0 0 8px rgba(239,68,68,0.08);
            opacity: 0; transform: scale(0.5);
            animation: iconPop 0.45s cubic-bezier(0.34,1.56,0.64,1) 0.15s forwards;
        }
        @keyframes iconPop { to { opacity: 1; transform: scale(1); } }

        .x-mark {
            width: 30px; height: 30px;
            stroke: #ffffff; stroke-width: 2.8;
            stroke-linecap: round; fill: none;
            stroke-dasharray: 50; stroke-dashoffset: 50;
            animation: drawX 0.3s ease forwards 0.5s;
        }
        @keyframes drawX { to { stroke-dashoffset: 0; } }

        /* ── Stagger ── */
        .stag { opacity: 0; transform: translateY(10px); z-index: 1; position: relative; }
        .stag-1 { animation: fadeUp 0.45s ease forwards 0.55s; }
        .stag-2 { animation: fadeUp 0.45s ease forwards 0.67s; }
        .stag-3 { animation: fadeUp 0.45s ease forwards 0.77s; }
        .stag-4 { animation: fadeUp 0.45s ease forwards 0.87s; }
        .stag-5 { animation: fadeUp 0.45s ease forwards 0.96s; }
        .stag-6 { animation: fadeUp 0.45s ease forwards 1.04s; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        /* ── Tag ── */
        .tag {
            display: inline-block;
            background: #fef2f2; color: #dc2626;
            font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            padding: 0.28rem 0.85rem;
            border-radius: 100px;
            border: 1px solid rgba(220,38,38,0.18);
            margin-bottom: 0.9rem;
        }

        h1 {
            color: #0f172a;
            font-size: 1.6rem; font-weight: 800;
            line-height: 1.2; margin-bottom: 0.6rem;
            letter-spacing: -0.03em;
        }

        .message { color: #64748b; font-size: 0.9rem; line-height: 1.65; margin-bottom: 1.5rem; }

        /* ── Error detail box ── */
        .error-box {
            background: #fff5f5;
            border: 1px solid rgba(239,68,68,0.2);
            border-left: 3px solid #ef4444;
            border-radius: 12px;
            padding: 1rem 1.1rem;
            display: flex; gap: 0.75rem; align-items: flex-start;
            text-align: left; margin-bottom: 1.25rem;
        }
        .error-box-icon { flex-shrink: 0; margin-top: 1px; }
        .error-box-icon svg {
            width: 17px; height: 17px;
            stroke: #ef4444; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }
        .error-box-text { font-size: 0.84rem; color: #b91c1c; line-height: 1.55; font-weight: 500; }

        /* ── Hint ── */
        .hint {
            background: #f8faff; border: 1px solid rgba(37,99,235,0.12);
            border-radius: 12px; padding: 0.9rem 1.1rem;
            display: flex; align-items: center; gap: 0.75rem;
            margin-bottom: 1.5rem; text-align: left;
        }
        .hint-icon {
            width: 34px; height: 34px; background: #eff6ff;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .hint-icon svg { width: 16px; height: 16px; stroke: #2563eb; fill: none; stroke-width: 2; stroke-linecap: round; }
        .hint-text { font-size: 0.8rem; color: #64748b; line-height: 1.5; }
        .hint-text strong { color: #1e293b; font-weight: 600; }

        /* ── Countdown ── */
        .countdown-wrap { margin-bottom: 1.75rem; }
        .countdown-bar-bg { height: 4px; background: #fee2e2; border-radius: 100px; overflow: hidden; margin-bottom: 0.5rem; }
        .countdown-bar {
            height: 100%;
            background: linear-gradient(90deg, #dc2626, #ef4444);
            border-radius: 100px; width: 100%;
            transition: width 1s linear;
        }
        .countdown-text { font-size: 0.75rem; color: #94a3b8; }
        .countdown-text span { color: #dc2626; font-weight: 700; font-variant-numeric: tabular-nums; }

        /* ── Buttons ── */
        .btn-primary {
            display: block; width: 100%;
            padding: 0.88rem 1.5rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #fff; font-size: 0.88rem; font-weight: 700;
            text-align: center; border-radius: 12px;
            text-decoration: none; letter-spacing: 0.01em;
            border: none; cursor: pointer;
            position: relative; overflow: hidden;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(220,38,38,0.4); }
        .btn-primary:active { transform: translateY(0) scale(0.985); }
        .btn-primary::after {
            content: ''; position: absolute; top: 0; left: -100%; width: 55%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover::after { left: 130%; }

        .btn-secondary {
            display: block; width: 100%;
            padding: 0.75rem 1.5rem;
            background: transparent; color: #94a3b8;
            font-size: 0.84rem; font-weight: 500;
            text-align: center; border-radius: 12px;
            text-decoration: none; margin-top: 0.5rem;
            border: 1px solid #e2e8f0; cursor: pointer;
            transition: color 0.2s, border-color 0.2s, background 0.2s;
        }
        .btn-secondary:hover { color: #475569; background: #f8faff; border-color: rgba(37,99,235,0.2); }

        /* ── Logo ── */
        .logo {
            position: absolute; top: 1.25rem; left: 1.5rem;
            display: flex; align-items: center; gap: 0.5rem; z-index: 1;
        }
        .logo-icon {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
        }
        .logo-icon svg { width: 15px; height: 15px; stroke: #fff; fill: none; stroke-width: 2.2; stroke-linecap: round; }
        .logo-text { font-size: 0.78rem; font-weight: 700; color: #1e293b; letter-spacing: -0.01em; }
        .logo-sub  { font-size: 0.62rem; color: #94a3b8; font-weight: 500; margin-top: -2px; }

        @media (max-width: 460px) { .card { padding: 2.5rem 1.5rem 2rem; } h1 { font-size: 1.35rem; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; } }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>
    <div class="bg-dots"></div>

    <div class="card">
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="logo-text">Attendify</div>
                <div class="logo-sub">PORTAL</div>
            </div>
        </div>

        <div class="icon-wrap">
            <div class="icon-circle">
                <svg class="x-mark" viewBox="0 0 24 24">
                    <line x1="18" y1="6"  x2="6"  y2="18"/>
                    <line x1="6"  y1="6"  x2="18" y2="18"/>
                </svg>
            </div>
        </div>

        <div class="tag stag stag-1">✕ Attendance Failed</div>

        <h1 class="stag stag-2">Couldn't record attendance</h1>

        <p class="message stag stag-3">
            Something prevented your attendance from being recorded. Please check the details below and try again.
        </p>

        @if(session('error'))
        <div class="error-box stag stag-4">
            <div class="error-box-icon">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8"  x2="12"   y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div class="error-box-text">{{ session('error') }}</div>
        </div>
        @endif

        <div class="hint stag stag-4">
            <div class="hint-icon">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4M12 8h.01"/>
                </svg>
            </div>
            <div class="hint-text">
                <strong>Need help?</strong> Make sure you are physically inside the office. If this keeps happening, contact your administrator.
            </div>
        </div>

        <div class="countdown-wrap stag stag-5">
            <div class="countdown-bar-bg">
                <div class="countdown-bar" id="countdownBar"></div>
            </div>
            <div class="countdown-text">Returning to attendance in <span id="countdownNum">10</span>s</div>
        </div>

        <a href="{{ route('checkin') }}" class="btn-primary stag stag-6" id="tryAgainBtn">Try Again</a>
        <a href="{{ url('/') }}" class="btn-secondary stag stag-6">Dashboard</a>
    </div>

<script>
(function () {
    /* ── Sound ── */
    const action = '{{ session('action') ?? 'checkin' }}';
    const soundSrc = action === 'checkout'
        ? '/sounds/error-scan-checkout.mp3'
        : '/sounds/error-scan-checkin.mp3';
    const audio = new Audio(soundSrc);
    audio.volume = 0.85;

    let played = false;
    function tryPlay() {
        if (played) return; played = true;
        audio.play().catch(() => {});
    }
    tryPlay();
    document.addEventListener('click',      tryPlay, { once: true });
    document.addEventListener('touchstart', tryPlay, { once: true });

    /* ── Countdown ── */
    let seconds = 10;
    const bar = document.getElementById('countdownBar');
    const num = document.getElementById('countdownNum');

    const timer = setInterval(() => {
        seconds--;
        num.textContent = seconds;
        bar.style.width = (seconds / 10 * 100) + '%';
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = '{{ route("checkin") }}';
        }
    }, 1000);

    document.getElementById('tryAgainBtn').addEventListener('click', () => clearInterval(timer));
})();
</script>
</body>
</html>
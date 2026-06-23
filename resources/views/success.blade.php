<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('action') === 'checkout' ? 'Check-Out' : 'Check-In' }} Successful — Attendify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f4ff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* ── Background pattern ── */
        .bg-pattern {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(37,99,235,0.07) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(99,102,241,0.05) 0%, transparent 50%),
                radial-gradient(circle at 60% 10%, rgba(59,130,246,0.06) 0%, transparent 40%);
        }

        .bg-dots {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image: radial-gradient(circle, rgba(37,99,235,0.08) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 10%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 10%, transparent 100%);
        }

        /* ── Card ── */
        .card {
            background: #ffffff;
            border: 1px solid rgba(37,99,235,0.1);
            border-radius: 24px;
            padding: 3rem 2.5rem 2.5rem;
            max-width: 440px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
            box-shadow:
                0 1px 3px rgba(37,99,235,0.06),
                0 8px 32px rgba(37,99,235,0.1),
                0 32px 64px -16px rgba(37,99,235,0.12);
            opacity: 0;
            transform: translateY(24px) scale(0.98);
            animation: cardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.05s forwards;
        }
        @keyframes cardIn {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Blue top bar — the Attendify signature stripe */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #1d4ed8, #2563eb 40%, #3b82f6 70%, #60a5fa);
            border-radius: 24px 24px 0 0;
        }

        /* canvas for particles — inside card */
        #particles {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none; z-index: 0;
            border-radius: 24px;
        }

        /* ── Icon ── */
        .icon-wrap {
            position: relative;
            display: inline-flex;
            align-items: center; justify-content: center;
            width: 96px; height: 96px;
            margin-bottom: 1.75rem;
            z-index: 1;
        }

        .ring-wave {
            position: absolute;
            width: 76px; height: 76px;
            border-radius: 50%;
            border: 2px solid rgba(37,99,235,0.35);
            opacity: 0;
            transform: scale(0.5);
        }
        .ring-wave.active {
            animation: ringExpand 1s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        @keyframes ringExpand {
            0%   { transform: scale(0.5); opacity: 0.9; border-width: 2.5px; }
            100% { transform: scale(2.4); opacity: 0;   border-width: 0.3px; }
        }

        .icon-circle {
            position: relative;
            width: 76px; height: 76px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
            box-shadow:
                0 4px 20px rgba(37,99,235,0.35),
                0 0 0 8px rgba(37,99,235,0.08);
            opacity: 0;
            transform: scale(0.5);
            animation: iconPop 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.2s forwards;
        }
        @keyframes iconPop {
            to { opacity: 1; transform: scale(1); }
        }

        .checkmark {
            width: 32px; height: 32px;
            stroke: #ffffff;
            stroke-width: 2.8;
            stroke-linecap: round; stroke-linejoin: round;
            fill: none;
            stroke-dasharray: 60; stroke-dashoffset: 60;
            animation: drawCheck 0.35s ease forwards 0.5s;
        }
        @keyframes drawCheck { to { stroke-dashoffset: 0; } }

        /* ── Stagger ── */
        .stag { opacity: 0; transform: translateY(10px); z-index: 1; position: relative; }
        .stag-1 { animation: fadeUp 0.45s ease forwards 0.55s; }
        .stag-2 { animation: fadeUp 0.45s ease forwards 0.68s; }
        .stag-3 { animation: fadeUp 0.45s ease forwards 0.78s; }
        .stag-4 { animation: fadeUp 0.45s ease forwards 0.88s; }
        .stag-5 { animation: fadeUp 0.45s ease forwards 0.97s; }
        .stag-6 { animation: fadeUp 0.45s ease forwards 1.05s; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        /* ── Tag ── */
        .tag {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            padding: 0.28rem 0.85rem;
            border-radius: 100px;
            border: 1px solid rgba(37,99,235,0.18);
            margin-bottom: 0.9rem;
        }

        h1 {
            color: #0f172a;
            font-size: 1.6rem; font-weight: 800;
            line-height: 1.2; margin-bottom: 0.6rem;
            letter-spacing: -0.03em;
        }

        .message {
            color: #64748b;
            font-size: 0.9rem; line-height: 1.65;
            margin-bottom: 1.75rem;
        }

        /* ── Info row ── */
        .info-row {
            background: #f8faff;
            border: 1px solid rgba(37,99,235,0.1);
            border-radius: 14px;
            padding: 0.95rem 1.1rem;
            display: flex; align-items: center; gap: 0.8rem;
            margin-bottom: 1.5rem; text-align: left;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .info-row:hover {
            border-color: rgba(37,99,235,0.25);
            box-shadow: 0 2px 12px rgba(37,99,235,0.07);
        }

        .info-icon {
            width: 38px; height: 38px;
            background: #eff6ff;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .info-icon svg {
            width: 17px; height: 17px;
            stroke: #2563eb; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        .info-label {
            font-size: 0.67rem; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.08em;
            font-weight: 600; margin-bottom: 0.12rem;
        }
        .info-value { color: #1e293b; font-size: 0.86rem; font-weight: 600; }

        /* ── Countdown ── */
        .countdown-wrap { margin-bottom: 1.75rem; }
        .countdown-bar-bg {
            height: 4px; background: #e2e8f0;
            border-radius: 100px; overflow: hidden; margin-bottom: 0.5rem;
        }
        .countdown-bar {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 100px; width: 100%;
            transition: width 1s linear;
        }
        .countdown-text { font-size: 0.75rem; color: #94a3b8; }
        .countdown-text span { color: #2563eb; font-weight: 700; font-variant-numeric: tabular-nums; }

        /* ── Buttons ── */
        .btn-primary {
            display: block; width: 100%;
            padding: 0.88rem 1.5rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 0.88rem; font-weight: 700;
            text-align: center; border-radius: 12px;
            text-decoration: none; letter-spacing: 0.01em;
            border: none; cursor: pointer;
            position: relative; overflow: hidden;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(37,99,235,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(37,99,235,0.4);
        }
        .btn-primary:active { transform: translateY(0) scale(0.985); }
        .btn-primary::after {
            content: '';
            position: absolute; top: 0; left: -100%; width: 55%; height: 100%;
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
        .btn-secondary:hover {
            color: #475569; background: #f8faff; border-color: rgba(37,99,235,0.2);
        }

        /* ── Logo top-left ── */
        .logo {
            position: absolute; top: 1.25rem; left: 1.5rem;
            display: flex; align-items: center; gap: 0.5rem;
            z-index: 1;
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

        @media (max-width: 460px) {
            .card { padding: 2.5rem 1.5rem 2rem; }
            h1 { font-size: 1.35rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>
    <div class="bg-dots"></div>

    <div class="card" id="card">
        <canvas id="particles"></canvas>

        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="logo-text">Attendify</div>
                <div class="logo-sub">PORTAL</div>
            </div>
        </div>

        <!-- Icon -->
        <div class="icon-wrap" id="iconWrap">
            <div class="ring-wave" id="ring1"></div>
            <div class="ring-wave" id="ring2"></div>
            <div class="ring-wave" id="ring3"></div>
            <div class="icon-circle">
                <svg class="checkmark" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>

        <div class="tag stag stag-1">
            {{ session('action') === 'checkout' ? '✓ Checked Out' : '✓ Checked In' }}
        </div>

        <h1 class="stag stag-2">
            {{ session('action') === 'checkout' ? 'See you tomorrow!' : "You're clocked in!" }}
        </h1>

        <p class="message stag stag-3">
            @if(session('success'))
                {{ session('success') }}
            @else
                Your attendance has been recorded successfully.
            @endif
        </p>

        @if(session('session'))
        <div class="info-row stag stag-4">
            <div class="info-icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="info-label">Session</div>
                <div class="info-value">{{ ucfirst(session('session')) }} &mdash; {{ now()->format('D, d M Y · H:i') }}</div>
            </div>
        </div>
        @else
        <div class="info-row stag stag-4">
            <div class="info-icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="info-label">Recorded at</div>
                <div class="info-value">{{ now()->format('D, d M Y · H:i') }}</div>
            </div>
        </div>
        @endif

        <div class="countdown-wrap stag stag-5">
            <div class="countdown-bar-bg">
                <div class="countdown-bar" id="countdownBar"></div>
            </div>
            <div class="countdown-text">Returning to attendance in <span id="countdownNum">10</span>s</div>
        </div>

        <a href="{{ route('checkin') }}" class="btn-primary stag stag-6" id="goNowBtn">Go to Attendance</a>
        <a href="{{ url('/') }}" class="btn-secondary stag stag-6">Dashboard</a>
    </div>

<script>
(function () {
    /* ── Sound: use actual mp3 files ── */
    const action = '{{ session('action') ?? 'checkin' }}';
    const soundSrc = action === 'checkout' ? '/sounds/scan-checkout.mp3' : '/sounds/scan-checkin.mp3';
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

    /* ── Ring waves ── */
    const rings = ['ring1','ring2','ring3'].map(id => document.getElementById(id));
    function fireRings() {
        rings.forEach((r, i) => {
            r.classList.remove('active');
            void r.offsetWidth;
            setTimeout(() => r.classList.add('active'), i * 160);
        });
    }
    setTimeout(fireRings, 260);
    document.getElementById('iconWrap').addEventListener('click', fireRings);

    /* ── Particle burst (blue palette) ── */
    const canvas = document.getElementById('particles');
    const card   = document.getElementById('card');
    const ctx    = canvas.getContext('2d');
    let particles = [];

    function resize() { canvas.width = card.offsetWidth; canvas.height = card.offsetHeight; }
    resize(); window.addEventListener('resize', resize);

    class Particle {
        constructor(x, y) {
            this.x = x; this.y = y;
            const angle = Math.random() * Math.PI * 2;
            const speed = 1.5 + Math.random() * 3.8;
            this.vx = Math.cos(angle) * speed;
            this.vy = Math.sin(angle) * speed - 1.2;
            this.life = 1;
            this.decay = 0.013 + Math.random() * 0.016;
            this.size = 2 + Math.random() * 2.5;
            // Blue-to-indigo palette matching Attendify
            const blues = ['rgba(37,99,235,', 'rgba(59,130,246,', 'rgba(96,165,250,', 'rgba(99,102,241,', 'rgba(147,197,253,'];
            this.color = blues[Math.floor(Math.random() * blues.length)];
        }
        update() {
            this.x += this.vx; this.y += this.vy;
            this.vy += 0.05; this.vx *= 0.988;
            this.life -= this.decay;
        }
        draw() {
            if (this.life <= 0) return;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size * this.life, 0, Math.PI * 2);
            ctx.fillStyle = this.color + this.life + ')';
            ctx.fill();
        }
    }

    function burst() {
        const cx = canvas.width / 2;
        const cy = 72;
        for (let i = 0; i < 42; i++) particles.push(new Particle(cx, cy));
    }

    (function loop() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles = particles.filter(p => p.life > 0);
        particles.forEach(p => { p.update(); p.draw(); });
        requestAnimationFrame(loop);
    })();

    setTimeout(burst, 700);

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

    document.getElementById('goNowBtn').addEventListener('click', () => clearInterval(timer));
})();
</script>
</body>
</html>
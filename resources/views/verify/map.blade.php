<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify Location - Attendify</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "DM Sans", "Noto Sans Khmer", sans-serif;
            background: #EEF2FF;
            color: #0A1628;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .icon {
            font-family: "Material Symbols Rounded";
            font-style: normal;
            font-weight: 400;
            font-size: 24px;
            line-height: 1;
            display: inline-block;
            white-space: nowrap;
            user-select: none;
        }

        .wrap { width: 100%; max-width: 420px; }

        .card {
            background: #fff;
            border: 1px solid #D6E0FF;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 12px;
        }

        /* Sound toggle — floating, top-right */
        .sound-fab {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 1000;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #D6E0FF;
            box-shadow: 0 4px 12px rgba(10, 22, 40, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s ease;
            color: #0066FF;
            font-family: inherit;
        }
        .sound-fab:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(10, 22, 40, 0.12);
        }
        .sound-fab.muted {
            color: #EF4444;
            background: #FEF2F2;
            border-color: #FECACA;
        }
        .sound-fab .icon { font-size: 22px; }

        /* Ring */
        .ring-wrap {
            width: 100px; height: 100px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            position: relative;
        }
        .ring-arc {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #0066FF;
            animation: spin 0.9s linear infinite;
        }
        .ring-arc.off { display: none; }
        .ring-inner {
            width: 72px; height: 72px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.3s;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes pop {
            0%  { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.12); }
            100%{ transform: scale(1);   opacity: 1; }
        }
        .pop { animation: pop 0.35s cubic-bezier(.34,1.56,.64,1) both; }

        /* Map */
        #map { width: 100%; height: 220px; border-radius: 14px; }
        @media(min-width:480px){ #map { height: 260px; } }

        /* Chips */
        .chips { display: flex; gap: 8px; }
        .chip {
            flex: 1; border-radius: 12px;
            border: 1.5px solid #D6E0FF;
            padding: 10px; text-align: center; font-size: 13px;
            transition: border-color .2s, background .2s;
        }
        .chip.hi-morning  { border-color: #10B981; background: #ECFDF5; }
        .chip.hi-afternoon{ border-color: #10B981; background: #ECFDF5; }

        /* Buttons */
        .btn {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; padding: 12px; border-radius: 12px;
            font-size: 14px; font-family: inherit; font-weight: 600;
            cursor: pointer; width: 100%; text-decoration: none;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn-retry { background: #0066FF; color: #fff; border: none; margin-bottom: 8px; display: none; }
        .btn-retry.show { display: flex; }
        .btn-back  { background: #fff; color: #64748B; border: 1.5px solid #D6E0FF; }
    </style>
</head>
<body>

{{-- Sound toggle — floating top-right --}}
<button type="button" id="sound-toggle" class="sound-fab" onclick="SoundManager.toggleMute()" aria-label="Toggle notification sound" title="Toggle notification sound">
    <span id="sound-icon" class="icon">volume_up</span>
</button>

<div class="wrap">

    {{-- Status card --}}
    <div class="card" style="text-align:center;">
        <div class="ring-wrap">
            <div class="ring-arc" id="ring-arc"></div>
            <div class="ring-inner" id="ring-inner" style="background:#EBF2FF;">
                <span class="icon" id="ring-icon" style="font-size:34px;color:#0066FF;">my_location</span>
            </div>
        </div>
        <p id="s-title" style="font-size:1.1rem;font-weight:700;margin-bottom:4px;">Detecting location…</p>
        <p id="s-sub"   style="font-size:13px;color:#64748B;">Please allow location access</p>
    </div>

    {{-- Session chips --}}
    <div class="card">
        <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;margin-bottom:10px;">Session</p>
        <div class="chips">
            <div class="chip" id="chip-morning">
                <span class="icon" style="font-size:20px;color:#F59E0B;display:block;margin-bottom:2px;">wb_sunny</span>
                <div style="font-weight:600;">Morning</div>
                <div style="font-size:11px;color:#94A3B8;">07:00 – 12:00</div>
                @if($todayAttendance?->morning_check_in)
                    <div style="font-size:11px;color:#10B981;margin-top:4px;">✓ In {{ $todayAttendance->morning_check_in->format('h:i A') }}</div>
                @endif
                @if($todayAttendance?->morning_check_out)
                    <div style="font-size:11px;color:#3B82F6;">✓ Out {{ $todayAttendance->morning_check_out->format('h:i A') }}</div>
                @endif
            </div>
            <div class="chip" id="chip-afternoon">
                <span class="icon" style="font-size:20px;color:#FB923C;display:block;margin-bottom:2px;">wb_twilight</span>
                <div style="font-weight:600;">Afternoon</div>
                <div style="font-size:11px;color:#94A3B8;">13:00 – 18:00</div>
                @if($todayAttendance?->afternoon_check_in)
                    <div style="font-size:11px;color:#10B981;margin-top:4px;">✓ In {{ $todayAttendance->afternoon_check_in->format('h:i A') }}</div>
                @endif
                @if($todayAttendance?->afternoon_check_out)
                    <div style="font-size:11px;color:#3B82F6;">✓ Out {{ $todayAttendance->afternoon_check_out->format('h:i A') }}</div>
                @endif
            </div>
        </div>
        <div id="action-label" style="margin-top:10px;font-size:13px;color:#94A3B8;text-align:center;min-height:20px;"></div>
    </div>

    {{-- Map --}}
    <div class="card" style="padding:12px;">
        <div id="map"></div>
        <div id="dist-label" style="margin-top:8px;font-size:12px;color:#94A3B8;text-align:center;"></div>
    </div>

    <button class="btn btn-retry" id="retry-btn" onclick="start()">
        <span class="icon" style="font-size:18px;">refresh</span> Try Again
    </button>
    <a href="/" class="btn btn-back">
        <span class="icon" style="font-size:18px;">arrow_back</span> Back
    </a>
</div>

{{-- Hidden form – note always empty --}}
<form id="form" method="POST" action="{{ route('attendance.submit') }}" style="display:none;">
    @csrf
    <input type="hidden" name="latitude"  id="f-lat">
    <input type="hidden" name="longitude" id="f-lng">
    <input type="hidden" name="action"    id="f-action">
    <input type="hidden" name="session"   id="f-session">
    <input type="hidden" name="note"      value="">
</form>

<script>
// ── Sound Manager ────────────────────────────────────────────
const SoundManager = {
    STORAGE_KEY: 'attendify_sound_muted',
    isMuted: false,
    unlocked: false,
    sounds: {},

    init() {
        this.isMuted = localStorage.getItem(this.STORAGE_KEY) === 'true';

        this.sounds = {
            // checkin:       new Audio("{{ asset('sounds/scan-checkin.mp3') }}"),
            // checkout:      new Audio("{{ asset('sounds/scan-checkout.mp3') }}"),
            // errorCheckin:  new Audio("{{ asset('sounds/error-scan-checkin.mp3') }}"),
            // errorCheckout: new Audio("{{ asset('sounds/error-scan-checkout.mp3') }}"),
        };
        Object.values(this.sounds).forEach(a => { a.preload = 'auto'; a.volume = 0.8; });

        this.updateUI();

        // Unlock audio on first user interaction (browser autoplay policy)
        const unlock = () => {
            if (this.unlocked) return;
            this.unlocked = true;
            Object.values(this.sounds).forEach(a => {
                a.play().then(() => { a.pause(); a.currentTime = 0; }).catch(() => {});
            });
            document.removeEventListener('click', unlock);
            document.removeEventListener('touchstart', unlock);
        };
        document.addEventListener('click', unlock);
        document.addEventListener('touchstart', unlock);
    },

    play(name) {
        if (this.isMuted) return null;
        const audio = this.sounds[name];
        if (!audio) return null;
        try {
            audio.currentTime = 0;
            return audio.play().catch(err => console.log('Audio play blocked:', err));
        } catch (e) {
            console.log('Audio error:', e);
            return null;
        }
    },

    toggleMute() {
        this.isMuted = !this.isMuted;
        localStorage.setItem(this.STORAGE_KEY, this.isMuted);
        this.updateUI();
        if (!this.isMuted) this.play('checkin'); // confirmation chime on un-mute
    },

    updateUI() {
        const btn = document.getElementById('sound-toggle');
        const icon = document.getElementById('sound-icon');
        if (!btn || !icon) return;

        if (this.isMuted) {
            btn.classList.add('muted');
            icon.textContent = 'volume_off';
            btn.setAttribute('title', 'Sound muted — tap to enable');
        } else {
            btn.classList.remove('muted');
            icon.textContent = 'volume_up';
            btn.setAttribute('title', 'Tap to mute sound');
        }
    }
};

// ── Config ───────────────────────────────────────────────────
const OFFICE_LAT     = {{ $officeLocation->latitude  ?? 10.635982 }};
const OFFICE_LNG     = {{ $officeLocation->longitude ?? 103.515688 }};
const ALLOWED_RADIUS = {{ $officeLocation->radius    ?? 100 }};
const OFFICE_NAME    = "{{ $officeLocation->name ?? 'Office' }}";

@php
    $canCheckInMorning    = !$todayAttendance || !$todayAttendance->morning_check_in;
    $canCheckOutMorning   = $todayAttendance  &&  $todayAttendance->morning_check_in   && !$todayAttendance->morning_check_out;
    $canCheckInAfternoon  = !$todayAttendance || !$todayAttendance->afternoon_check_in;
    $canCheckOutAfternoon = $todayAttendance  &&  $todayAttendance->afternoon_check_in && !$todayAttendance->afternoon_check_out;
@endphp

const CAN = {
    morning:   { in: {{ $canCheckInMorning   ? 'true':'false' }}, out: {{ $canCheckOutMorning   ? 'true':'false' }} },
    afternoon: { in: {{ $canCheckInAfternoon ? 'true':'false' }}, out: {{ $canCheckOutAfternoon ? 'true':'false' }} },
};

// Session clock windows
const SESSION_WINDOWS = {
    morning:   { start: '07:00', end: '12:00' },
    afternoon: { start: '13:00', end: '18:00' },
};

// Sound playback delay before form submit / state change (ms)
const SOUND_DELAY = 700;

// Tracks the most recently resolved action so error sounds can match it
let lastAction = null; // 'checkin' | 'checkout' | null

// ── Map ──────────────────────────────────────────────────────
let map, userMarker;

document.addEventListener('DOMContentLoaded', () => {
    SoundManager.init();
    initMap();
    start();
});

function initMap() {
    map = L.map('map', { zoomControl: false, attributionControl: false })
           .setView([OFFICE_LAT, OFFICE_LNG], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    L.marker([OFFICE_LAT, OFFICE_LNG], {
        icon: L.divIcon({
            className: '',
            html: `<div style="width:34px;height:34px;border-radius:50%;background:#0066FF;border:3px solid #fff;
                               box-shadow:0 4px 10px rgba(0,102,255,.35);display:flex;align-items:center;justify-content:center;">
                     <span class="icon" style="font-size:17px;color:#fff;">corporate_fare</span>
                   </div>`,
            iconSize: [34,34], iconAnchor: [17,17]
        })
    }).addTo(map).bindPopup(`<b style="color:#0066FF">${OFFICE_NAME}</b><br><small>${ALLOWED_RADIUS}m radius</small>`);

    L.circle([OFFICE_LAT, OFFICE_LNG], {
        color: '#0066FF', fillColor: '#0066FF',
        fillOpacity: 0.1, weight: 2, radius: ALLOWED_RADIUS
    }).addTo(map);

    L.control.zoom({ position: 'bottomright' }).addTo(map);
}

// ── Main ─────────────────────────────────────────────────────
function start() {
    ui('locating', 'Detecting location…', 'Please allow location access');
    hideRetry();
    lastAction = null;

    if (!navigator.geolocation) {
        return ui('error', 'Geolocation not supported', 'Switch to a modern browser');
    }

    navigator.geolocation.getCurrentPosition(onGot, onFail, {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0
    });
}

function onGot(pos) {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;

    document.getElementById('f-lat').value = lat;
    document.getElementById('f-lng').value = lng;

    // User dot on map
    if (userMarker) map.removeLayer(userMarker);
    userMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            className: '',
            html: `<div style="width:20px;height:20px;border-radius:50%;background:#10B981;border:3px solid #fff;
                               box-shadow:0 3px 8px rgba(16,185,129,.4);"></div>`,
            iconSize: [20,20], iconAnchor: [10,10]
        })
    }).addTo(map);

    map.fitBounds(
        L.latLngBounds([[OFFICE_LAT, OFFICE_LNG],[lat, lng]]),
        { padding: [40,40], maxZoom: 17 }
    );

    const dist = haversine(lat, lng, OFFICE_LAT, OFFICE_LNG);
    document.getElementById('dist-label').textContent = `📍 ${Math.round(dist)}m from ${OFFICE_NAME}`;

    // ── Outside radius ───────────────────────────────────────
    if (dist > ALLOWED_RADIUS) {
        // Try to know whether user was attempting check-in or check-out for matching error sound
        const probe = resolveSessionAction();
        lastAction = probe.action;

        // 🔊 Error sound — match action if known
        SoundManager.play(lastAction === 'checkout' ? 'errorCheckout' : 'errorCheckin');

        ui('outside', 'Outside Allowed Zone',
           `${Math.round(dist)}m away — ${Math.round(dist - ALLOWED_RADIUS)}m outside the zone`);
        showRetry();
        return;
    }

    // ── Resolve session + action from current time ───────────
    const { session, action } = resolveSessionAction();

    if (!session || !action) {
        // 🔊 Error sound — nothing to do
        SoundManager.play('errorCheckin');

        ui('error', 'Nothing to Do',
           'All sessions are complete or outside operating hours');
        showRetry();
        return;
    }

    lastAction = action;

    // Highlight chip
    document.getElementById('chip-' + session).classList.add('hi-' + session);
    setActionLabel(session, action);
    ui('submitting', 'Submitting…',
       `${cap(session)} ${action === 'checkin' ? 'Check-In' : 'Check-Out'}`);

    // Fill form
    document.getElementById('f-action').value  = action;
    document.getElementById('f-session').value = session;

    // 🔊 Success sound — play matching check-in/check-out chime, THEN submit
    SoundManager.play(action === 'checkout' ? 'checkout' : 'checkin');
    setTimeout(() => {
        document.getElementById('form').submit();
    }, SOUND_DELAY);
}

function onFail(err) {
    // 🔊 Error sound — geolocation failure (generic, action not yet known)
    SoundManager.play('errorCheckin');

    const msgs = {
        1: 'Location access denied. Enable it in browser settings.',
        2: 'Position unavailable. Please try again.',
        3: 'Request timed out. Please try again.',
    };
    ui('error', 'Location Error', msgs[err.code] || 'Unable to get location.');
    showRetry();
}

// ── Session resolver ─────────────────────────────────────────
function resolveSessionAction() {
    const t = now24();

    // Pass 1: match by current clock window
    for (const session of ['morning', 'afternoon']) {
        const { start, end } = SESSION_WINDOWS[session];
        if (t >= start && t <= end) {
            if (CAN[session].in)  return { session, action: 'checkin'  };
            if (CAN[session].out) return { session, action: 'checkout' };
        }
    }

    // Pass 2: outside windows — pick first session with pending action
    for (const session of ['morning', 'afternoon']) {
        if (CAN[session].in)  return { session, action: 'checkin'  };
        if (CAN[session].out) return { session, action: 'checkout' };
    }

    return { session: null, action: null };
}

// ── UI helpers ───────────────────────────────────────────────
const STATES = {
    locating:   { icon: 'my_location',    bg: '#EBF2FF', color: '#0066FF', spin: true  },
    submitting: { icon: 'check_circle',   bg: '#ECFDF5', color: '#10B981', spin: true  },
    outside:    { icon: 'wrong_location', bg: '#FFFBEB', color: '#F59E0B', spin: false },
    error:      { icon: 'error',          bg: '#FEF2F2', color: '#EF4444', spin: false },
};

function ui(state, title, sub) {
    const c = STATES[state] || STATES.error;
    const arc   = document.getElementById('ring-arc');
    const inner = document.getElementById('ring-inner');
    const icon  = document.getElementById('ring-icon');

    arc.className = c.spin ? 'ring-arc' : 'ring-arc off';
    inner.style.background = c.bg;
    inner.classList.remove('pop');
    requestAnimationFrame(() => inner.classList.add('pop'));

    icon.style.color = c.color;
    icon.textContent = c.icon;

    document.getElementById('s-title').textContent = title;
    document.getElementById('s-sub').textContent   = sub;
}

function setActionLabel(session, action) {
    const col  = action === 'checkin' ? '#10B981' : '#3B82F6';
    const verb = action === 'checkin' ? 'Check-In' : 'Check-Out';
    const ico  = action === 'checkin' ? 'login' : 'logout';
    const em   = session === 'morning' ? '🌞' : '🌅';
    document.getElementById('action-label').innerHTML =
        `<span style="color:${col};font-weight:600;">
            <span class="icon" style="font-size:15px;vertical-align:middle;">${ico}</span>
            ${em} ${cap(session)} ${verb}
         </span>`;
}

function showRetry() { document.getElementById('retry-btn').classList.add('show'); }
function hideRetry() { document.getElementById('retry-btn').classList.remove('show'); }

// ── Utils ─────────────────────────────────────────────────────
function now24() {
    const n = new Date();
    return `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
}

function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

function haversine(lat1, lon1, lat2, lon2) {
    const R = 6371e3;
    const p1 = lat1*Math.PI/180, p2 = lat2*Math.PI/180;
    const dp = (lat2-lat1)*Math.PI/180, dl = (lon2-lon1)*Math.PI/180;
    const a  = Math.sin(dp/2)**2 + Math.cos(p1)*Math.cos(p2)*Math.sin(dl/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}
</script>
</body>
</html>
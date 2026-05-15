{{-- resources/views/admin/report-violations.blade.php --}}
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Time Violations Report - Attendify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
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
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: "Inter", "Noto Sans Khmer", sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes badge-in {
            from { opacity: 0; transform: scale(0.85); }
            to   { opacity: 1; transform: scale(1); }
        }
        .badge-anim { animation: badge-in 0.3s ease-out both; }

        @media print {
            .no-print { display: none !important; }
            #sidebar, header { display: none !important; }
            .flex-1 { margin: 0 !important; padding: 16px !important; }
        }
    </style>
    @livewireStyles
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-display flex h-screen overflow-hidden">

    <div id="sidebar" class="no-print">
        @include('home.Layouts.sidebar')
    </div>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="h-16 md:h-20 flex items-center justify-between px-4 md:px-6 lg:px-10 bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800 shrink-0 z-10 no-print">
            <div class="flex items-center gap-3 lg:hidden">
                <span class="font-bold text-base">Time Violations</span>
            </div>
            <div class="hidden lg:block">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">warning</span>
                    Time Violations Report
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Late check-ins &amp; out-of-window check-outs</p>
            </div>
            @include('home.Layouts.header')
        </header>

        <div class="no-print">@include('home.Layouts.mobile')</div>

        <main class="flex-1 overflow-y-auto p-3 md:p-6 lg:p-10">

            {{-- Rule legend --}}
            <div class="mb-4 md:mb-6 grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 md:p-4">
                    <p class="text-[10px] md:text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase mb-1">🌞 Morning Check-In</p>
                    <p class="text-sm md:text-base font-bold text-amber-900 dark:text-amber-100">After 09:00 AM</p>
                    <p class="text-[10px] md:text-xs text-amber-600 dark:text-amber-400 mt-0.5">Flagged as late</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-3 md:p-4">
                    <p class="text-[10px] md:text-xs font-semibold text-orange-700 dark:text-orange-400 uppercase mb-1">🌞 Morning Check-Out</p>
                    <p class="text-sm md:text-base font-bold text-orange-900 dark:text-orange-100">11:00 – 12:30 PM</p>
                    <p class="text-[10px] md:text-xs text-orange-600 dark:text-orange-400 mt-0.5">Outside window = early / late</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-3 md:p-4">
                    <p class="text-[10px] md:text-xs font-semibold text-purple-700 dark:text-purple-400 uppercase mb-1">🌅 Afternoon Check-In</p>
                    <p class="text-sm md:text-base font-bold text-purple-900 dark:text-purple-100">After 03:00 PM</p>
                    <p class="text-[10px] md:text-xs text-purple-600 dark:text-purple-400 mt-0.5">Flagged as late</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 md:p-4">
                    <p class="text-[10px] md:text-xs font-semibold text-red-700 dark:text-red-400 uppercase mb-1">🌅 Afternoon Check-Out</p>
                    <p class="text-sm md:text-base font-bold text-red-900 dark:text-red-100">05:00 – 06:30 PM</p>
                    <p class="text-[10px] md:text-xs text-red-600 dark:text-red-400 mt-0.5">Outside window = early / late</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-6 mb-4 md:mb-6 no-print">
                <h2 class="text-sm md:text-base font-bold text-gray-900 dark:text-white mb-3">Filters</h2>
                <form method="GET" action="{{ route('reports.violations') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Employee</label>
                        <select name="user_id"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Employees</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Violation Type</label>
                        <select name="type"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Violations</option>
                            <option value="late_morning_in"     {{ $type === 'late_morning_in'     ? 'selected' : '' }}>Late Morning Check-In (after 09:00)</option>
                            <option value="early_morning_out"   {{ $type === 'early_morning_out'   ? 'selected' : '' }}>Early Morning Check-Out (before 11:00)</option>
                            <option value="late_morning_out"    {{ $type === 'late_morning_out'    ? 'selected' : '' }}>Late Morning Check-Out (after 12:30)</option>
                            <option value="late_afternoon_in"   {{ $type === 'late_afternoon_in'   ? 'selected' : '' }}>Late Afternoon Check-In (after 15:00)</option>
                            <option value="early_afternoon_out" {{ $type === 'early_afternoon_out' ? 'selected' : '' }}>Early Afternoon Check-Out (before 17:00)</option>
                            <option value="late_afternoon_out"  {{ $type === 'late_afternoon_out'  ? 'selected' : '' }}>Late Afternoon Check-Out (after 18:30)</option>
                        </select>
                    </div>

                    <div class="md:col-span-4 flex flex-wrap gap-2 pt-1">
                        <button type="submit"
                            class="px-4 py-2 text-sm bg-primary hover:bg-primary-dark text-white rounded-xl font-medium flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">filter_alt</span> Apply
                        </button>
                        <a href="{{ route('reports.violations') }}"
                            class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">restart_alt</span> Reset
                        </a>
                        <a href="{{ route('reports.violations.export', request()->all()) }}"
                            class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">download</span> Export CSV
                        </a>
                        <a href="{{ route('reports') }}"
                            class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 rounded-xl font-medium flex items-center gap-1.5 ml-auto">
                            <span class="material-symbols-outlined text-base">receipt_long</span> Full Report
                        </a>
                    </div>
                </form>

                <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    @foreach([
                        'Today'       => [now()->format('Y-m-d'), now()->format('Y-m-d')],
                        'This Week'   => [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
                        'This Month'  => [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
                        'Last Month'  => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
                    ] as $label => [$s, $e])
                        <a href="{{ route('reports.violations', array_merge(request()->except(['start_date','end_date']), ['start_date'=>$s,'end_date'=>$e])) }}"
                            class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Summary stats (6 categories) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 md:gap-4 mb-4 md:mb-6">
                @php
                    $statDefs = [
                        ['label'=>'Late Morning In',     'count'=>$stats['late_morning_in'],     'color'=>'amber',  'icon'=>'schedule'],
                        ['label'=>'Early Morning Out',   'count'=>$stats['early_morning_out'],   'color'=>'orange', 'icon'=>'logout'],
                        ['label'=>'Late Morning Out',    'count'=>$stats['late_morning_out'],    'color'=>'yellow', 'icon'=>'logout'],
                        ['label'=>'Late Afternoon In',   'count'=>$stats['late_afternoon_in'],   'color'=>'purple', 'icon'=>'schedule'],
                        ['label'=>'Early Afternoon Out', 'count'=>$stats['early_afternoon_out'], 'color'=>'red',    'icon'=>'logout'],
                        ['label'=>'Late Afternoon Out',  'count'=>$stats['late_afternoon_out'],  'color'=>'pink',   'icon'=>'logout'],
                    ];
                @endphp
                @foreach($statDefs as $s)
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 p-3 md:p-4">
                        <div class="flex items-center justify-between mb-1 md:mb-2">
                            <span class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase">{{ $s['label'] }}</span>
                            <span class="material-symbols-outlined text-base md:text-xl text-{{ $s['color'] }}-500">{{ $s['icon'] }}</span>
                        </div>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $s['count'] }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500 mt-1">violations</p>
                    </div>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white">Violation Records</h3>
                        <p class="text-[10px] md:text-xs text-gray-500 mt-0.5">
                            {{ $violations->firstItem() ?? 0 }}–{{ $violations->lastItem() ?? 0 }} of {{ $violations->total() }} records
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs md:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">Employee</th>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">Date</th>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">🌞 Morning In</th>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">🌞 Morning Out</th>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">🌅 Afternoon In</th>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">🌅 Afternoon Out</th>
                                <th class="text-left py-3 px-3 md:px-6 text-[10px] font-semibold text-gray-500 uppercase">Flags</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($violations as $row)
                                @php
                                    $flags = [];

                                    if ($row->morning_check_in && $row->morning_check_in->format('H:i') > '09:00') {
                                        $flags[] = ['label' => 'Late In', 'color' => 'amber', 'time' => $row->morning_check_in->format('h:i A')];
                                    }
                                    if ($row->morning_check_out && $row->morning_check_out->format('H:i') < '11:00') {
                                        $flags[] = ['label' => 'Early Out', 'color' => 'orange', 'time' => $row->morning_check_out->format('h:i A')];
                                    } elseif ($row->morning_check_out && $row->morning_check_out->format('H:i') > '12:30') {
                                        $flags[] = ['label' => 'Late Out',  'color' => 'yellow', 'time' => $row->morning_check_out->format('h:i A')];
                                    }
                                    if ($row->afternoon_check_in && $row->afternoon_check_in->format('H:i') > '15:00') {
                                        $flags[] = ['label' => 'Late In', 'color' => 'purple', 'time' => $row->afternoon_check_in->format('h:i A')];
                                    }
                                    if ($row->afternoon_check_out && $row->afternoon_check_out->format('H:i') < '17:00') {
                                        $flags[] = ['label' => 'Early Out', 'color' => 'red',  'time' => $row->afternoon_check_out->format('h:i A')];
                                    } elseif ($row->afternoon_check_out && $row->afternoon_check_out->format('H:i') > '18:30') {
                                        $flags[] = ['label' => 'Late Out',  'color' => 'pink', 'time' => $row->afternoon_check_out->format('h:i A')];
                                    }

                                    $colorMap = [
                                        'amber'  => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        'orange' => 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-800',
                                        'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                                        'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                        'red'    => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800',
                                        'pink'   => 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300 border-pink-200 dark:border-pink-800',
                                    ];

                                    $okStyle = 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800';

                                    $mInBad  = $row->morning_check_in   && $row->morning_check_in->format('H:i')   > '09:00';
                                    $mOutColor = null;
                                    if ($row->morning_check_out) {
                                        if ($row->morning_check_out->format('H:i') < '11:00')      $mOutColor = 'orange';
                                        elseif ($row->morning_check_out->format('H:i') > '12:30')  $mOutColor = 'yellow';
                                    }
                                    $aInBad  = $row->afternoon_check_in  && $row->afternoon_check_in->format('H:i')  > '15:00';
                                    $aOutColor = null;
                                    if ($row->afternoon_check_out) {
                                        if ($row->afternoon_check_out->format('H:i') < '17:00')     $aOutColor = 'red';
                                        elseif ($row->afternoon_check_out->format('H:i') > '18:30') $aOutColor = 'pink';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-3 px-3 md:px-6">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate max-w-[130px]">{{ $row->user->name }}</p>
                                        <p class="text-[10px] text-gray-400 truncate hidden sm:block">{{ $row->user->email }}</p>
                                    </td>

                                    <td class="py-3 px-3 md:px-6 whitespace-nowrap">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $row->attendance_date->format('M d, Y') }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $row->attendance_date->format('l') }}</p>
                                    </td>

                                    <td class="py-3 px-3 md:px-6 whitespace-nowrap">
                                        @if($row->morning_check_in)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold border
                                                {{ $mInBad ? $colorMap['amber'] : $okStyle }}">
                                                @if($mInBad)<span class="material-symbols-outlined text-sm">warning</span>@endif
                                                {{ $row->morning_check_in->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 md:px-6 whitespace-nowrap">
                                        @if($row->morning_check_out)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold border
                                                {{ $mOutColor ? $colorMap[$mOutColor] : $okStyle }}">
                                                @if($mOutColor)<span class="material-symbols-outlined text-sm">warning</span>@endif
                                                {{ $row->morning_check_out->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 md:px-6 whitespace-nowrap">
                                        @if($row->afternoon_check_in)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold border
                                                {{ $aInBad ? $colorMap['purple'] : $okStyle }}">
                                                @if($aInBad)<span class="material-symbols-outlined text-sm">warning</span>@endif
                                                {{ $row->afternoon_check_in->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 md:px-6 whitespace-nowrap">
                                        @if($row->afternoon_check_out)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold border
                                                {{ $aOutColor ? $colorMap[$aOutColor] : $okStyle }}">
                                                @if($aOutColor)<span class="material-symbols-outlined text-sm">warning</span>@endif
                                                {{ $row->afternoon_check_out->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 md:px-6">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($flags as $flag)
                                                <span class="badge-anim inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md text-[10px] font-bold border {{ $colorMap[$flag['color']] }}">
                                                    {{ $flag['label'] }}
                                                    <span class="opacity-70">({{ $flag['time'] }})</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <span class="material-symbols-outlined text-5xl text-gray-200">check_circle</span>
                                        <p class="text-sm text-gray-400 mt-2">No violations found for the selected period</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($violations->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-800 no-print">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs text-gray-500">
                                {{ $violations->firstItem() }}–{{ $violations->lastItem() }} of {{ $violations->total() }}
                            </p>
                            <div class="flex items-center gap-2">
                                @if($violations->onFirstPage())
                                    <button disabled class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-300 cursor-not-allowed">
                                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                                    </button>
                                @else
                                    <a href="{{ $violations->appends(request()->except('page'))->previousPageUrl() }}" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                                    </a>
                                @endif

                                <span class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Page {{ $violations->currentPage() }} / {{ $violations->lastPage() }}
                                </span>

                                @if($violations->hasMorePages())
                                    <a href="{{ $violations->appends(request()->except('page'))->nextPageUrl() }}" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                                    </a>
                                @else
                                    <button disabled class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-300 cursor-not-allowed">
                                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    @livewireScripts
    <script>
        const menuToggle = document.getElementById("menu-toggle");
        const mobileMenu = document.getElementById("mobile-menu");
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener("click", e => { e.stopPropagation(); mobileMenu.classList.toggle("hidden"); });
        }
        document.addEventListener("click", e => {
            if (menuToggle && mobileMenu && !menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add("hidden");
            }
        });
    </script>
</body>
</html>
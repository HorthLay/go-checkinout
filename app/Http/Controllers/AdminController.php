<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOffDay;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{


  public function index()
    {
        // Get current month date range
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        // Overall Statistics
        $stats = [
            'total_employees' => User::where('role_type', 'user')->count(),
            'total_present_today' => Attendance::whereDate('attendance_date', today())
                                              ->whereIn('status', ['on_time', 'late'])
                                              ->count(),
            'total_late_today' => Attendance::whereDate('attendance_date', today())
                                           ->where('status', 'late')
                                           ->count(),
            'total_absent_today' => Attendance::whereDate('attendance_date', today())
                                             ->where('status', 'absent')
                                             ->count(),
            'total_hours_month' => Attendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                            ->sum('work_hours'),
            'avg_hours_month' => Attendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                          ->avg('work_hours'),
        ];
        
        // Top 5 Performers (This Month)
        $topPerformers = User::where('role_type', 'user')
                            ->withSum(['attendances as total_hours' => function($query) use ($startOfMonth, $endOfMonth) {
                                $query->whereBetween('attendance_date', [$startOfMonth, $endOfMonth]);
                            }], 'work_hours')
                            ->withCount(['attendances as present_days' => function($query) use ($startOfMonth, $endOfMonth) {
                                $query->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                      ->whereIn('status', ['on_time', 'late']);
                            }])
                            ->withCount(['attendances as late_days' => function($query) use ($startOfMonth, $endOfMonth) {
                                $query->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                      ->where('status', 'late');
                            }])
                            ->having('total_hours', '>', 0)
                            ->orderByDesc('total_hours')
                            ->take(5)
                            ->get();
        
        // Recent Attendance (Last 10 with any check-in)
        $recentAttendance = Attendance::with(['user'])
                                     ->where(function($query) {
                                         $query->whereNotNull('morning_check_in')
                                               ->orWhereNotNull('afternoon_check_in');
                                     })
                                     ->orderBy('created_at', 'desc')
                                     ->take(10)
                                     ->get();
        
        // Daily Attendance for Last 7 Days (for chart)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayAttendances = Attendance::whereDate('attendance_date', $date)->get();
            
            $last7Days->push([
                'date' => $date->format('D'),
                'full_date' => $date->format('M d'),
                'present' => $dayAttendances->whereIn('status', ['on_time', 'late'])->count(),
                'late' => $dayAttendances->where('status', 'late')->count(),
                'absent' => $dayAttendances->where('status', 'absent')->count(),
                'leave' => $dayAttendances->where('status', 'leave')->count(),
            ]);
        }
        
        // Status Distribution (This Month)
        $statusDistribution = [
            'on_time' => Attendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                  ->where('status', 'on_time')
                                  ->count(),
            'late' => Attendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                               ->where('status', 'late')
                               ->count(),
            'absent' => Attendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                 ->where('status', 'absent')
                                 ->count(),
            'leave' => Attendance::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                                ->where('status', 'leave')
                                ->count(),
        ];
        
        // Office Location Usage
        $locationStats = OfficeLocation::withCount(['attendances' => function($query) use ($startOfMonth, $endOfMonth) {
                                          $query->whereBetween('attendance_date', [$startOfMonth, $endOfMonth]);
                                      }])
                                      ->having('attendances_count', '>', 0)
                                      ->orderByDesc('attendances_count')
                                      ->get();
        
        // Today's Summary with Morning/Afternoon breakdown
        $todayAttendances = Attendance::whereDate('attendance_date', today())->get();
        
        $todaySummary = [
            'morning_checked_in' => $todayAttendances->whereNotNull('morning_check_in')->count(),
            'afternoon_checked_in' => $todayAttendances->whereNotNull('afternoon_check_in')->count(),
            'on_time' => $todayAttendances->where('status', 'on_time')->count(),
            'late' => $todayAttendances->where('status', 'late')->count(),
            'complete_days' => $todayAttendances->filter(function($attendance) {
                return $attendance->isFullDayComplete();
            })->count(),
            'pending' => User::where('role_type', 'user')->count() - 
                        $todayAttendances->whereNotNull('morning_check_in')->count(),
        ];
        
        return view('admin.dashboard', compact(
            'stats',
            'topPerformers',
            'recentAttendance',
            'last7Days',
            'statusDistribution',
            'locationStats',
            'todaySummary'
        ));
    }

  public function qrmake(){
    return view("admin.qrmake");
  }

    public function attendance(Request $request)
    {
        $activeTab = $request->get('tab', 'schedules');

        $users = User::where('role_type', 'user')
                    ->with('attendanceSchedule')
                    ->orderBy('name')
                    ->paginate(9, ['*'], 'schedules_page')
                    ->appends(['tab' => 'schedules']);

        $dayOffs = AttendanceOffDay::with('user')
                                  ->orderBy('off_date', 'desc')
                                  ->paginate(10, ['*'], 'dayoffs_page')
                                  ->appends(['tab' => 'dayoffs']);

        $attendances = Attendance::with('user')
                                ->whereMonth('attendance_date', now()->month)
                                ->orderBy('attendance_date', 'desc')
                                ->paginate(15, ['*'], 'records_page')
                                ->appends(['tab' => 'records']);

        return view('admin.adminattendance', compact('users', 'dayOffs', 'attendances', 'activeTab'));
    }


        public function mapcreated()
    {
        $locations = OfficeLocation::orderBy('created_at', 'desc')->paginate(9);
        return view('admin.map', compact('locations'));
    }

    public function create()
    {
        return view('admin.map-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        OfficeLocation::create($validated);

        return redirect()->route('map.created')
                       ->with('success', 'Location created successfully!');
    }

    public function edit($id)
    {
        $location = OfficeLocation::findOrFail($id);
        return view('admin.map-edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $location = OfficeLocation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        return redirect()->route('map.created')
                       ->with('success', 'Location updated successfully!');
    }

    public function toggle($id)
    {
        $location = OfficeLocation::findOrFail($id);
        $location->is_active = !$location->is_active;
        $location->save();

        return redirect()->route('map.created')
                       ->with('success', 'Location status updated successfully!');
    }

    public function destroy($id)
    {
        $location = OfficeLocation::findOrFail($id);
        $location->delete();

        return redirect()->route('map.created')
                       ->with('success', 'Location deleted successfully!');
    }


public function report(Request $request)
{
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));
    $userId    = $request->input('user_id');

    $users = User::where('role_type', 'user')
                 ->when($userId, fn($q) => $q->where('id', $userId))
                 ->orderBy('created_at')
                 ->orderBy('name')
                 ->get();

    $dateRange = [];
    $currentDate   = Carbon::parse($startDate);
    $endDateCarbon = Carbon::parse($endDate);
    while ($currentDate->lte($endDateCarbon)) {
        $dayOfWeek = $currentDate->dayOfWeek;
        if ($dayOfWeek >= Carbon::MONDAY && $dayOfWeek <= Carbon::FRIDAY) {
            $dateRange[] = $currentDate->copy();
        }
        $currentDate->addDay();
    }

    $existingAttendances = Attendance::with(['user'])
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->where(function ($q) {
            $q->whereNull('morning_check_in')
              ->orWhereRaw("TIME(morning_check_in) <= '09:00:00'");
        })
        ->where(function ($q) {
            $q->whereNull('morning_check_out')
              ->orWhereRaw("TIME(morning_check_out) BETWEEN '11:00:00' AND '12:30:00'");
        })
        ->where(function ($q) {
            $q->whereNull('afternoon_check_in')
              ->orWhereRaw("TIME(afternoon_check_in) <= '15:00:00'");
        })
        ->where(function ($q) {
            $q->whereNull('afternoon_check_out')
              ->orWhereRaw("TIME(afternoon_check_out) BETWEEN '17:00:00' AND '18:30:00'");
        })
        ->get()
        ->groupBy(fn($item) => $item->user_id . '_' . $item->attendance_date->format('Y-m-d'));

    $missions = Mission::with(['user'])
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->get()
        ->groupBy(fn($item) => $item->user_id . '_' . $item->created_at->format('Y-m-d'));

    $allAttendances = collect();

    foreach ($users as $user) {
        foreach ($dateRange as $date) {
            $key = $user->id . '_' . $date->format('Y-m-d');

            if (isset($existingAttendances[$key])) {
                $attendance = $existingAttendances[$key]->first();

                if (is_null($attendance->morning_check_in) &&
                    is_null($attendance->afternoon_check_in) &&
                    $attendance->status !== 'leave') {
                    $attendance->status = 'absent';
                    if (empty($attendance->absent_note)) {
                        $attendance->absent_note = 'No check-in recorded';
                    }
                }

                $attendance->day_missions = $missions[$key] ?? collect([]);
                $allAttendances->push($attendance);
            } else {
                $virtualAttendance = new Attendance([
                    'user_id'         => $user->id,
                    'attendance_date' => $date,
                    'status'          => 'absent',
                    'absent_note'     => 'No attendance record',
                    'work_hours'      => 0,
                ]);
                $virtualAttendance->user         = $user;
                $virtualAttendance->exists       = false;
                $virtualAttendance->day_missions = $missions[$key] ?? collect([]);

                $allAttendances->push($virtualAttendance);
            }
        }
    }

    $allAttendances = $allAttendances->sortByDesc(fn($item) => $item->attendance_date->format('Y-m-d'))->values();

    $perPage     = 20;
    $currentPage = $request->input('page', 1);
    $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
        $allAttendances->forPage($currentPage, $perPage),
        $allAttendances->count(),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $stats = [
        'total_present' => $allAttendances->whereIn('status', ['on_time', 'late'])->count(),
        'total_late'    => $allAttendances->where('status', 'late')->count(),
        'total_absent'  => $allAttendances->where('status', 'absent')->count(),
        'total_leave'   => $allAttendances->where('status', 'leave')->count(),
        'total_hours'   => $allAttendances->sum('work_hours'),
        'avg_hours'     => $allAttendances->avg('work_hours'),
        'total_morning_hours'   => $allAttendances->sum(fn($a) => $a->morning_work_hours ?? 0),
        'total_afternoon_hours' => $allAttendances->sum(fn($a) => $a->afternoon_work_hours ?? 0),
    ];

    $topUsers = User::where('role_type', 'user')
        ->withSum(['attendances as total_hours' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('attendance_date', [$startDate, $endDate])
                  ->whereRaw('DAYOFWEEK(attendance_date) BETWEEN 2 AND 6')
                  ->where(function ($q) {
                      $q->whereNull('morning_check_in')
                        ->orWhereRaw("TIME(morning_check_in) <= '09:00:00'");
                  })
                  ->where(function ($q) {
                      $q->whereNull('morning_check_out')
                        ->orWhereRaw("TIME(morning_check_out) BETWEEN '11:00:00' AND '12:30:00'");
                  })
                  ->where(function ($q) {
                      $q->whereNull('afternoon_check_in')
                        ->orWhereRaw("TIME(afternoon_check_in) <= '15:00:00'");
                  })
                  ->where(function ($q) {
                      $q->whereNull('afternoon_check_out')
                        ->orWhereRaw("TIME(afternoon_check_out) BETWEEN '17:00:00' AND '18:30:00'");
                  });
        }], 'work_hours')
        ->withCount(['attendances as total_days' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('attendance_date', [$startDate, $endDate])
                  ->whereIn('status', ['on_time', 'late'])
                  ->whereRaw('DAYOFWEEK(attendance_date) BETWEEN 2 AND 6')
                  ->where(function ($q) {
                      $q->whereNull('morning_check_in')
                        ->orWhereRaw("TIME(morning_check_in) <= '09:00:00'");
                  })
                  ->where(function ($q) {
                      $q->whereNull('morning_check_out')
                        ->orWhereRaw("TIME(morning_check_out) BETWEEN '11:00:00' AND '12:30:00'");
                  })
                  ->where(function ($q) {
                      $q->whereNull('afternoon_check_in')
                        ->orWhereRaw("TIME(afternoon_check_in) <= '15:00:00'");
                  })
                  ->where(function ($q) {
                      $q->whereNull('afternoon_check_out')
                        ->orWhereRaw("TIME(afternoon_check_out) BETWEEN '17:00:00' AND '18:30:00'");
                  });
        }])
        ->having('total_hours', '>', 0)
        ->orderByDesc('total_hours')
        ->take(5)
        ->get();

    $dailySummary = $allAttendances->groupBy(fn($item) => $item->attendance_date->format('Y-m-d'))
        ->map(function ($dayAttendances) {
            $date = $dayAttendances->first()->attendance_date;
            return [
                'date'           => $date->format('Y-m-d'),
                'day_name'       => $date->format('l'),
                'day_name_short' => $date->format('D'),
                'present'        => $dayAttendances->whereIn('status', ['on_time', 'late'])->count(),
                'late'           => $dayAttendances->where('status', 'late')->count(),
                'absent'         => $dayAttendances->where('status', 'absent')->count(),
                'leave'          => $dayAttendances->where('status', 'leave')->count(),
                'total_hours'    => $dayAttendances->sum('work_hours'),
            ];
        })->sortBy('date')->values();

    $allUsers  = User::where('role_type', 'user')->orderBy('name')->get();
    $locations = OfficeLocation::all();

    return view('admin.report', compact(
        'attendances', 'stats', 'topUsers', 'dailySummary',
        'allUsers', 'locations', 'startDate', 'endDate', 'userId'
    ));
}

public function exportCSV(Request $request)
{
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));
    $userId    = $request->input('user_id');

    $users = User::where('role_type', 'user')
                 ->when($userId, fn($q) => $q->where('id', $userId))
                 ->orderBy('created_at')
                 ->orderBy('name')
                 ->get();

    $dateRange = [];
    $currentDate   = Carbon::parse($startDate);
    $endDateCarbon = Carbon::parse($endDate);
    while ($currentDate->lte($endDateCarbon)) {
        $dayOfWeek = $currentDate->dayOfWeek;
        if ($dayOfWeek >= Carbon::MONDAY && $dayOfWeek <= Carbon::FRIDAY) {
            $dateRange[] = $currentDate->copy();
        }
        $currentDate->addDay();
    }

    $existingAttendances = Attendance::with(['user'])
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->where(function ($q) {
            $q->whereNull('morning_check_in')
              ->orWhereRaw("TIME(morning_check_in) <= '09:00:00'");
        })
        ->where(function ($q) {
            $q->whereNull('morning_check_out')
              ->orWhereRaw("TIME(morning_check_out) BETWEEN '11:00:00' AND '12:30:00'");
        })
        ->where(function ($q) {
            $q->whereNull('afternoon_check_in')
              ->orWhereRaw("TIME(afternoon_check_in) <= '15:00:00'");
        })
        ->where(function ($q) {
            $q->whereNull('afternoon_check_out')
              ->orWhereRaw("TIME(afternoon_check_out) BETWEEN '17:00:00' AND '18:30:00'");
        })
        ->get()
        ->groupBy(fn($item) => $item->user_id . '_' . $item->attendance_date->format('Y-m-d'));

    $missions = Mission::with(['user'])
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->get()
        ->groupBy(fn($item) => $item->user_id . '_' . $item->created_at->format('Y-m-d'));

    $attendances = collect();

    foreach ($users as $user) {
        foreach ($dateRange as $date) {
            $key = $user->id . '_' . $date->format('Y-m-d');

            if (isset($existingAttendances[$key])) {
                $attendance = $existingAttendances[$key]->first();

                if (is_null($attendance->morning_check_in) &&
                    is_null($attendance->afternoon_check_in) &&
                    $attendance->status !== 'leave') {
                    $attendance->status = 'absent';
                    if (empty($attendance->absent_note)) {
                        $attendance->absent_note = 'No check-in recorded';
                    }
                }

                $attendance->day_missions = $missions[$key] ?? collect([]);
                $attendances->push($attendance);
            } else {
                $virtualAttendance = new Attendance([
                    'user_id'              => $user->id,
                    'attendance_date'      => $date,
                    'status'               => 'absent',
                    'absent_note'          => 'No attendance record',
                    'work_hours'           => 0,
                    'morning_work_hours'   => 0,
                    'afternoon_work_hours' => 0,
                ]);
                $virtualAttendance->user         = $user;
                $virtualAttendance->day_missions = $missions[$key] ?? collect([]);
                $attendances->push($virtualAttendance);
            }
        }
    }

    $attendances = $attendances->sortBy(fn($item) => $item->attendance_date->format('Y-m-d'))->values();

    $filename = 'attendance-report-' . now()->format('Y-m-d-His') . '.csv';
    $headers = [
        'Content-Type'        => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    $callback = function () use ($attendances) {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, [
            'Employee', 'Day', 'Date',
            'Morning Check-In', 'Morning Check-Out',
            'Afternoon Check-In', 'Afternoon Check-Out',
            'Morning Status', 'Afternoon Status',
            'Mission Status', 'Mission Check-In',
        ]);

        foreach ($attendances as $attendance) {
            $morningStatus = ($attendance->morning_work_hours === null || $attendance->morning_work_hours == 0)
                ? '--' : 'វត្តមាន';
            $afternoonStatus = ($attendance->afternoon_work_hours === null || $attendance->afternoon_work_hours == 0)
                ? '--' : 'វត្តមាន';

            $missionStatus  = '--';
            $missionCheckIn = '--';
            if (isset($attendance->day_missions) && $attendance->day_missions->count() > 0) {
                $mission = $attendance->day_missions->first();
                if ($mission->status === 'approved')      $missionStatus = 'បេសកកម្ម';
                elseif ($mission->status === 'pending')   $missionStatus = 'ពិនិត្យ';
                $missionCheckIn = $mission->check_in_time
                    ? Carbon::parse($mission->check_in_time)->format('H:i:s')
                    : $mission->created_at->format('H:i:s');
            }

            fputcsv($file, [
                $attendance->user->name,
                $attendance->attendance_date->format('l'),
                $attendance->attendance_date->format('Y-m-d'),
                $attendance->morning_check_in    ? $attendance->morning_check_in->format('H:i:s')    : '-',
                $attendance->morning_check_out   ? $attendance->morning_check_out->format('H:i:s')   : '-',
                $attendance->afternoon_check_in  ? $attendance->afternoon_check_in->format('H:i:s')  : '-',
                $attendance->afternoon_check_out ? $attendance->afternoon_check_out->format('H:i:s') : '-',
                $morningStatus, $afternoonStatus, $missionStatus, $missionCheckIn,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
// notification

/**
 * Display notifications with filters
 */
public function notifications(Request $request)
{
    $query = Notification::where('user_id', Auth::id());

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    // Type filter
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Status filter
    if ($request->filled('status')) {
        if ($request->status === 'unread') {
            $query->unread();
        } elseif ($request->status === 'read') {
            $query->read();
        }
    }

    // Date filter
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Get paginated notifications
    $notifications = $query->orderBy('created_at', 'desc')
                          ->paginate(20)
                          ->withQueryString();

    // Statistics
    $stats = [
        'total' => Notification::where('user_id', Auth::id())->count(),
        'unread' => Notification::where('user_id', Auth::id())->unread()->count(),
        'today' => Notification::where('user_id', Auth::id())
                              ->whereDate('created_at', today())
                              ->count(),
    ];

    // Notification types count (optional, if needed in view)
    $typeStats = Notification::where('user_id', Auth::id())
                            ->selectRaw('type, count(*) as count')
                            ->groupBy('type')
                            ->get()
                            ->pluck('count', 'type');

    return view('admin.notifications', compact('notifications', 'stats', 'typeStats'));
}

/**
 * Mark single notification as read
 */
public function markNotificationRead($id)
{
    $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
    $notification->markAsRead();
    
    return back()->with('success', 'Notification marked as read');
}

/**
 * Mark all notifications as read
 */
public function markAllNotificationsRead()
{
    $updatedCount = Notification::where('user_id', Auth::id())
                                ->unread()
                                ->update([
                                    'is_read' => true,
                                    'read_at' => now(),
                                ]);
    
    if ($updatedCount > 0) {
        return back()->with('success', "Successfully marked {$updatedCount} notification(s) as read");
    }
    
    return back()->with('success', 'No unread notifications to mark');
}

/**
 * Delete single notification
 */
public function deleteNotification($id)
{
    $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
    $notification->delete();
    
    return back()->with('success', 'Notification deleted successfully');
}


public function deleteAllReadNotifications()
{
    try {
        // Get the count first
        $readNotifications = Notification::where('user_id', Auth::id())
                                        ->where('is_read', true)
                                        ->get();
        
        $count = $readNotifications->count();
        
        if ($count === 0) {
            return back()->with('error', 'No read notifications to delete');
        }
        
        // Delete them
        Notification::where('user_id', Auth::id())
                   ->where('is_read', true)
                   ->delete();
        
        return back()->with('success', "Successfully deleted {$count} read notification(s)");
        
    } catch (\Exception $e) {
        Log::error('Delete All Read Notifications Error', [
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('error', 'Failed to delete notifications. Please try again.');
    }
}


public function reportViolations(Request $request)
{
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));
    $userId    = $request->input('user_id');
    $type      = $request->input('type');

    $query = Attendance::with('user')
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->whereRaw('DAYOFWEEK(attendance_date) BETWEEN 2 AND 6')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->where(function ($q) use ($type) {
            if ($type === 'late_morning_in') {
                $q->whereRaw("TIME(morning_check_in) > '09:00:00'");
            } elseif ($type === 'early_morning_out') {
                $q->whereNotNull('morning_check_out')
                  ->whereRaw("TIME(morning_check_out) < '11:00:00'");
            } elseif ($type === 'late_morning_out') {
                $q->whereNotNull('morning_check_out')
                  ->whereRaw("TIME(morning_check_out) > '12:30:00'");
            } elseif ($type === 'late_afternoon_in') {
                $q->whereRaw("TIME(afternoon_check_in) > '15:00:00'");
            } elseif ($type === 'early_afternoon_out') {
                $q->whereNotNull('afternoon_check_out')
                  ->whereRaw("TIME(afternoon_check_out) < '17:00:00'");
            } elseif ($type === 'late_afternoon_out') {
                $q->whereNotNull('afternoon_check_out')
                  ->whereRaw("TIME(afternoon_check_out) > '18:30:00'");
            } else {
                $q->where(function ($inner) {
                    $inner->whereRaw("TIME(morning_check_in) > '09:00:00'")
                          ->orWhere(function ($o) {
                              $o->whereNotNull('morning_check_out')
                                ->whereRaw("TIME(morning_check_out) < '11:00:00'");
                          })
                          ->orWhere(function ($o) {
                              $o->whereNotNull('morning_check_out')
                                ->whereRaw("TIME(morning_check_out) > '12:30:00'");
                          })
                          ->orWhereRaw("TIME(afternoon_check_in) > '15:00:00'")
                          ->orWhere(function ($o) {
                              $o->whereNotNull('afternoon_check_out')
                                ->whereRaw("TIME(afternoon_check_out) < '17:00:00'");
                          })
                          ->orWhere(function ($o) {
                              $o->whereNotNull('afternoon_check_out')
                                ->whereRaw("TIME(afternoon_check_out) > '18:30:00'");
                          });
                });
            }
        })
        ->orderByDesc('attendance_date')
        ->orderBy(fn($q) => $q->select('name')->from('users')->whereColumn('users.id', 'attendances.user_id'));

    $allForStats = Attendance::with('user')
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->whereRaw('DAYOFWEEK(attendance_date) BETWEEN 2 AND 6')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->get();

    $stats = [
        'late_morning_in'     => $allForStats->filter(fn($a) => $a->morning_check_in    && $a->morning_check_in->format('H:i')    > '09:00')->count(),
        'early_morning_out'   => $allForStats->filter(fn($a) => $a->morning_check_out   && $a->morning_check_out->format('H:i')   < '11:00')->count(),
        'late_morning_out'    => $allForStats->filter(fn($a) => $a->morning_check_out   && $a->morning_check_out->format('H:i')   > '12:30')->count(),
        'late_afternoon_in'   => $allForStats->filter(fn($a) => $a->afternoon_check_in  && $a->afternoon_check_in->format('H:i')  > '15:00')->count(),
        'early_afternoon_out' => $allForStats->filter(fn($a) => $a->afternoon_check_out && $a->afternoon_check_out->format('H:i') < '17:00')->count(),
        'late_afternoon_out'  => $allForStats->filter(fn($a) => $a->afternoon_check_out && $a->afternoon_check_out->format('H:i') > '18:30')->count(),
    ];

    $violations = $query->paginate(20)->withQueryString();
    $allUsers   = User::where('role_type', 'user')->orderBy('name')->get();

    return view('admin.report-violations', compact(
        'violations', 'stats', 'allUsers',
        'startDate', 'endDate', 'userId', 'type'
    ));
}

public function exportViolationsCSV(Request $request)
{
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->input('end_date',   now()->endOfMonth()->format('Y-m-d'));
    $userId    = $request->input('user_id');
    $type      = $request->input('type');

    $rows = Attendance::with('user')
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->whereRaw('DAYOFWEEK(attendance_date) BETWEEN 2 AND 6')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->where(function ($q) use ($type) {
            if ($type === 'late_morning_in') {
                $q->whereRaw("TIME(morning_check_in) > '09:00:00'");
            } elseif ($type === 'early_morning_out') {
                $q->whereNotNull('morning_check_out')
                  ->whereRaw("TIME(morning_check_out) < '11:00:00'");
            } elseif ($type === 'late_morning_out') {
                $q->whereNotNull('morning_check_out')
                  ->whereRaw("TIME(morning_check_out) > '12:30:00'");
            } elseif ($type === 'late_afternoon_in') {
                $q->whereRaw("TIME(afternoon_check_in) > '15:00:00'");
            } elseif ($type === 'early_afternoon_out') {
                $q->whereNotNull('afternoon_check_out')
                  ->whereRaw("TIME(afternoon_check_out) < '17:00:00'");
            } elseif ($type === 'late_afternoon_out') {
                $q->whereNotNull('afternoon_check_out')
                  ->whereRaw("TIME(afternoon_check_out) > '18:30:00'");
            } else {
                $q->where(function ($inner) {
                    $inner->whereRaw("TIME(morning_check_in) > '09:00:00'")
                          ->orWhere(function ($o) {
                              $o->whereNotNull('morning_check_out')
                                ->whereRaw("TIME(morning_check_out) < '11:00:00'");
                          })
                          ->orWhere(function ($o) {
                              $o->whereNotNull('morning_check_out')
                                ->whereRaw("TIME(morning_check_out) > '12:30:00'");
                          })
                          ->orWhereRaw("TIME(afternoon_check_in) > '15:00:00'")
                          ->orWhere(function ($o) {
                              $o->whereNotNull('afternoon_check_out')
                                ->whereRaw("TIME(afternoon_check_out) < '17:00:00'");
                          })
                          ->orWhere(function ($o) {
                              $o->whereNotNull('afternoon_check_out')
                                ->whereRaw("TIME(afternoon_check_out) > '18:30:00'");
                          });
                });
            }
        })
        ->orderBy('attendance_date')
        ->get();

    $filename = 'violations-report-' . now()->format('Y-m-d-His') . '.csv';
    $headers = [
        'Content-Type'        => 'text/csv; charset=utf-8',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    $callback = function () use ($rows) {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, [
            'Employee', 'Date', 'Day',
            'Morning In',  'Morning In Violation',
            'Morning Out', 'Morning Out Violation',
            'Afternoon In',  'Afternoon In Violation',
            'Afternoon Out', 'Afternoon Out Violation',
            'Flags',
        ]);

        foreach ($rows as $row) {
            $mIn  = $row->morning_check_in;
            $mOut = $row->morning_check_out;
            $aIn  = $row->afternoon_check_in;
            $aOut = $row->afternoon_check_out;

            $mInViolation = $mIn && $mIn->format('H:i') > '09:00'
                ? 'Late (' . $mIn->format('h:i A') . ')' : '';

            if ($mOut && $mOut->format('H:i') < '11:00') {
                $mOutViolation = 'Early (' . $mOut->format('h:i A') . ')';
            } elseif ($mOut && $mOut->format('H:i') > '12:30') {
                $mOutViolation = 'Late (' . $mOut->format('h:i A') . ')';
            } else {
                $mOutViolation = '';
            }

            $aInViolation = $aIn && $aIn->format('H:i') > '15:00'
                ? 'Late (' . $aIn->format('h:i A') . ')' : '';

            if ($aOut && $aOut->format('H:i') < '17:00') {
                $aOutViolation = 'Early (' . $aOut->format('h:i A') . ')';
            } elseif ($aOut && $aOut->format('H:i') > '18:30') {
                $aOutViolation = 'Late (' . $aOut->format('h:i A') . ')';
            } else {
                $aOutViolation = '';
            }

            $flags = array_filter([$mInViolation, $mOutViolation, $aInViolation, $aOutViolation]);

            fputcsv($file, [
                $row->user->name,
                $row->attendance_date->format('Y-m-d'),
                $row->attendance_date->format('l'),
                $mIn  ? $mIn->format('h:i A')  : '-',
                $mInViolation  ?: 'OK',
                $mOut ? $mOut->format('h:i A') : '-',
                $mOutViolation ?: 'OK',
                $aIn  ? $aIn->format('h:i A')  : '-',
                $aInViolation  ?: 'OK',
                $aOut ? $aOut->format('h:i A') : '-',
                $aOutViolation ?: 'OK',
                implode(' | ', $flags),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
 
}

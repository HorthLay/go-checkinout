<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOffDay;
use App\Models\Notification;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Get filter parameters
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        
        // Build query for attendances
        $query = Attendance::with(['user'])
                          ->whereBetween('attendance_date', [$startDate, $endDate]);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $attendances = $query->get();
        
        // Calculate session completion statistics
        $morningComplete = $attendances->filter(function($a) {
            return $a->isMorningSessionComplete();
        })->count();
        
        $afternoonComplete = $attendances->filter(function($a) {
            return $a->isAfternoonSessionComplete();
        })->count();
        
        $fullDayComplete = $attendances->filter(function($a) {
            return $a->isFullDayComplete();
        })->count();
        
        // Calculate statistics
        $stats = [
            'total_present' => $attendances->whereIn('status', ['on_time', 'late'])->count(),
            'total_late' => $attendances->where('status', 'late')->count(),
            'total_absent' => $attendances->where('status', 'absent')->count(),
            'total_leave' => $attendances->where('status', 'leave')->count(),
            'total_hours' => $attendances->sum('work_hours'),
            'avg_hours' => $attendances->avg('work_hours'),
            'morning_sessions_complete' => $morningComplete,
            'afternoon_sessions_complete' => $afternoonComplete,
            'full_days_complete' => $fullDayComplete,
            'total_morning_hours' => $attendances->sum('morning_work_hours'),
            'total_afternoon_hours' => $attendances->sum('afternoon_work_hours'),
        ];
        
        // Get top 5 users by work hours
        $topUsers = User::where('role_type', 'user')
                       ->withSum(['attendances as total_hours' => function($query) use ($startDate, $endDate) {
                           $query->whereBetween('attendance_date', [$startDate, $endDate]);
                       }], 'work_hours')
                       ->withCount(['attendances as total_days' => function($query) use ($startDate, $endDate) {
                           $query->whereBetween('attendance_date', [$startDate, $endDate])
                                 ->whereIn('status', ['on_time', 'late']);
                       }])
                       ->having('total_hours', '>', 0)
                       ->orderByDesc('total_hours')
                       ->take(5)
                       ->get();
        
        // Daily attendance summary with day of week
        $dailySummary = $attendances->groupBy(function($item) {
            return $item->attendance_date->format('Y-m-d');
        })->map(function($dayAttendances) {
            $date = $dayAttendances->first()->attendance_date;
            return [
                'date' => $date,
                'day_name' => $date->format('l'), // Day of week (Monday, Tuesday, etc.)
                'day_name_short' => $date->format('D'), // Short day (Mon, Tue, etc.)
                'present' => $dayAttendances->whereIn('status', ['on_time', 'late'])->count(),
                'late' => $dayAttendances->where('status', 'late')->count(),
                'absent' => $dayAttendances->where('status', 'absent')->count(),
                'leave' => $dayAttendances->where('status', 'leave')->count(),
                'total_hours' => $dayAttendances->sum('work_hours'),
                'morning_hours' => $dayAttendances->sum('morning_work_hours'),
                'afternoon_hours' => $dayAttendances->sum('afternoon_work_hours'),
            ];
        })->values();
        
        // Get all users for filter
        $users = User::where('role_type', 'user')->orderBy('name')->get();
        
        // Get office locations
        $locations = OfficeLocation::all();
        
        return view('admin.report', compact(
            'attendances',
            'stats',
            'topUsers',
            'dailySummary',
            'users',
            'locations',
            'startDate',
            'endDate',
            'userId'
        ));
    }

    public function reportPrint(Request $request)
    {
        // Get filter parameters
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        
        // Build query for attendances
        $query = Attendance::with(['user'])
                          ->whereBetween('attendance_date', [$startDate, $endDate]);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $attendances = $query->orderBy('attendance_date', 'asc')->get();
        
        // Calculate session completion statistics
        $morningComplete = $attendances->filter(function($a) {
            return $a->isMorningSessionComplete();
        })->count();
        
        $afternoonComplete = $attendances->filter(function($a) {
            return $a->isAfternoonSessionComplete();
        })->count();
        
        $fullDayComplete = $attendances->filter(function($a) {
            return $a->isFullDayComplete();
        })->count();
        
        // Calculate statistics
        $stats = [
            'total_present' => $attendances->whereIn('status', ['on_time', 'late'])->count(),
            'total_late' => $attendances->where('status', 'late')->count(),
            'total_absent' => $attendances->where('status', 'absent')->count(),
            'total_leave' => $attendances->where('status', 'leave')->count(),
            'total_hours' => $attendances->sum('work_hours'),
            'avg_hours' => $attendances->avg('work_hours'),
            'morning_sessions_complete' => $morningComplete,
            'afternoon_sessions_complete' => $afternoonComplete,
            'full_days_complete' => $fullDayComplete,
            'total_morning_hours' => $attendances->sum('morning_work_hours'),
            'total_afternoon_hours' => $attendances->sum('afternoon_work_hours'),
        ];
        
        // Get top 5 users by work hours
        $topUsers = User::where('role_type', 'user')
                       ->withSum(['attendances as total_hours' => function($query) use ($startDate, $endDate) {
                           $query->whereBetween('attendance_date', [$startDate, $endDate]);
                       }], 'work_hours')
                       ->withCount(['attendances as total_days' => function($query) use ($startDate, $endDate) {
                           $query->whereBetween('attendance_date', [$startDate, $endDate])
                                 ->whereIn('status', ['on_time', 'late']);
                       }])
                       ->having('total_hours', '>', 0)
                       ->orderByDesc('total_hours')
                       ->take(5)
                       ->get();
        
        return view('admin.report-print', compact(
            'attendances',
            'stats',
            'topUsers',
            'startDate',
            'endDate',
            'userId'
        ));
    }



    public function exportCSV(Request $request)
{
    // Get filter parameters
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
    $userId    = $request->input('user_id');

    // Build query
    $query = Attendance::with(['user'])
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->orderBy('attendance_date', 'asc');

    if ($userId) {
        $query->where('user_id', $userId);
    }

    $attendances = $query->get();

    // Filename
    $filename = 'attendance-report-' . now()->format('Y-m-d-His') . '.csv';

    // Headers
    $headers = [
        'Content-Type'        => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    // Stream CSV
    $callback = function () use ($attendances) {
        $file = fopen('php://output', 'w');

        // UTF-8 BOM (Excel safe)
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV Header Row
        fputcsv($file, [
            'Employee',
            'Email',
            'Day',
            'Date',
            'Morning Check-In',
            'Morning Check-Out',
            'Afternoon Check-In',
            'Afternoon Check-Out',
            'Total Hours',
            'Morning Hours',
            'Afternoon Hours',
            'Status',
            'Absent Note',
        ]);

        // CSV Rows
        foreach ($attendances as $attendance) {
            fputcsv($file, [
                $attendance->user->name,
                $attendance->user->email,
                $attendance->attendance_date->format('l'),
                $attendance->attendance_date->format('Y-m-d'),
                $attendance->morning_check_in ? $attendance->morning_check_in->format('H:i:s') : '-',
                $attendance->morning_check_out ? $attendance->morning_check_out->format('H:i:s') : '-',
                $attendance->afternoon_check_in ? $attendance->afternoon_check_in->format('H:i:s') : '-',
                $attendance->afternoon_check_out ? $attendance->afternoon_check_out->format('H:i:s') : '-',
                $attendance->work_hours ?? '0',
                $attendance->morning_work_hours ?? '0',
                $attendance->afternoon_work_hours ?? '0',
                ucfirst(str_replace('_', ' ', $attendance->status)),
                $attendance->status === 'absent' ? $attendance->absent_note : '',
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

// notification

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

    // Notification types count
    $typeStats = Notification::where('user_id', Auth::id())
                            ->selectRaw('type, count(*) as count')
                            ->groupBy('type')
                            ->get()
                            ->pluck('count', 'type');

    return view('admin.notifications', compact('notifications', 'stats', 'typeStats'));
}

/**
 * Mark notification as read
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
    Notification::where('user_id', Auth::id())
               ->unread()
               ->update([
                   'is_read' => true,
                   'read_at' => now(),
               ]);
    
    return back()->with('success', 'All notifications marked as read');
}

/**
 * Delete notification
 */
public function deleteNotification($id)
{
    $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
    $notification->delete();
    
    return back()->with('success', 'Notification deleted');
}

/**
 * Delete all read notifications
 */
public function deleteAllReadNotifications()
{
    Notification::where('user_id', Auth::id())
               ->read()
               ->delete();
    
    return back()->with('success', 'All read notifications deleted');
}
}

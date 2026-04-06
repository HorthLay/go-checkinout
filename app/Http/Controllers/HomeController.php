<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOffDay;
use App\Models\AttendanceSchedule;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
   public function index()
    {
        $user = Auth::user();
        
        // Get user's schedule
        $schedule = AttendanceSchedule::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->first();
        
        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('attendance_date', today())
                                    ->first();
        
        // Today's missions - using created_at to filter by today's date
        $todayMissions = Mission::where('user_id', $user->id)
                               ->whereDate('created_at', today())
                               ->orderBy('created_at', 'desc')
                               ->get();
        
        // This month's attendance records with pagination
        $monthlyAttendance = Attendance::where('user_id', $user->id)
                                      ->whereYear('attendance_date', now()->year)
                                      ->whereMonth('attendance_date', now()->month)
                                      ->orderBy('attendance_date', 'desc')
                                      ->paginate(15);
        
        // This month's missions grouped by created_at date
        $monthlyMissions = Mission::where('user_id', $user->id)
                                 ->whereYear('created_at', now()->year)
                                 ->whereMonth('created_at', now()->month)
                                 ->orderBy('created_at', 'desc')
                                 ->get()
                                 ->groupBy(function($mission) {
                                     return $mission->created_at->format('Y-m-d');
                                 });
        
        // Calculate monthly statistics
        $totalPresent = Attendance::where('user_id', $user->id)
                                 ->whereYear('attendance_date', now()->year)
                                 ->whereMonth('attendance_date', now()->month)
                                 ->whereIn('status', ['on_time', 'late'])
                                 ->count();
        
        $totalLate = Attendance::where('user_id', $user->id)
                              ->whereYear('attendance_date', now()->year)
                              ->whereMonth('attendance_date', now()->month)
                              ->where('status', 'late')
                              ->count();
        
        $totalAbsent = Attendance::where('user_id', $user->id)
                                ->whereYear('attendance_date', now()->year)
                                ->whereMonth('attendance_date', now()->month)
                                ->where('status', 'absent')
                                ->count();
        
        $totalLeave = Attendance::where('user_id', $user->id)
                               ->whereYear('attendance_date', now()->year)
                               ->whereMonth('attendance_date', now()->month)
                               ->where('status', 'leave')
                               ->count();
        
        // Check if user has day off today
        $hasDayOffToday = AttendanceOffDay::where('user_id', $user->id)
                                         ->whereDate('off_date', today())
                                         ->exists();
        
        return view('home.home', compact(
            'schedule',
            'todayAttendance',
            'todayMissions',
            'monthlyAttendance',
            'monthlyMissions',
            'totalPresent',
            'totalLate',
            'totalAbsent',
            'totalLeave',
            'hasDayOffToday'
        ));
    }

    public function support(){
        return view('home.support');
    }
}

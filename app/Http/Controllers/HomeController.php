<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOffDay;
use App\Models\AttendanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        $user = Auth::user();
        
        // Get user's schedule
        $schedule = AttendanceSchedule::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->first();
        
        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('attendance_date', today())
                                    ->first();
        
        // This month's attendance records with pagination
        $monthlyAttendance = Attendance::where('user_id', $user->id)
                                      ->whereYear('attendance_date', now()->year)
                                      ->whereMonth('attendance_date', now()->month)
                                      ->orderBy('attendance_date', 'desc')
                                      ->paginate(15);
        
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
            'monthlyAttendance',
            'totalPresent',
            'totalLate',
            'totalAbsent',
            'totalLeave',
            'hasDayOffToday'
        ));
    }
}

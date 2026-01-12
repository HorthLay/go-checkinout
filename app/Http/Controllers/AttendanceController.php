<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceSchedule;
use App\Models\AttendanceOffDay;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{


// user
public function index(){
    
        $user = Auth::user();
        
        // Get user's schedule
        $schedule = AttendanceSchedule::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->first();

    return view("home.myschedlue",compact("schedule"));
}



// admin
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'user_id_select' => 'required|exists:users,id',
            'scheduled_check_in' => 'required',
            'scheduled_check_out' => 'required',
            'late_allowed_min' => 'required|integer|min:0|max:60',
        ]);

        $checkIn = Carbon::createFromFormat('H:i', $request->scheduled_check_in)->format('H:i:s');
        $checkOut = Carbon::createFromFormat('H:i', $request->scheduled_check_out)->format('H:i:s');

        AttendanceSchedule::updateOrCreate(
            ['user_id' => $request->user_id_select],
            [
                'scheduled_check_in' => $checkIn,
                'scheduled_check_out' => $checkOut,
                'late_allowed_min' => $request->late_allowed_min,
                'is_active' => true,
            ]
        );

        return redirect()->route('attendance', ['tab' => 'schedules'])
                        ->with('success', 'Work schedule set successfully!');
    }

    public function storeDayOff(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'off_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:255',
        ]);

        // Check if already exists
        $existing = AttendanceOffDay::where('user_id', $request->user_id)
                                   ->whereDate('off_date', $request->off_date)
                                   ->exists();

        if ($existing) {
            return redirect()->route('attendance', ['tab' => 'dayoffs'])
                           ->with('error', 'Day off already exists for this date.');
        }

        // Create day off (model event will handle attendance creation)
        AttendanceOffDay::create([
            'user_id' => $request->user_id,
            'off_date' => $request->off_date,
            'reason' => $request->reason,
        ]);

        return redirect()->route('attendance', ['tab' => 'dayoffs'])
                        ->with('success', 'Day off added successfully!');
    }

    public function deleteDayOff($id)
    {
        $dayOff = AttendanceOffDay::findOrFail($id);
        
        // Delete the day off (model event will handle attendance cleanup)
        $dayOff->delete();

        return redirect()->route('attendance', ['tab' => 'dayoffs'])
                        ->with('success', 'Day off deleted successfully!');
    }
}
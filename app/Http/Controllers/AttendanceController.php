<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceSchedule;
use App\Models\AttendanceOffDay;
use App\Models\OfficeLocation;
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
            'scheduled_check_in_morning' => 'required',
            'scheduled_check_out_morning' => 'required',
            'scheduled_check_in_afternoon' => 'required',
            'scheduled_check_out_afternoon' => 'required',
            'late_allowed_min' => 'required|integer|min:0|max:60',
        ]);

        // Format time inputs to H:i:s
        $morningIn = Carbon::createFromFormat('H:i', $request->scheduled_check_in_morning)->format('H:i:s');
        $morningOut = Carbon::createFromFormat('H:i', $request->scheduled_check_out_morning)->format('H:i:s');
        $afternoonIn = Carbon::createFromFormat('H:i', $request->scheduled_check_in_afternoon)->format('H:i:s');
        $afternoonOut = Carbon::createFromFormat('H:i', $request->scheduled_check_out_afternoon)->format('H:i:s');

        // Validate time logic
        if ($morningOut <= $morningIn) {
            return redirect()->route('attendance', ['tab' => 'schedules'])
                           ->with('error', 'Morning check-out must be after morning check-in.');
        }

        if ($afternoonOut <= $afternoonIn) {
            return redirect()->route('attendance', ['tab' => 'schedules'])
                           ->with('error', 'Afternoon check-out must be after afternoon check-in.');
        }

        if ($afternoonIn <= $morningOut) {
            return redirect()->route('attendance', ['tab' => 'schedules'])
                           ->with('error', 'Afternoon check-in must be after morning check-out.');
        }

        AttendanceSchedule::updateOrCreate(
            ['user_id' => $request->user_id_select],
            [
                'scheduled_check_in_morining' => $morningIn, // Note: keeping typo from schema
                'scheduled_check_out_morining' => $morningOut,
                'scheduled_check_in_afternoon' => $afternoonIn,
                'scheduled_check_out_afternoon' => $afternoonOut,
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



    public function show($id)
{
    $attendance = Attendance::with(['user', 'user.attendanceSchedule'])->findOrFail($id);
    $officeLocation = OfficeLocation::first();
    
    return view('admin.attendance.show', compact('attendance', 'officeLocation'));
}

/**
 * Show the form for editing the specified attendance record
 */
public function edit($id)
{
    $attendance = Attendance::with(['user'])->findOrFail($id);
    
    return view('admin.attendance.edit', compact('attendance'));
}

/**
 * Update the specified attendance record
 */
public function update(Request $request, $id)
{
    $request->validate([
        'morning_check_in' => 'nullable|date_format:H:i',
        'morning_check_out' => 'nullable|date_format:H:i',
        'afternoon_check_in' => 'nullable|date_format:H:i',
        'afternoon_check_out' => 'nullable|date_format:H:i',
        'status' => 'required|in:on_time,late,absent,leave',
        'note' => 'nullable|string|max:500',
    ]);

    $attendance = Attendance::findOrFail($id);
    
    // Update times
    if ($request->morning_check_in) {
        $attendance->morning_check_in = \Carbon\Carbon::parse($attendance->attendance_date->format('Y-m-d') . ' ' . $request->morning_check_in);
    }
    
    if ($request->morning_check_out) {
        $attendance->morning_check_out = \Carbon\Carbon::parse($attendance->attendance_date->format('Y-m-d') . ' ' . $request->morning_check_out);
    }
    
    if ($request->afternoon_check_in) {
        $attendance->afternoon_check_in = \Carbon\Carbon::parse($attendance->attendance_date->format('Y-m-d') . ' ' . $request->afternoon_check_in);
    }
    
    if ($request->afternoon_check_out) {
        $attendance->afternoon_check_out = \Carbon\Carbon::parse($attendance->attendance_date->format('Y-m-d') . ' ' . $request->afternoon_check_out);
    }
    
    $attendance->status = $request->status;
    $attendance->note = $request->note;
    
    // Recalculate work hours
    $attendance->calculateWorkHours();
    $attendance->save();
    
    return redirect()->route('attendance', ['tab' => 'records'])
                     ->with('success', 'Attendance record updated successfully');
}

/**
 * Remove the specified attendance record
 */
public function destroy($id)
{
    $attendance = Attendance::findOrFail($id);
    $attendance->delete();
    
    return redirect()->route('admin.attendance.log', ['tab' => 'records'])
                     ->with('success', 'Attendance record deleted successfully');
}
}
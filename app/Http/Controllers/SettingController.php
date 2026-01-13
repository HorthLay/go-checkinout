<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSchedule;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
        public function index()
    {
        $officeLocation = OfficeLocation::first();
        $defaultSchedule = AttendanceSchedule::where('is_active', true)->first();
        
        // Load working days configuration
         $workingDaysConfig = Cache::get('working_days_config', []);
        
        return view('home.setting', compact('officeLocation', 'defaultSchedule', 'workingDaysConfig'));
    }

    /**
     * Update settings based on section
     */
    public function update(Request $request)
    {
        $section = $request->input('section');

        try {
            switch ($section) {
                case 'company':
                    return $this->updateCompanySettings($request);
                case 'location':
                    return $this->updateLocationSettings($request);
                case 'schedule':
                    return $this->updateScheduleSettings($request);
                case 'working_days':
                    return $this->updateWorkingDays($request);
                case 'notifications':
                    return $this->updateNotificationSettings($request);
                default:
                    return redirect()->back()->with('error', 'Invalid settings section');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Update company information settings
     */
    private function updateCompanySettings(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'company_address' => 'nullable|string|max:500',
        ]);

        // Update .env file or config
        $this->updateEnvFile([
            'APP_NAME' => $request->company_name,
            'APP_TIMEZONE' => $request->timezone,
        ]);

        // Store company address in cache or database
        Cache::put('company_address', $request->company_address);

        return redirect()->back()->with('success', 'Company settings updated successfully!');
    }

    /**
     * Update office location settings
     */
    private function updateLocationSettings(Request $request)
    {
        $request->validate([
            'location_name' => 'required|string|max:255',
            'radius' => 'required|integer|min:10|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $officeLocation = OfficeLocation::first();

        if ($officeLocation) {
            // Update existing location
            $officeLocation->update([
                'name' => $request->location_name,
                'radius' => $request->radius,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        } else {
            // Create new location
            OfficeLocation::create([
                'name' => $request->location_name,
                'radius' => $request->radius,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Office location updated successfully!');
    }

    /**
     * Update default work schedule settings
     */
    private function updateScheduleSettings(Request $request)
    {
        $request->validate([
            'morning_check_in' => 'required|date_format:H:i',
            'morning_check_out' => 'required|date_format:H:i|after:morning_check_in',
            'afternoon_check_in' => 'required|date_format:H:i|after:morning_check_out',
            'afternoon_check_out' => 'required|date_format:H:i|after:afternoon_check_in',
            'late_allowed_min' => 'required|integer|min:0|max:60',
        ]);

        // Update all active schedules or create default if none exist
        $schedules = AttendanceSchedule::where('is_active', true)->get();

        if ($schedules->isEmpty()) {
            // Create default schedule for each user
            $users = User::where('role_type', 'user')->get();
            
            foreach ($users as $user) {
                AttendanceSchedule::create([
                    'user_id' => $user->id,
                    'scheduled_check_in_morining' => $request->morning_check_in,
                    'scheduled_check_out_morining' => $request->morning_check_out,
                    'scheduled_check_in_afternoon' => $request->afternoon_check_in,
                    'scheduled_check_out_afternoon' => $request->afternoon_check_out,
                    'late_allowed_min' => $request->late_allowed_min,
                    'is_active' => true,
                ]);
            }
            
            return redirect()->back()->with('success', 'Default schedule created for all employees!');
        } else {
            // Update existing schedules
            foreach ($schedules as $schedule) {
                $schedule->update([
                    'scheduled_check_in_morining' => $request->morning_check_in,
                    'scheduled_check_out_morining' => $request->morning_check_out,
                    'scheduled_check_in_afternoon' => $request->afternoon_check_in,
                    'scheduled_check_out_afternoon' => $request->afternoon_check_out,
                    'late_allowed_min' => $request->late_allowed_min,
                ]);
            }
            
            return redirect()->back()->with('success', 'Work schedule updated for all employees!');
        }
    }

    /**
     * Update working days settings
     */
    private function updateWorkingDays(Request $request)
    {
        $workingDays = $request->input('working_days', []);

        if (empty($workingDays)) {
            return redirect()->back()->with('error', 'Please select at least one working day!');
        }

        $allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $workingDaysData = [];

        foreach ($allDays as $day) {
            $isWorking = in_array($day, $workingDays);
            
            $workingDaysData[$day] = [
                'is_working' => $isWorking,
                'morning_start' => $isWorking ? $request->input($day . '_morning_start', '07:30') : null,
                'morning_end' => $isWorking ? $request->input($day . '_morning_end', '11:30') : null,
                'afternoon_start' => $isWorking ? $request->input($day . '_afternoon_start', '14:00') : null,
                'afternoon_end' => $isWorking ? $request->input($day . '_afternoon_end', '17:30') : null,
            ];
        }

        // Store working days configuration in cache
        Cache::put('working_days_config', $workingDaysData, now()->addYears(10));

        // Also store simple array of working day names for backward compatibility
        Cache::put('working_days', $workingDays, now()->addYears(10));

        return redirect()->back()->with('success', 'Working days configuration updated successfully!');
    }

    /**
     * Update notification settings
     */
    private function updateNotificationSettings(Request $request)
    {
        $request->validate([
            'telegram_bot_token' => 'nullable|string',
        ]);

        // Update Telegram bot token
        if ($request->filled('telegram_bot_token')) {
            $this->updateEnvFile([
                'TELEGRAM_BOT_TOKEN' => $request->telegram_bot_token,
            ]);
        }

        // Store notification preferences
        Cache::put('notify_checkin', $request->has('notify_checkin'));
        Cache::put('notify_checkout', $request->has('notify_checkout'));
        Cache::put('notify_late', $request->has('notify_late'));
        Cache::put('notify_location', $request->has('notify_location'));

        return redirect()->back()->with('success', 'Notification settings updated successfully!');
    }

    /**
     * Export all attendance data
     */
    public function exportData()
    {
        try {
            $attendances = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.name',
                    'users.email',
                    'attendances.attendance_date',
                    'attendances.morning_check_in',
                    'attendances.morning_check_out',
                    'attendances.afternoon_check_in',
                    'attendances.afternoon_check_out',
                    'attendances.work_hours',
                    'attendances.status'
                )
                ->orderBy('attendances.attendance_date', 'desc')
                ->get();

            $filename = 'attendance_export_' . now()->format('Y-m-d_His') . '.csv';
            $handle = fopen('php://output', 'w');
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            // CSV Headers
            fputcsv($handle, [
                'Employee Name',
                'Email',
                'Date',
                'Morning Check-In',
                'Morning Check-Out',
                'Afternoon Check-In',
                'Afternoon Check-Out',
                'Total Hours',
                'Status'
            ]);

            // CSV Data
            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->name,
                    $attendance->email,
                    $attendance->attendance_date,
                    $attendance->morning_check_in ?? '-',
                    $attendance->morning_check_out ?? '-',
                    $attendance->afternoon_check_in ?? '-',
                    $attendance->afternoon_check_out ?? '-',
                    $attendance->work_hours ?? '0',
                    $attendance->status,
                ]);
            }

            fclose($handle);
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Clear system cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return redirect()->back()->with('success', 'System cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Reset all attendance data (DANGER!)
     */
    public function resetData(Request $request)
    {
        // Require confirmation
        if (!$request->has('confirm') || $request->confirm !== 'DELETE') {
            return redirect()->back()->with('error', 'Data reset cancelled. Type DELETE to confirm.');
        }

        try {
            DB::beginTransaction();

            // Delete all attendance records
            DB::table('attendances')->truncate();
            
            // Optionally reset other related data
            // DB::table('attendance_schedules')->truncate();
            // DB::table('attendance_off_days')->truncate();

            DB::commit();

            return redirect()->back()->with('success', 'All attendance data has been deleted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reset data: ' . $e->getMessage());
        }
    }

    /**
     * Update .env file with new values
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=\"{$value}\"";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);

        // Clear config cache to load new values
        Artisan::call('config:clear');
    }
}

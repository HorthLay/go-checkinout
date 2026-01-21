<?php

namespace App\Console;

use App\Models\Attendance;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
       // Run daily at 12:01 AM to mark day-offs as leave
        $schedule->command('attendance:mark-absent')->dailyAt('00:01');
        
        // Also run every hour to catch any missed ones
        $schedule->command('attendance:mark-absent')->hourly();

            $schedule->call(function () {
            $today = now()->format('Y-m-d');
            
            Attendance::whereDate('attendance_date', $today)
                      ->whereNotNull('morning_check_in')
                      ->whereNull('morning_check_out')
                      ->each(function ($attendance) {
                          $attendance->checkAndMarkAbsent();
                      });
        })->dailyAt('14:00')->name('check-morning-absences')->withoutOverlapping();
        
        // Check for missing afternoon check-outs at 5:30 PM daily
        $schedule->call(function () {
            $today = now()->format('Y-m-d');
            
            Attendance::whereDate('attendance_date', $today)
                      ->whereNotNull('afternoon_check_in')
                      ->whereNull('afternoon_check_out')
                      ->each(function ($attendance) {
                          $attendance->checkAndMarkAbsent();
                      });
        })->dailyAt('17:30')->name('check-afternoon-absences')->withoutOverlapping();

        // Alternative: Run auto-check every 30 minutes during work hours
        $schedule->call(function () {
            Attendance::autoCheckMissingCheckouts();
        })->everyThirtyMinutes()
          ->between('14:00', '18:00')
          ->name('auto-check-absences')
          ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceOffDay;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkAbsentForDayOffs extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Mark users as absent for their scheduled day-offs if they did not check in';

    public function handle()
    {
        $today = Carbon::today();
        
        // Get all day-offs for today
        $dayOffs = AttendanceOffDay::whereDate('off_date', $today)->get();
        
        $count = 0;
        
        foreach ($dayOffs as $dayOff) {
            $attendance = Attendance::where('user_id', $dayOff->user_id)
                                   ->whereDate('attendance_date', $today)
                                   ->first();
            
            if (!$attendance) {
                // Create absent record if no attendance exists
                Attendance::create([
                    'user_id' => $dayOff->user_id,
                    'attendance_date' => $today,
                    'check_in' => null,
                    'check_out' => null,
                    'longitude' => null,
                    'latitude' => null,
                    'status' => 'absent',
                    'work_hours' => null,
                    'absent_note' => $dayOff->reason,
                    'note' => 'Day off (absent): ' . $dayOff->reason,
                ]);
                
                $this->info("✓ Marked {$dayOff->user->name} as absent (day off)");
                $count++;
            } elseif ($attendance->check_in || $attendance->check_out) {
                // They checked in/out on their day off - mark as leave
                if ($attendance->status !== 'leave') {
                    $attendance->update([
                        'status' => 'leave',
                        'absent_note' => $dayOff->reason,
                        'note' => 'Day off (with attendance): ' . $dayOff->reason,
                    ]);
                    
                    $this->info("✓ Marked {$dayOff->user->name} as leave (checked in on day off)");
                    $count++;
                }
            } else {
                // No check-in/out - ensure it's marked as absent
                if ($attendance->status !== 'absent') {
                    $attendance->update([
                        'status' => 'absent',
                        'absent_note' => $dayOff->reason,
                        'note' => 'Day off (absent): ' . $dayOff->reason,
                    ]);
                    
                    $this->info("✓ Marked {$dayOff->user->name} as absent (day off)");
                    $count++;
                }
            }
        }
        
        if ($count === 0) {
            $this->info('No day-offs to process for today.');
        } else {
            $this->info("✓ Processed {$count} day-off(s) successfully!");
        }
        
        return Command::SUCCESS;
    }
}
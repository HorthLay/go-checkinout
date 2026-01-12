<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance Report - Attendify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      @page {
        size: A4;
        margin: 10mm;
      }

      @media print {
        body {
          margin: 0;
          padding: 0;
        }
        .no-print {
          display: none;
        }
        .page {
          page-break-after: avoid;
          box-shadow: none;
          padding: 0;
        }
      }

      body {
        font-family: "Inter", Arial, sans-serif;
      }

      .page {
        width: 210mm;
        min-height: 297mm;
        padding: 10mm;
        margin: 0 auto;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      @media screen {
        body {
          background: #f0f0f0;
          padding: 20px 0;
        }
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      th,
      td {
        border: 1px solid #000;
        padding: 4px 6px;
      }

      .page-break {
        page-break-after: always;
      }
    </style>
  </head>
  <body>
    <div class="page">
      <!-- Header Section -->
      <div class="flex justify-between items-start text-xs mb-3">
        <div>{{ now()->format('m/d/y, g:i A') }}</div>
        <div>Attendify System</div>
      </div>

      <!-- Company Logo and Title -->
      <div class="flex justify-between items-start mb-4">
        <div class="flex items-start gap-3">
          <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white flex-shrink-0">
            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L1 8L12 14L21 9.27V17H23V8M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z"/>
            </svg>
          </div>
          <div>
            <div class="text-lg font-bold leading-tight">Attendify</div>
            <div class="text-xs mt-0.5">Attendance Management System</div>
            <div class="text-xs font-semibold">ATTENDANCE REPORT</div>
          </div>
        </div>
        <div class="text-right">
          <div class="text-xs">Report Generated</div>
          <div class="text-sm font-semibold">{{ now()->format('F j, Y') }}</div>
          <div class="text-xs mt-1">{{ now()->format('h:i A') }}</div>
        </div>
      </div>

      <!-- Report Title -->
      <div class="text-center mb-3 border-b-2 border-black pb-2">
        <div class="text-2xl font-bold leading-tight">ATTENDANCE REPORT</div>
        <div class="text-sm mt-1">Period: {{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</div>
      </div>

      <!-- Report Details -->
      <div class="mb-3">
        <div class="grid grid-cols-2 gap-x-8 gap-y-1 text-xs">
          <div class="flex">
            <span class="font-semibold w-32">Report Type:</span>
            <span>{{ $userId ? 'Individual' : 'All Employees' }}</span>
          </div>
          <div class="flex justify-end">
            <span class="font-semibold w-32">Total Records:</span>
            <span>{{ $attendances->count() }}</span>
          </div>
          
          @if($userId)
            <div class="flex">
              <span class="font-semibold w-32">Employee Name:</span>
              <span>{{ $attendances->first()->user->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-end">
              <span class="font-semibold w-32">Employee ID:</span>
              <span>{{ $userId }}</span>
            </div>
            <div class="flex">
              <span class="font-semibold w-32">Email:</span>
              <span>{{ $attendances->first()->user->email ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-end">
              <span class="font-semibold w-32">Phone:</span>
              <span>{{ $attendances->first()->user->phone ?? 'N/A' }}</span>
            </div>
          @endif
          
          <div class="flex">
            <span class="font-semibold w-32">Start Date:</span>
            <span>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</span>
          </div>
          <div class="flex justify-end">
            <span class="font-semibold w-32">End Date:</span>
            <span>{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
          </div>
        </div>
      </div>

      <!-- Statistics Summary -->
      <div class="border-2 border-black p-2 mb-3">
        <div class="font-semibold mb-1 text-xs">SUMMARY STATISTICS:</div>
        <div class="grid grid-cols-4 gap-2 text-xs">
          <div class="text-center border border-gray-400 p-2">
            <div class="text-xs text-gray-600">PRESENT</div>
            <div class="text-lg font-bold text-green-600">{{ $stats['total_present'] }}</div>
            <div class="text-xs">Days</div>
          </div>
          <div class="text-center border border-gray-400 p-2">
            <div class="text-xs text-gray-600">LATE</div>
            <div class="text-lg font-bold text-orange-600">{{ $stats['total_late'] }}</div>
            <div class="text-xs">Days</div>
          </div>
          <div class="text-center border border-gray-400 p-2">
            <div class="text-xs text-gray-600">ABSENT</div>
            <div class="text-lg font-bold text-red-600">{{ $stats['total_absent'] }}</div>
            <div class="text-xs">Days</div>
          </div>
          <div class="text-center border border-gray-400 p-2">
            <div class="text-xs text-gray-600">TOTAL HOURS</div>
            <div class="text-lg font-bold text-blue-600">{{ number_format($stats['total_hours'], 1) }}</div>
            <div class="text-xs">Hours</div>
          </div>
        </div>
      </div>

      <!-- Top Performers Section -->
      @if(!$userId && $topUsers->count() > 0)
        <div class="border border-black p-2 mb-3">
          <div class="font-semibold mb-2 text-xs">TOP 5 PERFORMERS (BY WORK HOURS):</div>
          <table class="text-xs">
            <thead>
              <tr class="bg-gray-100">
                <th class="text-center w-8">Rank</th>
                <th class="text-left">Employee Name</th>
                <th class="text-center w-24">Total Hours</th>
                <th class="text-center w-20">Days</th>
                <th class="text-center w-24">Avg Hours/Day</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topUsers as $index => $topUser)
                <tr class="{{ $index === 0 ? 'bg-yellow-50' : '' }}">
                  <td class="text-center font-bold">
                    @if($index === 0) 🥇
                    @elseif($index === 1) 🥈
                    @elseif($index === 2) 🥉
                    @else {{ $index + 1 }}
                    @endif
                  </td>
                  <td>{{ $topUser->name }}</td>
                  <td class="text-center font-semibold">
                    @php
                      $hours = floor($topUser->total_hours ?? 0);
                      $minutes = (($topUser->total_hours ?? 0) - $hours) * 60;
                    @endphp
                    {{ $hours }}h {{ round($minutes) }}m
                  </td>
                  <td class="text-center">{{ $topUser->total_days ?? 0 }}</td>
                  <td class="text-center">
                    {{ $topUser->total_days > 0 ? number_format(($topUser->total_hours ?? 0) / $topUser->total_days, 1) : '0' }}h
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      <!-- Attendance Records Table -->
      <table class="text-xs mb-3">
        <thead>
          <tr class="bg-gray-100">
            <th class="text-center w-8">No</th>
            @if(!$userId)
              <th class="text-left">Employee</th>
            @endif
            <th class="text-center w-20">Date</th>
            <th class="text-center w-24">Morning</th>
            <th class="text-center w-24">Afternoon</th>
            <th class="text-center w-20">Total Hours</th>
            <th class="text-center w-16">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attendances as $index => $attendance)
            <tr>
              <td class="text-center">{{ $index + 1 }}</td>
              @if(!$userId)
                <td>
                  <div class="font-semibold">{{ $attendance->user->name }}</div>
                  <div class="text-xs text-gray-600">{{ $attendance->user->email }}</div>
                </td>
              @endif
              <td class="text-center">
                <div>{{ $attendance->attendance_date->format('M d, Y') }}</div>
                <div class="text-xs text-gray-600">{{ $attendance->attendance_date->format('l') }}</div>
              </td>
              <td class="text-center">
                <div class="text-xs">
                  <div>In: {{ $attendance->morning_check_in ? $attendance->morning_check_in->format('h:i A') : '—' }}</div>
                  <div>Out: {{ $attendance->morning_check_out ? $attendance->morning_check_out->format('h:i A') : '—' }}</div>
                  <div class="font-semibold text-blue-600">{{ $attendance->formatted_morning_hours }}</div>
                </div>
              </td>
              <td class="text-center">
                <div class="text-xs">
                  <div>In: {{ $attendance->afternoon_check_in ? $attendance->afternoon_check_in->format('h:i A') : '—' }}</div>
                  <div>Out: {{ $attendance->afternoon_check_out ? $attendance->afternoon_check_out->format('h:i A') : '—' }}</div>
                  <div class="font-semibold text-blue-600">{{ $attendance->formatted_afternoon_hours }}</div>
                </div>
              </td>
              <td class="text-center font-bold">{{ $attendance->formatted_work_hours ?? '—' }}</td>
              <td class="text-center">
                <span class="font-semibold
                  {{ $attendance->status === 'on_time' ? 'text-green-600' : '' }}
                  {{ $attendance->status === 'late' ? 'text-orange-600' : '' }}
                  {{ $attendance->status === 'absent' ? 'text-red-600' : '' }}
                  {{ $attendance->status === 'leave' ? 'text-purple-600' : '' }}
                ">
                  {{ strtoupper(str_replace('_', ' ', $attendance->status)) }}
                </span>
              </td>
            </tr>
            
            @if(($index + 1) % 20 === 0 && !$loop->last)
              </tbody>
            </table>
          </div>
          
          <div class="page page-break">
            <!-- Repeat Header on New Page -->
            <div class="text-center mb-3 border-b-2 border-black pb-2">
              <div class="text-xl font-bold">ATTENDANCE REPORT (Continued)</div>
              <div class="text-xs">Page {{ floor(($index + 1) / 20) + 1 }}</div>
            </div>
            
            <table class="text-xs mb-3">
              <thead>
                <tr class="bg-gray-100">
                  <th class="text-center w-8">No</th>
                  @if(!$userId)
                    <th class="text-left">Employee</th>
                  @endif
                  <th class="text-center w-20">Date</th>
                  <th class="text-center w-24">Morning</th>
                  <th class="text-center w-24">Afternoon</th>
                  <th class="text-center w-20">Total Hours</th>
                  <th class="text-center w-16">Status</th>
                </tr>
              </thead>
              <tbody>
            @endif
          @empty
            <tr>
              <td colspan="{{ $userId ? '6' : '7' }}" class="text-center py-4">
                No attendance records found for the selected period.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Total Section -->
      <div class="mb-3">
        <div class="flex justify-between border-t-2 border-b-2 border-black py-2 text-xs font-bold">
          <span>REPORT SUMMARY</span>
          <div class="text-right">
            <div>TOTAL WORK HOURS: {{ number_format($stats['total_hours'], 1) }} hours</div>
            <div>AVERAGE HOURS: {{ number_format($stats['avg_hours'], 1) }} hours/day</div>
          </div>
        </div>
      </div>

      <!-- Footer Notes -->
      <div class="border border-black p-2 mb-2">
        <div class="font-semibold mb-1 text-xs">IMPORTANT NOTES:</div>
        <div class="text-xs space-y-1">
          <div>• This report is generated automatically by the Attendify system.</div>
          <div>• All times are based on the system timezone and office location verification.</div>
          <div>• Work hours are calculated from morning and afternoon sessions combined.</div>
          <div>• Morning Session: Typically 07:30 AM - 11:30 AM | Afternoon Session: Typically 02:00 PM - 05:30 PM</div>
          <div>• Status indicators: ON TIME (arrived within grace period), LATE (arrived after grace period), ABSENT (no check-in), LEAVE (scheduled day off).</div>
          <div>• For discrepancies or questions, please contact the HR department.</div>
          <div>• This document is confidential and intended for authorized personnel only.</div>
        </div>
      </div>

      <!-- Signature Section -->
      <div class="grid grid-cols-2 gap-8 mt-6 text-xs">
        <div class="text-center">
          <div class="border-t border-black pt-1 mt-16">Prepared By</div>
          <div class="text-xs text-gray-600 mt-1">HR Department</div>
        </div>
        <div class="text-center">
          <div class="border-t border-black pt-1 mt-16">Approved By</div>
          <div class="text-xs text-gray-600 mt-1">Management</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-xs text-gray-600 text-center mt-4 border-t pt-2">
        <div>Attendify Attendance Management System</div>
        <div>Report ID: ATT-{{ now()->format('YmdHis') }} | Generated: {{ now()->format('Y-m-d H:i:s') }}</div>
        <div class="text-xs">This is a computer-generated report. No signature is required.</div>
      </div>

      <!-- Print Button -->
      <div class="no-print mt-6 text-center space-x-2">
        <button
          onclick="window.print()"
          class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
        >
          Print Report
        </button>
        <button
          onclick="window.close()"
          class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700"
        >
          Close
        </button>
      </div>
    </div>
  </body>
</html>
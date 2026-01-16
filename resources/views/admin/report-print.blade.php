<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance Report - Attendify</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    
    <!-- Inter font for English -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    />
    
    <!-- Noto Sans Khmer font -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;500;600;700;800&display=swap"
    />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      @page {
        size: A4;
        margin: 15mm 10mm;
      }

      @media print {
        body {
          margin: 0;
          padding: 0;
        }
        
        /* Ensure Khmer prints correctly */
        * {
          font-family: "Inter", "Noto Sans Khmer", sans-serif !important;
        }
        
        .no-print {
          display: none !important;
        }
        .page {
          page-break-after: avoid;
          box-shadow: none;
          padding: 0;
          margin: 0;
        }
        .page-break {
          page-break-after: always;
        }
      }

      body {
        font-family: "Inter", "Noto Sans Khmer", sans-serif;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      
      /* Khmer text styling */
      .khmer-text {
        font-family: "Noto Sans Khmer", sans-serif;
        line-height: 1.8;
      }
      
      /* Mixed content (English + Khmer) */
      .mixed-text {
        font-family: "Inter", "Noto Sans Khmer", sans-serif;
      }

      .page {
        width: 210mm;
        min-height: 297mm;
        padding: 15mm 10mm;
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
        padding: 3px 4px;
        font-size: 9px;
      }

      th {
        background-color: #f3f4f6 !important;
        font-weight: 600;
      }

      .gender-badge {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 8px;
        font-weight: 600;
      }

      .gender-male {
        background-color: #dbeafe;
        color: #1e40af;
      }

      .gender-female {
        background-color: #fce7f3;
        color: #be185d;
      }
    </style>
</head>
  <body>
    <div class="page">
      <!-- Header Section -->
      <div class="flex justify-between items-start text-[8px] mb-2">
        <div>{{ now()->format('m/d/y, g:i A') }}</div>
        <div>Attendify System</div>
      </div>

      <!-- Company Logo and Title -->
      <div class="flex justify-between items-start mb-3">
        <div class="flex items-start gap-2">
          <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white flex-shrink-0">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L1 8L12 14L21 9.27V17H23V8M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z"/>
            </svg>
          </div>
          <div>
            <div class="text-base font-bold leading-tight">Attendify</div>
            <div class="text-[8px] mt-0.5">Attendance Management System</div>
            <div class="text-[9px] font-semibold">ATTENDANCE REPORT</div>
          </div>
        </div>
        <div class="text-right">
          <div class="text-[8px]">Report Generated</div>
          <div class="text-[10px] font-semibold">{{ now()->format('F j, Y') }}</div>
          <div class="text-[8px] mt-0.5">{{ now()->format('h:i A') }}</div>
        </div>
      </div>

      <!-- Report Title -->
      <div class="text-center mb-2 border-b-2 border-black pb-1.5">
        <div class="text-xl font-bold leading-tight">ATTENDANCE REPORT</div>
        <div class="text-[10px] mt-0.5">Period: {{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</div>
      </div>

      <!-- Report Details -->
      <div class="mb-2">
        <div class="grid grid-cols-2 gap-x-6 gap-y-0.5 text-[9px]">
          <div class="flex">
            <span class="font-semibold w-28">Report Type:</span>
            <span>{{ $userId ? 'Individual' : 'All Employees' }}</span>
          </div>
          <div class="flex justify-end">
            <span class="font-semibold w-28">Total Records:</span>
            <span>{{ $attendances->count() }}</span>
          </div>
          
          @if($userId)
            <div class="flex">
              <span class="font-semibold w-28">Employee Name:</span>
              <span>{{ $attendances->first()->user->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-end">
              <span class="font-semibold w-28">Employee ID:</span>
              <span>{{ $userId }}</span>
            </div>
            <div class="flex">
              <span class="font-semibold w-28">Gender:</span>
              <span>{{ ucfirst($attendances->first()->user->gender ?? 'N/A') }}</span>
            </div>
            <div class="flex justify-end">
              <span class="font-semibold w-28">Phone:</span>
              <span>{{ $attendances->first()->user->phone ?? 'N/A' }}</span>
            </div>
            <div class="flex col-span-2">
              <span class="font-semibold w-28">Email:</span>
              <span>{{ $attendances->first()->user->email ?? 'N/A' }}</span>
            </div>
          @endif
          
          <div class="flex">
            <span class="font-semibold w-28">Start Date:</span>
            <span>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</span>
          </div>
          <div class="flex justify-end">
            <span class="font-semibold w-28">End Date:</span>
            <span>{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
          </div>
        </div>
      </div>

      <!-- Statistics Summary -->
      <div class="border-2 border-black p-1.5 mb-2">
        <div class="font-semibold mb-1 text-[9px]">SUMMARY STATISTICS:</div>
        <div class="grid grid-cols-4 gap-1.5 text-[8px]">
          <div class="text-center border border-gray-400 p-1.5">
            <div class="text-[8px] text-gray-600">PRESENT</div>
            <div class="text-base font-bold text-green-600">{{ $stats['total_present'] }}</div>
            <div class="text-[8px]">Days</div>
          </div>
          <div class="text-center border border-gray-400 p-1.5">
            <div class="text-[8px] text-gray-600">LATE</div>
            <div class="text-base font-bold text-orange-600">{{ $stats['total_late'] }}</div>
            <div class="text-[8px]">Days</div>
          </div>
          <div class="text-center border border-gray-400 p-1.5">
            <div class="text-[8px] text-gray-600">ABSENT</div>
            <div class="text-base font-bold text-red-600">{{ $stats['total_absent'] }}</div>
            <div class="text-[8px]">Days</div>
          </div>
          <div class="text-center border border-gray-400 p-1.5">
            <div class="text-[8px] text-gray-600">TOTAL HOURS</div>
            <div class="text-base font-bold text-blue-600">{{ number_format($stats['total_hours'], 1) }}</div>
            <div class="text-[8px]">Hours</div>
          </div>
        </div>
      </div>

      <!-- Top Performers Section -->
      @if(!$userId && $topUsers->count() > 0)
        <div class="border border-black p-1.5 mb-2">
          <div class="font-semibold mb-1 text-[9px]">TOP 5 PERFORMERS (BY WORK HOURS):</div>
          <table class="text-[8px]">
            <thead>
              <tr class="bg-gray-100">
                <th class="text-center" style="width: 8%;">Rank</th>
                <th class="text-left" style="width: 30%;">Employee Name</th>
                <th class="text-center" style="width: 15%;">Gender</th>
                <th class="text-center" style="width: 17%;">Total Hours</th>
                <th class="text-center" style="width: 15%;">Days</th>
                <th class="text-center" style="width: 15%;">Avg Hrs/Day</th>
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
                  <td class="text-center">
                    <span class="gender-badge {{ $topUser->gender === 'male' ? 'gender-male' : 'gender-female' }}">
                      {{ $topUser->gender === 'male' ? '♂' : '♀' }}
                      {{ ucfirst($topUser->gender ?? 'N/A') }}
                    </span>
                  </td>
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
      <table class="text-[8px] mb-2">
        <thead>
          <tr class="bg-gray-100">
            <th class="text-center" style="width: 5%;">No</th>
            @if(!$userId)
              <th class="text-left" style="width: 20%;">Employee</th>
              <th class="text-center" style="width: 10%;">Gender</th>
            @endif
            <th class="text-center" style="width: 12%;">Date</th>
            <th class="text-center" style="width: 16%;">Morning</th>
            <th class="text-center" style="width: 16%;">Afternoon</th>
            <th class="text-center" style="width: 10%;">Total Hrs</th>
            <th class="text-center" style="width: 11%;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attendances as $index => $attendance)
            <tr>
              <td class="text-center">{{ $index + 1 }}</td>
              @if(!$userId)
                <td>
                  <div class="font-semibold">{{ $attendance->user->name }}</div>
                  <div class="text-[7px] text-gray-600">{{ $attendance->user->email }}</div>
                </td>
                <td class="text-center">
                  <span class="gender-badge {{ $attendance->user->gender === 'male' ? 'gender-male' : 'gender-female' }}">
                    {{ $attendance->user->gender === 'male' ? '♂' : '♀' }}
                    {{ ucfirst($attendance->user->gender ?? 'N/A') }}
                  </span>
                </td>
              @endif
              <td class="text-center">
                <div class="font-semibold">{{ $attendance->attendance_date->format('M d') }}</div>
                <div class="text-[7px] text-gray-600">{{ $attendance->attendance_date->format('D') }}</div>
              </td>
              <td class="text-center">
                <div class="text-[7px]">
                  <div>In: {{ $attendance->morning_check_in ? $attendance->morning_check_in->format('h:i A') : '—' }}</div>
                  <div>Out: {{ $attendance->morning_check_out ? $attendance->morning_check_out->format('h:i A') : '—' }}</div>
                  <div class="font-semibold text-blue-600">{{ $attendance->formatted_morning_hours }}</div>
                </div>
              </td>
              <td class="text-center">
                <div class="text-[7px]">
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
            
            @if(($index + 1) % 25 === 0 && !$loop->last)
              </tbody>
            </table>
          </div>
          
          <div class="page page-break">
            <!-- Repeat Header on New Page -->
            <div class="text-center mb-2 border-b-2 border-black pb-1.5">
              <div class="text-lg font-bold">ATTENDANCE REPORT (Continued)</div>
              <div class="text-[8px]">Page {{ floor(($index + 1) / 25) + 1 }}</div>
            </div>
            
            <table class="text-[8px] mb-2">
              <thead>
                <tr class="bg-gray-100">
                  <th class="text-center" style="width: 5%;">No</th>
                  @if(!$userId)
                    <th class="text-left" style="width: 20%;">Employee</th>
                    <th class="text-center" style="width: 10%;">Gender</th>
                  @endif
                  <th class="text-center" style="width: 12%;">Date</th>
                  <th class="text-center" style="width: 16%;">Morning</th>
                  <th class="text-center" style="width: 16%;">Afternoon</th>
                  <th class="text-center" style="width: 10%;">Total Hrs</th>
                  <th class="text-center" style="width: 11%;">Status</th>
                </tr>
              </thead>
              <tbody>
            @endif
          @empty
            <tr>
              <td colspan="{{ $userId ? '6' : '8' }}" class="text-center py-3">
                No attendance records found for the selected period.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Total Section -->
      <div class="mb-2">
        <div class="flex justify-between border-t-2 border-b-2 border-black py-1.5 text-[9px] font-bold">
          <span>REPORT SUMMARY</span>
          <div class="text-right">
            <div>TOTAL WORK HOURS: {{ number_format($stats['total_hours'], 1) }} hours</div>
            <div>AVERAGE HOURS: {{ number_format($stats['avg_hours'], 1) }} hours/day</div>
          </div>
        </div>
      </div>

      <!-- Footer Notes -->
      <div class="border border-black p-1.5 mb-1.5">
        <div class="font-semibold mb-0.5 text-[9px]">IMPORTANT NOTES:</div>
        <div class="text-[7px] space-y-0.5">
          <div>• This report is generated automatically by the Attendify system.</div>
          <div>• All times are based on the system timezone and office location verification.</div>
          <div>• Work hours are calculated from morning and afternoon sessions combined.</div>
          <div>• Morning Session: Typically 07:30 AM - 11:30 AM | Afternoon Session: Typically 02:00 PM - 05:30 PM</div>
          <div>• Status indicators: ON TIME (arrived within grace period), LATE (arrived after grace period), ABSENT (no check-in), LEAVE (scheduled day off).</div>
          <div>• Gender information is displayed for statistical and HR purposes only.</div>
          <div>• For discrepancies or questions, please contact the HR department.</div>
          <div>• This document is confidential and intended for authorized personnel only.</div>
        </div>
      </div>

      <!-- Signature Section -->
      <div class="grid grid-cols-2 gap-6 mt-4 text-[8px]">
        <div class="text-center">
          <div class="border-t border-black pt-0.5 mt-10">Prepared By</div>
          <div class="text-[7px] text-gray-600 mt-0.5">HR Department</div>
        </div>
        <div class="text-center">
          <div class="border-t border-black pt-0.5 mt-10">Approved By</div>
          <div class="text-[7px] text-gray-600 mt-0.5">Management</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-[7px] text-gray-600 text-center mt-2 border-t pt-1">
        <div>Attendify Attendance Management System</div>
        <div>Report ID: ATT-{{ now()->format('YmdHis') }} | Generated: {{ now()->format('Y-m-d H:i:s') }}</div>
        <div class="text-[7px]">This is a computer-generated report. No signature is required.</div>
      </div>

      <!-- Print Button -->
      <div class="no-print mt-4 text-center space-x-2">
        <button
          onclick="window.print()"
          class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm"
        >
          Print Report
        </button>
        <button
          onclick="window.close()"
          class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 text-sm"
        >
          Close
        </button>
      </div>
    </div>
  </body>
</html>
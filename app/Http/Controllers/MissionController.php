<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Mission;
use App\Models\User;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MissionController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Show mission check-in page (Admin view)
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = Mission::with(['user', 'attendance']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('mission_date', $request->date);
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $missions = $query->orderBy('mission_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        $users = User::where('role_type', '!=', 'admin')->get();

        $stats = [
            'total' => Mission::count(),
            'pending' => Mission::where('status', 'pending')->count(),
            'approved' => Mission::where('status', 'approved')->count(),
            'rejected' => Mission::where('status', 'rejected')->count(),
        ];

        return view("home.mission-view", compact('missions', 'users', 'stats'));
    }

    /**
     * Show mission verification page
     */
    public function verify()
    {
        return view('verify.mission');
    }

    /**
     * Store mission check-in (ONE TIME ONLY - no checkout needed)
     */
    public function storeMission(Request $request)
    {
        $validated = $request->validate([
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
        ]);

        $today = Carbon::today();
        $userId = Auth::id();
        $user = Auth::user();

        // Check if user already has attendance for today
        $attendance = Attendance::where('user_id', $userId)
            ->where('attendance_date', $today)
            ->first();

        if ($attendance) {
            // User already checked in today - show error page
            return view('home.mission-result', [
                'success' => false,
                'message' => 'You have already checked in today',
                'details' => 'Each user can only check in once per day.',
                'missionDate' => $attendance->attendance_date->format('F d, Y'),
            ]);
        }

        // Create mission record
        $mission = Mission::create([
            'user_id' => $userId,
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
            'mission_date' => $today,
            'status' => 'pending',
            'active' => true,
        ]);

        // Create attendance record for mission (all-day tracking)
        $attendance = Attendance::create([
            'user_id' => $userId,
            'mission_id' => $mission->id,
            'attendance_date' => $today,
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
            'status' => 'absent',
            'work_hours' => 0, // Will be calculated when approved
        ]);

        // 🔥 SEND TELEGRAM NOTIFICATION TO ADMINS
        try {
            $this->telegramService->notifyAdminsAboutMission($user, $mission);
            Log::info("Mission check-in notification sent to admins for user {$user->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send mission notification: " . $e->getMessage());
            // Don't fail the request if notification fails
        }

        return view('home.mission-result', [
            'success' => true,
            'message' => 'Mission Check-in Submitted!',
            'details' => 'Your mission attendance is pending admin approval. Admins have been notified via Telegram.',
            'isPending' => true,
            'missionDate' => $attendance->attendance_date->format('F d, Y'),
        ]);
    }

    /**
     * Admin approve mission
     */
    public function approveMission(Mission $mission)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $mission->load('user');
        $adminName = Auth::user()->name;
        $mission->approve(Auth::id());

        // 🔥 SEND TELEGRAM NOTIFICATION TO ALL ADMINS
        try {
            $this->telegramService->notifyAdminsAboutApproval($mission, $adminName);
            Log::info("Approval notification sent to admins for mission #{$mission->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send approval notification: " . $e->getMessage());
        }

        return back()->with('success', 'Mission approved successfully.');
    }

    /**
     * Show my mission page
     */
    public function mymission()
    {
        return view('home.my-mission');
    }

    /**
     * Admin reject mission
     */
    public function rejectMission(Request $request, Mission $mission)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $mission->load('user');
        $adminName = Auth::user()->name;
        $mission->reject(Auth::id(), $validated['rejection_reason']);

        // 🔥 SEND TELEGRAM NOTIFICATION TO ALL ADMINS
        try {
            $this->telegramService->notifyAdminsAboutRejection($mission, $adminName, $validated['rejection_reason']);
            Log::info("Rejection notification sent to admins for mission #{$mission->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send rejection notification: " . $e->getMessage());
        }

        return back()->with('success', 'Mission rejected.');
    }

    /**
     * Show pending missions for admin approval
     */
    public function pendingMissions()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $missions = Mission::with(['user', 'attendance'])
            ->pending()
            ->orderBy('mission_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.missions.pending', compact('missions'));
    }

    /**
     * Show mission history for current user
     */
    public function myMissions()
    {
        $missions = Mission::with('attendance')
            ->where('user_id', Auth::id())
            ->orderBy('mission_date', 'desc')
            ->paginate(15);

        return view('missions.my-missions', compact('missions'));
    }

    /**
     * Show all missions for admin
     */
    public function allMissions()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $missions = Mission::with(['user', 'attendance', 'approvedBy', 'rejectedBy'])
            ->orderBy('mission_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.missions.all', compact('missions'));
    }

    /**
     * Get mission details for authenticated user (AJAX)
     */
    public function getMissionDetails($id)
    {
        $mission = Mission::with(['user', 'attendance'])->findOrFail($id);

        // Verify ownership
        if ($mission->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Status badge HTML
        $statusBadge = '';
        if ($mission->status === 'pending') {
            $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 shadow-sm"><span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>Pending Approval</span>';
        } elseif ($mission->status === 'approved') {
            $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 shadow-sm"><span class="material-symbols-outlined text-sm">check_circle</span>Approved</span>';
        } else {
            $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 shadow-sm"><span class="material-symbols-outlined text-sm">cancel</span>Rejected</span>';
        }

        return response()->json([
            'id' => $mission->id,
            'mission_date' => $mission->mission_date->format('M d, Y'),
            'check_in_time' => $mission->created_at->format('h:i A'),
            'latitude' => number_format($mission->latitude, 6),
            'longitude' => number_format($mission->longitude, 6),
            'latitude_raw' => $mission->latitude,
            'longitude_raw' => $mission->longitude,
            'status' => $mission->status,
            'status_badge' => $statusBadge,
            'work_hours' => $mission->attendance && $mission->isApproved() ? $mission->attendance->formatted_work_hours : null,
            'rejection_reason' => $mission->rejection_reason,
            'created_at' => $mission->created_at->format('M d, Y h:i A'),
            'updated_at' => $mission->updated_at->format('M d, Y h:i A'),
        ]);
    }

    /**
     * Show single mission details
     */
    public function show(Mission $mission)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && $mission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $mission->load(['user', 'attendance', 'approvedBy', 'rejectedBy']);

        return view('missions.show', compact('mission'));
    }

    /**
     * Cancel pending mission (user can cancel their own pending missions)
     */
    public function cancelMission($id)
    {
        $mission = Mission::findOrFail($id);
        
        // Only mission owner can cancel
        if ($mission->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Can only cancel pending missions
        if ($mission->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending missions can be cancelled.');
        }

        // Delete associated attendance
        if ($mission->attendance) {
            $mission->attendance->delete();
        }

        // Delete mission
        $mission->delete();

        return redirect()->back()->with('success', 'Mission check-in cancelled successfully.');
    }
}
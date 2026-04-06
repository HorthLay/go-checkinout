<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMissionController extends Controller
{
      public function show($id)
    {
        $mission = Mission::with(['user', 'attendance'])->findOrFail($id);

        $statusBadge = '';
        if ($mission->status === 'pending') {
            $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300"><span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>Pending</span>';
        } elseif ($mission->status === 'approved') {
            $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300"><span class="material-symbols-outlined text-sm">check_circle</span>Approved</span>';
        } else {
            $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300"><span class="material-symbols-outlined text-sm">cancel</span>Rejected</span>';
        }

        return response()->json([
            'id' => $mission->id,
            'user_name' => $mission->user->name,
            'user_email' => $mission->user->email,
            'user_image' => $mission->user->image,
            'user_initials' => strtoupper(substr($mission->user->name, 0, 2)),
            'mission_date' => $mission->mission_date->format('M d, Y'),
            'check_in_time' => $mission->created_at->format('h:i A'),
            'latitude' => number_format($mission->latitude, 6),
            'longitude' => number_format($mission->longitude, 6),
            'status' => $mission->status,
            'status_badge' => $statusBadge,
            'work_hours' => $mission->attendance && $mission->isApproved() ? $mission->attendance->formatted_work_hours : null,
            'rejection_reason' => $mission->rejection_reason,
        ]);
    }

    /**
     * Approve a mission
     */
    public function approve($id)
    {
        $mission = Mission::findOrFail($id);

        if ($mission->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending missions can be approved.');
        }

        $mission->approve(Auth::id());

        return redirect()->route('mission')->with('success', 'Mission approved successfully!');
    }

    /**
     * Reject a mission
     */
    public function reject(Request $request, $id)
    {

        $mission = Mission::findOrFail($id);

        if ($mission->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending missions can be rejected.');
        }

        $mission->reject(Auth::id(), $request->rejection_reason);

        return redirect()->route('mission')->with('success', 'Mission rejected.');
    }

    /**
     * Delete a mission
     */
    public function destroy($id)
    {
        $mission = Mission::findOrFail($id);

        // Only allow deleting rejected or pending missions
        if ($mission->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot delete approved missions.');
        }

        // Delete associated attendance
        if ($mission->attendance) {
            $mission->attendance->delete();
        }

        $mission->delete();

        return redirect()->route('mission')->with('success', 'Mission deleted successfully.');
    }
}

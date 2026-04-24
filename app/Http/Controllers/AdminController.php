<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use App\Models\Application;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Main Dashboard
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_pets' => Pet::count(),
            'successful_adoptions' => Application::where('status', 'Approved for Adoption')->count(),
        ];

        $recentLogs = ActivityLog::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs'));
    }

    // 2. User Management
    public function users()
    {
        // Fetch all users, sorting admins first, then staff, then adopters
        $users = User::orderByRaw("FIELD(role, 'admin', 'staff', 'adopter')")
                     ->latest()
                     ->get();
                     
        return view('admin.users', compact('users'));
    }

    // 3. Update User Role
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:adopter,staff,admin'
        ]);

        // Safety Catch: Prevent the admin from demoting themselves!
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Action denied: You cannot demote your own admin account.');
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        // Log the promotion/demotion
        ActivityLog::create([
            'type' => 'system',
            'title' => auth()->user()->name . " changed " . $user->name . "'s role from " . ucfirst($oldRole) . " to " . ucfirst($validated['role']),
            'status' => 'Role Updated',
            'icon' => '🛡️'
        ]);

        return back()->with('success', $user->name . "'s role has been updated to " . ucfirst($validated['role']) . ".");
    }

    // 4. Full Audit Logs
    public function logs()
    {
        $logs = ActivityLog::latest()->paginate(20);
        return view('admin.logs', compact('logs'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Application;
use App\Models\ActivityLog; // 👈 NEW MODEL IMPORTED
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    // 1. Dashboard View (NOW USING REAL LOGS)
   // 1. Dashboard View
    public function dashboard()
    {
        $stats = [
            'total_pets' => Pet::count(),
            'ready_pets' => Pet::where('status', 'Ready for Adoption')->count(),
            'pending_apps' => Application::where('status', 'Under Review')->count(),
        ];

        // 🌟 THE FIX: Only fetch 'pet' and 'application' logs. Hide 'system' logs!
        $recentActivities = ActivityLog::whereIn('type', ['pet', 'application'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function($log) {
                $log->date = $log->created_at;
                return $log;
            });

        return view('staff.dashboard', compact('stats', 'recentActivities'));
    }

    // 2. Manage Pets View
    public function managePets(Request $request)
    {
        $sort = $request->query('sort', 'latest');
        $search = $request->query('search'); 
        
        $pets = Pet::when($search, function($query) use ($search) {
                       $query->where('name', 'like', "%{$search}%")
                             ->orWhere('type', 'like', "%{$search}%");
                   })
                   ->when($sort === 'oldest', fn($q) => $q->oldest())
                   ->when($sort === 'latest', fn($q) => $q->latest())
                   ->when($sort === 'ready', fn($q) => $q->where('status', 'Ready for Adoption')->latest())
                   ->when($sort === 'review', fn($q) => $q->where('status', 'Under Review')->latest())
                   ->when($sort === 'home', fn($q) => $q->where('status', 'Found a Home')->latest())
                   ->get();

        return view('staff.manage-pets', compact('pets'));
    }

    // 3. Applications View
    public function applications(Request $request)
    {
        $applications = Application::with(['user', 'pet'])
            ->orderByRaw("FIELD(status, 'Pending', 'Under Review', 'Approved', 'Rejected')")
            ->latest()
            ->get();

        return view('staff.applications', compact('applications'));
    }

    // 4. Store New Pet
    public function storePet(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Dog,Cat',
            'age' => 'required|string|max:50',
            'status' => 'required|in:Ready for Adoption,Under Review,Found a Home,No Longer Available',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
            'health_radio' => 'required|string',
            'health_other' => 'nullable|string|max:255',
        ]);

        $healthSummary = $request->health_radio === 'Other' ? $request->health_other : $request->health_radio;

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'age' => $validated['age'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'health_summary' => $healthSummary,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $request->file('image')->store('pets', 'public');
        }

        $pet = Pet::create($data);

        // 📝 WRITE TO LOG: Add Pet
        ActivityLog::create([
            'type' => 'pet',
            'title' => auth()->user()->name . ' added a new pet profile: ' . $pet->name,
            'status' => $pet->status,
            'icon' => '🐾'
        ]);

        return back()->with('success', 'New pet added successfully!');
    }

    // 5. Update Existing Pet
    // 5. Update Existing Pet (WITH SMART LOGGING)
    // 5. Update Existing Pet (WITH REFINED SMART LOGGING)
    public function updatePet(Request $request, Pet $pet) 
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required',
            'age' => 'required',
            'status' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'health_summary' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            if ($pet->image) { Storage::disk('public')->delete($pet->image); }
            $data['image'] = $request->file('image')->store('pets', 'public');
        }

        // 1. Fill the model with new data but DO NOT save it yet
        $pet->fill($data);

        // 2. See EXACTLY how many fields are changing
        $changedFields = $pet->getDirty();
        $changeCount = count($changedFields);

        // Default message (used if multiple fields changed, or if only name/age/bio changed)
        $actionMessage = auth()->user()->name . " updated " . $pet->name . "'s profile."; 

        // 3. If ONLY ONE specific thing changed, give a targeted message
        if ($changeCount === 1) {
            if (isset($changedFields['status'])) {
                $actionMessage = auth()->user()->name . " updated " . $pet->name . "'s status to " . $pet->status . ".";
            } elseif (isset($changedFields['health_summary'])) {
                $actionMessage = auth()->user()->name . " updated " . $pet->name . "'s medical records.";
            } elseif (isset($changedFields['image'])) {
                $actionMessage = auth()->user()->name . " changed " . $pet->name . "'s profile photo.";
            }
        }

        // 4. Save the changes to the database
        $pet->save();

        // 5. Write the message to the Activity Log
        // Only log it if something actually changed!
        if ($changeCount > 0) {
            ActivityLog::create([
                'type' => 'pet',
                'title' => $actionMessage,
                'status' => $pet->status,
                'icon' => '✏️'
            ]);
        }

        return back()->with('success', 'Pet updated successfully!');
    }

    public function updateApplication(Request $request, Application $application)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,decline'
        ]);

        if ($validated['action'] === 'approve') {
            $application->status = 'Approved for Adoption';
            $application->pet->update(['status' => 'Found a Home']);
            $message = "Application approved! The adopter has been notified.";
        } else {
            $application->status = 'Application Declined';
            $application->pet->update(['status' => 'Ready for Adoption']);
            $message = "Application declined. The pet is ready for adoption again.";
        }

        $application->reviewed_by = auth()->id();
        $application->save();

        // Trigger Notification to Adopter
        $application->user->notify(new \App\Notifications\ApplicationStatusUpdated($application));

        // Log it for the Staff Dashboard
        ActivityLog::create([
            'type' => 'application',
            'title' => auth()->user()->name . " " . $validated['action'] . "d an application for " . $application->pet->name,
            'status' => $application->status,
            'icon' => $validated['action'] === 'approve' ? '🎉' : '❌'
        ]);

        return back()->with('success', $message);
    }
}
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

        
        $pets = Pet::with('photos')->when($search, function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
                })
                ->when($sort === 'oldest', fn($q) => $q->oldest())
                ->when($sort === 'latest', fn($q) => $q->latest())
                ->when($sort === 'ready', fn($q) => $q->where('status', 'Ready for Adoption')->latest())
                ->when($sort === 'review', fn($q) => $q->where('status', 'Under Review')->latest())
                ->when($sort === 'home', fn($q) => $q->where('status', 'Found a Home')->latest())
                ->paginate(12)->withQueryString();

        return view('staff.manage-pets', compact('pets'));
    }

    // 3. Applications View
    public function applications(Request $request) {
    $status = $request->query('status');
    $applications = Application::with(['user', 'pet', 'welfareCheckins'])
        ->when($status, fn($q) => $q->where('status', $status))
        ->orderByRaw("FIELD(status, 'Under Review', 'Approved for Adoption', 'Application Declined')")
        ->latest()->get();
    return view('staff.applications', compact('applications', 'status'));
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

    public function archivePet(Pet $pet)
{
    $pet->update(['status' => 'No Longer Available']);

    ActivityLog::create([
        'type'  => 'pet',
        'title' => auth()->user()->name . ' archived ' . $pet->name . '.',
        'status' => 'No Longer Available',
        'icon'  => '📦'
    ]);

    return back()->with('success', $pet->name . ' has been archived.');
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

            // Decline all OTHER pending applications for this pet
            Application::where('pet_id', $application->pet_id)
                ->where('id', '!=', $application->id)
                ->where('status', 'Under Review')
                ->each(function($other) {
                    $other->update(['status' => 'Application Declined']);
                    $other->user->notify(new \App\Notifications\ApplicationStatusUpdated($other));
                });

        } else {
            $application->status = 'Application Declined';
            $application->pet->update(['status' => 'Ready for Adoption']);
            $message = "Application declined. The pet is ready for adoption again.";
        }

        $application->reviewed_by = auth()->id();
        $application->save();

        // Notify the adopter whose application was just actioned
        $application->user->notify(new \App\Notifications\ApplicationStatusUpdated($application));

        ActivityLog::create([
            'type' => 'application',
            'title' => auth()->user()->name . " " . $validated['action'] . "d an application for " . $application->pet->name,
            'status' => $application->status,
            'icon' => $validated['action'] === 'approve' ? '🎉' : '❌'
        ]);

        return back()->with('success', $message);
    }

    public function addPhoto(Request $request, Pet $pet)
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        foreach ($request->file('photos') as $file) {
            $path = $file->store('pet-photos', 'public');
            $pet->photos()->create(['image' => $path]);
        }

        ActivityLog::create([
            'type'   => 'pet',
            'title'  => auth()->user()->name . ' added photos for ' . $pet->name,
            'status' => $pet->status,
            'icon'   => '🖼️'
        ]);

        return back()->with('success', 'Photos uploaded successfully!');
    }

    public function deletePhoto(Pet $pet, \App\Models\PetPhoto $photo)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->image);
        $photo->delete();
        return back()->with('success', 'Photo removed.');
    }

    public function managePhotos(Pet $pet)
    {
        $pet->load('photos');
        return view('staff.pet-photos', compact('pet'));
    }

    public function verifyUser(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
        ]);

        $user->update([
            'verification_status' => $request->action === 'verify' ? 'verified' : 'rejected',
        ]);

        \App\Models\ActivityLog::create([
            'type'   => 'system',
            'title'  => auth()->user()->name . ' ' . ($request->action === 'verify' ? 'verified' : 'rejected') . ' ' . $user->name . "'s ID.",
            'status' => $user->verification_status,
            'icon'   => $request->action === 'verify' ? '✅' : '❌',
        ]);

        return back()->with('success', $user->name . "'s verification has been " . ($request->action === 'verify' ? 'approved' : 'rejected') . '.');
    }
}
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdoptController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\StaffController; 
use App\Models\Pet;
use App\Http\Middleware\CheckRole; // 👈 IMPORT YOUR MIDDLEWARE DIRECTLY

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// 1. Landing Page (Bounces logged-in users to their correct portals)
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'staff') return redirect()->route('staff.dashboard');
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// 2. Browse Page (Bounces logged-in users to their correct portals)
Route::get('/browse', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'staff') return redirect()->route('staff.dashboard');
        return redirect()->route('discover'); 
    }
    return app(\App\Http\Controllers\AdoptController::class)->index(request());
})->name('browse');

// 3. Live Search & Filter
Route::get('/pets/search', [AdoptController::class, 'search'])->name('pets.search');


/*
|--------------------------------------------------------------------------
| ADOPTER PORTAL (Strictly Adopters Only)
|--------------------------------------------------------------------------
*/
// 👈 BYPASS ALIASES: Use CheckRole::class directly!
Route::middleware(['auth', 'verified', CheckRole::class.':adopter'])->group(function () {
    
    // Dashboard: View submitted applications
    Route::get('/dashboard', function () {
        $applications = auth()->user()->applications()->with('pet')->latest()->get();
        return view('dashboard', compact('applications'));
    })->name('dashboard');

    // 🌟 THE UPGRADED DISCOVER ROUTE: Now handles searches and dropdown filters!
    Route::get('/discover', function (\Illuminate\Http\Request $request) {
        $query = Pet::query();

        // 1. Text Search (Name, Breed, Type)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('breed', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('type', 'LIKE', '%' . $request->search . '%');
            });
        }

        // 2. The Dropdown Filters
        $filter = $request->input('filter', 'all');

        if ($filter === 'favorites') {
            // ONLY show pets this user has favorited
            $query->whereIn('id', auth()->user()->favorites->pluck('id'));
        } else {
            // For all normal searches, ensure we only show available pets
            $query->where('status', 'Ready for Adoption');
            
            if ($filter === 'dogs') {
                $query->where('type', 'Dog');
            } elseif ($filter === 'cats') {
                $query->where('type', 'Cat');
            } elseif ($filter === 'recent') {
                $query->latest();
            }
        }

        // Fetch the filtered pets
        $pets = $query->latest()->get();
        
        // Grab the user's favorites array so the hearts turn red!
        $favorites = auth()->user()->favorites->pluck('id')->toArray();

        return view('discover', compact('pets', 'favorites'));
    })->name('discover');

    // Individual Pet Profile
    Route::get('/pets/{pet}', [AdoptController::class, 'show'])->name('pets.show');

    // Adoption Applications
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');

    // Favorite Toggle Logic
    Route::post('/favorites/{pet}', function (Pet $pet) {
        auth()->user()->favorites()->toggle($pet->id);
        return back();
    })->name('favorites.toggle');

    // Mark a specific notification as read
    Route::post('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    })->name('notifications.read');

    // Profile Settings (Standard Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| STAFF PORTAL (Strictly Staff Only)
|--------------------------------------------------------------------------
*/
// 👈 Use CheckRole::class here too!
Route::middleware(['auth', CheckRole::class.':staff'])->prefix('staff')->name('staff.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    
    // Manage Pets
    Route::get('/pets', [StaffController::class, 'managePets'])->name('pets.index');
    
    // Applications
    Route::get('/applications', [StaffController::class, 'applications'])->name('applications.index');

    // Form Submissions
    Route::post('/pets', [StaffController::class, 'storePet'])->name('pets.store');
    Route::patch('/pets/{pet}', [StaffController::class, 'updatePet'])->name('pets.update');

    // 🌟 THE MISSING ROUTE: Form Submission for Application Reviews
    Route::patch('/applications/{application}', [StaffController::class, 'updateApplication'])->name('applications.update');

});


/*
|--------------------------------------------------------------------------
| ADMIN PORTAL (Strictly Admins Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role', [\App\Http\Controllers\AdminController::class, 'updateRole'])->name('users.role');
    // System Logs
    Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('logs');

});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
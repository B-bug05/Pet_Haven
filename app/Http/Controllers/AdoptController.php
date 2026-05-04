<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use App\Models\Application;

class AdoptController extends Controller
{
    public function index(Request $request)
    {
        $query = Pet::where('status', 'Ready for Adoption');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%") 
                ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        $pets = $query->latest()->paginate(9);
        return view('guest-browse', compact('pets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'adopter_address' => 'required|string',
            'contact_number' => 'required|string',
            'adopter_message' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        Application::create($validated);

        return back()->with('success', 'Your application has been submitted successfully!');
    }

    public function show(\App\Models\Pet $pet)
    {
        $pet->load('photos');
        $hasApplied = false;

        if (auth()->check()) {
            $hasApplied = \App\Models\Application::where('user_id', auth()->id())
                ->where('pet_id', $pet->id)
                ->exists();
        }

        return view('pets.show', [
            'pet' => $pet,
            'hasApplied' => $hasApplied
        ]);
    }

    public function search(Request $request)
{
    $query = \App\Models\Pet::where('status', 'Ready for Adoption');

    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->search . '%')
              ->orWhere('breed', 'LIKE', '%' . $request->search . '%')
              ->orWhere('type', 'LIKE', '%' . $request->search . '%');
        });
    }

    if ($request->filled('filter') && $request->filter !== 'all') {
        $filter = $request->filter;
        if ($filter === 'my_favorites' && auth()->check()) {
            $query->whereIn('id', auth()->user()->favorites->pluck('id'));
        } elseif ($filter === 'dogs') {
            $query->where('type', 'Dog');
        } elseif ($filter === 'cats') {
            $query->where('type', 'Cat');
        }
    }

    $pets = $query->latest()->paginate(9, ['*'], 'page', $request->page ?? 1);
    $favorites = auth()->check() ? auth()->user()->favorites->pluck('id')->toArray() : [];
    $hasMore = $pets->hasMorePages();

    return response()->json([
        'html'    => view('pets._grid', compact('pets', 'favorites'))->render(),
        'hasMore' => $hasMore,
    ]);
}
}
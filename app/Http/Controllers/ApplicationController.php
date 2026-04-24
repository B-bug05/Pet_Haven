<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'pet_id' => 'required|exists:pets,id',
        'adopter_address' => 'required|string',
        'contact_number' => 'required|string',
    ]);

    // 1. Save the Application
    $application = new \App\Models\Application();
    $application->user_id = auth()->id();
    $application->pet_id = $request->pet_id;
    $application->adopter_address = $request->adopter_address;
    $application->contact_number = $request->contact_number;
    $application->adopter_message = $request->adopter_message;
    $application->status = 'Under Review';
    $application->save();

    // 2. FORCE the Pet status change
    // Using find() and save() is the most direct way to bypass logic errors
    $pet = \App\Models\Pet::find($request->pet_id);
    if ($pet) {
        $pet->status = 'Under Review'; // Must match your migration EXACTLY
        $pet->save();
    }

    return back()->with('success', 'Application submitted and pet status updated!');
}
}
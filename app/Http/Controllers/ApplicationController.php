<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Notifications\NewApplicationSubmitted;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pet_id'                  => 'required|exists:pets,id',
            'adopter_address'         => 'required|string',
            'contact_number'          => 'required|string',
            'housing_type'            => 'required|string',
            'landlord_allows_pets'    => 'nullable|string',
            'has_other_pets'          => 'required|string',
            'has_outdoor_space'       => 'required|string',
            'hours_alone'             => 'required|string',
            'previous_pet_experience' => 'required|string',
            'why_this_pet'            => 'required|string',
        ]);

        $already = \App\Models\Application::where('user_id', auth()->id())
            ->where('pet_id', $request->pet_id)
            ->whereIn('status', ['Under Review', 'Approved for Adoption'])
            ->exists();

        if ($already) {
            return back()->with('error', 'You already have an active application for this pet.');
        }

        $application = new \App\Models\Application();
        $application->user_id                = auth()->id();
        $application->pet_id                 = $request->pet_id;
        $application->adopter_address        = $request->adopter_address;
        $application->contact_number         = $request->contact_number;
        $application->adopter_message        = $request->adopter_message;
        $application->housing_type           = $request->housing_type;
        $application->landlord_allows_pets   = $request->landlord_allows_pets;
        $application->has_other_pets         = $request->has_other_pets;
        $application->has_outdoor_space      = $request->has_outdoor_space;
        $application->hours_alone            = $request->hours_alone;
        $application->previous_pet_experience = $request->previous_pet_experience;
        $application->why_this_pet           = $request->why_this_pet;
        $application->status                 = 'Under Review';
        $application->save();

        $application->load(['pet', 'user']);

        $staffUsers = \App\Models\User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $staff->notify(new \App\Notifications\NewApplicationSubmitted($application));
        }

        return back()->with('success', 'Application submitted successfully!');
    }
}
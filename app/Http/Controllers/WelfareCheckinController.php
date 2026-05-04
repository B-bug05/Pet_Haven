<?php

namespace App\Http\Controllers;

use App\Models\WelfareCheckin;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ApplicationStatusUpdated;

class WelfareCheckinController extends Controller
{
    // Staff requests a check-in
    public function request(Application $application)
    {
        WelfareCheckin::create([
            'application_id' => $application->id,
            'requested_by'   => auth()->id(),
            'status'         => 'pending',
        ]);

        // Notify the adopter
        $application->user->notify(new \App\Notifications\WelfareCheckinRequested($application));

        return back()->with('success', 'Welfare check-in requested. The adopter has been notified.');
    }

    // Adopter submits a check-in
    public function store(Request $request, WelfareCheckin $checkin)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'photo' => 'required|image|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('welfare', 'public');
        }

        $checkin->update([
            'status'       => 'submitted',
            'message'      => $request->message,
            'photo'        => $photoPath,
            'submitted_by' => auth()->id(),
        ]);

        // Notify all staff
        User::where('role', 'staff')->get()->each(function ($staff) use ($checkin) {
            $staff->notify(new \App\Notifications\WelfareCheckinSubmitted($checkin));
        });

        return back()->with('success', 'Thank you! Your check-in has been submitted.');
    }
}
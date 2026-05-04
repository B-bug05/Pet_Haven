<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function uploadId(Request $request)
    {
        $request->validate([
            'id_document' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $user = auth()->user();

        // Delete old document if exists
        if ($user->id_document) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->id_document);
        }

        $path = $request->file('id_document')->store('id-documents', 'public');

        $user->update([
            'id_document'         => $path,
            'verification_status' => 'pending',
        ]);

        return back()->with('success', 'Your ID has been submitted for verification.');
    }
}

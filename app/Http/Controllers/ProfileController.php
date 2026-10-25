<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $teacher = $request->user();
        $teacher->fill($request->validated());
        $teacher->save();

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $teacher = $request->user();

        $teacher->update([
            'password_hash' => Hash::make($request->validated('password')),
        ]);

        return back()->with('status', 'password-updated');
    }
}

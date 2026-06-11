<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ];

        if ($user->role === 'doctor') {
            $rules['photo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->role === 'doctor' && $request->hasFile('photo')) {
            $doctorProfile = $user->doctorProfile;
            if ($doctorProfile) {
                if (filled($doctorProfile->photo_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doctorProfile->photo_path);
                }
                $doctorProfile->photo_path = $request->file('photo')->store('doctor-photos', 'public');
                $doctorProfile->save();
            }
        }

        return back()->with('status', 'Account settings updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPatientProfileController extends Controller
{
    public function edit(User $user): View
    {
        abort_unless($user->role === 'patient', 404);

        return view('admin.patients.profile-edit', [
            'patient' => $user,
            'profile' => $user->patientProfile,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'patient', 404);

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:50'],
            'height' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'string', 'max:50'],
            'allergies' => ['nullable', 'string', 'max:3000'],
            'known_conditions' => ['nullable', 'string', 'max:3000'],
            'medical_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->save();

        $user->patientProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'known_conditions' => $validated['known_conditions'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
            ]
        );

        return redirect()->route('admin.appointments.index')->with('status', 'Patient profile updated successfully.');
    }
}

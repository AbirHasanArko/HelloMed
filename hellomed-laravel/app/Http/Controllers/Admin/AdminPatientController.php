<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPatientController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse|View
    {
        $query = User::query()->where('role', 'patient')->with('patientProfile');

        // Map gender filter to patientProfile.gender
        if ($request->has('filters') && !empty($request->filters['gender'])) {
            $filters = $request->filters;
            $filters['patientProfile.gender'] = $filters['gender'];
            unset($filters['gender']);
            $request->merge(['filters' => $filters]);
        }

        $result = User::handleSearchAndFilters($request, $query, function ($user) {
            return [
                'id' => $user->id,
                'title' => $user->name,
                'subtitle' => $user->email . ' | ' . $user->phone
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.patients.index', [
            'patients' => $result->latest()->paginate(15)->withQueryString(),
            'routePrefix' => 'admin',
        ]);
    }

    public function create(): View
    {
        return view('admin.patients.create', [
            'routePrefix' => 'admin',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:50'],
            'height' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'string', 'max:50'],
            'allergies' => ['nullable', 'string', 'max:3000'],
            'known_conditions' => ['nullable', 'string', 'max:3000'],
            'medical_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        $user->patientProfile()->create([
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'known_conditions' => $validated['known_conditions'] ?? null,
            'medical_notes' => $validated['medical_notes'] ?? null,
        ]);

        return redirect()->route('admin.patients.index')->with('status', 'Patient created successfully.');
    }

    public function edit(User $patient): View
    {
        abort_unless($patient->role === 'patient', 404);

        return view('admin.patients.edit', [
            'patient' => $patient,
            'profile' => $patient->patientProfile,
            'routePrefix' => 'admin',
        ]);
    }

    public function update(Request $request, User $patient): RedirectResponse
    {
        abort_unless($patient->role === 'patient', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($patient->id)],
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

        $patient->name = $validated['name'];
        $patient->email = $validated['email'];
        if (isset($validated['phone'])) {
            $patient->phone = $validated['phone'];
        }
        if (!empty($validated['password'])) {
            $patient->password = Hash::make($validated['password']);
        }
        $patient->save();

        $patient->patientProfile()->updateOrCreate(
            ['user_id' => $patient->id],
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

        return redirect()->route('admin.patients.index')->with('status', 'Patient profile updated successfully.');
    }

    public function destroy(User $patient): RedirectResponse
    {
        abort_unless($patient->role === 'patient', 404);
        
        $patient->delete();
        
        return redirect()->route('admin.patients.index')->with('status', 'Patient deleted successfully.');
    }
}

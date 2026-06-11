<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function create()
    {
        return view('admin.doctors.create', [
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateDoctorPayload($request, true);

        $doctorUser = \App\Models\User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['doctor_email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['initial_password']),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('doctor-photos', 'public');
        }

        Doctor::query()->create([
            ...$validated,
            'user_id' => $doctorUser->id,
            'photo_path' => $photoPath,
            'online_available' => $request->boolean('online_available'),
            'offline_available' => $request->boolean('offline_available'),
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'featured_order' => $request->integer('featured_order', 0),
            'available_days' => $request->input('available_days', []),
            'online_available_days' => $request->input('online_available_days', []),
            'offline_available_days' => $request->input('offline_available_days', []),
        ]);

        AuditLogger::log('user.role_assigned', $doctorUser, [], [
            'role' => 'doctor',
            'is_active' => $doctorUser->is_active,
        ]);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor account created successfully.');
    }

    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse|View
    {
        $this->authorize('viewAny', Doctor::class);
        $query = Doctor::query()->with(['department', 'user']);

        $result = Doctor::handleSearchAndFilters($request, $query, function ($doctor) {
            return [
                'id' => $doctor->id,
                'title' => $doctor->name,
                'subtitle' => $doctor->department?->name ?? ''
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.doctors.index', [
            'doctors' => $result->latest()->paginate(15)->withQueryString(),
            'routePrefix' => 'admin',
        ]);
    }

    public function edit(Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        return view('admin.doctors.edit', [
            'doctor' => $doctor,
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $validated = $this->validateDoctorPayload($request, false, $doctor);

        $photoPath = $doctor->photo_path;
        if ($request->hasFile('photo')) {
            if (filled($doctor->photo_path)) {
                Storage::disk('public')->delete($doctor->photo_path);
            }
            $photoPath = $request->file('photo')->store('doctor-photos', 'public');
        }

        $user = $doctor->user;
        if ($user) {
            $user->name = $validated['name'];
            if (isset($validated['phone'])) {
                $user->phone = $validated['phone'];
            }
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
        }

        $doctor->update([
            ...$validated,
            'photo_path' => $photoPath,
            'online_available' => $request->boolean('online_available'),
            'offline_available' => $request->boolean('offline_available'),
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured', false),
            'featured_order' => $request->integer('featured_order', 0),
            'available_days' => $request->input('available_days', []),
            'online_available_days' => $request->input('online_available_days', []),
            'online_available_from' => $validated['online_available_from'] ?? null,
            'online_available_to' => $validated['online_available_to'] ?? null,
            'offline_available_days' => $request->input('offline_available_days', []),
            'offline_available_from' => $validated['offline_available_from'] ?? null,
            'offline_available_to' => $validated['offline_available_to'] ?? null,
        ]);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor schedule updated.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $this->authorize('delete', $doctor);

        $user = $doctor->user;
        if (filled($doctor->photo_path)) {
            Storage::disk('public')->delete($doctor->photo_path);
        }
        $doctor->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor deleted successfully.');
    }

    private function validateDoctorPayload(Request $request, bool $isCreate, ?Doctor $doctor = null): array
    {
        $rules = [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'online_fee' => ['nullable', 'numeric', 'min:0'],
            'offline_fee' => ['nullable', 'numeric', 'min:0'],
            'clinic_address' => ['nullable', 'string', 'max:255'],
            'available_days' => ['nullable', 'array'],
            'available_days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_to' => ['nullable', 'date_format:H:i', 'after:available_from'],
            'online_available_days' => ['nullable', 'array'],
            'online_available_days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'online_available_from' => ['nullable', 'date_format:H:i'],
            'online_available_to' => ['nullable', 'date_format:H:i', 'after:online_available_from'],
            'offline_available_days' => ['nullable', 'array'],
            'offline_available_days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'offline_available_from' => ['nullable', 'date_format:H:i'],
            'offline_available_to' => ['nullable', 'date_format:H:i', 'after:offline_available_from'],
            'slot_minutes' => ['required', 'integer', 'in:15,20,30,45,60'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($isCreate) {
            $rules['doctor_email'] = ['required', 'email', 'max:255', Rule::unique('users', 'email')];
            $rules['initial_password'] = ['required', 'string', 'min:8', 'max:255'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'max:255'];
        }

        return $request->validate($rules);
    }
}

<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OfflineAppointmentController extends Controller
{
    public function create()
    {
        // Get departments with active doctors
        $departments = Department::whereHas('doctors', function ($query) {
            $query->where('is_active', true);
        })->with(['doctors' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        return view('staff.offline-appointments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'required|email|max:255',
            'patient_phone' => 'required|string|max:255',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:1000',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|string|max:50',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'allergies' => 'nullable|string|max:3000',
            'known_conditions' => 'nullable|string|max:3000',
            'medical_notes' => 'nullable|string|max:5000',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $scheduledFor = Carbon::parse($validated['scheduled_date'] . ' ' . $validated['scheduled_time']);
            
            // Find or create the user
            $user = User::where('email', $validated['patient_email'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $validated['patient_name'],
                    'email' => $validated['patient_email'],
                    'password' => Hash::make('password123'),
                    'role' => 'patient',
                ]);
            }

            // Update or create patient profile
            $profileData = array_filter([
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'known_conditions' => $validated['known_conditions'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
            ]);

            if (!empty($profileData)) {
                $user->patientProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
            }

            // Validate slot
            $service = app(\App\Services\AppointmentSlotService::class);
            $service->checkAvailability($validated['doctor_id'], $scheduledFor->toDateTimeString(), 'offline', $user->id);

            $doctor = Doctor::findOrFail($validated['doctor_id']);

            // Create confirmed appointment directly
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'doctor_id' => $doctor->id,
                'department_id' => $doctor->department_id,
                'patient_name' => $validated['patient_name'],
                'patient_email' => $validated['patient_email'],
                'patient_phone' => $validated['patient_phone'],
                'service_mode' => 'offline',
                'scheduled_for' => $scheduledFor,
                'reason' => $validated['reason'],
                'status' => 'confirmed',
                'payment_status' => 'not_required',
            ]);

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Offline appointment successfully booked for ' . $user->name . '. Password for new account (if any) is password123.');
        });
    }
}

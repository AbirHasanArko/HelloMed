<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $profile = $user->patientProfile;

        return view('patient.appointments', [
            'profile' => $profile,
            'appointments' => Appointment::query()
                ->with(['doctor.department', 'payments'])
                ->where('user_id', $user->id)
                ->latest('scheduled_for')
                ->paginate(15),
        ]);
    }

    public function show(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);

        return view('patient.appointment-show', [
            'appointment' => $appointment->load([
                'doctor.department',
                'payments',
                'prescriptionItems.medicine',
                'chatMessages' => fn ($query) => $query->with('user')->orderBy('created_at'),
            ]),
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'action' => ['required', 'in:cancel,reschedule'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ]);

        if ($validated['action'] === 'cancel') {
            if (! in_array($appointment->status, ['pending', 'confirmed'], true)) {
                return back()->withErrors([
                    'action' => 'Only pending or confirmed appointments can be cancelled.',
                ]);
            }

            $appointment->update(['status' => 'cancelled']);

            return back()->with('status', 'Appointment cancelled successfully.');
        }

        $newSlot = Carbon::parse($validated['scheduled_for']);
        $doctor = $appointment->doctor;

        if (Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('id', '!=', $appointment->id)
            ->where('scheduled_for', $newSlot)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists()) {
            return back()->withErrors([
                'scheduled_for' => 'The requested slot is already booked.',
            ]);
        }

        $appointment->update([
            'scheduled_for' => $newSlot,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Appointment rescheduled successfully.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'patient', 403);

        $validated = $request->validate([
            'allergies' => ['nullable', 'string', 'max:3000'],
            'medical_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $request->user()->patientProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'allergies' => $validated['allergies'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
            ]
        );

        return back()->with('status', 'Patient profile updated successfully.');
    }
}

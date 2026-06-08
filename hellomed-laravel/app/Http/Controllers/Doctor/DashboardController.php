<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $request = request();
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor, 403, 'No doctor profile is linked to this account.');

        $filter = $request->string('appointment_filter')->toString();
        if (! in_array($filter, ['today', 'next', 'past', 'all'], true)) {
            $filter = 'next';
        }

        $appointmentsQuery = Appointment::query()
            ->where('doctor_id', $doctor->id);

        if ($filter === 'today') {
            $appointmentsQuery->whereDate('scheduled_for', now()->toDateString());
        } elseif ($filter === 'next') {
            $appointmentsQuery->where('scheduled_for', '>=', now());
        } elseif ($filter === 'past') {
            $appointmentsQuery->where('scheduled_for', '<', now());
        }

        $calendarSummary = Appointment::query()
            ->selectRaw('DATE(scheduled_for) as appointment_date, COUNT(*) as total')
            ->where('doctor_id', $doctor->id)
            ->whereBetween('scheduled_for', [now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()])
            ->groupByRaw('DATE(scheduled_for)')
            ->orderByRaw('DATE(scheduled_for)')
            ->get();

        return view('doctor.dashboard', [
            'doctor' => $doctor,
            'appointmentFilter' => $filter,
            'calendarSummary' => $calendarSummary,
            'appointments' => $appointmentsQuery
                ->orderByDesc('scheduled_for')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function updateSchedule(Request $request): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor, 403, 'No doctor profile is linked to this account.');

        $validated = $request->validate([
            'clinic_address' => ['nullable', 'string', 'max:255'],
            'online_available_days' => ['nullable', 'array'],
            'online_available_days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'online_available_from' => ['nullable', 'date_format:H:i'],
            'online_available_to' => ['nullable', 'date_format:H:i', 'after:online_available_from'],
            'offline_available_days' => ['nullable', 'array'],
            'offline_available_days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'offline_available_from' => ['nullable', 'date_format:H:i'],
            'offline_available_to' => ['nullable', 'date_format:H:i', 'after:offline_available_from'],
            'slot_minutes' => ['required', 'integer', 'in:15,20,30,45,60'],
        ]);

        $doctor->update([
            'clinic_address' => $validated['clinic_address'] ?? null,
            'slot_minutes' => $validated['slot_minutes'],
            'online_available' => $request->boolean('online_available'),
            'offline_available' => $request->boolean('offline_available'),
            'online_available_days' => $request->input('online_available_days', []),
            'online_available_from' => $validated['online_available_from'] ?? null,
            'online_available_to' => $validated['online_available_to'] ?? null,
            'offline_available_days' => $request->input('offline_available_days', []),
            'offline_available_from' => $validated['offline_available_from'] ?? null,
            'offline_available_to' => $validated['offline_available_to'] ?? null,
            // Keep legacy unified fields in sync for backward compatibility.
            'available_days' => $request->input('offline_available_days', $request->input('online_available_days', [])),
            'available_from' => $validated['offline_available_from'] ?? ($validated['online_available_from'] ?? null),
            'available_to' => $validated['offline_available_to'] ?? ($validated['online_available_to'] ?? null),
        ]);

        return back()->with('status', 'Schedule updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        AuditLogger::log('auth.password_changed', $request->user(), [], [
            'role' => $request->user()->role,
            'changed_via' => 'doctor_dashboard',
        ]);

        return back()->with('status', 'Password updated successfully.');
    }
}

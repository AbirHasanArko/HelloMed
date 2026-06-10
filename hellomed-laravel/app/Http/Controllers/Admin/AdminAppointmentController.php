<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Support\AuditLogger;
use App\Support\NotificationService;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', \App\Models\Appointment::class);

        return view('admin.appointments.index', [
            'appointments' => Appointment::query()->with(['doctor.department'])->latest()->paginate(15),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $oldStatus = $appointment->status;

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
        ]);

        $appointment->update($validated);

        AuditLogger::log('appointment.status_updated', $appointment, [
            'status' => $oldStatus,
        ], [
            'status' => $appointment->status,
        ]);

        NotificationService::sendEmail(
            $appointment->patient_email,
            'HelloMed Appointment Status Updated',
            "Your appointment with {$appointment->doctor->name} is now {$appointment->status}.",
            'appointment.status.updated',
            $appointment->user,
            $appointment
        );

        return back()->with('status', 'Appointment updated.');
    }
}

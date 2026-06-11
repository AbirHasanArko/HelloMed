<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Support\AuditLogger;
use App\Support\NotificationService;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
    {
        $query = Appointment::query()->with(['doctor.department', 'user.patientProfile', 'payments']);

        $result = Appointment::handleSearchAndFilters($request, $query, function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => 'Apt #' . $appointment->id . ' - ' . $appointment->patient_name,
                'subtitle' => 'Dr. ' . $appointment->doctor->name . ' | ' . $appointment->status
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.appointments.index', [
            'appointments' => $result->latest()->paginate(15)->withQueryString(),
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

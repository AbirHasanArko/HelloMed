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
            'payment_status' => ['required', 'in:pending,paid,failed,refunded,not_required'],
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

        if ($appointment->user) {
            $appointment->user->notify(new \App\Notifications\SystemNotification(
                'Appointment Status Updated',
                "Your appointment with Dr. {$appointment->doctor->name} is now {$appointment->status}.",
                'moderate',
                route('patient.appointments.show', $appointment)
            ));
        }

        if ($validated['payment_status'] === 'paid' && $appointment->status === 'completed') {
            $month = date('Y-m', strtotime($appointment->scheduled_for));
            \Illuminate\Support\Facades\Artisan::call('app:sync-financials', ['--month' => $month]);
            
            $appointment->refresh(); // Get updated cuts
            if ($appointment->doctor?->user) {
                $appointment->doctor->user->notify(new \App\Notifications\SystemNotification(
                    'Manual Payment Cleared',
                    "Patient payment for appointment #{$appointment->id} was manually marked as paid. Your cut of ৳" . number_format($appointment->doctor_cut, 2) . " has been added to your pending payout.",
                    'normal',
                    route('doctor.appointments.show', $appointment)
                ));
            }
        }

        return back()->with('status', 'Appointment updated.');
    }
}

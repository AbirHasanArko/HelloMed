<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use App\Support\AuditLogger;
use App\Support\NotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function create(Doctor $doctor)
    {
        $upcomingAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_for', '>=', now())
            ->where('scheduled_for', '<', now()->addDays(14))
            ->orderBy('scheduled_for')
            ->get(['scheduled_for', 'scheduled_end']);

        return view('appointments.create', compact('doctor', 'upcomingAppointments'));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();

        $appointment = DB::transaction(function () use ($request, $validated) {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($validated['doctor_id']);
            $scheduledFor = Carbon::parse($validated['scheduled_for']);

            $alreadyBooked = Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->where('scheduled_for', $scheduledFor)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($alreadyBooked) {
                throw ValidationException::withMessages([
                    'scheduled_for' => 'The selected time slot is already booked.',
                ]);
            }

            $appointment = Appointment::query()->create([
                ...$validated,
                'scheduled_for' => $scheduledFor,
                'user_id' => $request->user()?->id,
                'payment_status' => $request->input('payment_method') && $request->input('payment_method') !== 'none' ? 'pending' : 'not_required',
                'online_meeting_link' => null,
                'doctor_prescription' => null,
                'prescription_diagnosis' => null,
                'prescription_medicines' => null,
                'prescription_advice' => null,
                'prescription_follow_up_date' => null,
                'prescription_written_at' => null,
            ]);

            if ($request->filled('payment_method') && $request->input('payment_method') !== 'none') {
                $amount = $request->input('service_mode') === 'online'
                    ? ($appointment->doctor->online_fee ?? $appointment->doctor->consultation_fee)
                    : ($appointment->doctor->offline_fee ?? $appointment->doctor->consultation_fee);

                Payment::query()->create([
                    'appointment_id' => $appointment->id,
                    'user_id' => $request->user()?->id,
                    'method' => $request->input('payment_method'),
                    'amount' => $amount,
                    'status' => 'pending',
                ]);
            }

            return $appointment;
        });

        NotificationService::sendEmail(
            $appointment->patient_email,
            'HelloMed Appointment Request Submitted',
            "Hello {$appointment->patient_name}, your appointment request with {$appointment->doctor->name} on {$appointment->scheduled_for?->format('M d, Y h:i A')} has been submitted.",
            'appointment.request.submitted',
            $request->user(),
            $appointment
        );

        $adminRecipients = User::query()
            ->whereIn('role', ['admin', 'staff'])
            ->pluck('email')
            ->all();

        if ($adminRecipients !== []) {
            foreach ($adminRecipients as $recipient) {
                NotificationService::sendEmail(
                    $recipient,
                    'New Appointment Request',
                    "New appointment request: {$appointment->patient_name} with {$appointment->doctor->name} on {$appointment->scheduled_for?->format('M d, Y h:i A')}.",
                    'appointment.request.admin_alert',
                    null,
                    $appointment
                );
            }
        }

        AuditLogger::log('appointment.created', $appointment, [], [
            'status' => $appointment->status,
            'service_mode' => $appointment->service_mode,
        ]);

        return redirect()
            ->route('home')
            ->with('status', 'Appointment request submitted successfully.');
    }
}

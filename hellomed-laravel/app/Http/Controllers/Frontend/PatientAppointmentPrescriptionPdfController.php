<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientAppointmentPrescriptionPdfController extends Controller
{
    public function __invoke(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        $hasPrescription = filled($appointment->doctor_prescription)
            || filled($appointment->prescription_diagnosis)
            || filled($appointment->prescription_medicines)
            || filled($appointment->prescription_advice)
            || $appointment->prescriptionItems()->exists();

        abort_if(! $hasPrescription, 404, 'Prescription is not available yet.');

        $appointment->load(['doctor.department', 'prescriptionItems.medicine']);

        $pdf = Pdf::loadView('pdfs.appointment-prescription', [
            'appointment' => $appointment,
        ]);

        return $pdf->download('prescription-'.$appointment->id.'.pdf');
    }
}

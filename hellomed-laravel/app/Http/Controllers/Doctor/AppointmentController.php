<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\LabTestRequest;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function show(Appointment $appointment): View
    {
        $doctor = request()->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        return view('doctor.appointment-show', [
            'appointment' => $appointment->load(['user.patientProfile', 'chatMessages.user', 'prescriptionItems.medicine', 'labTests']),
            'availableTests' => \App\Models\AvailableTest::where('is_active', true)->orderBy('name')->get(),
            'medicines' => Medicine::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'power', 'amount']),
            'medicinesForJs' => Medicine::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'power', 'amount'])
                ->map(fn ($medicine) => [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'power' => $medicine->power,
                    'amount' => $medicine->amount,
                ])
                ->values()
                ->all(),
            'existingPrescriptionItemsForJs' => old('prescription_items', $appointment->prescriptionItems->map(fn ($item) => [
                'medicine_id' => $item->medicine_id,
                'medicine_name' => $item->medicine_name,
                'amount' => $item->amount,
                'dosage' => $item->dosage,
                'intake_time' => $item->intake_time,
                'instructions' => $item->instructions,
            ])->values()->all()),
        ]);
    }

    public function updateMeetingLink(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        if ($appointment->service_mode !== 'online') {
            return back()->withErrors([
                'online_meeting_link' => 'Meeting link can only be set for online appointments.',
            ]);
        }

        $validated = $request->validate([
            'online_meeting_link' => ['required', 'url', 'max:1000'],
        ]);

        $oldMeetingLink = $appointment->online_meeting_link;

        $appointment->update([
            'online_meeting_link' => $validated['online_meeting_link'],
        ]);

        AuditLogger::log('appointment.meeting_link_updated', $appointment, [
            'online_meeting_link' => $oldMeetingLink,
        ], [
            'online_meeting_link' => $appointment->online_meeting_link,
        ]);

        return back()->with('status', 'Online meeting link updated.');
    }

    public function updatePrescription(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $validated = $request->validate([
            'prescription_diagnosis' => ['required', 'string', 'max:2000'],
            'prescription_medicines' => ['nullable', 'string', 'max:4000'],
            'prescription_advice' => ['nullable', 'string', 'max:5000'],
            'prescription_follow_up_date' => ['nullable', 'date', 'after_or_equal:today'],
            'prescription_follow_up_fee' => ['nullable', 'numeric', 'min:0'],
            'prescription_items' => ['nullable', 'array'],
            'prescription_items.*.medicine_id' => ['nullable', 'exists:medicines,id'],
            'prescription_items.*.medicine_name' => ['nullable', 'string', 'max:255'],
            'prescription_items.*.amount' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.dosage' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.intake_time' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.instructions' => ['nullable', 'string', 'max:255'],
        ]);

        $items = collect($validated['prescription_items'] ?? [])
            ->filter(fn (array $item): bool => filled($item['medicine_name'] ?? null))
            ->values();

        $safetyWarnings = [];

        $names = $items->map(fn (array $item): string => mb_strtolower(trim((string) $item['medicine_name'])))->filter();
        if ($names->count() !== $names->unique()->count()) {
            $safetyWarnings[] = 'Duplicate medicine found in prescription list.';
        }

        $highDosageFound = $items->contains(function (array $item): bool {
            $dosage = (string) ($item['dosage'] ?? '');
            if (! preg_match('/^(\d+)(\+\d+){2,}$/', str_replace(' ', '', $dosage))) {
                return false;
            }

            $parts = array_map('intval', explode('+', str_replace(' ', '', $dosage)));
            return array_sum($parts) > 6;
        });

        if ($highDosageFound) {
            $safetyWarnings[] = 'High frequency dosage pattern detected. Please re-check dosage instructions.';
        }

        $allergies = collect(explode(',', (string) ($appointment->user?->patientProfile?->allergies ?? '')))
            ->map(fn (string $allergy): string => mb_strtolower(trim($allergy)))
            ->filter();

        if ($allergies->isNotEmpty()) {
            foreach ($items as $item) {
                $medicineName = mb_strtolower((string) ($item['medicine_name'] ?? ''));
                foreach ($allergies as $allergy) {
                    if ($allergy !== '' && str_contains($medicineName, $allergy)) {
                        $safetyWarnings[] = "Possible allergy conflict: {$item['medicine_name']} may conflict with recorded allergy '{$allergy}'.";
                    }
                }
            }
        }

        $safetyWarnings = collect($safetyWarnings)->unique()->values();
        $safetyNotes = $safetyWarnings->implode("\n");

        $structuredLines = $items->map(function (array $item): string {
            $line = $item['medicine_name'];
            if (filled($item['amount'] ?? null)) {
                $line .= ' | Amount: '.$item['amount'];
            }
            if (filled($item['dosage'] ?? null)) {
                $line .= ' | Dosage: '.$item['dosage'];
            }
            if (filled($item['intake_time'] ?? null)) {
                $line .= ' | Time: '.$item['intake_time'];
            }
            if (filled($item['instructions'] ?? null)) {
                $line .= ' | Note: '.$item['instructions'];
            }

            return $line;
        })->implode("\n");

        $medicinesText = trim((string) ($validated['prescription_medicines'] ?? ''));
        if ($structuredLines !== '') {
            $medicinesText = trim($structuredLines."\n".($medicinesText !== '' ? "\nAdditional notes:\n{$medicinesText}" : ''));
        }

        $composed = "Diagnosis:\n{$validated['prescription_diagnosis']}\n\n".
            "Medicines:\n{$medicinesText}\n\n".
            "Advice:\n{$validated['prescription_advice']}";

        if (! empty($validated['prescription_follow_up_date'])) {
            $composed .= "\n\nFollow up date: {$validated['prescription_follow_up_date']}";
        }

        $oldPrescription = $appointment->only([
            'prescription_diagnosis',
            'prescription_medicines',
            'prescription_advice',
            'prescription_follow_up_date',
            'prescription_follow_up_fee',
            'status',
        ]);

        DB::transaction(function () use ($appointment, $validated, $composed, $medicinesText, $items, $oldPrescription, $safetyNotes): void {
            $appointment->update([
                'doctor_prescription' => $composed,
                'prescription_diagnosis' => $validated['prescription_diagnosis'],
                'prescription_medicines' => $medicinesText,
                'prescription_advice' => $validated['prescription_advice'],
                'prescription_safety_notes' => $safetyNotes !== '' ? $safetyNotes : null,
                'prescription_follow_up_date' => $validated['prescription_follow_up_date'] ?? null,
                'prescription_follow_up_fee' => isset($validated['prescription_follow_up_date']) ? ($validated['prescription_follow_up_fee'] ?? null) : null,
                'prescription_written_at' => now(),
            ]);

            $appointment->prescriptionItems()->delete();
            foreach ($items as $index => $item) {
                $appointment->prescriptionItems()->create([
                    'medicine_id' => $item['medicine_id'] ?? null,
                    'medicine_name' => $item['medicine_name'],
                    'amount' => $item['amount'] ?? null,
                    'dosage' => $item['dosage'] ?? null,
                    'intake_time' => $item['intake_time'] ?? null,
                    'instructions' => $item['instructions'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }

            AuditLogger::log('appointment.prescription_updated', $appointment, $oldPrescription, [
                'prescription_diagnosis' => $appointment->prescription_diagnosis,
                'prescription_medicines' => $appointment->prescription_medicines,
                'prescription_advice' => $appointment->prescription_advice,
                'prescription_follow_up_date' => optional($appointment->prescription_follow_up_date)->toDateString(),
                'status' => $appointment->status,
                'prescription_items_count' => $appointment->prescriptionItems()->count(),
            ]);
        });

        if ($appointment->user) {
            $appointment->user->notify(new \App\Notifications\SystemNotification(
                'Prescription Ready',
                "Dr. {$doctor->user->name} has uploaded your prescription for appointment #{$appointment->id}.",
                'important',
                route('patient.appointments.show', $appointment)
            ));
        }

        return back()->with('status', 'Prescription saved. Patient can now download it as PDF.');
    }

    public function markAsComplete(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        if (! in_array($appointment->status, ['pending', 'confirmed'], true)) {
            return back()->withErrors(['status' => 'Only pending or confirmed appointments can be completed.']);
        }

        $appointment->update(['status' => 'completed']);

        AuditLogger::log('appointment.status_updated', $appointment, [
            'status' => $appointment->getOriginal('status'),
        ], [
            'status' => 'completed',
        ]);

        if ($appointment->user) {
            $appointment->user->notify(new \App\Notifications\SystemNotification(
                'Appointment Completed',
                "Your appointment with Dr. {$doctor->user->name} has been marked as completed.",
                'normal',
                route('patient.appointments.show', $appointment)
            ));
        }

        if ($appointment->payment_status === 'paid') {
            $month = date('Y-m', strtotime($appointment->scheduled_for));
            \Illuminate\Support\Facades\Artisan::call('app:sync-financials', ['--month' => $month]);
            
            $appointment->refresh();
            if ($appointment->doctor_cut > 0) {
                $doctor->user->notify(new \App\Notifications\SystemNotification(
                    'Earnings Updated',
                    "Appointment #{$appointment->id} is complete. Your cut of ৳" . number_format($appointment->doctor_cut, 2) . " has been added to your pending payout.",
                    'normal',
                    route('doctor.appointments.show', $appointment)
                ));
            }
        }

        return back()->with('status', 'Appointment marked as completed.');
    }

    public function updatePatientProfile(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $validated = $request->validate([
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:50'],
            'height' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'string', 'max:50'],
            'allergies' => ['nullable', 'string', 'max:3000'],
            'known_conditions' => ['nullable', 'string', 'max:3000'],
        ]);

        $user = $appointment->user;
        if ($user) {
            $user->patientProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'height' => $validated['height'] ?? null,
                    'weight' => $validated['weight'] ?? null,
                    'allergies' => $validated['allergies'] ?? null,
                    'known_conditions' => $validated['known_conditions'] ?? null,
                ]
            );
        }

        return back()->with('status', 'Patient medical profile updated successfully.');
    }

    public function storeLabTest(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $validated = $request->validate([
            'test_names' => ['nullable', 'array'],
            'test_names.*' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['test_names'])) {
            return back()->with('status', 'No lab tests were added.');
        }

        foreach ($validated['test_names'] as $index => $testName) {
            $appointment->labTests()->create([
                'test_name' => $testName,
                'notes' => $validated['notes'][$index] ?? null,
                'status' => 'pending',
            ]);

            AuditLogger::log('appointment.lab_test_requested', $appointment, [], [
                'test_name' => $testName,
            ]);
        }

        $adminStaff = \App\Models\User::whereIn('role', ['admin', 'staff'])->get();
        foreach ($adminStaff as $staff) {
            $staff->notify(new \App\Notifications\SystemNotification(
                'New Diagnostic Test Request',
                "Dr. {$doctor->user->name} prescribed lab tests for {$appointment->patient_name}.",
                'moderate',
                route($staff->role . '.diagnostic-services.index')
            ));
        }

        return back()->with('status', 'Lab test requested successfully.');
    }

    public function destroyLabTest(Request $request, LabTestRequest $labTest): RedirectResponse
    {
        $appointment = $labTest->appointment;
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);
        abort_unless($labTest->status === 'pending', 400, 'Cannot delete a completed lab test.');

        $testName = $labTest->test_name;
        $labTest->delete();

        AuditLogger::log('appointment.lab_test_deleted', $appointment, ['test_name' => $testName], []);

        return back()->with('status', 'Lab test request deleted.');
    }
}

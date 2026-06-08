<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AppointmentSlotService
{
    /**
     * Check if the requested appointment slot is available.
     * Throws a ValidationException if it is not available.
     *
     * @param int $doctorId
     * @param string $scheduledFor datetime string
     * @param string $serviceMode 'online' or 'offline'
     * @param int|null $userId to check if patient overlaps with themselves
     * @throws ValidationException
     */
    public function checkAvailability(int $doctorId, string $scheduledFor, string $serviceMode, ?int $userId = null): void
    {
        $doctor = Doctor::query()->find($doctorId);
        if (!$doctor) {
            throw ValidationException::withMessages([
                'doctor_id' => 'The selected doctor does not exist.',
            ]);
        }

        if ($serviceMode === 'online' && !$doctor->online_available) {
            throw ValidationException::withMessages([
                'service_mode' => 'The selected doctor does not offer online consultations.',
            ]);
        }

        if ($serviceMode === 'offline' && !$doctor->offline_available) {
            throw ValidationException::withMessages([
                'service_mode' => 'The selected doctor does not offer offline consultations.',
            ]);
        }

        $startDatetime = Carbon::parse($scheduledFor);
        $dayName = strtolower($startDatetime->format('l'));
        $scheduledTime = $startDatetime->format('H:i:s');
        
        $slotMinutes = $doctor->slot_minutes ?: 30;
        $endDatetime = $startDatetime->copy()->addMinutes($slotMinutes);

        $isOnline = $serviceMode === 'online';
        $availableDays = $isOnline
            ? ($doctor->online_available_days ?: $doctor->available_days)
            : ($doctor->offline_available_days ?: $doctor->available_days);
        $availableFrom = $isOnline
            ? ($doctor->online_available_from ?: $doctor->available_from)
            : ($doctor->offline_available_from ?: $doctor->available_from);
        $availableTo = $isOnline
            ? ($doctor->online_available_to ?: $doctor->available_to)
            : ($doctor->offline_available_to ?: $doctor->available_to);

        if (is_array($availableDays) && $availableDays !== [] && !in_array($dayName, array_map('strtolower', $availableDays), true)) {
            throw ValidationException::withMessages([
                'scheduled_for' => 'The selected doctor is not available on the chosen day.',
            ]);
        }

        if ($availableFrom && $availableTo) {
            if ($scheduledTime < $availableFrom || $scheduledTime > $availableTo) {
                throw ValidationException::withMessages([
                    'scheduled_for' => 'The selected time is outside the doctor availability window.',
                ]);
            }
        }

        // Check Doctor Overlap
        $doctorOverlap = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_for', '<', $endDatetime)
            ->where('scheduled_end', '>', $startDatetime)
            ->exists();

        if ($doctorOverlap) {
            throw ValidationException::withMessages([
                'scheduled_for' => 'The selected time slot is already booked for this doctor.',
            ]);
        }

        // Check Patient Overlap
        if ($userId) {
            $patientOverlap = Appointment::query()
                ->where('user_id', $userId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('scheduled_for', '<', $endDatetime)
                ->where('scheduled_end', '>', $startDatetime)
                ->exists();

            if ($patientOverlap) {
                throw ValidationException::withMessages([
                    'scheduled_for' => 'You already have another appointment booked during this time slot.',
                ]);
            }
        }
    }
}

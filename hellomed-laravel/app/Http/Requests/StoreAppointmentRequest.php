<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_email' => ['required', 'email', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:30'],
            'service_mode' => ['required', 'in:online,offline'],
            'scheduled_for' => ['required', 'date', 'after:now'],
            'payment_method' => ['nullable', 'in:none,bkash,nagad,cash-counter'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $doctor = Doctor::query()->find($this->integer('doctor_id'));

            if (! $doctor) {
                return;
            }

            if ($this->input('service_mode') === 'online' && ! $doctor->online_available) {
                $validator->errors()->add('service_mode', 'The selected doctor does not offer online consultations.');
            }

            if ($this->input('service_mode') === 'offline' && ! $doctor->offline_available) {
                $validator->errors()->add('service_mode', 'The selected doctor does not offer offline consultations.');
            }

            $scheduledFor = Carbon::parse($this->input('scheduled_for'));
            $dayName = strtolower($scheduledFor->format('l'));
            $scheduledTime = $scheduledFor->format('H:i:s');

            $isOnline = $this->input('service_mode') === 'online';
            $availableDays = $isOnline
                ? ($doctor->online_available_days ?: $doctor->available_days)
                : ($doctor->offline_available_days ?: $doctor->available_days);
            $availableFrom = $isOnline
                ? ($doctor->online_available_from ?: $doctor->available_from)
                : ($doctor->offline_available_from ?: $doctor->available_from);
            $availableTo = $isOnline
                ? ($doctor->online_available_to ?: $doctor->available_to)
                : ($doctor->offline_available_to ?: $doctor->available_to);

            if (is_array($availableDays) && $availableDays !== [] && ! in_array($dayName, array_map('strtolower', $availableDays), true)) {
                $validator->errors()->add('scheduled_for', 'The selected doctor is not available on the chosen day.');
            }

            if ($availableFrom && $availableTo) {
                if ($scheduledTime < $availableFrom || $scheduledTime > $availableTo) {
                    $validator->errors()->add('scheduled_for', 'The selected time is outside the doctor availability window.');
                }
            }

            if (Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->where('scheduled_for', $scheduledFor)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists()) {
                $validator->errors()->add('scheduled_for', 'The selected time slot is already booked.');
            }
        });
    }
}

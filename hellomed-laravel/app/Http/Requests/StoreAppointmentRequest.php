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
            'sender_number' => ['nullable', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $service = app(\App\Services\AppointmentSlotService::class);
            try {
                $service->checkAvailability(
                    $this->integer('doctor_id'),
                    $this->input('scheduled_for'),
                    $this->input('service_mode'),
                    $this->user()?->id
                );
            } catch (\Illuminate\Validation\ValidationException $e) {
                foreach ($e->errors() as $key => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add($key, $message);
                    }
                }
            }
        });
    }
}

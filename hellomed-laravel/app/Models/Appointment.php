<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Searchable;

class Appointment extends Model
{
    use HasFactory, Searchable;

    protected array $searchableFields = ['patient_name', 'patient_email', 'patient_phone', 'doctor.name'];

    protected $fillable = [
        'user_id',
        'doctor_id',
        'department_id',
        'service_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'service_mode',
        'scheduled_for',
        'scheduled_end',
        'status',
        'payment_method',
        'payment_status',
        'online_meeting_link',
        'reason',
        'notes',
        'doctor_prescription',
        'prescription_diagnosis',
        'prescription_medicines',
        'prescription_advice',
        'prescription_safety_notes',
        'prescription_follow_up_date',
        'prescription_written_at',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'scheduled_end' => 'datetime',
        'prescription_written_at' => 'datetime',
        'prescription_follow_up_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Appointment $appointment): void {
            if ($appointment->isDirty('scheduled_for') && $appointment->scheduled_for) {
                $doctor = $appointment->doctor ?? Doctor::find($appointment->doctor_id);
                if ($doctor) {
                    $slotMinutes = $doctor->slot_minutes ?: 30;
                    $appointment->scheduled_end = $appointment->scheduled_for->copy()->addMinutes($slotMinutes);
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(AppointmentChatMessage::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(AppointmentPrescriptionItem::class)->orderBy('sort_order');
    }
    public function labTests(): HasMany
    {
        return $this->hasMany(LabTestRequest::class);
    }
}

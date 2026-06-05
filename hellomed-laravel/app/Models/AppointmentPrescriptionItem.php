<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentPrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'medicine_id',
        'medicine_name',
        'amount',
        'dosage',
        'intake_time',
        'instructions',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}

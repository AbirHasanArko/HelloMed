<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTestRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'test_name',
        'notes',
        'status',
        'payment_status',
        'result_file_path',
        'uploaded_by',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function availableTest(): BelongsTo
    {
        return $this->belongsTo(AvailableTest::class, 'test_name', 'name');
    }
}

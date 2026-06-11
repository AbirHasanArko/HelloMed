<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'allergies',
        'medical_notes',
        'date_of_birth',
        'gender',
        'height',
        'weight',
        'known_conditions',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isIncomplete(): bool
    {
        return empty($this->date_of_birth)
            || empty($this->gender)
            || empty($this->height)
            || empty($this->weight)
            || empty($this->known_conditions);
    }
}

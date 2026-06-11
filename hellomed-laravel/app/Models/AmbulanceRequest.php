<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Searchable;

class AmbulanceRequest extends Model
{
    use HasFactory, Searchable;

    protected array $searchableFields = ['patient_name', 'patient_phone', 'address', 'notes'];

    protected $fillable = [
        'user_id',
        'patient_name',
        'patient_phone',
        'latitude',
        'longitude',
        'address',
        'status',
        'dispatched_at',
        'resolved_at',
        'staff_id',
        'notes',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}

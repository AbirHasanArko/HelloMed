<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'name',
        'slug',
        'specialty',
        'bio',
        'qualification',
        'experience_years',
        'consultation_fee',
        'online_fee',
        'offline_fee',
        'online_available',
        'offline_available',
        'clinic_address',
        'photo_path',
        'available_days',
        'online_available_days',
        'online_available_from',
        'online_available_to',
        'offline_available_days',
        'offline_available_from',
        'offline_available_to',
        'available_from',
        'available_to',
        'slot_minutes',
        'is_active',
        'is_featured',
        'featured_order',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'online_fee' => 'decimal:2',
        'offline_fee' => 'decimal:2',
        'available_days' => 'array',
        'online_available_days' => 'array',
        'offline_available_days' => 'array',
        'slot_minutes' => 'integer',
        'online_available' => 'boolean',
        'offline_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Doctor $doctor): void {
            if (blank($doctor->slug) && filled($doctor->name)) {
                $doctor->slug = Str::slug($doctor->name);
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }
}

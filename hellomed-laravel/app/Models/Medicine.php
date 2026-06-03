<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'medicine_group',
        'slug',
        'description',
        'strength',
        'power',
        'amount',
        'manufacturer',
        'price',
        'stock_quantity',
        'is_active',
        'requires_prescription',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'requires_prescription' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Medicine $medicine): void {
            if (blank($medicine->slug) && filled($medicine->name)) {
                $medicine->slug = Str::slug($medicine->name);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicineOrderItem::class);
    }

    public function appointmentPrescriptionItems(): HasMany
    {
        return $this->hasMany(AppointmentPrescriptionItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class AvailableTest extends Model
{
    /** @use HasFactory<\Database\Factories\AvailableTestFactory> */
    use HasFactory, Searchable;

    protected array $searchableFields = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'lab_room_number',
        'location',
        'fee_bdt',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee_bdt' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (AvailableTest $test): void {
            if (blank($test->slug) && filled($test->name)) {
                $test->slug = \Illuminate\Support\Str::slug($test->name);
            }
        });
    }
}

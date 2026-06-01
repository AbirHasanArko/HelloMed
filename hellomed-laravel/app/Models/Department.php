<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'service_scope',
        'is_active',
        'is_featured',
        'featured_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Department $department): void {
            if (blank($department->slug) && filled($department->name)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}

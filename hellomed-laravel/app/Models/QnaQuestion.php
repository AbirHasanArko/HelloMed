<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Searchable;

class QnaQuestion extends Model
{
    use HasFactory, Searchable;

    protected array $searchableFields = ['title', 'question', 'user.name'];

    protected $fillable = [
        'user_id',
        'title',
        'question',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QnaAnswer::class);
    }
}

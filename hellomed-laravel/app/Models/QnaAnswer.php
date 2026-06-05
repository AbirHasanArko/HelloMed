<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QnaAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'qna_question_id',
        'user_id',
        'answer',
        'is_official',
    ];

    protected $casts = [
        'is_official' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(QnaQuestion::class, 'qna_question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

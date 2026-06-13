<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'messages',
        'suggested_doctor_ids',
        'suggested_article_ids',
        'suggested_test_ids',
        'primary_department',
        'last_intent',
        'urgency_level',
    ];

    protected $casts = [
        'messages'              => 'array',
        'suggested_doctor_ids'  => 'array',
        'suggested_article_ids' => 'array',
        'suggested_test_ids'    => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

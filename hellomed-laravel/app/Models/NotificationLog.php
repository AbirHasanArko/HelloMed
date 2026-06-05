<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_email',
        'channel',
        'event_key',
        'status',
        'attempts',
        'last_error',
        'notifiable_type',
        'notifiable_id',
        'payload',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

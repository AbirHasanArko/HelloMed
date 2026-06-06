<?php

namespace App\Support;

use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    public static function sendEmail(
        string $recipientEmail,
        string $subject,
        string $body,
        string $eventKey,
        ?User $user = null,
        ?Model $notifiable = null,
        array $payload = []
    ): NotificationLog {
        $log = NotificationLog::query()->create([
            'user_id' => $user?->id,
            'recipient_email' => $recipientEmail,
            'channel' => 'email',
            'event_key' => $eventKey,
            'status' => 'pending',
            'attempts' => 0,
            'notifiable_type' => $notifiable ? class_basename($notifiable) : null,
            'notifiable_id' => $notifiable?->getKey(),
            'payload' => $payload,
        ]);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                Mail::raw($body, function ($message) use ($recipientEmail, $subject): void {
                    $message->to($recipientEmail)->subject($subject);
                });

                $log->update([
                    'status' => 'sent',
                    'attempts' => $attempt,
                    'sent_at' => now(),
                    'last_error' => null,
                ]);

                return $log;
            } catch (Throwable $e) {
                $log->update([
                    'status' => $attempt >= 3 ? 'failed' : 'retrying',
                    'attempts' => $attempt,
                    'last_error' => mb_substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

        return $log;
    }
}

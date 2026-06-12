<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    public string $title;
    public string $message;
    public string $level;
    public ?string $actionUrl;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param string $level 'important', 'moderate', or 'normal'
     * @param string|null $actionUrl
     */
    public function __construct(string $title, string $message, string $level = 'normal', ?string $actionUrl = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->level = $level;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level,
            'action_url' => $this->actionUrl,
        ];
    }
}

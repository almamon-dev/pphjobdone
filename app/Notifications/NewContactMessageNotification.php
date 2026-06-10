<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public $contactMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct($contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contact_message',
            'contact_id' => $this->contactMessage->id,
            'name' => trim($this->contactMessage->first_name . ' ' . $this->contactMessage->last_name),
            'email' => $this->contactMessage->email,
            'message' => \Illuminate\Support\Str::limit($this->contactMessage->message, 50),
        ];
    }
}

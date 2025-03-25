<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\NotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification type.
     *
     * @return NotificationType
     */
    abstract public function getType(): NotificationType;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $preference = NotificationPreference::getPreference($notifiable->id, $this->getType()->value);
        
        if ($preference->email) {
            $channels[] = 'mail';
        }
        
        if ($preference->in_app) {
            $channels[] = 'database';
        }
        
        if ($preference->sms && $notifiable->phone) {
            $channels[] = 'vonage';
        }
        
        return $channels;
    }
} 
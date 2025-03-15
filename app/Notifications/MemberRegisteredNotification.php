<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Welcome to Khairat Kematian'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('Thank you for registering with our Khairat Kematian system.'))
            ->line(__('Your account has been created successfully.'))
            ->line(__('Please complete your profile and add your dependents to fully benefit from our services.'))
            ->action(__('Complete Your Profile'), url('/profile'))
            ->line(__('If you have any questions, please contact our support team.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('Welcome to Khairat Kematian! Your account has been created successfully.'),
            'icon' => 'user-plus',
            'color' => 'purple',
        ];
    }
} 
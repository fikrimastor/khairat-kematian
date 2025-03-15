<?php

namespace App\Notifications;

use App\Models\Dependent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DependentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The dependent instance.
     *
     * @var \App\Models\Dependent
     */
    protected $dependent;

    /**
     * Create a new notification instance.
     */
    public function __construct(Dependent $dependent)
    {
        $this->dependent = $dependent;
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
            ->subject(__('Dependent Added'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('A new dependent has been added to your account:'))
            ->line(__('Name: :name', ['name' => $this->dependent->name]))
            ->line(__('Relationship: :relationship', ['relationship' => $this->dependent->relationship]))
            ->action(__('View Dependents'), url('/dependents'))
            ->line(__('If you did not add this dependent, please contact our support team immediately.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'dependent_id' => $this->dependent->id,
            'dependent_name' => $this->dependent->name,
            'relationship' => $this->dependent->relationship,
            'message' => __('New dependent :name has been added to your account.', ['name' => $this->dependent->name]),
            'icon' => 'users',
            'color' => 'indigo',
        ];
    }
}

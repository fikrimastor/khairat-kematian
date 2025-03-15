<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification subject.
     *
     * @var string
     */
    protected $subject;

    /**
     * The notification message.
     *
     * @var string
     */
    protected $message;

    /**
     * Additional data for the notification.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $subject, string $message, array $data = [])
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->data = $data;
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
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line($this->message);

        if (isset($this->data['action_text']) && isset($this->data['action_url'])) {
            $mail->action($this->data['action_text'], $this->data['action_url']);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'subject' => $this->subject,
            'message' => $this->message,
            'icon' => 'bell',
            'color' => 'gray',
        ], $this->data);
    }
} 
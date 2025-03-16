<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Payment $payment
    ) {}

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
            ->subject('Payment Rejected - Khairat Kematian')
            ->greeting('Assalamualaikum '.$notifiable->name)
            ->line('Unfortunately, your payment has been rejected.')
            ->line('Payment Details:')
            ->line('- Amount: RM'.number_format($this->payment->amount, 2))
            ->line('- Month: '.$this->payment->month)
            ->line('- Year: '.$this->payment->year)
            ->when($this->payment->notes, function (MailMessage $mail) {
                return $mail->line('Reason: '.$this->payment->notes);
            })
            ->line('Please submit a new payment or contact the administrator for more information.')
            ->action('Submit New Payment', route('payments.create'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'month' => $this->payment->month,
            'year' => $this->payment->year,
            'notes' => $this->payment->notes,
            'message' => 'Your payment has been rejected.',
            'type' => 'payment_rejected',
        ];
    }
}

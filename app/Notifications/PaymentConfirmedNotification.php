<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Payment $payment,
        public readonly Receipt $receipt
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
            ->subject('Payment Confirmed - Khairat Kematian')
            ->greeting('Assalamualaikum '.$notifiable->name)
            ->line('Your payment has been verified and confirmed.')
            ->line('Payment Details:')
            ->line('- Amount: RM'.number_format($this->payment->amount, 2))
            ->line('- Month: '.$this->payment->month)
            ->line('- Year: '.$this->payment->year)
            ->line('- Receipt Number: '.$this->receipt->receipt_number)
            ->action('View Receipt', route('receipts.show', $this->receipt->id))
            ->line('Thank you for your contribution to Khairat Kematian.');
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
            'receipt_id' => $this->receipt->id,
            'amount' => $this->payment->amount,
            'month' => $this->payment->month,
            'year' => $this->payment->year,
            'message' => 'Your payment has been verified and confirmed.',
            'type' => 'payment_confirmed',
        ];
    }
}

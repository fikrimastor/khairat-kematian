<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The payment instance.
     *
     * @var \App\Models\Payment
     */
    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
            ->subject(__('Payment Received'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('We have received your payment of :amount for :type.', [
                'amount' => 'RM ' . number_format($this->payment->amount, 2),
                'type' => __($this->payment->payment_type),
            ]))
            ->line(__('Your payment is now being processed and will be verified by our team.'))
            ->line(__('Payment Reference: :reference', ['reference' => $this->payment->reference_no ?? $this->payment->id]))
            ->action(__('View Payment Details'), url('/payments/' . $this->payment->id))
            ->line(__('Thank you for using our application!'));
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
            'payment_type' => $this->payment->payment_type,
            'payment_method' => $this->payment->payment_method,
            'status' => $this->payment->status,
            'reference_no' => $this->payment->reference_no,
            'icon' => 'cash',
            'color' => 'blue',
        ];
    }
} 
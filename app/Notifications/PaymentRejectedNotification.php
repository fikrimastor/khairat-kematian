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
        $mail = (new MailMessage)
            ->subject(__('Payment Rejected'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('Unfortunately, your payment of :amount for :type has been rejected.', [
                'amount' => 'RM '.number_format($this->payment->amount, 2),
                'type' => __($this->payment->payment_type),
            ]));

        if ($this->payment->notes) {
            $mail->line(__('Reason: :reason', ['reason' => $this->payment->notes]));
        }

        return $mail
            ->line(__('Please submit a new payment or contact our support team for assistance.'))
            ->action(__('Submit New Payment'), url('/payments/create'))
            ->line(__('If you have any questions, please contact us.'));
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
            'notes' => $this->payment->notes,
            'icon' => 'x-circle',
            'color' => 'red',
        ];
    }
}

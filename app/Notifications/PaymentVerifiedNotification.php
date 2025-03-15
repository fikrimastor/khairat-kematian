<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The payment instance.
     *
     * @var \App\Models\Payment
     */
    protected $payment;

    /**
     * The receipt instance.
     *
     * @var \App\Models\Receipt
     */
    protected $receipt;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, Receipt $receipt)
    {
        $this->payment = $payment;
        $this->receipt = $receipt;
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
            ->subject(__('Payment Verified'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('Your payment of :amount for :type has been verified.', [
                'amount' => 'RM ' . number_format($this->payment->amount, 2),
                'type' => __($this->payment->payment_type),
            ]))
            ->line(__('Receipt Number: :receipt', ['receipt' => $this->receipt->receipt_number]))
            ->action(__('View Receipt'), url('/receipts/' . $this->receipt->id))
            ->line(__('Thank you for your payment!'));
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
            'payment_type' => $this->payment->payment_type,
            'receipt_number' => $this->receipt->receipt_number,
            'icon' => 'check-circle',
            'color' => 'green',
        ];
    }
} 
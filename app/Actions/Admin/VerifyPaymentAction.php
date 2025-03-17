<?php

namespace App\Actions\Admin;

use App\Actions\Payments\GenerateReceiptAction;
use App\Enums\PaymentStatus;
use App\Events\Payment\PaymentRejected;
use App\Events\Payment\PaymentVerified;
use App\Models\Payment;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Support\Facades\DB;

class VerifyPaymentAction
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        private readonly GenerateReceiptAction $generateReceiptAction
    ) {}

    /**
     * Execute the action to verify or reject a payment.
     *
     * @param  int  $paymentId  The ID of the payment to verify
     * @param  int  $adminId  The ID of the admin performing the verification
     * @param  bool  $isApproved  Whether the payment is approved or rejected
     * @param  string|null  $notes  Optional notes about the verification
     * @return Payment The updated payment
     */
    public function execute(int $paymentId, int $adminId, bool $isApproved, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($paymentId, $adminId, $isApproved, $notes) {
            $payment = Payment::findOrFail($paymentId);

            if ($isApproved) {
                $payment->update([
                    'status' => PaymentStatus::VERIFIED->value,
                    'verified_by' => $adminId,
                    'verified_at' => now(),
                    'notes' => $notes,
                ]);

                // Generate receipt
                $receipt = $this->generateReceiptAction->execute($payment);

                // Notify user
                $payment->user->notify(new PaymentConfirmedNotification($payment, $receipt));

                // Dispatch event
                event(new PaymentVerified($payment));
            } else {
                $payment->update([
                    'status' => PaymentStatus::REJECTED->value,
                    'verified_by' => $adminId,
                    'verified_at' => now(),
                    'notes' => $notes,
                ]);

                // Notify user
                $payment->user->notify(new PaymentRejectedNotification($payment));

                // Dispatch event
                event(new PaymentRejected($payment));
            }

            return $payment;
        });
    }
}

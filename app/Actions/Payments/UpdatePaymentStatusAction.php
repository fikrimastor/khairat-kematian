<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdatePaymentStatusAction
{
    /**
     * Update the status of a payment.
     *
     * @param  Payment  $payment  The payment to update
     * @param  PaymentStatus  $status  The new status
     * @param  string|null  $notes  Optional notes about the status change
     * @return bool Whether the update was successful
     */
    public function execute(Payment $payment, PaymentStatus $status, ?string $notes = null): bool
    {
        try {
            // Check if status transition is valid
            if (!$this->isValidStatusTransition($payment->status, $status)) {
                Log::warning('Invalid payment status transition', [
                    'payment_id' => $payment->id,
                    'current_status' => $payment->status->value,
                    'new_status' => $status->value,
                ]);

                return false;
            }

            // Update payment status
            $updateData = [
                'status' => $status,
            ];

            // If status is verified or rejected, add verification details
            if ($status === PaymentStatus::VERIFIED || $status === PaymentStatus::REJECTED) {
                $updateData['verified_by'] = Auth::id();
                $updateData['verified_at'] = now();
            }

            // Add notes if provided
            if ($notes !== null) {
                $updateData['notes'] = $notes;
            }

            // Perform update
            $payment->update($updateData);

            Log::info('Payment status updated', [
                'payment_id' => $payment->id,
                'old_status' => $payment->getOriginal('status'),
                'new_status' => $status->value,
                'updated_by' => Auth::id(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update payment status', [
                'payment_id' => $payment->id,
                'status' => $status->value,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if a status transition is valid.
     *
     * @param  PaymentStatus  $currentStatus  The current status
     * @param  PaymentStatus  $newStatus  The new status
     * @return bool Whether the transition is valid
     */
    private function isValidStatusTransition(PaymentStatus $currentStatus, PaymentStatus $newStatus): bool
    {
        // Define valid transitions
        $validTransitions = [
            PaymentStatus::PENDING->value => [
                PaymentStatus::PROCESSING->value,
                PaymentStatus::VERIFIED->value,
                PaymentStatus::REJECTED->value,
            ],
            PaymentStatus::PROCESSING->value => [
                PaymentStatus::VERIFIED->value,
                PaymentStatus::REJECTED->value,
                PaymentStatus::FAILED->value,
            ],
            PaymentStatus::VERIFIED->value => [
                // Usually no change from verified, but could allow for special cases
                PaymentStatus::REJECTED->value, // Admin found issues after verification
            ],
            PaymentStatus::REJECTED->value => [
                PaymentStatus::PENDING->value, // Resubmission
                PaymentStatus::VERIFIED->value, // Reconsidered and approved
            ],
            PaymentStatus::FAILED->value => [
                PaymentStatus::PENDING->value, // Retry
            ],
        ];

        // Check if transition is valid
        return in_array(
            $newStatus->value,
            $validTransitions[$currentStatus->value] ?? []
        );
    }
}

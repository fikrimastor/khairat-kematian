<?php

namespace App\Actions\Payments;

use App\Enums\NotificationType;
use App\Enums\PaymentStatus;
use App\Helpers\NotificationHelper;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyPaymentAction
{
    protected UpdatePaymentStatusAction $updateStatusAction;

    /**
     * Constructor with dependency injection
     */
    public function __construct(UpdatePaymentStatusAction $updateStatusAction)
    {
        $this->updateStatusAction = $updateStatusAction;
    }

    /**
     * Verify a payment and generate a receipt if approved.
     *
     * @param  int  $paymentId  The ID of the payment to verify
     * @param  string  $status  The verification status (verified or rejected)
     * @param  string|null  $notes  Optional notes about the verification
     * @return array Result of the verification process
     */
    public function execute(int $paymentId, string $status, ?string $notes = null): array
    {
        // Start a database transaction
        return DB::transaction(function () use ($paymentId, $status, $notes) {
            try {
                // Find the payment
                $payment = Payment::findOrFail($paymentId);

                // Check if payment is already verified
                if ($payment->status === PaymentStatus::VERIFIED) {
                    return [
                        'success' => false,
                        'message' => 'Payment has already been verified',
                    ];
                }

                // Check if payment is pending or processing (can be verified)
                if (!in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::PROCESSING])) {
                    return [
                        'success' => false,
                        'message' => "Cannot verify payment with status: {$payment->status->value}",
                    ];
                }

                // Update payment status based on verification decision
                $newStatus = $status === 'verified'
                    ? PaymentStatus::VERIFIED
                    : PaymentStatus::REJECTED;

                $updated = $this->updateStatusAction->execute($payment, $newStatus, $notes);

                if (!$updated) {
                    throw new \Exception('Failed to update payment status');
                }

                // If verified, generate a receipt
                if ($newStatus === PaymentStatus::VERIFIED) {
                    // Check if receipt already exists
                    if (!$payment->receipt) {
                        // Generate receipt
                        $receipt = Receipt::create([
                            'payment_id' => $payment->id,
                            'receipt_number' => Receipt::generateReceiptNumber(),
                            'generated_at' => now(),
                        ]);

                        Log::info('Receipt generated for verified payment', [
                            'payment_id' => $payment->id,
                            'receipt_id' => $receipt->id,
                            'receipt_number' => $receipt->receipt_number,
                        ]);

                        // Send notification to user
                        NotificationHelper::notify(
                            $payment->user,
                            NotificationType::PAYMENT_VERIFIED,
                            [
                                'payment' => $payment,
                                'receipt' => $receipt
                            ]
                        );

                        return [
                            'success' => true,
                            'message' => 'Payment verified and receipt generated',
                            'receipt_id' => $receipt->id,
                            'receipt_number' => $receipt->receipt_number,
                        ];
                    } else {
                        // Send notification to user
                        NotificationHelper::notify(
                            $payment->user,
                            NotificationType::PAYMENT_VERIFIED,
                            [
                                'payment' => $payment,
                                'receipt' => $payment->receipt
                            ]
                        );

                        return [
                            'success' => true,
                            'message' => 'Payment verified, receipt already exists',
                            'receipt_id' => $payment->receipt->id,
                            'receipt_number' => $payment->receipt->receipt_number,
                        ];
                    }
                } else {
                    // Payment was rejected
                    // Send notification to user
                    NotificationHelper::notify(
                        $payment->user,
                        NotificationType::PAYMENT_REJECTED,
                        [
                            'payment' => $payment
                        ]
                    );

                    return [
                        'success' => true,
                        'message' => 'Payment rejected',
                    ];
                }
            } catch (\Exception $e) {
                // Log error and roll back transaction
                Log::error('Payment verification failed', [
                    'payment_id' => $paymentId,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }
}

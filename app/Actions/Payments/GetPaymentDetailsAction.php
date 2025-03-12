<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class GetPaymentDetailsAction
{
    /**
     * Get detailed information about a payment, including related data.
     *
     * @param  int  $paymentId  The ID of the payment to get details for
     * @return array The payment details or error
     */
    public function execute(int $paymentId): array
    {
        try {
            // Find payment with relationships
            $payment = Payment::with(['user', 'receipt', 'verifier'])
                ->findOrFail($paymentId);

            // Format payment data
            return [
                'success' => true,
                'data' => [
                    'id' => $payment->id,
                    'user' => [
                        'id' => $payment->user->id,
                        'name' => $payment->user->name,
                        'email' => $payment->user->email,
                    ],
                    'amount' => $payment->amount,
                    'payment_method' => [
                        'value' => $payment->payment_method->value,
                        'label' => $payment->payment_method->getLabel(),
                    ],
                    'reference_no' => $payment->reference_no,
                    'status' => [
                        'value' => $payment->status->value,
                        'label' => $payment->status->getLabel(),
                        'color' => $payment->status->color(),
                    ],
                    'payment_date' => $payment->payment_date?->format('d-m-Y H:i'),
                    'period' => [
                        'month' => $payment->month,
                        'year' => $payment->year,
                        'formatted' => "{$payment->month} {$payment->year}",
                    ],
                    'household_count' => $payment->household_count,
                    'notes' => $payment->notes,
                    'created_at' => $payment->created_at->format('d-m-Y H:i'),
                    'updated_at' => $payment->updated_at->format('d-m-Y H:i'),
                    'verification' => $payment->verified_at ? [
                        'verified_by' => [
                            'id' => $payment->verifier->id,
                            'name' => $payment->verifier->name,
                        ],
                        'verified_at' => $payment->verified_at->format('d-m-Y H:i'),
                    ] : null,
                    'receipt' => $payment->receipt ? [
                        'id' => $payment->receipt->id,
                        'receipt_number' => $payment->receipt->receipt_number,
                        'receipt_path' => $payment->receipt->receipt_path,
                        'download_url' => $payment->receipt->downloadUrl,
                        'generated_at' => $payment->receipt->generated_at->format('d-m-Y H:i'),
                    ] : null,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get payment details', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get payment details: '.$e->getMessage(),
            ];
        }
    }
}

<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;

class BankTransferGateway implements PaymentGatewayInterface
{
    /**
     * Process a payment through bank transfer
     * For bank transfers, we just record the payment intent
     *
     * @param  array  $paymentData  Payment data including amount, reference, etc.
     * @return array Response for the bank transfer
     */
    public function processPayment(array $paymentData): array
    {
        // For bank transfers, we just create a record of the payment intent
        // The actual verification happens when an admin confirms the receipt

        try {
            // Generate a unique reference number for this bank transfer
            $transactionId = 'BT-'.date('YmdHis').'-'.substr(uniqid(), -6);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending', // Bank transfers are pending until admin verification
                'bank_details' => $this->getBankDetails(),
                'amount' => $paymentData['amount'],
                'reference' => $paymentData['reference'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Bank transfer processing exception', [
                'message' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Failed to process bank transfer: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment status
     * For bank transfers, verification is done manually by admins
     *
     * @param  string  $referenceId  The reference ID for the payment to verify
     * @return array Response with verification status
     */
    public function verifyPayment(string $referenceId): array
    {
        // Bank transfers require manual verification by an admin
        // This method would typically be called after an admin has reviewed a receipt

        return [
            'success' => true,
            'verified' => false, // Always false because it requires human verification
            'status' => 'pending_verification',
            'message' => 'Bank transfers require manual verification by an administrator',
        ];
    }

    /**
     * Get the bank account details for manual transfers
     *
     * @return array Bank account details
     */
    private function getBankDetails(): array
    {
        return [
            'bank_name' => config('services.bank_transfer.bank_name', 'Bank Islam'),
            'account_number' => config('services.bank_transfer.account_number', '12345678901'),
            'account_holder' => config('services.bank_transfer.account_holder', 'Masjid Al-Makmur'),
            'reference_format' => 'KK-[Your Member ID]',
        ];
    }

    /**
     * Generate instructions for bank transfer
     *
     * @param  array  $paymentData  Payment data
     * @return string|array Bank transfer instructions and details
     */
    public function getPaymentUrl(array $paymentData): string|array
    {
        // For bank transfers, we return instructions and bank details instead of a URL
        $result = $this->processPayment($paymentData);

        if ($result['success']) {
            return [
                'type' => 'bank_transfer',
                'transaction_id' => $result['transaction_id'],
                'bank_details' => $result['bank_details'],
                'amount' => $result['amount'],
                'reference' => $result['reference'],
                'instructions' => 'Please transfer the exact amount to the bank account provided. '.
                    'Include the reference number in your transfer details. '.
                    'After making the payment, upload your receipt for verification.',
            ];
        }

        throw new \RuntimeException('Failed to generate bank transfer instructions: '.
            ($result['error'] ?? 'Unknown error'));
    }
}

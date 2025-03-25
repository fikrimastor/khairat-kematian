<?php

namespace Tests\Mocks;

use App\Enums\PaymentStatus;
use App\Services\Payment\Providers\PaymentGatewayInterface;

class MockBankTransferGateway implements PaymentGatewayInterface
{
    /**
     * Process a payment through Bank Transfer
     *
     * @param  array  $data
     * @return array
     */
    public function processPayment(array $data): array
    {
        // Generate a reference number for the bank transfer
        $referenceNumber = 'BANK-'.($data['reference'] ?? uniqid());

        return [
            'transaction_id' => $referenceNumber,
            'status' => PaymentStatus::PENDING,
            'bank_details' => [
                'account_name' => env('BANK_TRANSFER_ACCOUNT_NAME', 'Khairat Kematian'),
                'account_number' => env('BANK_TRANSFER_ACCOUNT_NUMBER', '1234567890'),
                'bank_name' => env('BANK_TRANSFER_BANK_NAME', 'Bank Islam'),
                'reference' => $referenceNumber,
            ],
        ];
    }

    /**
     * Verify a payment status
     *
     * @param  string  $transactionId
     * @return array
     */
    public function verifyPayment(string $transactionId): array
    {
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'amount' => 50.00,
            'paid_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Validate signature from webhook - not applicable for bank transfers
     *
     * @param  array  $payload
     * @param  string  $signature
     * @return bool
     */
    public function validateSignature(array $payload, string $signature): bool
    {
        // Bank transfers don't use signatures, so always return true
        return true;
    }

    /**
     * Generate payment URL or instructions for bank transfers
     *
     * @param  array  $paymentData
     * @return array
     */
    public function getPaymentUrl(array $paymentData): string|array
    {
        // For bank transfers, we return instructions and bank details instead of a URL
        return [
            'type' => 'bank_transfer',
            'transaction_id' => 'MOCK-BTF-'.time(),
            'bank_details' => [
                'account_name' => 'Khairat Kematian Test',
                'account_number' => '9876543210',
                'bank_name' => 'Bank Islam Test',
                'reference' => $paymentData['reference'] ?? 'TEST-REF',
            ],
            'amount' => $paymentData['amount'] ?? 50.00,
            'reference' => $paymentData['reference'] ?? 'TEST-REF',
            'instructions' => 'This is a mock bank transfer for testing. No actual transfer needed.',
        ];
    }
}

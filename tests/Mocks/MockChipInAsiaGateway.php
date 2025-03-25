<?php

namespace Tests\Mocks;

use App\Enums\PaymentStatus;
use App\Services\Payment\Providers\PaymentGatewayInterface;

class MockChipInAsiaGateway implements PaymentGatewayInterface
{
    /**
     * Process a payment through the ChipInAsia gateway
     *
     * @param  array  $data
     * @return array
     */
    public function processPayment(array $data): array
    {
        // Simulate successful payment processing
        return [
            'transaction_id' => 'MOCK-TRX-'.time(),
            'status' => PaymentStatus::PENDING,
            'payment_url' => 'https://example.com/payment/mock-transaction',
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
     * Validate signature from webhook
     *
     * @param  array  $payload
     * @param  string  $signature
     * @return bool
     */
    public function validateSignature(array $payload, string $signature): bool
    {
        // In mock implementation, we'll validate by checking if signature isn't "invalid-signature"
        return $signature !== 'invalid-signature';
    }

    /**
     * Generate a payment URL for the test
     *
     * @param  array  $paymentData
     * @return string
     */
    public function getPaymentUrl(array $paymentData): string
    {
        return 'https://example.com/payment/mock-checkout?ref='.($paymentData['reference'] ?? 'test');
    }
}

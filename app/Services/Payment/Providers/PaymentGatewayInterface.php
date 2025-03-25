<?php

namespace App\Services\Payment\Providers;

interface PaymentGatewayInterface
{
    /**
     * Process a payment through the gateway.
     *
     * @param  array  $data  The payment data
     * @return array The processing result
     */
    public function processPayment(array $data): array;

    /**
     * Verify a payment status
     *
     * @param  string  $transactionId
     * @return array
     */
    public function verifyPayment(string $transactionId): array;

    /**
     * Validate signature from webhook
     *
     * @param  array  $payload
     * @param  string  $signature
     * @return bool
     */
    public function validateSignature(array $payload, string $signature): bool;

    /**
     * Generate a payment URL or payment instructions
     *
     * @param  array  $paymentData
     * @return string|array
     */
    public function getPaymentUrl(array $paymentData): string|array;
}

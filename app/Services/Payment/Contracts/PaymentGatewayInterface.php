<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Process a payment through the gateway
     *
     * @param  array  $paymentData  Payment data including amount, reference, etc.
     * @return array Response from the payment gateway with status and transaction ID
     */
    public function processPayment(array $paymentData): array;

    /**
     * Verify a payment status with the gateway
     *
     * @param  string  $referenceId  The reference ID for the payment to verify
     * @return array Response with verification status
     */
    public function verifyPayment(string $referenceId): array;

    /**
     * Generate a payment URL or redirect data if needed
     *
     * @param  array  $paymentData  Payment data
     * @return string|array URL to redirect to or data to use for the payment
     */
    public function getPaymentUrl(array $paymentData): string|array;
}

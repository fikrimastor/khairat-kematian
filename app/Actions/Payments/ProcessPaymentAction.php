<?php

namespace App\Actions\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Support\Facades\Log;

class ProcessPaymentAction
{
    protected PaymentGatewayFactory $gatewayFactory;

    /**
     * Constructor with dependency injection
     */
    public function __construct(PaymentGatewayFactory $gatewayFactory)
    {
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * Process a payment through the appropriate gateway.
     *
     * @param  Payment  $payment  The payment to process
     * @param  array  $paymentData  Additional data required for processing
     * @return array Processing result with status and redirection info
     */
    public function execute(Payment $payment, array $paymentData = []): array
    {
        try {
            // Get gateway type from payment method
            $gatewayType = match ($payment->payment_method) {
                PaymentMethod::ChipInAsia => 'chipinasia',
                PaymentMethod::BankTransfer => 'banktransfer',
                default => throw new \InvalidArgumentException("Unsupported payment method: {$payment->payment_method->value}")
            };

            // Get the appropriate gateway
            $gateway = $this->gatewayFactory->make($gatewayType);

            // Prepare payment data for the gateway
            $gatewayData = [
                'amount' => $payment->amount,
                'reference' => $payment->id,
                'description' => "Khairat Kematian Payment - {$payment->month} {$payment->year}",
                'customer_name' => $paymentData['customer_name'] ?? $payment->user->name ?? null,
                'customer_email' => $paymentData['customer_email'] ?? $payment->user->email ?? null,
                'customer_phone' => $paymentData['customer_phone'] ?? null,
                'redirect_url' => $paymentData['redirect_url'] ?? null,
                'webhook_url' => $paymentData['webhook_url'] ?? null,
            ];

            // Process the payment
            $result = $gateway->processPayment($gatewayData);

            // Update payment with gateway response
            $payment->update([
                'reference_no' => $result['transaction_id'] ?? null,
                'status' => $result['status'] === 'pending'
                    ? PaymentStatus::PENDING
                    : ($result['status'] === 'processing' ? PaymentStatus::PROCESSING : $payment->status),
            ]);

            if ($result['success']) {
                Log::info('Payment processed successfully', [
                    'payment_id' => $payment->id,
                    'gateway' => $gatewayType,
                    'status' => $result['status'],
                ]);

                // For online payment, return payment URL
                if ($payment->payment_method === PaymentMethod::ChipInAsia) {
                    return [
                        'success' => true,
                        'payment_url' => $result['payment_url'] ?? null,
                        'status' => $result['status'],
                        'message' => 'Payment processed successfully',
                    ];
                }

                // For bank transfer, return bank details
                return [
                    'success' => true,
                    'bank_details' => $result['bank_details'] ?? null,
                    'status' => $result['status'],
                    'message' => 'Payment recorded, please complete the bank transfer',
                ];
            } else {
                Log::error('Payment processing failed', [
                    'payment_id' => $payment->id,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return [
                    'success' => false,
                    'status' => $result['status'] ?? 'failed',
                    'message' => $result['error'] ?? 'Payment processing failed',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception during payment processing', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment processing failed: '.$e->getMessage(),
            ];
        }
    }
}

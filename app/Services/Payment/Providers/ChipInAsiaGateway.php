<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChipInAsiaGateway implements PaymentGatewayInterface
{
    /**
     * The ChipIn API base URL
     *
     * @var string
     */
    protected string $apiUrl;

    /**
     * The ChipIn API key
     *
     * @var string
     */
    protected string $apiKey;

    /**
     * The ChipIn API secret
     *
     * @var string|null
     */
    protected ?string $apiSecret;

    /**
     * Constructor
     *
     * @param  string|null  $apiKey  The API key for ChipIn
     * @param  string|null  $apiSecret  The API secret for ChipIn
     */
    public function __construct(?string $apiKey = null, ?string $apiSecret = null)
    {
        $this->apiUrl = config('services.chipin.api_url', 'https://api.chip-in.asia/v1');
        $this->apiKey = $apiKey ?? config('services.chipin.api_key', 'test_key');
        $this->apiSecret = $apiSecret ?? config('services.chipin.api_secret');
    }

    /**
     * Process a payment through ChipIn Asia
     *
     * @param  array  $paymentData  Payment data including amount, reference, etc.
     * @return array Response from the payment gateway
     */
    public function processPayment(array $paymentData): array
    {
        try {
            // This is a simulation of a ChipIn API call
            // In a real implementation, you would make an actual API call to ChipIn

            // Simulate API response for development purposes
            // In production, replace with actual API call:
            // $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
            //    ->post($this->apiUrl . '/payments', $paymentData);

            $transactionId = 'CHIP-'.date('YmdHis').'-'.substr(uniqid(), -6);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'redirect_url' => $this->getPaymentUrl([
                    'transaction_id' => $transactionId,
                    'amount' => $paymentData['amount'],
                    'reference' => $paymentData['reference'] ?? null,
                ]),
            ];
        } catch (\Exception $e) {
            Log::error('ChipIn payment processing exception', [
                'message' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Failed to process ChipIn payment: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment status with ChipIn
     *
     * @param  string  $referenceId  The reference ID for the payment to verify
     * @return array Response with verification status
     */
    public function verifyPayment(string $referenceId): array
    {
        try {
            // This is a simulation of a ChipIn API verification call
            // In a real implementation, you would make an actual API call to ChipIn

            // Simulate API response for development purposes
            // In production, replace with actual API call:
            // $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
            //    ->get($this->apiUrl . '/payments/' . $referenceId);

            // For demo purposes, we'll assume the payment is successful if the reference ID starts with 'CHIP-'
            $isValid = str_starts_with($referenceId, 'CHIP-');

            return [
                'success' => true,
                'verified' => $isValid,
                'status' => $isValid ? 'completed' : 'failed',
                'message' => $isValid ? 'Payment verified successfully' : 'Payment verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('ChipIn payment verification exception', [
                'message' => $e->getMessage(),
                'reference_id' => $referenceId,
            ]);

            return [
                'success' => false,
                'verified' => false,
                'status' => 'error',
                'message' => 'Failed to verify ChipIn payment: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Generate a payment URL for ChipIn
     *
     * @param  array  $paymentData  Payment data
     * @return string Payment URL
     */
    public function getPaymentUrl(array $paymentData): string
    {
        // In a real implementation, this would generate a URL to the ChipIn payment page
        // For now, we'll return a simulated URL

        $baseUrl = config('services.chipin.checkout_url', 'https://checkout.chip-in.asia');
        $params = http_build_query([
            'transaction_id' => $paymentData['transaction_id'],
            'amount' => $paymentData['amount'],
            'reference' => $paymentData['reference'] ?? '',
            'return_url' => route('payment-gateway.callback', ['gateway' => 'chipin']),
        ]);

        return $baseUrl.'?'.$params;
    }
}

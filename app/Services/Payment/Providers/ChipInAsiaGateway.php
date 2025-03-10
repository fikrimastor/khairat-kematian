<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChipInAsiaGateway implements PaymentGatewayInterface
{
    protected string $apiKey;

    protected string $apiSecret;

    protected string $baseUrl;

    /**
     * Create a new ChipInAsia gateway instance
     *
     * @param  string  $apiKey  The API key for ChipInAsia
     * @param  string  $apiSecret  The API secret for ChipInAsia
     */
    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = config('services.chipinasia.base_url', 'https://api.chipinasia.com/v1');
    }

    /**
     * Process a payment through ChipInAsia
     *
     * @param  array  $paymentData  Payment data including amount, reference, etc.
     * @return array Response from the payment gateway
     */
    public function processPayment(array $paymentData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/payments', [
                'amount' => $paymentData['amount'],
                'currency' => 'MYR',
                'reference_id' => $paymentData['reference'],
                'description' => $paymentData['description'] ?? 'Khairat Kematian Payment',
                'customer' => [
                    'name' => $paymentData['customer_name'] ?? null,
                    'email' => $paymentData['customer_email'] ?? null,
                    'phone' => $paymentData['customer_phone'] ?? null,
                ],
                'redirect_url' => $paymentData['redirect_url'] ?? null,
                'webhook_url' => $paymentData['webhook_url'] ?? null,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'transaction_id' => $data['id'] ?? null,
                    'status' => $data['status'] ?? 'processing',
                    'payment_url' => $data['payment_url'] ?? null,
                    'raw_response' => $data,
                ];
            } else {
                Log::error('ChipInAsia payment failed', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                    'payment_data' => $paymentData,
                ]);

                return [
                    'success' => false,
                    'status' => 'failed',
                    'error' => $response->json()['message'] ?? 'Payment processing failed',
                    'raw_response' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('ChipInAsia exception', [
                'message' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Payment service unavailable: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment status with ChipInAsia
     *
     * @param  string  $referenceId  The reference ID for the payment to verify
     * @return array Response with verification status
     */
    public function verifyPayment(string $referenceId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl.'/payments/'.$referenceId);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'verified' => $data['status'] === 'paid',
                    'status' => $data['status'],
                    'transaction_id' => $data['id'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'paid_at' => $data['paid_at'] ?? null,
                    'raw_response' => $data,
                ];
            } else {
                Log::error('ChipInAsia verification failed', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                    'reference_id' => $referenceId,
                ]);

                return [
                    'success' => false,
                    'verified' => false,
                    'status' => 'failed',
                    'error' => $response->json()['message'] ?? 'Payment verification failed',
                    'raw_response' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('ChipInAsia verification exception', [
                'message' => $e->getMessage(),
                'reference_id' => $referenceId,
            ]);

            return [
                'success' => false,
                'verified' => false,
                'status' => 'failed',
                'error' => 'Verification service unavailable: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Generate a payment URL for ChipInAsia
     *
     * @param  array  $paymentData  Payment data
     * @return string|array URL to redirect to
     */
    public function getPaymentUrl(array $paymentData): string|array
    {
        $result = $this->processPayment($paymentData);

        if ($result['success'] && isset($result['payment_url'])) {
            return $result['payment_url'];
        }

        throw new \RuntimeException('Failed to generate payment URL: '.
            ($result['error'] ?? 'Unknown error'));
    }
}

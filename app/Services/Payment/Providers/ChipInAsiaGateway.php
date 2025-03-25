<?php

namespace App\Services\Payment\Providers;

use App\Enums\PaymentStatus;
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
        $this->apiUrl = config('services.chipin.api_url', 'https://gate.chip-in.asia/api/v1');
        $this->apiKey = $apiKey ?? config('services.chipin.api_key', 'test_key');
        $this->apiSecret = $apiSecret ?? config('services.chipin.api_secret');
    }

    /**
     * Process a payment through ChipInAsia.
     *
     * @param  array  $data  The payment data
     * @return array The processing result
     */
    public function processPayment(array $data): array
    {
        // Generate a transaction ID
        $transactionId = 'CHA'.date('YmdHis').rand(1000, 9999);

        // For ChipInAsia, we would normally integrate with their API
        // For testing, we'll simulate a payment URL
        $paymentUrl = 'https://chipinasia.com/pay/'.$transactionId;

        return [
            'transaction_id' => $transactionId,
            'status' => PaymentStatus::PENDING,
            'payment_url' => $paymentUrl,
            'success' => true,
        ];
    }

    /**
     * Create a purchase using Chip In Asia API
     *
     * @param  array  $paymentData  Payment data
     * @return array Purchase data including transaction ID and redirect URL
     */
    protected function createPurchase(array $paymentData): array
    {
        // Prepare the request data for ChipIn API according to documentation
        $requestData = [
            'title' => 'Khairat Kematian '.($paymentData['payment_type'] ?? 'Payment'),
            'description' => 'Payment for Khairat Kematian services',
            'reference' => $paymentData['reference'] ?? null,
            'amount' => (float) $paymentData['amount'],
            'currency' => 'MYR',
            'redirect_url' => route('payment-gateway.callback', ['gateway' => 'chipin']),
            'callback_url' => route('payment-gateway.webhook'),
            'send_receipt' => true,
            'due' => now()->addDays(7)->toIso8601String(),
            'brand_id' => config('services.chipin.brand_id'),
            'client' => [
                'email' => $paymentData['user_email'] ?? null,
                'phone' => $paymentData['user_phone'] ?? null,
                'name' => $paymentData['user_name'] ?? null,
            ],
            'platform' => 'khairat-kematian',
        ];

        // In production, make the actual API call
        if (app()->environment('production')) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl.'/purchases', $requestData);

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'success' => true,
                    'transaction_id' => $responseData['id'],
                    'redirect_url' => $responseData['checkout_url'],
                ];
            } else {
                Log::error('ChipInAsia API error', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return [
                    'success' => false,
                    'status' => PaymentStatus::FAILED,
                    'error' => 'Failed to process payment: '.($response->json()['message'] ?? 'Unknown error'),
                ];
            }
        } else {
            // For development/testing, simulate a successful response
            $transactionId = 'CHIP-'.date('YmdHis').'-'.substr(uniqid(), -6);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'redirect_url' => $this->getPaymentUrl([
                    'transaction_id' => $transactionId,
                    'amount' => $paymentData['amount'],
                    'reference' => $paymentData['reference'] ?? null,
                ]),
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
            Log::info('Verifying payment with ChipInAsia', [
                'reference_id' => $referenceId,
            ]);

            // In production, make the actual API call
            if (app()->environment('production')) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ])->get($this->apiUrl.'/purchases/'.$referenceId);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $isCompleted = $responseData['status'] === 'paid' || $responseData['status'] === 'completed';

                    return [
                        'success' => true,
                        'verified' => $isCompleted,
                        'status' => $isCompleted ? PaymentStatus::COMPLETED : PaymentStatus::PENDING,
                        'message' => $isCompleted ? 'Payment verified successfully' : 'Payment is still pending',
                        'payment_details' => $responseData,
                    ];
                } else {
                    Log::error('ChipInAsia verification API error', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);

                    return [
                        'success' => false,
                        'verified' => false,
                        'status' => PaymentStatus::FAILED,
                        'message' => 'Failed to verify payment: '.($response->json()['message'] ?? 'Unknown error'),
                    ];
                }
            } else {
                // For development/testing, simulate verification
                $isValid = str_starts_with($referenceId, 'CHIP-');

                return [
                    'success' => true,
                    'verified' => $isValid,
                    'status' => $isValid ? PaymentStatus::COMPLETED : PaymentStatus::FAILED,
                    'message' => $isValid ? 'Payment verified successfully' : 'Payment verification failed',
                ];
            }
        } catch (\Exception $e) {
            Log::error('ChipIn payment verification exception', [
                'message' => $e->getMessage(),
                'reference_id' => $referenceId,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'verified' => false,
                'status' => PaymentStatus::FAILED,
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
        // In production, this would be the URL returned by the ChipIn API
        // For development/testing, we'll simulate a URL

        $baseUrl = config('services.chipin.checkout_url', 'https://checkout.chip-in.asia');
        $params = http_build_query([
            'transaction_id' => $paymentData['transaction_id'],
            'amount' => $paymentData['amount'],
            'reference' => $paymentData['reference'] ?? '',
            'return_url' => route('payment-gateway.callback', ['gateway' => 'chipin']),
        ]);

        return $baseUrl.'?'.$params;
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
        // In production, validate the signature using the ChipIn API's signature format
        if (app()->environment('production') && $this->apiSecret) {
            // Check if we have the required data
            if (empty($payload)) {
                return false;
            }

            // Generate the expected signature using the API secret
            $calculatedSignature = hash_hmac('sha256', json_encode($payload), $this->apiSecret);

            // Return true if the signatures match
            return hash_equals($calculatedSignature, $signature);
        }

        // For development/testing environment, always return true to facilitate testing
        return app()->environment(['local', 'testing']);
    }
}

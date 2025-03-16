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
            Log::info('Processing payment with ChipInAsia', [
                'amount' => $paymentData['amount'],
                'reference' => $paymentData['reference'] ?? null,
            ]);

            // Prepare the request data for ChipIn API
            $requestData = [
                'amount' => $paymentData['amount'],
                'currency' => 'MYR',
                'reference' => $paymentData['reference'] ?? null,
                'customer' => [
                    'name' => $paymentData['user_name'] ?? null,
                    'email' => $paymentData['user_email'] ?? null,
                ],
                'success_redirect' => route('payment-gateway.callback', ['gateway' => 'chipin']),
                'failure_redirect' => route('payment-gateway.callback', ['gateway' => 'chipin', 'status' => 'failed']),
                'callback_url' => route('payment-gateway.webhook'),
                'send_receipt' => true,
                'due' => now()->addHours(24)->toIso8601String(), // Payment due in 24 hours
                'description' => 'Khairat Kematian Payment',
            ];

            // In production, make the actual API call
            if (app()->environment('production')) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                    ->post($this->apiUrl.'/payments', $requestData);

                if ($response->successful()) {
                    $responseData = $response->json();

                    return [
                        'success' => true,
                        'transaction_id' => $responseData['id'],
                        'status' => $responseData['status'],
                        'redirect_url' => $responseData['checkout_url'],
                    ];
                } else {
                    Log::error('ChipInAsia API error', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);

                    return [
                        'success' => false,
                        'status' => 'failed',
                        'error' => 'Failed to process payment: '.($response->json()['message'] ?? 'Unknown error'),
                    ];
                }
            } else {
                // For development/testing, simulate a successful response
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
            }
        } catch (\Exception $e) {
            Log::error('ChipIn payment processing exception', [
                'message' => $e->getMessage(),
                'payment_data' => $paymentData,
                'trace' => $e->getTraceAsString(),
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
            Log::info('Verifying payment with ChipInAsia', [
                'reference_id' => $referenceId,
            ]);

            // In production, make the actual API call
            if (app()->environment('production')) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                    ->get($this->apiUrl.'/payments/'.$referenceId);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $isCompleted = $responseData['status'] === 'paid' || $responseData['status'] === 'completed';

                    return [
                        'success' => true,
                        'verified' => $isCompleted,
                        'status' => $responseData['status'],
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
                        'status' => 'error',
                        'message' => 'Failed to verify payment: '.($response->json()['message'] ?? 'Unknown error'),
                    ];
                }
            } else {
                // For development/testing, simulate verification
                $isValid = str_starts_with($referenceId, 'CHIP-');

                return [
                    'success' => true,
                    'verified' => $isValid,
                    'status' => $isValid ? 'completed' : 'failed',
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
}

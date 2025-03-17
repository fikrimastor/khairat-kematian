<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzGateway implements PaymentGatewayInterface
{
    /**
     * The Billplz API base URL
     *
     * @var string
     */
    protected string $apiUrl;

    /**
     * The Billplz API key
     *
     * @var string
     */
    protected string $apiKey;

    /**
     * The Billplz X-Signature Key
     *
     * @var string|null
     */
    protected ?string $xSignatureKey;

    /**
     * The Billplz Collection ID
     *
     * @var string
     */
    protected string $collectionId;

    /**
     * Constructor
     *
     * @param  string|null  $apiKey  The API key for Billplz
     * @param  string|null  $xSignatureKey  The X-Signature key for Billplz
     * @param  string|null  $collectionId  The Collection ID for Billplz
     */
    public function __construct(?string $apiKey = null, ?string $xSignatureKey = null, ?string $collectionId = null)
    {
        $this->apiUrl = config('services.billplz.api_url', 'https://www.billplz.com/api/v3');
        $this->apiKey = $apiKey ?? config('services.billplz.api_key', 'test_key');
        $this->xSignatureKey = $xSignatureKey ?? config('services.billplz.x_signature_key');
        $this->collectionId = $collectionId ?? config('services.billplz.collection_id', 'test_collection');
    }

    /**
     * Process a payment through Billplz
     *
     * @param  array  $paymentData  Payment data including amount, reference, etc.
     * @return array Response from the payment gateway
     */
    public function processPayment(array $paymentData): array
    {
        try {
            Log::info('Processing payment with Billplz', [
                'amount' => $paymentData['amount'],
                'reference' => $paymentData['reference'] ?? null,
            ]);

            // Prepare the request data for Billplz API
            // Billplz requires amount in cents
            $amountInCents = (int) ($paymentData['amount'] * 100);

            $requestData = [
                'collection_id' => $this->collectionId,
                'email' => $paymentData['user_email'] ?? 'user@example.com',
                'name' => $paymentData['user_name'] ?? 'User',
                'amount' => $amountInCents,
                'callback_url' => route('payment-gateway.webhook', ['gateway' => 'billplz']),
                'description' => 'Khairat Kematian Payment',
                'reference_1_label' => 'Reference ID',
                'reference_1' => $paymentData['reference'] ?? null,
                'redirect_url' => route('payment-gateway.callback', ['gateway' => 'billplz']),
            ];

            // In production, make the actual API call
            if (app()->environment('production')) {
                $response = Http::withBasicAuth($this->apiKey, '')
                    ->post($this->apiUrl.'/bills', $requestData);

                if ($response->successful()) {
                    $responseData = $response->json();

                    return [
                        'success' => true,
                        'transaction_id' => $responseData['id'],
                        'status' => 'pending',
                        'redirect_url' => $responseData['url'],
                    ];
                } else {
                    Log::error('Billplz API error', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);

                    return [
                        'success' => false,
                        'status' => 'failed',
                        'error' => 'Failed to process payment: '.($response->json()['error']['message'] ?? 'Unknown error'),
                    ];
                }
            } else {
                // For development/testing, simulate a successful response
                $transactionId = 'BILLPLZ-'.date('YmdHis').'-'.substr(uniqid(), -6);

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
            Log::error('Billplz payment processing exception', [
                'message' => $e->getMessage(),
                'payment_data' => $paymentData,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Failed to process Billplz payment: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment status with Billplz
     *
     * @param  string  $referenceId  The reference ID for the payment to verify
     * @return array Response with verification status
     */
    public function verifyPayment(string $referenceId): array
    {
        try {
            Log::info('Verifying payment with Billplz', [
                'reference_id' => $referenceId,
            ]);

            // In production, make the actual API call
            if (app()->environment('production')) {
                $response = Http::withBasicAuth($this->apiKey, '')
                    ->get($this->apiUrl.'/bills/'.$referenceId);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $isPaid = $responseData['paid'] === true;

                    return [
                        'success' => true,
                        'verified' => $isPaid,
                        'status' => $isPaid ? 'completed' : 'pending',
                        'message' => $isPaid ? 'Payment verified successfully' : 'Payment is still pending',
                        'payment_details' => $responseData,
                    ];
                } else {
                    Log::error('Billplz verification API error', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);

                    return [
                        'success' => false,
                        'verified' => false,
                        'status' => 'error',
                        'message' => 'Failed to verify payment: '.($response->json()['error']['message'] ?? 'Unknown error'),
                    ];
                }
            } else {
                // For development/testing, simulate verification
                $isValid = str_starts_with($referenceId, 'BILLPLZ-');

                return [
                    'success' => true,
                    'verified' => $isValid,
                    'status' => $isValid ? 'completed' : 'failed',
                    'message' => $isValid ? 'Payment verified successfully' : 'Payment verification failed',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz payment verification exception', [
                'message' => $e->getMessage(),
                'reference_id' => $referenceId,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'verified' => false,
                'status' => 'error',
                'message' => 'Failed to verify Billplz payment: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Generate a payment URL for Billplz
     *
     * @param  array  $paymentData  Payment data
     * @return string Payment URL
     */
    public function getPaymentUrl(array $paymentData): string
    {
        // In production, this would be the URL returned by the Billplz API
        // For development/testing, we'll simulate a URL

        $baseUrl = config('services.billplz.checkout_url', 'https://www.billplz.com/bills');
        $params = http_build_query([
            'id' => $paymentData['transaction_id'],
            'amount' => $paymentData['amount'],
            'reference' => $paymentData['reference'] ?? '',
            'return_url' => route('payment-gateway.callback', ['gateway' => 'billplz']),
        ]);

        return $baseUrl.'/'.$paymentData['transaction_id'].'?'.$params;
    }
}

<?php

namespace App\Http\Controllers\Payment;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Receipt;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    /**
     * Handle the callback from payment gateway
     */
    public function handleCallback(Request $request)
    {
        $gateway = $request->query('gateway');
        $transactionId = $request->query('transaction_id');
        $status = $request->query('status');

        Log::info('Payment gateway callback received', [
            'gateway' => $gateway,
            'transaction_id' => $transactionId,
            'status' => $status,
            'data' => $request->all(),
        ]);

        // If status is explicitly failed, handle the failure
        if ($status === 'failed') {
            return $this->handleFailedPayment($transactionId);
        }

        // Find the payment by transaction ID
        $payment = Payment::where('reference_id', $transactionId)->first();

        if (!$payment) {
            Log::error('Payment not found for transaction', [
                'transaction_id' => $transactionId,
            ]);

            return redirect()->route('payments.index')
                ->with('error', 'Pembayaran tidak dijumpai. Sila hubungi pentadbir sistem.');
        }

        // Verify the payment status with the gateway
        try {
            $gatewayFactory = new PaymentGatewayFactory;
            $gatewayService = $gatewayFactory->make($gateway);
            $verificationResult = $gatewayService->verifyPayment($transactionId);

            if ($verificationResult['success'] && $verificationResult['verified']) {
                // Update payment status to completed
                $payment->update([
                    'status' => PaymentStatus::COMPLETED,
                    'verified_at' => now(),
                    'verified_by' => null, // System verification
                ]);

                // Generate receipt if it doesn't exist
                if (!$payment->receipt) {
                    $receipt = Receipt::create([
                        'payment_id' => $payment->id,
                        'receipt_number' => Receipt::generateReceiptNumber(),
                        'generated_at' => now(),
                    ]);

                    // Generate PDF receipt (this would be implemented in the Receipt model)
                    if (method_exists($receipt, 'generatePdf')) {
                        $receipt->generatePdf();
                    }
                }

                return redirect()->route('payments.show', $payment)
                    ->with('success', 'Pembayaran berjaya! Resit telah dijana.');
            } else {
                // Payment is still processing or failed
                $newStatus = $verificationResult['status'] === 'failed' 
                    ? PaymentStatus::FAILED 
                    : PaymentStatus::PROCESSING;
                
                $payment->update([
                    'status' => $newStatus,
                ]);

                $message = $newStatus === PaymentStatus::FAILED
                    ? 'Pembayaran gagal. Sila cuba lagi.'
                    : 'Pembayaran sedang diproses. Sila semak semula kemudian.';

                Log::warning('Payment verification result', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                    'result' => $verificationResult,
                    'new_status' => $newStatus,
                ]);

                return redirect()->route('payments.show', $payment)
                    ->with('warning', $message);
            }
        } catch (\Exception $e) {
            Log::error('Payment gateway verification error', [
                'payment_id' => $payment->id,
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('payments.show', $payment)
                ->with('error', 'Ralat berlaku semasa mengesahkan pembayaran anda. Sila hubungi pentadbir sistem.');
        }
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment($transactionId)
    {
        $payment = Payment::where('reference_id', $transactionId)->first();

        if ($payment) {
            $payment->update([
                'status' => PaymentStatus::FAILED,
            ]);

            return redirect()->route('payments.show', $payment)
                ->with('error', 'Pembayaran gagal. Sila cuba lagi.');
        }

        return redirect()->route('payments.index')
            ->with('error', 'Pembayaran gagal. Sila cuba lagi.');
    }

    /**
     * Handle webhook notifications from payment gateway
     */
    public function handleWebhook(Request $request)
    {
        $gateway = $request->route('gateway') ?? $request->input('gateway');

        Log::info('Payment gateway webhook received', [
            'gateway' => $gateway,
            'data' => $request->all(),
        ]);

        // Validate webhook signature if available
        if (!$this->validateWebhookSignature($request, $gateway)) {
            Log::warning('Invalid webhook signature', [
                'gateway' => $gateway,
                'headers' => $request->headers->all(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        // Process the webhook based on the gateway
        try {
            $transactionId = $request->input('transaction_id') ?? $request->input('id');

            // Find the payment by transaction ID
            $payment = Payment::where('reference_id', $transactionId)->first();

            if (!$payment) {
                Log::error('Payment not found for webhook', [
                    'transaction_id' => $transactionId,
                ]);

                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // Update payment status based on webhook data
            $status = $request->input('status');
            $newStatus = null;

            switch ($status) {
                case 'completed':
                case 'paid':
                    $newStatus = PaymentStatus::COMPLETED;
                    break;
                case 'failed':
                case 'cancelled':
                    $newStatus = PaymentStatus::FAILED;
                    break;
                case 'refunded':
                    $newStatus = PaymentStatus::REFUNDED;
                    break;
                case 'processing':
                    $newStatus = PaymentStatus::PROCESSING;
                    break;
                default:
                    // No status change
                    break;
            }

            if ($newStatus) {
                $payment->update(['status' => $newStatus]);
                
                // Generate receipt for completed payments
                if ($newStatus === PaymentStatus::COMPLETED && !$payment->receipt) {
                    $receipt = Receipt::create([
                        'payment_id' => $payment->id,
                        'receipt_number' => Receipt::generateReceiptNumber(),
                        'generated_at' => now(),
                    ]);

                    // Generate PDF receipt (this would be implemented in the Receipt model)
                    if (method_exists($receipt, 'generatePdf')) {
                        $receipt->generatePdf();
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Payment webhook processing error', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Validate webhook signature
     */
    private function validateWebhookSignature(Request $request, $gateway)
    {
        // For ChipInAsia, validate the signature
        if ($gateway === 'chipin' || $gateway === 'chipinasia') {
            $signature = $request->header('X-Signature');
            
            // If no signature in production, reject
            if (app()->environment('production') && !$signature) {
                return false;
            }
            
            // In development, skip validation
            if (!app()->environment('production')) {
                return true;
            }
            
            // Get the API secret
            $apiSecret = config('services.chipinasia.secret') ?? config('services.chipin.api_secret');
            
            // Validate signature (implementation depends on ChipInAsia's signature method)
            $payload = $request->getContent();
            $calculatedSignature = hash_hmac('sha256', $payload, $apiSecret);
            
            return hash_equals($calculatedSignature, $signature);
        }
        
        // For Billplz, validate the X-Signature
        if ($gateway === 'billplz') {
            $xSignature = $request->header('X-Signature');
            
            // If no signature in production, reject
            if (app()->environment('production') && !$xSignature) {
                return false;
            }
            
            // In development, skip validation
            if (!app()->environment('production')) {
                return true;
            }
            
            // Get the X-Signature key
            $xSignatureKey = config('services.billplz.x_signature_key');
            
            // Billplz uses a different signature method
            // They concatenate all request parameters and sign them
            $data = $request->all();
            ksort($data);
            
            $signatureString = '';
            foreach ($data as $key => $value) {
                $signatureString .= $key . $value;
            }
            
            $calculatedSignature = hash_hmac('sha256', $signatureString, $xSignatureKey);
            
            return hash_equals($calculatedSignature, $xSignature);
        }
        
        // For other gateways or development environment, return true
        return true;
    }
}

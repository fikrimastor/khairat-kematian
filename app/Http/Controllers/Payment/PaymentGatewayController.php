<?php

namespace App\Http\Controllers\Payment;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
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

        Log::info('Payment gateway callback received', [
            'gateway' => $gateway,
            'transaction_id' => $transactionId,
            'data' => $request->all(),
        ]);

        // Find the payment by transaction ID
        $payment = Payment::where('reference_id', $transactionId)->first();

        if (!$payment) {
            Log::error('Payment not found for transaction', [
                'transaction_id' => $transactionId,
            ]);

            return redirect()->route('payments.index')
                ->with('error', 'Payment not found. Please contact support.');
        }

        // Verify the payment status with the gateway
        try {
            $gatewayFactory = new PaymentGatewayFactory;
            $gatewayService = $gatewayFactory->make($gateway);
            $verificationResult = $gatewayService->verifyPayment($transactionId);

            if ($verificationResult['success'] && $verificationResult['verified']) {
                $payment->update([
                    'status' => PaymentStatus::COMPLETED,
                ]);

                return redirect()->route('payments.show', $payment)
                    ->with('success', 'Payment completed successfully!');
            } else {
                Log::warning('Payment verification failed', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                    'result' => $verificationResult,
                ]);

                return redirect()->route('payments.show', $payment)
                    ->with('warning', 'Payment is being processed. Please check back later.');
            }
        } catch (\Exception $e) {
            Log::error('Payment gateway verification error', [
                'payment_id' => $payment->id,
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('payments.show', $payment)
                ->with('error', 'An error occurred while verifying your payment. Please contact support.');
        }
    }

    /**
     * Handle webhook notifications from payment gateway
     */
    public function handleWebhook(Request $request)
    {
        $gateway = $request->route('gateway');

        Log::info('Payment gateway webhook received', [
            'gateway' => $gateway,
            'data' => $request->all(),
        ]);

        // Process the webhook based on the gateway
        try {
            $transactionId = $request->input('transaction_id');

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

            switch ($status) {
                case 'completed':
                    $payment->update(['status' => PaymentStatus::COMPLETED]);
                    break;
                case 'failed':
                    $payment->update(['status' => PaymentStatus::FAILED]);
                    break;
                case 'refunded':
                    $payment->update(['status' => PaymentStatus::REFUNDED]);
                    break;
                default:
                    // No status change
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Payment webhook processing error', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
}

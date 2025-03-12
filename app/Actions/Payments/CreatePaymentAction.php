<?php

namespace App\Actions\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreatePaymentAction
{
    /**
     * Create a new payment record.
     *
     * @param  array  $data  Payment data
     * @return Payment|null The created payment or null on failure
     */
    public function execute(array $data): ?Payment
    {
        try {
            // Validate payment method
            $paymentMethod = $data['payment_method'] instanceof PaymentMethod
                ? $data['payment_method']
                : PaymentMethod::from($data['payment_method']);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $data['user_id'] ?? Auth::id(),
                'amount' => $data['amount'],
                'payment_method' => $paymentMethod,
                'status' => PaymentStatus::PENDING,
                'month' => $data['month'],
                'year' => $data['year'],
                'household_count' => $data['household_count'],
                'notes' => $data['notes'] ?? null,
            ]);

            Log::info('Payment created successfully', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method->value,
            ]);

            return $payment;
        } catch (\Exception $e) {
            Log::error('Failed to create payment', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return null;
        }
    }
}

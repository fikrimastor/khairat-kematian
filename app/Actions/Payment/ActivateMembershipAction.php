<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ActivateMembershipAction
{
    /**
     * Activate user membership based on a verified payment
     *
     * @param  Payment  $payment  The verified payment
     * @return bool Whether activation was successful
     */
    public function execute(Payment $payment): bool
    {
        // Only process verified payments
        if ($payment->status !== PaymentStatus::VERIFIED->value) {
            return false;
        }

        // Get the user
        $user = User::find($payment->user_id);
        if (!$user) {
            return false;
        }

        // Activate membership based on payment type
        $paymentType = $payment->payment_type;
        $user->activateMembership($paymentType);

        // Log the activation
        Log::info("Membership activated for user {$user->id} based on payment {$payment->id}");

        return true;
    }
}

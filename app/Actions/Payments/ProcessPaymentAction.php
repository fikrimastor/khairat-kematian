<?php

namespace App\Actions\Payments;

use App\DTOs\Payment\PaymentData;
use App\Enums\PaymentStatus;
use App\Events\Payment\PaymentCreated;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessPaymentAction
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        private readonly PaymentGatewayFactory $gatewayFactory
    ) {}

    /**
     * Execute the action.
     *
     * @param  PaymentData  $data  The payment data.
     * @return Payment The created payment.
     */
    public function execute(PaymentData $data): Payment
    {
        return DB::transaction(function () use ($data) {
            // Get the appropriate payment gateway
            $gateway = $this->gatewayFactory->make($data->paymentMethod);

            // Process payment with the gateway
            $result = $gateway->processPayment([
                'amount' => $data->amount,
                'type' => $data->paymentType,
                'user_id' => $data->userId,
                'year' => $data->year,
                'household_count' => $data->householdCount,
            ]);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $data->userId,
                'amount' => $data->amount,
                'payment_method' => $data->paymentMethod,
                'payment_type' => $data->paymentType,
                'reference_no' => $result['transaction_id'] ?? Str::random(10),
                'year' => $data->year,
                'month' => $data->month ?? date('F'),
                'household_count' => $data->householdCount,
                'status' => $result['status']->value ?? PaymentStatus::PENDING->value,
                'notes' => $data->notes,
                'payment_url' => $result['payment_url'] ?? null,
            ]);

            // Upload proof if provided
            if ($data->proofFile) {
                $fileName = $data->proofFile->getClientOriginalName();
                $path = $data->proofFile->storeAs(
                    'payment_proofs',
                    "{$payment->id}_{$fileName}",
                    'public'
                );

                PaymentProof::create([
                    'payment_id' => $payment->id,
                    'file_path' => $path,
                    'file_name' => $fileName,
                    'file_size' => $data->proofFile->getSize(),
                    'file_type' => $data->proofFile->getMimeType(),
                ]);
            }

            // Dispatch event
            event(new PaymentCreated($payment));

            return $payment;
        });
    }
}

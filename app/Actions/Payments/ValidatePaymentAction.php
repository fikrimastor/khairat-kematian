<?php

namespace App\Actions\Payments;

use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ValidatePaymentAction
{
    /**
     * Validate payment data before creating or processing a payment.
     *
     * @param  array  $data  The payment data to validate
     * @return array The validated data
     *
     * @throws ValidationException If validation fails
     */
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'month' => ['required', 'string', 'max:20'],
            'year' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'household_count' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],

            // Receipt validation for bank transfers
            'receipt_image' => [
                Rule::requiredIf(function () use ($data) {
                    return ($data['payment_method'] instanceof PaymentMethod
                            ? $data['payment_method']
                            : PaymentMethod::from($data['payment_method'])
                    ) === PaymentMethod::BankTransfer;
                }),
                'sometimes',
                'file',
                'image',
                'max:5120', // 5MB
            ],

            // Customer info for online payments
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'customer_email' => ['sometimes', 'email', 'max:255'],
            'customer_phone' => ['sometimes', 'string', 'max:20'],
        ]);

        // Run validation
        return $validator->validate();
    }

    /**
     * Validate payment verification data.
     *
     * @param  array  $data  The verification data to validate
     * @return array The validated data
     *
     * @throws ValidationException If validation fails
     */
    public function validateVerification(array $data): array
    {
        $validator = Validator::make($data, [
            'payment_id' => ['required', 'integer', 'exists:payments,id'],
            'status' => ['required', 'string', Rule::in(['verified', 'rejected'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Run validation
        return $validator->validate();
    }
}

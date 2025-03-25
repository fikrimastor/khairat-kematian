<?php

namespace App\DTOs\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use Illuminate\Http\UploadedFile;

class PaymentData
{
    /**
     * Create a new DTO instance.
     */
    public function __construct(
        public readonly int $userId,
        public readonly float $amount,
        public readonly PaymentMethod $paymentMethod,
        public readonly PaymentType $paymentType,
        public readonly int $year,
        public readonly int $householdCount,
        public readonly ?string $notes = null,
        public readonly ?string $month = null,
        public readonly ?UploadedFile $proofFile = null
    ) {}
}

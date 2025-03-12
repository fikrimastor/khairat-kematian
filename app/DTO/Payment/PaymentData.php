<?php

namespace App\DTO\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Support\Carbon;

class PaymentData
{
    public function __construct(
        public readonly int $userId,
        public readonly float $amount,
        public readonly PaymentMethod $paymentMethod,
        public readonly string $month,
        public readonly int $year,
        public readonly int $householdCount,
        public readonly ?string $referenceNo = null,
        public readonly ?string $notes = null,
        public readonly PaymentStatus $status = PaymentStatus::PENDING,
        public readonly ?Carbon $paymentDate = null,
        public readonly ?int $verifiedBy = null,
        public readonly ?Carbon $verifiedAt = null,
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param  array  $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            amount: $data['amount'],
            paymentMethod: is_string($data['payment_method'])
                ? PaymentMethod::from($data['payment_method'])
                : $data['payment_method'],
            month: $data['month'],
            year: $data['year'],
            householdCount: $data['household_count'],
            referenceNo: $data['reference_no'] ?? null,
            notes: $data['notes'] ?? null,
            status: isset($data['status'])
                ? (is_string($data['status']) ? PaymentStatus::from($data['status']) : $data['status'])
                : PaymentStatus::PENDING,
            paymentDate: isset($data['payment_date'])
                ? (is_string($data['payment_date']) ? Carbon::parse($data['payment_date']) : $data['payment_date'])
                : null,
            verifiedBy: $data['verified_by'] ?? null,
            verifiedAt: isset($data['verified_at'])
                ? (is_string($data['verified_at']) ? Carbon::parse($data['verified_at']) : $data['verified_at'])
                : null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod->value,
            'month' => $this->month,
            'year' => $this->year,
            'household_count' => $this->householdCount,
            'reference_no' => $this->referenceNo,
            'notes' => $this->notes,
            'status' => $this->status->value,
            'payment_date' => $this->paymentDate?->toDateTimeString(),
            'verified_by' => $this->verifiedBy,
            'verified_at' => $this->verifiedAt?->toDateTimeString(),
        ];
    }
}

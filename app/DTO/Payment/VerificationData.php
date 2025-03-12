<?php

namespace App\DTO\Payment;

use App\Enums\PaymentStatus;
use Illuminate\Support\Carbon;

class VerificationData
{
    public function __construct(
        public readonly int $paymentId,
        public readonly int $verifiedBy,
        public readonly PaymentStatus $status,
        public readonly ?string $notes = null,
        public readonly ?Carbon $verifiedAt = null,
        public readonly bool $generateReceipt = true,
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
            paymentId: $data['payment_id'],
            verifiedBy: $data['verified_by'],
            status: is_string($data['status'])
                ? PaymentStatus::from($data['status'])
                : $data['status'],
            notes: $data['notes'] ?? null,
            verifiedAt: isset($data['verified_at'])
                ? (is_string($data['verified_at']) ? Carbon::parse($data['verified_at']) : $data['verified_at'])
                : now(),
            generateReceipt: $data['generate_receipt'] ?? true,
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
            'payment_id' => $this->paymentId,
            'verified_by' => $this->verifiedBy,
            'status' => $this->status->value,
            'notes' => $this->notes,
            'verified_at' => $this->verifiedAt?->toDateTimeString(),
            'generate_receipt' => $this->generateReceipt,
        ];
    }

    /**
     * Check if the verification is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === PaymentStatus::VERIFIED;
    }

    /**
     * Check if the verification is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === PaymentStatus::REJECTED;
    }
}

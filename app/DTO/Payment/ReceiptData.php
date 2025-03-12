<?php

namespace App\DTO\Payment;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class ReceiptData
{
    public function __construct(
        public readonly int $paymentId,
        public readonly string $receiptNumber,
        public readonly ?string $receiptPath = null,
        public readonly ?Carbon $generatedAt = null,
        public readonly ?UploadedFile $receiptFile = null,
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
            receiptNumber: $data['receipt_number'],
            receiptPath: $data['receipt_path'] ?? null,
            generatedAt: isset($data['generated_at'])
                ? (is_string($data['generated_at']) ? Carbon::parse($data['generated_at']) : $data['generated_at'])
                : now(),
            receiptFile: $data['receipt_file'] ?? null,
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
            'receipt_number' => $this->receiptNumber,
            'receipt_path' => $this->receiptPath,
            'generated_at' => $this->generatedAt?->toDateTimeString(),
        ];
    }

    /**
     * Check if the DTO contains an uploaded file.
     *
     * @return bool
     */
    public function hasFile(): bool
    {
        return $this->receiptFile !== null;
    }
}

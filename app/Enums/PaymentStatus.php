<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    /**
     * Get the status badge color
     *
     * @return string The CSS color class
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
            self::REFUNDED => 'purple',
            self::VERIFIED => 'emerald',
            self::REJECTED => 'rose',
        };
    }

    /**
     * Get the display name for the status
     *
     * @return string The display name
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
            self::VERIFIED => 'Verified',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Check if status is final
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::FAILED,
            self::REFUNDED,
            self::VERIFIED,
            self::REJECTED,
        ]);
    }

    /**
     * Check if status is successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::VERIFIED,
        ]);
    }
}

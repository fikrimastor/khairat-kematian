<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Verified = 'verified';
    case Rejected = 'rejected';
    case Failed = 'failed';

    /**
     * Get all available payment statuses as an array.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = self::getLabel($case);

            return $carry;
        }, []);
    }

    /**
     * Get human-readable label for a status.
     */
    public static function getLabel(self $status): string
    {
        return match ($status) {
            self::Pending => 'Menunggu Pengesahan',
            self::Processing => 'Sedang Diproses',
            self::Verified => 'Disahkan',
            self::Rejected => 'Ditolak',
            self::Failed => 'Gagal',
        };
    }

    /**
     * Get color class for the status.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Verified => 'success',
            self::Rejected => 'danger',
            self::Failed => 'danger',
        };
    }

    /**
     * Check if status can be changed to verified.
     */
    public function canBeVerified(): bool
    {
        return in_array($this, [self::Pending, self::Processing]);
    }

    /**
     * Check if status can be changed to rejected.
     */
    public function canBeRejected(): bool
    {
        return in_array($this, [self::Pending, self::Processing]);
    }
}

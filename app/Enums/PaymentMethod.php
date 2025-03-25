<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CHIP_IN_ASIA = 'chip_in_asia';
    case Billplz = 'billplz';
    case Cash = 'cash';

    /**
     * Get all available payment methods as an array.
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
     * Get human-readable label for a method.
     */
    public static function getLabel(self $method): string
    {
        return match ($method) {
            self::BANK_TRANSFER => 'Pindahan Bank',
            self::CHIP_IN_ASIA => 'Pembayaran Dalam Talian (ChipIn Asia)',
            self::Billplz => 'Pembayaran Dalam Talian (Billplz)',
            self::Cash => 'Tunai',
        };
    }

    /**
     * Get label for the payment method.
     */
    public function label(): string
    {
        return self::getLabel($this);
    }

    /**
     * Check if the payment method requires receipt upload.
     */
    public function requiresReceipt(): bool
    {
        return $this === self::BANK_TRANSFER;
    }

    /**
     * Check if the payment method requires online processing.
     */
    public function isOnline(): bool
    {
        return in_array($this, [self::CHIP_IN_ASIA, self::Billplz]);
    }

    /**
     * Check if the payment method requires manual verification.
     */
    public function requiresVerification(): bool
    {
        return in_array($this, [self::BANK_TRANSFER, self::Cash]);
    }

    /**
     * Get icon class for the payment method.
     */
    public function icon(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'fas fa-university',
            self::CHIP_IN_ASIA => 'fas fa-credit-card',
            self::Billplz => 'fas fa-credit-card',
            self::Cash => 'fas fa-money-bill-wave',
        };
    }
}

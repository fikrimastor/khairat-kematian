<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BankTransfer = 'bank_transfer';
    case ChipInAsia = 'chipin_asia';
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
            self::BankTransfer => 'Pindahan Bank',
            self::ChipInAsia => 'Pembayaran Dalam Talian (ChipIn Asia)',
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
        return $this === self::BankTransfer;
    }

    /**
     * Check if the payment method requires online processing.
     */
    public function isOnline(): bool
    {
        return $this === self::ChipInAsia;
    }

    /**
     * Check if the payment method requires manual verification.
     */
    public function requiresVerification(): bool
    {
        return in_array($this, [self::BankTransfer, self::Cash]);
    }

    /**
     * Get icon class for the payment method.
     */
    public function icon(): string
    {
        return match ($this) {
            self::BankTransfer => 'fas fa-university',
            self::ChipInAsia => 'fas fa-credit-card',
            self::Cash => 'fas fa-money-bill-wave',
        };
    }
}

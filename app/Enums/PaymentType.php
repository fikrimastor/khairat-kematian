<?php

namespace App\Enums;

enum PaymentType: string
{
    case REGISTRATION = 'registration';
    case RENEWAL = 'renewal';

    /**
     * Get the display name for the payment type
     *
     * @return string The display name
     */
    public function label(): string
    {
        return match ($this) {
            self::REGISTRATION => 'Yuran Pendaftaran',
            self::RENEWAL => 'Yuran Pembaharuan',
        };
    }

    /**
     * Get all payment types as an array
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}

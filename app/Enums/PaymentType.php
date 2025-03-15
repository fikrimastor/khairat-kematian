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
            self::REGISTRATION => 'Registration',
            self::RENEWAL => 'Renewal',
        };
    }

    /**
     * Get the description for the payment type
     *
     * @return string The description
     */
    public function description(): string
    {
        return match ($this) {
            self::REGISTRATION => 'Initial registration fee',
            self::RENEWAL => 'Annual renewal fee',
        };
    }
}

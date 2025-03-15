<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';

    /**
     * Get the display name for the user status
     *
     * @return string The display name
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING => 'Pending',
            self::SUSPENDED => 'Suspended',
        };
    }

    /**
     * Get the localized display name for the user status
     *
     * @param  string  $locale  The locale to use
     * @return string The localized display name
     */
    public function localizedLabel(string $locale = 'ms'): string
    {
        if ($locale === 'ms') {
            return match ($this) {
                self::ACTIVE => 'Aktif',
                self::INACTIVE => 'Tidak Aktif',
                self::PENDING => 'Menunggu',
                self::SUSPENDED => 'Digantung',
            };
        }

        return $this->label();
    }

    /**
     * Get the color for the user status
     *
     * @return string The CSS color class
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::PENDING => 'yellow',
            self::SUSPENDED => 'red',
        };
    }

    /**
     * Check if the user status is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}

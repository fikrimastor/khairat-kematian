<?php

namespace App\Enums;

enum NotificationType: string
{
    case PAYMENT_CREATED = 'payment_created';
    case PAYMENT_VERIFIED = 'payment_verified';
    case PAYMENT_REJECTED = 'payment_rejected';
    case MEMBER_REGISTERED = 'member_registered';
    case DEPENDENT_ADDED = 'dependent_added';
    case SYSTEM_NOTIFICATION = 'system_notification';

    /**
     * Get the display name for the notification type
     *
     * @return string The display name
     */
    public function label(): string
    {
        return match ($this) {
            self::PAYMENT_CREATED => 'Payment Created',
            self::PAYMENT_VERIFIED => 'Payment Verified',
            self::PAYMENT_REJECTED => 'Payment Rejected',
            self::MEMBER_REGISTERED => 'Member Registered',
            self::DEPENDENT_ADDED => 'Dependent Added',
            self::SYSTEM_NOTIFICATION => 'System Notification',
        };
    }

    /**
     * Get the icon for the notification type
     *
     * @return string The icon class
     */
    public function icon(): string
    {
        return match ($this) {
            self::PAYMENT_CREATED => 'cash',
            self::PAYMENT_VERIFIED => 'check-circle',
            self::PAYMENT_REJECTED => 'x-circle',
            self::MEMBER_REGISTERED => 'user-plus',
            self::DEPENDENT_ADDED => 'users',
            self::SYSTEM_NOTIFICATION => 'bell',
        };
    }

    /**
     * Get the color for the notification type
     *
     * @return string The color class
     */
    public function color(): string
    {
        return match ($this) {
            self::PAYMENT_CREATED => 'blue',
            self::PAYMENT_VERIFIED => 'green',
            self::PAYMENT_REJECTED => 'red',
            self::MEMBER_REGISTERED => 'purple',
            self::DEPENDENT_ADDED => 'indigo',
            self::SYSTEM_NOTIFICATION => 'gray',
        };
    }
}

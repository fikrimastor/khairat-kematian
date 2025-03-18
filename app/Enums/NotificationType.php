<?php

namespace App\Enums;

enum NotificationType: string
{
    case PAYMENT_CONFIRMED = 'payment_confirmed';
    case PAYMENT_REJECTED = 'payment_rejected';
    case PAYMENT_REMINDER = 'payment_reminder';
    case MEMBER_REGISTERED = 'member_registered';
    case DEPENDENT_ADDED = 'dependent_added';
    case SYSTEM_NOTIFICATION = 'system_notification';

    /**
     * Get the translation key for the notification type
     *
     * @return string The translation key
     */
    public function translationKey(): string
    {
        return 'khairat.notification_type_'.$this->value;
    }

    /**
     * Get the translated display name for the notification type
     *
     * @return string The translated display name
     */
    public function translatedName(): string
    {
        return __($this->translationKey().'_name');
    }

    /**
     * Get the translated description for the notification type
     *
     * @return string The translated description
     */
    public function translatedDescription(): string
    {
        return __($this->translationKey().'_description');
    }

    /**
     * Get the display name for the notification type
     *
     * @return string The display name
     */
    public function displayName(): string
    {
        return match ($this) {
            self::PAYMENT_CONFIRMED => 'Payment Confirmed',
            self::PAYMENT_REJECTED => 'Payment Rejected',
            self::PAYMENT_REMINDER => 'Payment Reminder',
            self::MEMBER_REGISTERED => 'Member Registration',
            self::DEPENDENT_ADDED => 'Dependent Added',
            self::SYSTEM_NOTIFICATION => 'System Notification',
        };
    }

    /**
     * Get the description for the notification type
     *
     * @return string The description
     */
    public function description(): string
    {
        return match ($this) {
            self::PAYMENT_CONFIRMED => 'Notifications when your payment is confirmed',
            self::PAYMENT_REJECTED => 'Notifications when your payment is rejected',
            self::PAYMENT_REMINDER => 'Reminders for upcoming or overdue payments',
            self::MEMBER_REGISTERED => 'Notifications about your membership registration',
            self::DEPENDENT_ADDED => 'Notifications when dependents are added to your account',
            self::SYSTEM_NOTIFICATION => 'Important system notifications and announcements',
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
            self::PAYMENT_CONFIRMED => 'cash',
            self::PAYMENT_REJECTED => 'x-circle',
            self::PAYMENT_REMINDER => 'clock',
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
            self::PAYMENT_CONFIRMED => 'blue',
            self::PAYMENT_REJECTED => 'red',
            self::PAYMENT_REMINDER => 'yellow',
            self::MEMBER_REGISTERED => 'purple',
            self::DEPENDENT_ADDED => 'indigo',
            self::SYSTEM_NOTIFICATION => 'gray',
        };
    }
}

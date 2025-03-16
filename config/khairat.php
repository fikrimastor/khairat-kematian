<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Membership Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the membership system.
    |
    */

    // Registration fee for new members or expired memberships (in RM)
    'registration_fee' => env('REGISTRATION_FEE', 50),

    // Renewal fee for active members (in RM)
    'renewal_fee' => env('RENEWAL_FEE', 40),

    // Duration of membership in days (default: 1 year)
    'membership_duration' => env('MEMBERSHIP_DURATION', 365),

    // Grace period for renewals after expiry (in days)
    'grace_period' => env('GRACE_PERIOD', 0),

    // Available payment methods
    'payment_methods' => [
        'bank_transfer' => [
            'name' => 'Bank Transfer',
            'enabled' => true,
        ],
        'cash' => [
            'name' => 'Cash',
            'enabled' => true,
        ],
        // Add more payment methods as needed
    ],
];

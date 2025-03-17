<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default settings driver that will be used to
    | store and retrieve settings. Supported drivers: "database", "cache", "file".
    |
    */
    'driver' => env('SETTINGS_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache TTL for the cache settings driver.
    | The value is in seconds.
    |
    */
    'cache_ttl' => env('SETTINGS_CACHE_TTL', 86400),

    /*
    |--------------------------------------------------------------------------
    | File Path
    |--------------------------------------------------------------------------
    |
    | This option controls the default file path for the file settings driver.
    |
    */
    'file_path' => env('SETTINGS_FILE_PATH', storage_path('app/settings.json')),

    /*
    |--------------------------------------------------------------------------
    | Settings Categories
    |--------------------------------------------------------------------------
    |
    | This option defines the categories for grouping settings in the UI.
    |
    */
    'categories' => [
        'organization' => [
            'organization_name',
            'organization_address',
            'organization_phone',
            'organization_email',
        ],
        'payment' => [
            'registration_fee',
            'renewal_fee',
            'bank_name',
            'bank_account_name',
            'bank_account_number',
        ],
        'notification' => [
            'email_notifications_enabled',
            'admin_notification_email',
        ],
        'system' => [
            'maintenance_mode',
            'backup_enabled',
            'backup_frequency',
        ],
    ],
];

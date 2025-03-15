<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'registration_fee',
                'value' => '50',
                'description' => 'Registration fee amount in RM'
            ],
            [
                'key' => 'renewal_fee',
                'value' => '40',
                'description' => 'Annual renewal fee amount in RM'
            ],
            [
                'key' => 'organization_name',
                'value' => 'Khairat Kematian Masjid',
                'description' => 'Name of the organization'
            ],
            [
                'key' => 'organization_address',
                'value' => 'Jalan Masjid, 12345 Bandar, Malaysia',
                'description' => 'Address of the organization'
            ],
            [
                'key' => 'organization_phone',
                'value' => '+60123456789',
                'description' => 'Contact phone number'
            ],
            [
                'key' => 'organization_email',
                'value' => 'info@khairat-kematian.org',
                'description' => 'Contact email address'
            ],
            [
                'key' => 'bank_account_name',
                'value' => 'Khairat Kematian Masjid',
                'description' => 'Bank account name for transfers'
            ],
            [
                'key' => 'bank_account_number',
                'value' => '1234567890',
                'description' => 'Bank account number for transfers'
            ],
            [
                'key' => 'bank_name',
                'value' => 'Bank Islam Malaysia',
                'description' => 'Bank name for transfers'
            ]
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description']
                ]
            );
        }
    }
}

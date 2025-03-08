<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for payment module
        $paymentPermissions = [
            'payment.view',
            'payment.create',
            'payment.update',
            'payment.delete',
            'payment.verify',
            'payment.report',
            'receipt.view',
            'receipt.create',
            'receipt.download',
        ];

        foreach ($paymentPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'Super Admin' => $paymentPermissions,
            'Administrator' => $paymentPermissions,
            'Treasurer' => [
                'payment.view',
                'payment.create',
                'payment.update',
                'payment.verify',
                'payment.report',
                'receipt.view',
                'receipt.create',
                'receipt.download',
            ],
            'Area Leader' => [
                'payment.view',
                'payment.create',
                'receipt.view',
                'receipt.download',
            ],
            'Member' => [
                'payment.view',
                'payment.create',
                'receipt.view',
                'receipt.download',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        // Create admin user and assign role
        $admin = User::firstOrCreate(
            ['email' => 'admin@khairat-kematian.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('Super Admin');
    }
}

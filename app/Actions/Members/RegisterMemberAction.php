<?php

namespace App\Actions\Members;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RegisterMemberAction
{
    /**
     * Register a new member.
     *
     * @param array $data The member data
     * @return User The newly created user
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create the user record
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'identification_number' => $data['identification_number'] ?? null,
                'is_admin' => false,
                'language' => $data['language'] ?? 'ms', // Default to Bahasa Malaysia
            ]);
            
            // Check if the member role exists and assign it
            if (Role::where('name', 'member')->exists()) {
                $user->assignRole('member');
            }
            
            // Dispatch event if needed
            // MemberRegistered::dispatch($user);
            
            return $user;
        });
    }
} 
<?php

namespace App\Actions\Members;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateMemberAction
{
    /**
     * Update a member's profile.
     *
     * @param int $userId The user ID
     * @param array $data The updated member data
     * @return User The updated user
     */
    public function execute(int $userId, array $data): User
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);
            
            // Update the user record
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'address' => $data['address'] ?? $user->address,
                'phone' => $data['phone'] ?? $user->phone,
                'identification_number' => $data['identification_number'] ?? $user->identification_number,
                'language' => $data['language'] ?? $user->language,
            ]);
            
            // Dispatch event if needed
            // MemberUpdated::dispatch($user);
            
            return $user;
        });
    }
} 
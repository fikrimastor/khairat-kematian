<?php

namespace App\Actions\Members;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    /**
     * Change the user's password.
     *
     * @param int $userId
     * @param array $data
     * @return User
     */
    public function execute(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'password' => Hash::make($data['password']),
        ]);
        
        return $user;
    }
} 
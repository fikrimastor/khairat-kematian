<?php

namespace App\Actions\Members;

use App\Models\Dependent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddDependentAction
{
    /**
     * Add a dependent to a member.
     *
     * @param int $userId The user ID
     * @param array $data The dependent data
     * @return Dependent The newly created dependent
     */
    public function execute(int $userId, array $data): Dependent
    {
        return DB::transaction(function () use ($userId, $data) {
            // Check if user exists
            $user = User::findOrFail($userId);
            
            // Create the dependent record
            $dependent = Dependent::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'identification_number' => $data['identification_number'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'relationship' => $data['relationship'],
            ]);
            
            // Dispatch event if needed
            // DependentAdded::dispatch($dependent);
            
            return $dependent;
        });
    }
} 
<?php

namespace App\Services;

use App\Models\Dependent;
use App\Models\User;
use Illuminate\Support\Carbon;

class DependentManagementService
{
    /**
     * Create a new dependent for a member
     *
     * @param  User  $member
     * @param  array  $data
     * @return Dependent
     */
    public function createDependent(User $member, array $data): Dependent
    {
        return $member->dependents()->create([
            'name' => $data['name'],
            'identification_number' => $data['identification_number'],
            'birth_date' => $data['birth_date'] ? Carbon::parse($data['birth_date']) : null,
            'relationship' => $data['relationship'],
        ]);
    }

    /**
     * Update an existing dependent
     *
     * @param  Dependent  $dependent
     * @param  array  $data
     * @return Dependent
     */
    public function updateDependent(Dependent $dependent, array $data): Dependent
    {
        $dependent->update([
            'name' => $data['name'],
            'identification_number' => $data['identification_number'],
            'birth_date' => $data['birth_date'] ? Carbon::parse($data['birth_date']) : null,
            'relationship' => $data['relationship'],
        ]);

        return $dependent;
    }

    /**
     * Delete a dependent
     *
     * @param  Dependent  $dependent
     * @return bool
     */
    public function deleteDependent(Dependent $dependent): bool
    {
        return $dependent->delete();
    }

    /**
     * Get all dependents for a member
     *
     * @param  User  $member
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDependentsForMember(User $member)
    {
        return $member->dependents;
    }
}

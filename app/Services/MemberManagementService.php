<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class MemberManagementService
{
    /**
     * Update a member's information
     *
     * @param  User  $member
     * @param  array  $data
     * @return User
     */
    public function updateMember(User $member, array $data): User
    {
        $member->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'identification_number' => $data['identification_number'],
            'is_active' => $data['is_active'],
            'membership_expires_at' => $data['membership_expires_at'] ? Carbon::parse($data['membership_expires_at']) : null,
            'membership_type' => $data['membership_type'],
        ]);

        return $member;
    }

    /**
     * Activate a member
     *
     * @param  User  $member
     * @return User
     */
    public function activateMember(User $member): User
    {
        $member->update(['is_active' => true]);

        return $member;
    }

    /**
     * Deactivate a member
     *
     * @param  User  $member
     * @return User
     */
    public function deactivateMember(User $member): User
    {
        $member->update(['is_active' => false]);

        return $member;
    }

    /**
     * Extend a member's membership by 1 year
     *
     * @param  User  $member
     * @return User
     */
    public function extendMembership(User $member): User
    {
        $currentExpiry = $member->membership_expires_at ?? now();
        $member->update([
            'membership_expires_at' => $currentExpiry->addYear(),
            'is_active' => true,
        ]);

        return $member;
    }

    /**
     * Delete a member and their dependents
     *
     * @param  User  $member
     * @return bool
     */
    public function deleteMember(User $member): bool
    {
        // Delete dependents first
        $member->dependents()->delete();

        // Then delete the member
        return $member->delete();
    }

    /**
     * Get filtered and paginated members
     *
     * @param  string  $search
     * @param  string  $status
     * @param  string  $sortField
     * @param  string  $sortDirection
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredMembers(string $search = '', string $status = '', string $sortField = 'name', string $sortDirection = 'asc', int $perPage = 10)
    {
        return User::query()
            ->where('is_admin', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('identification_number', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status === 'active') {
                    $query->where('is_active', true)
                        ->where(function ($query) {
                            $query->whereNull('membership_expires_at')
                                ->orWhere('membership_expires_at', '>=', now());
                        });
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($status === 'expired') {
                    $query->where('membership_expires_at', '<', now());
                }
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
}

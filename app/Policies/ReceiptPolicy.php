<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceiptPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view receipts
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Receipt $receipt): bool
    {
        // Admin can view all receipts
        if ($user->is_admin) {
            return true;
        }
        
        // Users can only view their own receipts
        return $receipt->payment->user_id === $user->id;
    }

    /**
     * Determine whether the user can generate receipts.
     */
    public function generate(User $user): bool
    {
        return $user->is_admin;
    }
} 
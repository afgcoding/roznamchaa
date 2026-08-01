<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Store $store): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Super Admin can create stores anytime.
     * New shopkeepers with no store can self-register one.
     */
    public function create(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->stores()->doesntExist();
    }

    public function update(User $user, Store $store): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Store $store): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}

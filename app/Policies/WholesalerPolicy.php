<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wholesaler;

class WholesalerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Wholesaler $wholesaler): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Wholesaler $wholesaler): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Wholesaler $wholesaler): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Wholesaler $wholesaler): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Wholesaler $wholesaler): bool
    {
        return $user->isAdmin();
    }
}

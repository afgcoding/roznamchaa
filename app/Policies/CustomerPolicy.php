<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function restore(User $user, Customer $customer): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->isAdmin();
    }
}

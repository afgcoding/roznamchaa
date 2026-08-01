<?php

namespace App\Policies;

use App\Models\CustomerLedger;
use App\Models\User;

class CustomerLedgerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function view(User $user, CustomerLedger $customerLedger): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function update(User $user, CustomerLedger $customerLedger): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function delete(User $user, CustomerLedger $customerLedger): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, CustomerLedger $customerLedger): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, CustomerLedger $customerLedger): bool
    {
        return $user->isAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WholesalerPayment;

class WholesalerPaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, WholesalerPayment $wholesalerPayment): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, WholesalerPayment $wholesalerPayment): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, WholesalerPayment $wholesalerPayment): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, WholesalerPayment $wholesalerPayment): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, WholesalerPayment $wholesalerPayment): bool
    {
        return $user->isAdmin();
    }
}

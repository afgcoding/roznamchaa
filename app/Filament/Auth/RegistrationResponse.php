<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class RegistrationResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        $store = $user?->stores()
            ->where('stores.is_active', true)
            ->orderBy('stores.id')
            ->first();

        if ($store) {
            return redirect()->intended(Filament::getUrl($store));
        }

        // Fallback: send users with no store yet to tenant registration.
        if (Filament::hasTenantRegistration()) {
            return redirect()->intended(Filament::getTenantRegistrationUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}

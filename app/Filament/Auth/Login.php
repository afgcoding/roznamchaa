<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $user = User::query()
            ->where('email', $data['email'] ?? null)
            ->first();

        if (
            $user
            && ! $user->is_active
            && Hash::check($data['password'] ?? '', $user->getAuthPassword())
        ) {
            throw ValidationException::withMessages([
                'data.email' => 'Your account is inactive. Please contact an administrator.',
            ]);
        }

        return parent::authenticate();
    }
}

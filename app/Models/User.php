<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_CASHIER = 'cashier';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)->withTimestamps();
    }

    /**
     * Stores available in the tenant switcher.
     *
     * @return Collection<int, Store>
     */
    public function getTenants(Panel $panel): Collection
    {
        if ($this->isSuperAdmin()) {
            return Store::query()
                ->orderBy('name')
                ->get();
        }

        return $this->stores()
            ->where('stores.is_active', true)
            ->orderBy('stores.name')
            ->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Store) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        // Membership only — active/inactive is enforced by EnsureStoreIsActive.
        return $this->stores()->whereKey($tenant->getKey())->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->isSuperAdmin();
    }

    public function isCashier(): bool
    {
        return $this->role === self::ROLE_CASHIER;
    }

    /**
     * True when the user belongs to stores, but none of them are active.
     */
    public function hasOnlyInactiveStores(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        if ($this->stores()->doesntExist()) {
            return false;
        }

        return $this->stores()
            ->where('stores.is_active', true)
            ->doesntExist();
    }

    /**
     * Sales recorded by this user (cashier/admin).
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}

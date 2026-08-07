<?php

namespace App\Support;

use App\Enums\StoreFeature;
use App\Enums\StorePlanType;
use App\Models\Store;
use Filament\Facades\Filament;

class StoreFeatures
{
    public static function currentStore(): ?Store
    {
        $tenant = Filament::getTenant();

        if ($tenant instanceof Store) {
            return $tenant;
        }

        $routeTenant = request()->route('tenant');

        if ($routeTenant instanceof Store) {
            return $routeTenant;
        }

        if (is_string($routeTenant) && $routeTenant !== '') {
            return Store::query()->where('slug', $routeTenant)->first();
        }

        return null;
    }

    public static function currentPlan(): StorePlanType
    {
        $store = static::currentStore();

        if (! $store) {
            return StorePlanType::Grocery;
        }

        // Fresh DB read — avoids stale tenant attributes after plan upgrades.
        $raw = Store::query()->whereKey($store->getKey())->value('plan_type');

        if ($raw instanceof StorePlanType) {
            return $raw;
        }

        if (is_string($raw) && $raw !== '') {
            return StorePlanType::tryFrom($raw) ?? StorePlanType::Grocery;
        }

        if ($store->plan_type instanceof StorePlanType) {
            return $store->plan_type;
        }

        return StorePlanType::Grocery;
    }

    public static function enabled(StoreFeature $feature): bool
    {
        return static::currentPlan()->has($feature);
    }

    public static function forStore(?Store $store): StorePlanType
    {
        if (! $store) {
            return StorePlanType::Grocery;
        }

        $raw = Store::query()->whereKey($store->getKey())->value('plan_type') ?? $store->plan_type;

        if ($raw instanceof StorePlanType) {
            return $raw;
        }

        if (is_string($raw) && $raw !== '') {
            return StorePlanType::tryFrom($raw) ?? StorePlanType::Grocery;
        }

        return StorePlanType::Grocery;
    }

    public static function storeHas(?Store $store, StoreFeature $feature): bool
    {
        return static::forStore($store)->has($feature);
    }
}

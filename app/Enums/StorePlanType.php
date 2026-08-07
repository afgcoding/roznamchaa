<?php

namespace App\Enums;

enum StorePlanType: string
{
    case Grocery = 'grocery';
    case Wholesale = 'wholesale';
    case Supermarket = 'supermarket';

    public function label(): string
    {
        return match ($this) {
            self::Grocery => __('Grocery'),
            self::Wholesale => __('Wholesale'),
            self::Supermarket => __('Supermarket'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $plan): array => [$plan->value => $plan->label()])
            ->all();
    }

    /**
     * Cumulative plan features (higher plans include lower-plan unlocks).
     *
     * @return list<StoreFeature>
     */
    public function features(): array
    {
        $wholesale = [
            StoreFeature::MultiUnitConversion,
            StoreFeature::WholesalePricing,
            StoreFeature::CreditLimit,
            StoreFeature::DeliveryChallan,
        ];

        $supermarket = [
            ...$wholesale,
            StoreFeature::ExpiryAlerts,
            StoreFeature::CashierShifts,
            StoreFeature::PosShortkeys,
            StoreFeature::DiscountEngine,
        ];

        return match ($this) {
            self::Grocery => [],
            self::Wholesale => $wholesale,
            self::Supermarket => $supermarket,
        };
    }

    public function has(StoreFeature $feature): bool
    {
        return in_array($feature, $this->features(), true);
    }
}

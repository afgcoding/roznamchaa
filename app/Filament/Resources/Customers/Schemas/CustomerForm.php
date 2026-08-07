<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\StoreFeature;
use App\Support\NumberFormat;
use App\Support\StoreFeatures;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Customer Details'))
                    ->description(StoreFeatures::enabled(StoreFeature::CreditLimit)
                        ? __('Basic info and credit limit for this customer.')
                        : __('Basic info for this customer.'))
                    ->icon(Heroicon::OutlinedUser)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema(static::components()),
            ]);
    }

    /**
     * Full customer fields — reused by CustomerResource and SaleForm create-option modal.
     *
     * @return array<int, TextInput>
     */
    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label(__('Customer Name'))
                ->placeholder(__('e.g. احمد خان'))
                ->helperText(__('Full name of the customer so cashiers can find them quickly.'))
                ->required()
                ->maxLength(255)
                ->autofocus(),
            TextInput::make('phone')
                ->label(__('Phone'))
                ->placeholder(__('e.g. 0700000000'))
                ->helperText(__('Mobile number used to call or message this customer.'))
                ->tel()
                ->required()
                ->maxLength(255),
            TextInput::make('credit_limit')
                ->label(__('Credit Limit'))
                ->placeholder(__('e.g. 5000'))
                ->helperText(__('Maximum qarz this customer is allowed. Use 0 for cash-only.'))
                ->required(fn (): bool => StoreFeatures::enabled(StoreFeature::CreditLimit))
                ->numeric()
                ->default(0)
                ->prefix('AFN')
                ->minValue(0)
                ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                ->columnSpanFull()
                ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::CreditLimit))
                ->dehydrated(fn (): bool => StoreFeatures::enabled(StoreFeature::CreditLimit)),
        ];
    }
}

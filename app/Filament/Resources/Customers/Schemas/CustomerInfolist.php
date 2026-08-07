<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\StoreFeature;
use App\Support\NumberFormat;
use App\Support\StoreFeatures;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Customer Details'))
                    ->description(__('Basic info for this customer.'))
                    ->icon(Heroicon::OutlinedUser)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Customer Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('phone')
                            ->label(__('Phone'))
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                    ]),

                Section::make(__('Credit & Qarz'))
                    ->description(__('Credit limit and unpaid balance.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('credit_limit')
                            ->label(__('Credit Limit'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('gray')
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::CreditLimit)),
                        TextEntry::make('total_due')
                            ->label(__('Total Due (Qarz)'))
                            ->state(fn ($record): float => $record->total_due)
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color(function ($record): string {
                                $due = (float) $record->total_due;
                                $limit = (float) $record->credit_limit;

                                if ($due <= 0) {
                                    return 'success';
                                }

                                if (
                                    StoreFeatures::enabled(StoreFeature::CreditLimit)
                                    && $limit > 0
                                    && $due > $limit
                                ) {
                                    return 'danger';
                                }

                                return 'warning';
                            }),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this customer was created and last updated.'))
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label(__('Last Updated'))
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

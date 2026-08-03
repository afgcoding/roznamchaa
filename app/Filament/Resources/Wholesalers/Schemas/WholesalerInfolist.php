<?php

namespace App\Filament\Resources\Wholesalers\Schemas;

use App\Support\NumberFormat;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class WholesalerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Wholesaler Details'))
                    ->description(__('Supplier contact information.'))
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Wholesaler Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('phone')
                            ->label(__('Phone'))
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                        TextEntry::make('address')
                            ->label(__('Address'))
                            ->placeholder(__('No address'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Debt'))
                    ->description(__('How much you owe this wholesaler.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('total_debt')
                            ->label(__('Total Debt'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this wholesaler was created and last updated.'))
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

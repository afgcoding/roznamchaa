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
                Section::make('Wholesaler Details')
                    ->description('Supplier contact information.')
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Wholesaler Name')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                        TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('No address')
                            ->columnSpanFull(),
                    ]),

                Section::make('Debt')
                    ->description('How much you owe this wholesaler.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('total_debt')
                            ->label('Total Debt')
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
                    ]),

                Section::make('Record Info')
                    ->description('When this wholesaler was created and last updated.')
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

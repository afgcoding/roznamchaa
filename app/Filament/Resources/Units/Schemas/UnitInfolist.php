<?php

namespace App\Filament\Resources\Units\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class UnitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Unit Details')
                    ->description('Basic information about this measuring unit.')
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Unit Name')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('short_name')
                            ->label('Short Name')
                            ->badge()
                            ->color('primary')
                            ->helperText('English short code used on bills.'),
                        TextEntry::make('products_count')
                            ->label('Products Using This Unit')
                            ->state(fn ($record): int => Product::query()
                                ->where(function ($query) use ($record): void {
                                    $query->where('purchase_unit_id', $record->id)
                                        ->orWhere('sale_unit_id', $record->id);
                                })
                                ->count())
                            ->badge()
                            ->color('info')
                            ->helperText('How many products buy or sell with this unit.'),
                    ]),
                Section::make('Record Info')
                    ->description('When this unit was created and last updated.')
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

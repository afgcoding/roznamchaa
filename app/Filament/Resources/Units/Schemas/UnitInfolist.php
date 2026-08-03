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
                Section::make(__('Unit Details'))
                    ->description(__('Basic information about this measuring unit.'))
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Unit Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('short_name')
                            ->label(__('Short Name'))
                            ->badge()
                            ->color('primary')
                            ->helperText(__('English short code used on bills.')),
                        TextEntry::make('products_count')
                            ->label(__('Products Using This Unit'))
                            ->state(fn ($record): int => Product::query()
                                ->where(function ($query) use ($record): void {
                                    $query->where('purchase_unit_id', $record->id)
                                        ->orWhere('sale_unit_id', $record->id);
                                })
                                ->count())
                            ->badge()
                            ->color('info')
                            ->helperText(__('How many products buy or sell with this unit.')),
                    ]),
                Section::make(__('Record Info'))
                    ->description(__('When this unit was created and last updated.'))
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

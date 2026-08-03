<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Support\NumberFormat;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->description(__('Main product identity.'))
                    ->icon(Heroicon::OutlinedCube)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Product Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('category.name')
                            ->label(__('Category'))
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                        TextEntry::make('barcode')
                            ->label(__('Barcode'))
                            ->placeholder(__('No barcode'))
                            ->badge()
                            ->color('gray'),
                    ]),

                Section::make(__('Units & Conversion'))
                    ->description(__('Buy unit, sell unit, and conversion.'))
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('purchaseUnit.name')
                            ->label(__('Purchase Unit'))
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('saleUnit.name')
                            ->label(__('Sale Unit'))
                            ->badge()
                            ->color('success'),
                        TextEntry::make('unit_conversion')
                            ->label(__('Unit Conversion'))
                            ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 3))
                            ->badge()
                            ->color('warning')
                            ->helperText(__('Sale units inside one purchase unit.')),
                    ]),

                Section::make(__('Pricing'))
                    ->description(__('Cost and sell prices in AFN.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cost_price')
                            ->label(__('Cost Price'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('sale_price')
                            ->label(__('Sale Price'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('success'),
                    ]),

                Section::make(__('Stock'))
                    ->description(__('Current stock level and alert limit.'))
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('stock_quantity')
                            ->label(__('Stock Quantity'))
                            ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 3))
                            ->badge()
                            ->color(function ($record): string {
                                $stock = (float) $record->stock_quantity;
                                $min = (float) $record->min_stock_alert;

                                if ($stock <= $min) {
                                    return 'danger';
                                }

                                if ($stock <= ($min * 3)) {
                                    return 'warning';
                                }

                                return 'success';
                            }),
                        TextEntry::make('min_stock_alert')
                            ->label(__('Min Stock Alert'))
                            ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 0))
                            ->badge()
                            ->color('warning')
                            ->helperText(__('Warning starts at this stock level.')),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this product was created and last updated.'))
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

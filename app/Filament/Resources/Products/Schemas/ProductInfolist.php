<?php

namespace App\Filament\Resources\Products\Schemas;

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
                Section::make('Basic Information')
                    ->description('Main product identity.')
                    ->icon(Heroicon::OutlinedCube)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Product Name')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('category.name')
                            ->label('Category')
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                        TextEntry::make('barcode')
                            ->label('Barcode')
                            ->placeholder('No barcode')
                            ->badge()
                            ->color('gray'),
                    ]),

                Section::make('Units & Conversion')
                    ->description('Buy unit, sell unit, and conversion.')
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('purchaseUnit.name')
                            ->label('Purchase Unit')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('saleUnit.name')
                            ->label('Sale Unit')
                            ->badge()
                            ->color('success'),
                        TextEntry::make('unit_conversion')
                            ->label('Unit Conversion')
                            ->numeric()
                            ->badge()
                            ->color('warning')
                            ->helperText('Sale units inside one purchase unit.'),
                    ]),

                Section::make('Pricing')
                    ->description('Cost and sell prices in AFN.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cost_price')
                            ->label('Cost Price')
                            ->money('AFN')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('sale_price')
                            ->label('Sale Price')
                            ->money('AFN')
                            ->badge()
                            ->color('success'),
                    ]),

                Section::make('Stock')
                    ->description('Current stock level and alert limit.')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->numeric()
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
                            ->label('Min Stock Alert')
                            ->numeric()
                            ->badge()
                            ->color('warning')
                            ->helperText('Warning starts at this stock level.'),
                    ]),

                Section::make('Record Info')
                    ->description('When this product was created and last updated.')
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

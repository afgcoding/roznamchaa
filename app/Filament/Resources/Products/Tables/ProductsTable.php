<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Products\Concerns\ProtectsProductsUsedInSales;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    use ProtectsProductsUsedInSales;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('purchaseUnit.name')
                    ->label('Buy Unit')
                    ->formatStateUsing(fn ($state, $record): string => $record->purchaseUnit
                        ? "{$record->purchaseUnit->name} ({$record->purchaseUnit->short_name})"
                        : '—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('saleUnit.name')
                    ->label('Sell Unit')
                    ->formatStateUsing(fn ($state, $record): string => $record->saleUnit
                        ? "{$record->saleUnit->name} ({$record->saleUnit->short_name})"
                        : '—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('cost_price')
                    ->label('Cost')
                    ->money('AFN')
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sale_price')
                    ->label('Sale Price')
                    ->money('AFN')
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->badge()
                    ->color('warning'),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric(decimalPlaces: 3)
                    ->sortable()
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

                TextColumn::make('purchased_stock_units')
                    ->label('Purchased Stock Units')
                    ->state(function ($record): ?float {
                        $conversion = (float) $record->unit_conversion;

                        if ($conversion <= 0) {
                            return null;
                        }

                        return round((float) $record->stock_quantity / $conversion, 3);
                    })
                    ->formatStateUsing(function ($state, $record): string {
                        if ($state === null) {
                            return '—';
                        }

                        $amount = rtrim(rtrim(number_format((float) $state, 3, '.', ''), '0'), '.');
                        $unit = $record->purchaseUnit?->short_name;

                        return $unit ? "{$amount} {$unit}" : $amount;
                    })
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->badge()
                    ->color('gray'),
                TextColumn::make('min_stock_alert')
                    ->label('Min Alert')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->since()
                    ->color('success')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->since()
                    ->color('warning')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip('View details'),
                    EditAction::make()
                        ->label('Edit')
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip('Edit product'),
                    static::protectDeleteAction(
                        DeleteAction::make()
                            ->label('Delete')
                            ->icon(Heroicon::OutlinedTrash)
                            ->tooltip('Delete product')
                    ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    static::protectDeleteBulkAction(
                        DeleteBulkAction::make()
                            ->label('Delete Selected')
                            ->icon(Heroicon::OutlinedTrash)
                    ),
                ]),
            ]);
    }
}

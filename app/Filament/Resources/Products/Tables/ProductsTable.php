<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Products\Concerns\ProtectsProductsUsedInSales;
use App\Support\NumberFormat;
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
                    ->label(__('Product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('barcode')
                    ->label(__('Barcode'))
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('purchaseUnit.name')
                    ->label(__('Buy Unit'))
                    ->formatStateUsing(fn ($state, $record): string => $record->purchaseUnit
                        ? "{$record->purchaseUnit->name} ({$record->purchaseUnit->short_name})"
                        : '—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('saleUnit.name')
                    ->label(__('Sell Unit'))
                    ->formatStateUsing(fn ($state, $record): string => $record->saleUnit
                        ? "{$record->saleUnit->name} ({$record->saleUnit->short_name})"
                        : '—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('cost_price')
                    ->label(__('Cost'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sale_price')
                    ->label(__('Sale Price'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->badge()
                    ->color('warning'),
                TextColumn::make('stock_quantity')
                    ->label(__('Stock'))
                    ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 3))
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
                    ->label(__('Purchased Stock Units'))
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

                        $amount = NumberFormat::trim($state, 3);
                        $unit = $record->purchaseUnit?->short_name;

                        return $unit ? "{$amount} {$unit}" : $amount;
                    })
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->badge()
                    ->color('gray'),
                TextColumn::make('min_stock_alert')
                    ->label(__('Min Alert'))
                    ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 0))
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
                    ->label(__('Category'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit product')),
                    static::protectDeleteAction(
                        DeleteAction::make()
                            ->label(__('Delete'))
                            ->icon(Heroicon::OutlinedTrash)
                            ->tooltip(__('Delete product'))
                    ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    static::protectDeleteBulkAction(
                        DeleteBulkAction::make()
                            ->label(__('Delete Selected'))
                            ->icon(Heroicon::OutlinedTrash)
                    ),
                ]),
            ]);
    }
}

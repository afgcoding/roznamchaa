<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Unit Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('short_name')
                    ->label(__('Short Name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('purchase_products_count')
                    ->counts('purchaseProducts')
                    ->label(__('Purchase Use'))
                    ->badge()
                    ->color('info')
                    ->tooltip(__('Products that buy with this unit')),
                TextColumn::make('sale_products_count')
                    ->counts('saleProducts')
                    ->label(__('Sale Use'))
                    ->badge()
                    ->color('success')
                    ->tooltip(__('Products that sell with this unit')),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->since()
                    ->color('success')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->since()
                    ->color('warning')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
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
                        ->tooltip(__('Edit unit')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete unit')),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->icon(Heroicon::OutlinedTrash),
                ]),
            ]);
    }
}

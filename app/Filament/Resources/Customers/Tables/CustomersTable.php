<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Support\NumberFormat;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('credit_limit')
                    ->label(__('Credit Limit'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes),
                TextColumn::make('total_due')
                    ->label(__('Total Due'))
                    ->state(fn ($record): float => $record->total_due)
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->badge()
                    ->color(function ($record): string {
                        $due = (float) $record->total_due;
                        $limit = (float) $record->credit_limit;

                        if ($due <= 0) {
                            return 'success';
                        }

                        if ($limit > 0 && $due > $limit) {
                            return 'danger';
                        }

                        return 'warning';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(
                            COALESCE((SELECT SUM(amount) FROM customer_ledgers WHERE customer_id = customers.id AND type = \'credit\'), 0)
                            - COALESCE((SELECT SUM(amount) FROM customer_ledgers WHERE customer_id = customers.id AND type = \'payment\'), 0)
                        ) '.$direction);
                    }),
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
            ->filters([
                Filter::make('with_unpaid_debt')
                    ->label(__('Has unpaid qarz'))
                    ->query(fn (Builder $query): Builder => $query->withUnpaidDebt()),
                Filter::make('over_credit_limit')
                    ->label(__('Over credit limit'))
                    ->query(fn (Builder $query): Builder => $query->overCreditLimit()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View customer details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this customer')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this customer')),
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

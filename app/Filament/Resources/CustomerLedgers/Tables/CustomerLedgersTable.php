<?php

namespace App\Filament\Resources\CustomerLedgers\Tables;

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

class CustomerLedgersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('sale.id')
                    ->label(__('Related Bill'))
                    ->formatStateUsing(function ($state, $record): string {
                        return $record->sale?->ledgerBillLabel() ?? '—';
                    })
                    ->sortable()
                    ->wrap()
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'credit' => __('Credit (took goods on debt)'),
                        'payment' => __('Payment (paid debt back)'),
                        default => $state ?? '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'credit' => 'danger',
                        'payment' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->badge()
                    ->color(fn ($record): string => $record->type === 'payment' ? 'success' : 'danger'),
                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
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
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Entry Type'))
                    ->options([
                        'credit' => __('Credit (took goods on debt)'),
                        'payment' => __('Payment (paid debt back)'),
                    ]),
                SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View ledger entry')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this entry')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this entry')),
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

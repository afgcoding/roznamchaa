<?php

namespace App\Filament\Resources\WholesalerPayments\Tables;

use App\Services\WholesalerDebtService;
use App\Support\NumberFormat;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class WholesalerPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wholesaler.name')
                    ->label(__('Wholesaler'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount')
                    ->label(__('Amount Paid'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->badge()
                    ->color('success')
                    ->summarize(
                        Sum::make()
                            ->label(__('Total Paid'))
                            ->money('AFN')
                    ),
                TextColumn::make('date')
                    ->label(__('Payment Date'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('note')
                    ->label(__('Note'))
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('wholesaler_id')
                    ->relationship('wholesaler', 'name')
                    ->label(__('Wholesaler'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View payment details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this payment')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this payment'))
                        ->before(function ($record): void {
                            WholesalerDebtService::reversePayment($record);
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->before(function (Collection $records): void {
                            $records->each(fn ($payment) => WholesalerDebtService::reversePayment($payment));
                        }),
                ]),
            ]);
    }
}

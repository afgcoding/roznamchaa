<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Models\Sale;
use App\Services\SaleStockService;
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
use Illuminate\Database\Eloquent\Collection;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // sales.id
                TextColumn::make('id')
                    ->label('Invoice #')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                // sales.user_id
                TextColumn::make('user.name')
                    ->label('Cashier')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                // sales.customer_id (nullable)
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Walk-in')
                    ->badge()
                    ->color('gray'),
                // sales.total_amount decimal(12,2)
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes),
                // sales.discount decimal(12,2)
                TextColumn::make('discount')
                    ->label('Discount')
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes),
                // sales.payable_amount decimal(12,2)
                TextColumn::make('payable_amount')
                    ->label('Payable Amount')
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes),
                // sales.paid_amount decimal(12,2)
                TextColumn::make('paid_amount')
                    ->label('Paid Amount')
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->badge()
                    ->color('success'),
                // sales.due_amount decimal(12,2)
                TextColumn::make('due_amount')
                    ->label('Due Amount')
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
                // sales.payment_status enum(cash, credit, partial)
                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => 'Cash',
                        'credit' => 'Credit',
                        'partial' => 'Partial',
                        default => $state ?? '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'cash' => 'success',
                        'credit' => 'danger',
                        'partial' => 'warning',
                        default => 'gray',
                    }),
                // sales.timestamps
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->color('success')
                    ->badge()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->color('warning')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'cash' => 'Cash',
                        'credit' => 'Credit',
                        'partial' => 'Partial',
                    ]),
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Cashier')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label('Customer')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip('View invoice details'),
                    EditAction::make()
                        ->label('Edit')
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip('Edit this invoice'),
                    DeleteAction::make()
                        ->label('Delete')
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip('Delete this invoice')
                        ->before(function (Sale $record): void {
                            SaleStockService::restoreForSale($record);
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon(Heroicon::OutlinedTrash)
                        ->before(function (Collection $records): void {
                            $records->each(fn (Sale $sale) => SaleStockService::restoreForSale($sale));
                        }),
                ]),
            ]);
    }
}

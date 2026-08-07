<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Enums\StoreFeature;
use App\Models\Sale;
use App\Services\SaleStockService;
use App\Support\NumberFormat;
use App\Support\StoreFeatures;
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
                TextColumn::make('id')
                    ->label(__('Invoice #'))
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('user.name')
                    ->label(__('Cashier'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('Walk-in'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('total_amount')
                    ->label(__('Total Amount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes),
                TextColumn::make('discount')
                    ->label(__('Discount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::DiscountEngine)),
                TextColumn::make('payable_amount')
                    ->label(__('Payable Amount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes),
                TextColumn::make('paid_amount')
                    ->label(__('Paid Amount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('due_amount')
                    ->label(__('Due Amount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
                TextColumn::make('payment_status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => __('Cash'),
                        'credit' => __('Credit'),
                        'partial' => __('Partial'),
                        default => $state ?? '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'cash' => 'success',
                        'credit' => 'danger',
                        'partial' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->since()
                    ->color('success')
                    ->badge()
                    ->sortable(),
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
                SelectFilter::make('payment_status')
                    ->label(__('Payment Status'))
                    ->options([
                        'cash' => __('Cash'),
                        'credit' => __('Credit'),
                        'partial' => __('Partial'),
                    ]),
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label(__('Cashier'))
                    ->searchable()
                    ->preload(),
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
                        ->tooltip(__('View invoice details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this invoice')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this invoice'))
                        ->before(function (Sale $record): void {
                            SaleStockService::restoreForSale($record);
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->before(function (Collection $records): void {
                            $records->each(fn (Sale $sale) => SaleStockService::restoreForSale($sale));
                        }),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Wholesalers\Tables;

use App\Filament\Resources\WholesalerPayments\Schemas\WholesalerPaymentForm;
use App\Models\Wholesaler;
use App\Models\WholesalerPayment;
use App\Services\WholesalerDebtService;
use App\Support\NumberFormat;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WholesalersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Wholesaler'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('address')
                    ->label(__('Address'))
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('total_debt')
                    ->label(__('Total Debt'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->badge()
                    ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
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
                Filter::make('with_debt')
                    ->label(__('Has unpaid debt'))
                    ->query(fn (Builder $query): Builder => $query->where('total_debt', '>', 0)),
                Filter::make('no_debt')
                    ->label(__('No debt'))
                    ->query(fn (Builder $query): Builder => $query->where('total_debt', '<=', 0)),
            ])
            ->recordActions([
                Action::make('pay')
                    ->label(__('Pay'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->color('success')
                    ->tooltip(__('Quick payment to this wholesaler'))
                    ->modalHeading(fn (Wholesaler $record): string => __('Pay :name', ['name' => $record->name]))
                    ->modalDescription(fn (Wholesaler $record): string => __('Current debt: :amount AFN', [
                        'amount' => NumberFormat::trim($record->total_debt, 2),
                    ]))
                    ->modalSubmitActionLabel(__('Save Payment'))
                    ->fillForm(fn (Wholesaler $record): array => [
                        'wholesaler_id' => $record->id,
                        'current_debt' => (float) $record->total_debt,
                        'date' => now()->toDateString(),
                    ])
                    ->schema(WholesalerPaymentForm::components())
                    ->action(function (array $data, Wholesaler $record): void {
                        $data['wholesaler_id'] = $record->id;

                        $payment = WholesalerPayment::query()->create($data);

                        WholesalerDebtService::applyPayment($payment);

                        Notification::make()
                            ->title(__('Payment saved'))
                            ->body(__('Debt updated for :name.', ['name' => $record->name]))
                            ->success()
                            ->send();
                    }),
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View wholesaler details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this wholesaler')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this wholesaler')),
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

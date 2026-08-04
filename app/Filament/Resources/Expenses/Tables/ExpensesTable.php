<?php

namespace App\Filament\Resources\Expenses\Tables;

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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Expense'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->sortable()
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->badge()
                    ->color('danger')
                    ->summarize(
                        Sum::make()
                            ->label(__('Total Expenses'))
                            ->money('AFN')
                    ),
                TextColumn::make('date')
                    ->label(__('Expense Date'))
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('today')
                    ->label(__('Today'))
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', now()->toDateString())),
                Filter::make('this_month')
                    ->label(__('This month'))
                    ->query(fn (Builder $query): Builder => $query
                        ->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View expense details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this expense')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this expense')),
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

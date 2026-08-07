<?php

namespace App\Filament\Resources\Stores\Tables;

use App\Enums\StorePlanType;
use App\Models\Store;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Store'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                TextColumn::make('plan_type')
                    ->label(__('Plan'))
                    ->formatStateUsing(fn ($state): string => $state instanceof StorePlanType
                        ? $state->label()
                        : (StorePlanType::tryFrom((string) $state)?->label() ?? __('Grocery')))
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof StorePlanType ? $state->value : $state) {
                        'wholesale' => 'info',
                        'supermarket' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('Users'))
                    ->badge()
                    ->color('info')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function (Store $record, mixed $state): void {
                        Notification::make()
                            ->title($state ? __('Store activated') : __('Store deactivated'))
                            ->body($state
                                ? __(':name can access the panel again.', ['name' => $record->name])
                                : __(':name is blocked until reactivated.', ['name' => $record->name]))
                            ->color($state ? 'success' : 'danger')
                            ->send();
                    })
                    ->tooltip(fn (Store $record): string => $record->is_active
                        ? __('Active — click to deactivate')
                        : __('Inactive — click to activate')),
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
                SelectFilter::make('plan_type')
                    ->label(__('Plan'))
                    ->options(StorePlanType::options()),
                TernaryFilter::make('is_active')
                    ->label(__('Active'))
                    ->placeholder(__('All stores'))
                    ->trueLabel(__('Active only'))
                    ->falseLabel(__('Inactive only')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View store details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this store')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this store')),
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

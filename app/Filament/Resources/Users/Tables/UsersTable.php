<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable()
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->copyable()
                    ->copyMessage(__('Email copied')),
                TextColumn::make('role')
                    ->label(__('Role'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        User::ROLE_SUPER_ADMIN => __('Super Admin'),
                        User::ROLE_ADMIN => __('Admin'),
                        User::ROLE_CASHIER => __('Cashier'),
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        User::ROLE_SUPER_ADMIN => 'danger',
                        User::ROLE_ADMIN => 'warning',
                        User::ROLE_CASHIER => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn (User $record): bool => ! auth()->user()?->can('update', $record))
                    ->tooltip(fn (User $record): string => $record->is_active
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Role'))
                    ->options([
                        User::ROLE_ADMIN => __('Admin'),
                        User::ROLE_CASHIER => __('Cashier'),
                    ]),
                TernaryFilter::make('is_active')
                    ->label(__('Active'))
                    ->placeholder(__('All users'))
                    ->trueLabel(__('Active only'))
                    ->falseLabel(__('Inactive only')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('View'))
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip(__('View user details')),
                    EditAction::make()
                        ->label(__('Edit'))
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip(__('Edit this user')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip(__('Delete this user')),
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

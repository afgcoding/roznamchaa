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
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->copyable()
                    ->copyMessage('Email copied'),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'cashier' => 'Cashier',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'cashier' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn (User $record): bool => ! auth()->user()?->can('update', $record))
                    ->tooltip(fn (User $record): string => $record->is_active
                        ? 'Active — click to deactivate'
                        : 'Inactive — click to activate'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->color('success')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        User::ROLE_ADMIN => 'Admin',
                        User::ROLE_CASHIER => 'Cashier',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All users')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->tooltip('View user details'),
                    EditAction::make()
                        ->label('Edit')
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->color('warning')
                        ->tooltip('Edit this user'),
                    DeleteAction::make()
                        ->label('Delete')
                        ->icon(Heroicon::OutlinedTrash)
                        ->tooltip('Delete this user'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon(Heroicon::OutlinedTrash),
                ]),
            ]);
    }
}

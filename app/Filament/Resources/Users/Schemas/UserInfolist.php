<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Account Details'))
                    ->description(__('Login details and role for this shop user.'))
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Full Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('email')
                            ->label(__('Email Address'))
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->copyable()
                            ->copyMessage(__('Email copied'))
                            ->placeholder('—'),
                        TextEntry::make('role')
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
                            }),
                        IconEntry::make('is_active')
                            ->label(__('Active Account'))
                            ->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)
                            ->falseIcon(Heroicon::OutlinedXCircle)
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->helperText(__('Inactive users cannot log in to the panel.')),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this user was created and last updated.'))
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label(__('Last Updated'))
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

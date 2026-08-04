<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Account Details'))
                    ->description(__('Login details and role for this shop user.'))
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Full Name'))
                            ->placeholder(__('e.g. Ahmad Khan'))
                            ->helperText(__('Name shown in the panel and on sales records.'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        TextInput::make('email')
                            ->label(__('Email Address'))
                            ->placeholder(__('e.g. cashier@shop.com'))
                            ->helperText(__('Used to log in. Must be unique for each user.'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->revealable()
                            ->placeholder(fn (string $operation): string => $operation === 'create'
                                ? __('Enter a secure password')
                                : __('Leave blank to keep current password'))
                            ->helperText(fn (string $operation): string => $operation === 'create'
                                ? __('Password this user will use to log in.')
                                : __('Leave blank to keep the current password. Fill only if you want to change it.'))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),
                        Select::make('role')
                            ->label(__('Role'))
                            ->placeholder(__('Select a role'))
                            ->helperText(__('Admin can manage everything. Cashier handles daily sales.'))
                            ->options([
                                User::ROLE_ADMIN => __('Admin'),
                                User::ROLE_CASHIER => __('Cashier'),
                            ])
                            ->default(User::ROLE_CASHIER)
                            ->native(false)
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('Active Account'))
                            ->helperText(__('Inactive users cannot log in to the panel.'))
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon(Heroicon::Check)
                            ->offIcon(Heroicon::XMark)
                            ->default(true)
                            ->required()
                            ->inline(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

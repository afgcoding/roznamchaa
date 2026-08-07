<?php

namespace App\Filament\Resources\Stores\Schemas;

use App\Enums\StorePlanType;
use App\Models\Store;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Store Details'))
                    ->description(__('Manage shop identity, subscription plan, and activation.'))
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Store Name'))
                            ->placeholder(__('e.g. Ahmad Grocery'))
                            ->helperText(__('Display name for this shop.'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                                if (filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Store::uniqueSlugFor((string) $state));
                            }),
                        TextInput::make('slug')
                            ->label(__('URL Slug'))
                            ->placeholder(__('e.g. ahmad-grocery'))
                            ->helperText(__('Used in /admin/{slug}. Must be unique.'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->dehydrateStateUsing(fn (?string $state): string => Str::slug((string) $state)),
                        Select::make('plan_type')
                            ->label(__('Subscription Plan'))
                            ->options(StorePlanType::options())
                            ->default(StorePlanType::Grocery->value)
                            ->required()
                            ->native(false)
                            ->helperText(__('Upgrade or downgrade features for this store. Grocery is the default starter plan.'))
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label(__('Store Active'))
                            ->helperText(__('When off, store admins and cashiers cannot open this store panel.'))
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon(Heroicon::Check)
                            ->offIcon(Heroicon::XMark)
                            ->default(true)
                            ->required()
                            ->inline(false)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Store Admin Account'))
                    ->description(__('Create the first admin login for this store. They can manage the shop panel.'))
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columnSpanFull()
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->schema([
                        TextInput::make('admin_name')
                            ->label(__('Admin Name'))
                            ->placeholder(__('e.g. Ahmad Khan'))
                            ->helperText(__('Full name of the store owner / admin.'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('admin_email')
                            ->label(__('Admin Email'))
                            ->placeholder(__('e.g. owner@shop.com'))
                            ->helperText(__('Login email for this store admin. Must be unique.'))
                            ->email()
                            ->required()
                            ->unique(table: 'users', column: 'email')
                            ->maxLength(255),
                        TextInput::make('admin_password')
                            ->label(__('Admin Password'))
                            ->password()
                            ->revealable()
                            ->placeholder(__('Enter a secure password'))
                            ->helperText(__('At least 8 characters. The store admin will use this to log in.'))
                            ->required()
                            ->string()
                            ->minLength(8)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

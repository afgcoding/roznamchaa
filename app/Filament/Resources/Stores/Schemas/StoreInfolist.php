<?php

namespace App\Filament\Resources\Stores\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class StoreInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Store Details'))
                    ->description(__('Shop identity, plan, and activation status.'))
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Store Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('slug')
                            ->label(__('URL Slug'))
                            ->badge()
                            ->color('gray')
                            ->copyable()
                            ->copyMessage(__('Slug copied')),
                        TextEntry::make('plan_type')
                            ->label(__('Subscription Plan'))
                            ->formatStateUsing(fn ($state): string => $state?->label() ?? __('Grocery'))
                            ->badge()
                            ->color(fn ($state): string => match ($state?->value ?? $state) {
                                'wholesale' => 'info',
                                'supermarket' => 'success',
                                default => 'gray',
                            }),
                        IconEntry::make('is_active')
                            ->label(__('Active'))
                            ->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)
                            ->falseIcon(Heroicon::OutlinedXCircle)
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->helperText(__('Inactive stores are blocked for shop staff.')),
                        TextEntry::make('users_count')
                            ->label(__('Users'))
                            ->state(fn ($record): int => $record->users->count())
                            ->badge()
                            ->color('info'),
                    ]),

                Section::make(__('Store Users'))
                    ->description(__('People linked to this store who can open the shop panel.'))
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('users')
                            ->label('')
                            ->columnSpanFull()
                            ->columns(4)
                            ->placeholder(__('No users linked to this store yet.'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('Full Name'))
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('—'),
                                TextEntry::make('email')
                                    ->label(__('Email Address'))
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
                                    ->falseColor('danger'),
                            ]),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this store was created and last updated.'))
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

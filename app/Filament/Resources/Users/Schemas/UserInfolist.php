<?php

namespace App\Filament\Resources\Users\Schemas;

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
                Section::make('Account Details')
                    ->description('Login details and role for this shop user.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->copyable()
                            ->copyMessage('Email copied')
                            ->placeholder('—'),
                        TextEntry::make('role')
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
                            }),
                        IconEntry::make('is_active')
                            ->label('Active Account')
                            ->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)
                            ->falseIcon(Heroicon::OutlinedXCircle)
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->helperText('Inactive users cannot log in to the panel.'),
                    ]),

                Section::make('Record Info')
                    ->description('When this user was created and last updated.')
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

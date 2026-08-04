<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Infolists\Components\IconEntry;
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
                    ->description(__('Shop identity and activation status.'))
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
                            ->state(fn ($record): int => $record->users()->count())
                            ->badge()
                            ->color('info'),
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

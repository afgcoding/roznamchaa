<?php

namespace App\Filament\Resources\Stores\Schemas;

use App\Models\Store;
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
                    ->description(__('Manage shop identity and subscription activation.'))
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
            ]);
    }
}

<?php

namespace App\Filament\Resources\Wholesalers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WholesalerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Wholesaler Details'))
                    ->description(__('Supplier info and how much you currently owe them.'))
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Wholesaler Name'))
                            ->placeholder(__('e.g. غله منډۍ احمد'))
                            ->helperText(__('Shop or supplier name you buy stock from.'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        TextInput::make('phone')
                            ->label(__('Phone'))
                            ->placeholder(__('e.g. 0700000000'))
                            ->helperText(__('Mobile number to contact this supplier.'))
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label(__('Address'))
                            ->placeholder(__('e.g. کابل، منډۍ'))
                            ->helperText(__('Optional. Where this wholesaler is located.'))
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('total_debt')
                            ->label(__('Total Debt'))
                            ->placeholder(__('e.g. 15000'))
                            ->helperText(__('How much you currently owe this wholesaler (qarz).'))
                            ->required()
                            ->rule('numeric')
                            ->inputMode('decimal')
                            ->default(0)
                            ->prefix('AFN')
                            ->minValue(0)
                            ->extraInputAttributes([
                                // Block letters/symbols on keypress; numeric rule also validates on save.
                                'x-on:keydown' => 'const k=$event.key; if ($event.ctrlKey||$event.metaKey||$event.altKey||k.length!==1) return; if (!/[0-9.]/.test(k)) $event.preventDefault()',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

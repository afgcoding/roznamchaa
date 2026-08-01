<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Unit Details')
                    ->description('Define how products are measured when buying or selling, like carton, bag, piece, or kilogram.')
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Unit Name')
                            ->placeholder('e.g. کارتن، بوجۍ، دانه، کیلو')
                            ->helperText('Full name of the unit. You can write it in Pashto for shop staff.')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        TextInput::make('short_name')
                            ->label('Short Name')
                            ->placeholder('e.g. ctn, bag, pcs, kg')
                            ->helperText('Short English code used in bills and reports. Keep it lowercase and simple.')
                            ->required()
                            ->maxLength(50)
                            ->scopedUnique(ignoreRecord: true),
                    ]),
            ]);
    }
}

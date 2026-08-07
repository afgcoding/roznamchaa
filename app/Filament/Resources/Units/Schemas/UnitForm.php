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
                Section::make(__('Unit Details'))
                    ->description(__('Define how products are measured when buying or selling, like carton, bag, piece, or kilogram.'))
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema(static::components()),
            ]);
    }

    /**
     * Unit fields — reused by UnitResource and ProductForm create-option modals.
     *
     * @return array<int, TextInput>
     */
    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label(__('Unit Name'))
                ->placeholder(__('e.g. کارتن، بوجۍ، دانه، کیلو'))
                ->helperText(__('Full name of the unit. You can write it in Pashto for shop staff.'))
                ->required()
                ->maxLength(255)
                ->autofocus(),
            TextInput::make('short_name')
                ->label(__('Short Name'))
                ->placeholder(__('e.g. ctn, bag, pcs, kg'))
                ->helperText(__('Short English code used in bills and reports. Keep it lowercase and simple.'))
                ->required()
                ->maxLength(50)
                ->scopedUnique(ignoreRecord: true),
        ];
    }
}

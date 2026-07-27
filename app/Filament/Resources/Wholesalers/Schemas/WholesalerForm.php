<?php

namespace App\Filament\Resources\Wholesalers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WholesalerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('total_debt')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }
}

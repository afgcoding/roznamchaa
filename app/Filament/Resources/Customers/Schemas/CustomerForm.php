<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::components());
    }

    /**
     * Full customer fields — reused by CustomerResource and Sale form create-option modal.
     *
     * @return array<int, TextInput>
     */
    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label('Customer Name')
                ->placeholder('e.g. احمد خان')
                ->helperText('Full name of the customer.')
                ->required()
                ->maxLength(255)
                ->autofocus(),
            TextInput::make('phone')
                ->label('Phone')
                ->placeholder('e.g. 0700000000')
                ->helperText('Mobile number used to contact the customer.')
                ->tel()
                ->required()
                ->maxLength(255),
            TextInput::make('credit_limit')
                ->label('Credit Limit')
                ->placeholder('e.g. 5000')
                ->helperText('Maximum qarz this customer is allowed.')
                ->required()
                ->numeric()
                ->default(0)
                ->prefix('AFN')
                ->minValue(0),
        ];
    }
}

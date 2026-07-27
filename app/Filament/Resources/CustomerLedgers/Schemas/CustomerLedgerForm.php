<?php

namespace App\Filament\Resources\CustomerLedgers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerLedgerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('sale_id')
                    ->relationship('sale', 'id')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label('Related bill'),
                Select::make('type')
                    ->options([
                        'credit' => 'Credit (took goods on debt)',
                        'payment' => 'Payment (paid debt back)',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('AFN'),
                DatePicker::make('date')
                    ->required()
                    ->default(now()),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => auth()->id())
                    ->label('Cashier'),
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label('Customer')
                    ->helperText('Leave empty for walk-in cash sale'),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('AFN'),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('AFN'),
                TextInput::make('payable_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('AFN'),
                TextInput::make('paid_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('AFN'),
                TextInput::make('due_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('AFN'),
                Select::make('payment_status')
                    ->options([
                        'cash' => 'Cash',
                        'credit' => 'Credit',
                        'partial' => 'Partial',
                    ])
                    ->default('cash')
                    ->required(),
            ]);
    }
}

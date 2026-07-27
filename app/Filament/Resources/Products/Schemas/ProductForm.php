<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->unique(ignoreRecord: true),
                Select::make('purchase_unit_id')
                    ->relationship('purchaseUnit', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Purchase unit'),
                Select::make('sale_unit_id')
                    ->relationship('saleUnit', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Sale unit'),
                TextInput::make('unit_conversion')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->helperText('How many sale units in one purchase unit'),
                TextInput::make('cost_price')
                    ->required()
                    ->numeric()
                    ->prefix('AFN'),
                TextInput::make('sale_price')
                    ->required()
                    ->numeric()
                    ->prefix('AFN'),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Stock quantity'),
                TextInput::make('min_stock_alert')
                    ->required()
                    ->numeric()
                    ->default(5)
                    ->label('Min stock alert'),
            ]);
    }
}

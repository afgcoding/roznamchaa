<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Main details that identify this product in your shop.')
                    ->icon(Heroicon::OutlinedCube)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->placeholder('e.g. پیپسي ۱.۵ لیټره، سيلا وریجې')
                            ->helperText('Clear product name so cashiers can find it quickly.')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select a category')
                            ->helperText('Which group this product belongs to, like Oils or Beverages.'),
                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->placeholder('e.g. 8901001001001')
                            ->helperText('Optional. Add barcode if the product has one for scanning.')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Units & Conversion')
                    ->description('How you buy this product and how you sell it to customers.')
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Select::make('purchase_unit_id')
                            ->label('Purchase Unit')
                            ->relationship('purchaseUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('e.g. کارتن، بوجۍ')
                            ->helperText('Unit used when buying from wholesaler.'),
                        Select::make('sale_unit_id')
                            ->label('Sale Unit')
                            ->relationship('saleUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('e.g. دانه، کیلو')
                            ->helperText('Unit used when selling to customers.'),
                        TextInput::make('unit_conversion')
                            ->label('Unit Conversion')
                            ->placeholder('e.g. 12')
                            ->helperText('How many sale units are inside one purchase unit. Example: 1 carton = 12 pieces.')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0.001)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateCostPrice($get, $set);
                                self::recalculateStockQuantity($get, $set);
                            }),
                    ]),

                Section::make('Pricing')
                    ->description('Enter the bag/carton buy price. Cost per sale unit is calculated for you.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchase_unit_price')
                            ->label('Purchase Unit Price')
                            ->placeholder('e.g. 480')
                            ->helperText('Total price of 1 purchase unit (e.g. 1 bag / 1 carton). Not saved to database.')
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->dehydrated(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateCostPrice($get, $set);
                            }),
                        TextInput::make('cost_price')
                            ->label('Cost Price (per sale unit)')
                            ->placeholder('Auto')
                            ->helperText('Calculated: purchase unit price ÷ unit conversion.')
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->readOnly(),
                        TextInput::make('sale_price')
                            ->label('Sale Price')
                            ->placeholder('e.g. 50')
                            ->helperText('What customers pay for one sale unit.')
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0),
                    ]),

                Section::make('Stock')
                    ->description('Enter how many bags/cartons you bought. Stock in sale units is calculated for you.')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchased_stock_units')
                            ->label('Purchased Stock Units')
                            ->placeholder('e.g. 5')
                            ->helperText('Total purchase units added (e.g. 5 bags). Not saved to database.')
                            ->numeric()
                            ->minValue(0)
                            ->dehydrated(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateStockQuantity($get, $set);
                            }),
                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity (sale units)')
                            ->placeholder('Auto')
                            ->helperText('Calculated: purchased stock units × unit conversion.')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->readOnly(),
                        TextInput::make('min_stock_alert')
                            ->label('Min Stock Alert')
                            ->placeholder('e.g. 5')
                            ->helperText('Warn when stock falls to this number or below.')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->minValue(0),
                    ]),
            ]);
    }

    /**
     * cost_price = purchase_unit_price / unit_conversion
     */
    protected static function recalculateCostPrice(callable $get, callable $set): void
    {
        $conversion = (float) $get('unit_conversion');
        $purchaseUnitPrice = $get('purchase_unit_price');

        if ($conversion <= 0 || $purchaseUnitPrice === null || $purchaseUnitPrice === '') {
            return;
        }

        $set('cost_price', round((float) $purchaseUnitPrice / $conversion, 2));
    }

    /**
     * stock_quantity = purchased_stock_units * unit_conversion
     */
    protected static function recalculateStockQuantity(callable $get, callable $set): void
    {
        $conversion = (float) $get('unit_conversion');
        $purchasedUnits = $get('purchased_stock_units');

        if ($conversion <= 0 || $purchasedUnits === null || $purchasedUnits === '') {
            return;
        }

        $set('stock_quantity', round((float) $purchasedUnits * $conversion, 3));
    }
}

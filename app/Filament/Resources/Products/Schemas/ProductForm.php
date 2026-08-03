<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Support\NumberFormat;
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
                Section::make(__('Basic Information'))
                    ->description(__('Main details that identify this product in your shop.'))
                    ->icon(Heroicon::OutlinedCube)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Product Name'))
                            ->placeholder(__('e.g. پیپسي ۱.۵ لیټره، سيلا وریجې'))
                            ->helperText(__('Clear product name so cashiers can find it quickly.'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Select::make('category_id')
                            ->label(__('Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder(__('Select a category'))
                            ->helperText(__('Which group this product belongs to, like Oils or Beverages.')),
                        TextInput::make('barcode')
                            ->label(__('Barcode'))
                            ->placeholder(__('e.g. 8901001001001'))
                            ->helperText(__('Optional. Add barcode if the product has one for scanning.'))
                            ->scopedUnique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Units & Conversion'))
                    ->description(__('How you buy this product and how you sell it to customers.'))
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Select::make('purchase_unit_id')
                            ->label(__('Purchase Unit'))
                            ->relationship('purchaseUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder(__('e.g. کارتن، بوجۍ'))
                            ->helperText(__('Unit used when buying from wholesaler.')),
                        Select::make('sale_unit_id')
                            ->label(__('Sale Unit'))
                            ->relationship('saleUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder(__('e.g. دانه، کیلو'))
                            ->helperText(__('Unit used when selling to customers.')),
                        TextInput::make('unit_conversion')
                            ->label(__('Unit Conversion'))
                            ->placeholder(__('e.g. 12'))
                            ->helperText(__('How many sale units are inside one purchase unit. Example: 1 carton = 12 pieces.'))
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0.001)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 3))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateCostPrice($get, $set);
                                self::recalculateStockQuantity($get, $set);
                            }),
                    ]),

                Section::make(__('Pricing'))
                    ->description(__('Enter the bag/carton buy price. Cost per sale unit is calculated for you.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchase_unit_price')
                            ->label(__('Purchase Unit Price'))
                            ->placeholder(__('e.g. 480'))
                            ->helperText(__('Total price of 1 purchase unit (e.g. 1 bag / 1 carton). Not saved to database.'))
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 2))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateCostPrice($get, $set);
                            }),
                        TextInput::make('cost_price')
                            ->label(__('Cost Price (per sale unit)'))
                            ->placeholder(__('Auto'))
                            ->helperText(__('Calculated: purchase unit price ÷ unit conversion.'))
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 2))
                            ->readOnly(),
                        TextInput::make('sale_price')
                            ->label(__('Sale Price'))
                            ->placeholder(__('e.g. 50'))
                            ->helperText(__('What customers pay for one sale unit.'))
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 2)),
                    ]),

                Section::make(__('Stock'))
                    ->description(__('Enter how many bags/cartons you bought. Stock in sale units is calculated for you.'))
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchased_stock_units')
                            ->label(__('Purchased Stock Units'))
                            ->placeholder(__('e.g. 5'))
                            ->helperText(__('Total purchase units added (e.g. 5 bags). Not saved to database.'))
                            ->numeric()
                            ->minValue(0)
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 3))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateStockQuantity($get, $set);
                            }),
                        TextInput::make('stock_quantity')
                            ->label(__('Stock Quantity (sale units)'))
                            ->placeholder(__('Auto'))
                            ->helperText(__('Calculated: purchased stock units × unit conversion.'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 3))
                            ->readOnly(),
                        TextInput::make('min_stock_alert')
                            ->label(__('Min Stock Alert'))
                            ->placeholder(__('e.g. 5'))
                            ->helperText(__('Warn when stock falls to this number or below.'))
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 0)),
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

        $set('cost_price', NumberFormat::trim((float) $purchaseUnitPrice / $conversion, 2));
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

        $set('stock_quantity', NumberFormat::trim((float) $purchasedUnits * $conversion, 3));
    }

    protected static function trimOrKeep(mixed $state, int $maxDecimals): mixed
    {
        if ($state === null || $state === '') {
            return $state;
        }

        return NumberFormat::trim($state, $maxDecimals);
    }
}

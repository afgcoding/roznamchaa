<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\StoreFeature;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Units\Schemas\UnitForm;
use App\Models\Category;
use App\Models\Unit;
use App\Support\NumberFormat;
use App\Support\StoreFeatures;
use Filament\Actions\Action;
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
                            ->helperText(__('Which group this product belongs to, like Oils or Beverages. Use + to add a new category here.'))
                            ->createOptionForm(CategoryForm::components())
                            ->createOptionUsing(fn (array $data): int => Category::query()->create($data)->getKey())
                            ->createOptionAction(fn (Action $action): Action => $action
                                ->modalHeading(__('New Category'))
                                ->modalDescription(__('Add a category without leaving this product form.'))
                                ->modalSubmitActionLabel(__('Save Category'))
                                ->modalWidth('lg')
                                ->tooltip(__('Add new category'))),
                        TextInput::make('barcode')
                            ->label(__('Barcode'))
                            ->placeholder(__('e.g. 8901001001001'))
                            ->helperText(__('Optional. Add barcode if the product has one for scanning.'))
                            ->scopedUnique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Units & Conversion'))
                    ->description(fn (): string => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)
                        ? __('How you buy this product and how you sell it to customers.')
                        : __('Select the unit used when selling this product.'))
                    ->icon(Heroicon::OutlinedScale)
                    ->columnSpanFull()
                    ->columns(fn (): int => StoreFeatures::enabled(StoreFeature::MultiUnitConversion) ? 3 : 1)
                    ->schema([
                        Select::make('purchase_unit_id')
                            ->label(__('Packaging Unit'))
                            ->relationship('purchaseUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion))
                            ->placeholder(__('e.g. کارتن، بوجۍ'))
                            ->helperText(__('Secondary packaging when buying (carton, box, bag). Use + to add a new unit here.'))
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion))
                            ->dehydrated(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion))
                            ->createOptionForm(UnitForm::components())
                            ->createOptionUsing(fn (array $data): int => Unit::query()->create($data)->getKey())
                            ->createOptionAction(fn (Action $action): Action => $action
                                ->modalHeading(__('New Unit'))
                                ->modalDescription(__('Add a unit without leaving this product form.'))
                                ->modalSubmitActionLabel(__('Save Unit'))
                                ->modalWidth('lg')
                                ->tooltip(__('Add new unit'))),
                        Select::make('sale_unit_id')
                            ->label(__('Sale Unit'))
                            ->relationship('saleUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder(__('e.g. دانه، کیلو'))
                            ->helperText(__('Unit used when selling to customers. Use + to add a new unit here.'))
                            ->createOptionForm(UnitForm::components())
                            ->createOptionUsing(fn (array $data): int => Unit::query()->create($data)->getKey())
                            ->createOptionAction(fn (Action $action): Action => $action
                                ->modalHeading(__('New Unit'))
                                ->modalDescription(__('Add a unit without leaving this product form.'))
                                ->modalSubmitActionLabel(__('Save Unit'))
                                ->modalWidth('lg')
                                ->tooltip(__('Add new unit'))),
                        TextInput::make('unit_conversion')
                            ->label(__('Items per Package'))
                            ->placeholder(__('e.g. 24'))
                            ->helperText(__('How many sale units are inside one packaging unit. Example: 1 carton = 24 pieces.'))
                            ->required(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion))
                            ->numeric()
                            ->default(1)
                            ->minValue(0.001)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 3))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                // Grocery plan enters cost/stock manually — never overwrite those fields.
                                if (StoreFeatures::enabled(StoreFeature::WholesalePricing)) {
                                    self::recalculateCostPrice($get, $set);
                                }

                                if (StoreFeatures::enabled(StoreFeature::MultiUnitConversion)) {
                                    self::recalculateStockQuantity($get, $set);
                                }
                            })
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion))
                            ->dehydrated(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)),
                    ]),

                Section::make(__('Pricing'))
                    ->description(fn (): string => StoreFeatures::enabled(StoreFeature::WholesalePricing)
                        ? __('Enter buy price helpers, retail price, and wholesale price.')
                        : __('Enter cost and sale price per unit.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('purchase_unit_price')
                            ->label(__('Purchase Unit Price'))
                            ->placeholder(__('e.g. 480'))
                            ->helperText(__('Total price of 1 packaging unit (e.g. 1 bag / 1 carton). Not saved to database.'))
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 2))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                if (! StoreFeatures::enabled(StoreFeature::WholesalePricing)) {
                                    return;
                                }

                                self::recalculateCostPrice($get, $set);
                            })
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::WholesalePricing)),
                        TextInput::make('cost_price')
                            ->label(__('Cost Price (per sale unit)'))
                            ->placeholder(fn (): string => StoreFeatures::enabled(StoreFeature::WholesalePricing)
                                ? __('Auto')
                                : __('e.g. 40'))
                            ->helperText(fn (): string => StoreFeatures::enabled(StoreFeature::WholesalePricing)
                                ? __('Calculated: purchase unit price ÷ items per package.')
                                : __('What you paid for one sale unit.'))
                            ->required()
                            // Use text + numeric rule so grocery typing is not blocked by type=number casting.
                            ->rule('numeric')
                            ->inputMode('decimal')
                            ->prefix('AFN')
                            ->minValue(0)
                            // Grocery: manually editable. Wholesale+: auto from purchase unit price.
                            ->readOnly(fn (): bool => StoreFeatures::enabled(StoreFeature::WholesalePricing)),
                        TextInput::make('sale_price')
                            ->label(fn (): string => StoreFeatures::enabled(StoreFeature::WholesalePricing)
                                ? __('Retail Price')
                                : __('Sale Price'))
                            ->placeholder(__('e.g. 50'))
                            ->helperText(fn (): string => StoreFeatures::enabled(StoreFeature::WholesalePricing)
                                ? __('Price for retail / walk-in customers (per sale unit).')
                                : __('What customers pay for one sale unit.'))
                            ->required()
                            ->rule('numeric')
                            ->inputMode('decimal')
                            ->prefix('AFN')
                            ->minValue(0),
                        TextInput::make('wholesale_price')
                            ->label(__('Wholesale Price'))
                            ->placeholder(__('e.g. 45'))
                            ->helperText(__('Price when selling in bulk / to wholesale customers (per sale unit).'))
                            ->rule('numeric')
                            ->inputMode('decimal')
                            ->prefix('AFN')
                            ->minValue(0)
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::WholesalePricing))
                            ->dehydrated(fn (): bool => StoreFeatures::enabled(StoreFeature::WholesalePricing)),
                    ]),

                Section::make(__('Stock'))
                    ->description(fn (): string => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)
                        ? __('Enter how many bags/cartons you bought. Stock in sale units is calculated for you.')
                        : __('Enter current stock in sale units.'))
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->columnSpanFull()
                    ->columns(fn (): int => StoreFeatures::enabled(StoreFeature::MultiUnitConversion) ? 3 : 2)
                    ->schema([
                        TextInput::make('purchased_stock_units')
                            ->label(__('Purchased Stock Units'))
                            ->placeholder(__('e.g. 5'))
                            ->helperText(__('Total packaging units added (e.g. 5 bags). Not saved to database.'))
                            ->numeric()
                            ->minValue(0)
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 3))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                if (! StoreFeatures::enabled(StoreFeature::MultiUnitConversion)) {
                                    return;
                                }

                                self::recalculateStockQuantity($get, $set);
                            })
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)),
                        TextInput::make('stock_quantity')
                            ->label(__('Stock Quantity (sale units)'))
                            ->placeholder(fn (): string => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)
                                ? __('Auto')
                                : __('e.g. 100'))
                            ->helperText(fn (): string => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)
                                ? __('Calculated: purchased stock units × items per package.')
                                : __('How many sale units you currently have.'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => self::trimOrKeep($state, 3))
                            ->readOnly(fn (): bool => StoreFeatures::enabled(StoreFeature::MultiUnitConversion)),
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
        if (! StoreFeatures::enabled(StoreFeature::WholesalePricing)) {
            return;
        }

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
        if (! StoreFeatures::enabled(StoreFeature::MultiUnitConversion)) {
            return;
        }

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

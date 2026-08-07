<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Enums\StoreFeature;
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Models\Customer;
use App\Models\Product;
use App\Support\NumberFormat;
use App\Support\StoreFeatures;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ToggleButtons::make('price_type')
                    ->label(__('Price Type'))
                    ->options([
                        'retail' => __('Retail Price'),
                        'wholesale' => __('Wholesale Price'),
                    ])
                    ->default('retail')
                    ->inline()
                    ->grouped()
                    ->live()
                    ->dehydrated(false)
                    ->helperText(__('Choose retail or wholesale price for this bill. Unit prices update when you change this.'))
                    ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::WholesalePricing))
                    ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                        self::repriceItemsFromPriceType($get, $set);
                    })
                    ->columnSpanFull(),

                Repeater::make('items')
                    ->relationship()
                    ->label(__('Sale Items'))
                    ->helperText(__('Add products sold on this bill. Price fills automatically when you pick a product.'))
                    ->defaultItems(1)
                    ->minItems(1)
                    ->addActionLabel(__('Add product'))
                    ->reorderable(false)
                    ->columnSpanFull()
                    ->columns(4)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                        self::recalculateTotals($get, $set);
                    })
                    ->schema([
                        Select::make('product_id')
                            ->label(__('Product'))
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->placeholder(__('Select product'))
                            ->helperText(fn (): string => StoreFeatures::enabled(StoreFeature::WholesalePricing)
                                ? __('Choose the product. Unit price follows the selected price type (retail / wholesale).')
                                : __('Choose the product the customer is buying. Sale price fills in automatically.'))
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                if (! $state) {
                                    return;
                                }

                                $product = Product::query()->find($state);

                                if (! $product) {
                                    return;
                                }

                                $unitPrice = self::resolveUnitPrice($product, $get('../../price_type'));

                                $set('unit_price', NumberFormat::trim($unitPrice, 2));
                                $set(
                                    'subtotal',
                                    NumberFormat::trim((float) $get('quantity') * $unitPrice, 2)
                                );

                                self::recalculateTotals($get, $set, fromItem: true);
                            }),
                        TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->placeholder(__('e.g. 2'))
                            ->helperText(__('How many sale units (pieces / kg) the customer is buying.'))
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(0.001)
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 3))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                $set(
                                    'subtotal',
                                    NumberFormat::trim((float) $state * (float) $get('unit_price'), 2)
                                );

                                self::recalculateTotals($get, $set, fromItem: true);
                            }),
                        TextInput::make('unit_price')
                            ->label(__('Unit Price'))
                            ->placeholder(__('e.g. 50'))
                            ->helperText(__('Sell price for one sale unit. Auto-filled from the product; you can change it.'))
                            ->numeric()
                            ->required()
                            ->prefix('AFN')
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                $set(
                                    'subtotal',
                                    NumberFormat::trim((float) $get('quantity') * (float) $state, 2)
                                );

                                self::recalculateTotals($get, $set, fromItem: true);
                            }),
                        TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->placeholder(__('Auto'))
                            ->helperText(__('Line total: quantity × unit price. Calculated for you.'))
                            ->numeric()
                            ->required()
                            ->prefix('AFN')
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                            ->readOnly(),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::fillItemCostPrice($data))
                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::fillItemCostPrice($data))
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        foreach (['quantity' => 3, 'unit_price' => 2, 'subtotal' => 2, 'cost_price' => 2] as $field => $decimals) {
                            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                                $data[$field] = NumberFormat::trim($data[$field], $decimals);
                            }
                        }

                        return $data;
                    }),

                Section::make(__('Sale Header'))
                    ->description(__('Customer, payment, and bill totals. Grand total and due update from items.'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id())
                            ->required(),
                        TextInput::make('discount')
                            ->label(__('Discount'))
                            ->placeholder(__('e.g. 50'))
                            ->helperText(__('Amount off the grand total (supermarket discount engine).'))
                            ->numeric()
                            ->default(0)
                            ->prefix('AFN')
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                self::recalculateTotals($get, $set);
                            })
                            ->visible(fn (): bool => StoreFeatures::enabled(StoreFeature::DiscountEngine))
                            ->dehydrated(),
                        Select::make('customer_id')
                            ->label(__('Customer'))
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder(__('Walk-in customer (optional)'))
                            ->helperText(__('Leave empty for walk-in cash sale. Use + to add a new customer here.'))
                            ->createOptionForm(CustomerForm::components())
                            ->createOptionUsing(function (array $data): int {
                                if (! StoreFeatures::enabled(StoreFeature::CreditLimit)) {
                                    $data['credit_limit'] = $data['credit_limit'] ?? 0;
                                }

                                return Customer::query()->create($data)->getKey();
                            })
                            ->createOptionAction(fn (Action $action): Action => $action
                                ->modalHeading(__('New Customer'))
                                ->modalDescription(__('Add a customer without leaving this sale.'))
                                ->modalSubmitActionLabel(__('Save Customer'))
                                ->modalWidth('lg')
                                ->tooltip(__('Add new customer'))),
                        Select::make('payment_status')
                            ->label(__('Payment Status'))
                            ->options([
                                'cash' => __('Cash'),
                                'credit' => __('Credit'),
                                'partial' => __('Partial'),
                            ])
                            ->default('cash')
                            ->required()
                            ->placeholder(__('Select payment type'))
                            ->helperText(__('Cash = fully paid. Credit = unpaid. Partial = paid some.')),
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('paid_amount')
                                    ->label(__('Paid Amount'))
                                    ->placeholder(__('e.g. 1000'))
                                    ->helperText(__('How much the customer paid now.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AFN')
                                    ->minValue(0)
                                    ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                        self::recalculateTotals($get, $set);
                                    }),
                                TextInput::make('total_amount')
                                    ->label(__('Grand Total'))
                                    ->placeholder(__('Auto'))
                                    ->helperText(__('Sum of all item subtotals.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AFN')
                                    ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                                    ->readOnly(),
                                TextInput::make('due_amount')
                                    ->label(__('Due Amount'))
                                    ->placeholder(__('Auto'))
                                    ->helperText(__('Payable − paid amount. Remaining qarz.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AFN')
                                    ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2))
                                    ->readOnly(),
                            ]),
                        Hidden::make('payable_amount')
                            ->default(0),
                    ]),
            ]);
    }

    /**
     * Resolve unit price from product + optional price_type (wholesale plans only).
     * Grocery always uses sale_price.
     */
    protected static function resolveUnitPrice(Product $product, mixed $priceType): float
    {
        if (
            StoreFeatures::enabled(StoreFeature::WholesalePricing)
            && $priceType === 'wholesale'
            && filled($product->wholesale_price)
        ) {
            return (float) $product->wholesale_price;
        }

        return (float) $product->sale_price;
    }

    /**
     * Re-apply unit prices on all line items when price_type changes.
     */
    protected static function repriceItemsFromPriceType(callable $get, callable $set): void
    {
        if (! StoreFeatures::enabled(StoreFeature::WholesalePricing)) {
            return;
        }

        $items = $get('items') ?? [];
        $priceType = $get('price_type');
        $changed = false;

        foreach ($items as $key => $item) {
            if (! is_array($item) || blank($item['product_id'] ?? null)) {
                continue;
            }

            $product = Product::query()->find($item['product_id']);

            if (! $product) {
                continue;
            }

            $unitPrice = self::resolveUnitPrice($product, $priceType);
            $quantity = (float) ($item['quantity'] ?? 0);

            $items[$key]['unit_price'] = NumberFormat::trim($unitPrice, 2);
            $items[$key]['subtotal'] = NumberFormat::trim($quantity * $unitPrice, 2);
            $changed = true;
        }

        if ($changed) {
            $set('items', $items);
        }

        self::recalculateTotals($get, $set);
    }

    /**
     * Ensure sale_items.cost_price is stored from the product.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function fillItemCostPrice(array $data): array
    {
        $product = Product::query()->find($data['product_id'] ?? null);

        $data['cost_price'] = $product?->cost_price ?? ($data['cost_price'] ?? 0);
        $data['subtotal'] = round(
            (float) ($data['quantity'] ?? 0) * (float) ($data['unit_price'] ?? 0),
            2
        );

        return $data;
    }

    /**
     * grand total = sum(qty × unit_price); payable = grand − discount; due = payable − paid.
     * Maps to sales.total_amount / payable_amount / due_amount.
     */
    protected static function recalculateTotals(callable $get, callable $set, bool $fromItem = false): void
    {
        $itemsPath = $fromItem ? '../../items' : 'items';
        $prefix = $fromItem ? '../../' : '';

        $items = $get($itemsPath) ?? [];
        $grandTotal = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $grandTotal += round(
                (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0),
                2
            );
        }

        $grandTotal = round($grandTotal, 2);
        $discount = StoreFeatures::enabled(StoreFeature::DiscountEngine)
            ? max(0, (float) ($get($prefix.'discount') ?? 0))
            : 0;
        $discount = min($discount, $grandTotal);
        $payable = max(0, round($grandTotal - $discount, 2));
        $paid = (float) ($get($prefix.'paid_amount') ?? 0);
        $due = max(0, round($payable - $paid, 2));

        $set($prefix.'total_amount', NumberFormat::trim($grandTotal, 2));
        $set($prefix.'payable_amount', NumberFormat::trim($payable, 2));
        $set($prefix.'discount', NumberFormat::trim($discount, 2));
        $set($prefix.'due_amount', NumberFormat::trim($due, 2));
    }
}

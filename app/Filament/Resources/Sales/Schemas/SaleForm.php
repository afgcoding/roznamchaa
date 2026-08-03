<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Models\Customer;
use App\Models\Product;
use App\Support\NumberFormat;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                            ->helperText(__('Choose the product the customer is buying. Sale price fills in automatically.'))
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                if (! $state) {
                                    return;
                                }

                                $product = Product::query()->find($state);

                                if (! $product) {
                                    return;
                                }

                                $set('unit_price', NumberFormat::trim($product->sale_price, 2));
                                $set(
                                    'subtotal',
                                    NumberFormat::trim((float) $get('quantity') * (float) $product->sale_price, 2)
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
                        Hidden::make('discount')
                            ->default(0),
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
                                    ->helperText(__('Grand total − paid amount. Remaining qarz.'))
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
     * grand total = sum(qty × unit_price); due = grand − paid.
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
        $paid = (float) ($get($prefix.'paid_amount') ?? 0);
        $due = max(0, round($grandTotal - $paid, 2));

        $set($prefix.'total_amount', NumberFormat::trim($grandTotal, 2));
        $set($prefix.'payable_amount', NumberFormat::trim($grandTotal, 2));
        $set($prefix.'discount', 0);
        $set($prefix.'due_amount', NumberFormat::trim($due, 2));
    }
}

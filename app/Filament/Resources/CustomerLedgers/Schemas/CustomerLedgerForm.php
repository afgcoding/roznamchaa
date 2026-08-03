<?php

namespace App\Filament\Resources\CustomerLedgers\Schemas;

use App\Models\Sale;
use App\Support\NumberFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class CustomerLedgerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Ledger Entry'))
                    ->description(__('Record customer qarz (credit) or a payment against qarz.'))
                    ->icon(Heroicon::OutlinedBookOpen)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label(__('Customer'))
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->placeholder(__('Select a customer'))
                            ->helperText(__('Which customer this credit or payment belongs to. Their oldest unpaid bill is selected automatically.'))
                            ->afterStateUpdated(function ($state, callable $set): void {
                                if (! filled($state)) {
                                    $set('sale_id', null);

                                    return;
                                }

                                $oldestUnpaidSaleId = Sale::query()
                                    ->where('customer_id', $state)
                                    ->where('due_amount', '>', 0)
                                    ->orderBy('created_at')
                                    ->orderBy('id')
                                    ->value('id');

                                $set('sale_id', $oldestUnpaidSaleId);
                            }),
                        Select::make('sale_id')
                            ->label(__('Related Bill'))
                            ->relationship(
                                name: 'sale',
                                titleAttribute: 'id',
                                modifyQueryUsing: function (Builder $query, callable $get): Builder {
                                    $customerId = $get('customer_id');

                                    if (! filled($customerId)) {
                                        return $query->whereRaw('0 = 1');
                                    }

                                    return $query
                                        ->where('customer_id', $customerId)
                                        ->where('due_amount', '>', 0)
                                        ->orderBy('created_at')
                                        ->orderBy('id');
                                },
                            )
                            ->getOptionLabelFromRecordUsing(fn (Sale $record): string => $record->ledgerBillLabel())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder(__('No unpaid bill for this customer'))
                            ->helperText(__('Only unpaid bills for the selected customer. Oldest unpaid bill is auto-selected.')),
                        Select::make('type')
                            ->label(__('Entry Type'))
                            ->options([
                                'credit' => __('Credit (took goods on debt)'),
                                'payment' => __('Payment (paid debt back)'),
                            ])
                            ->required()
                            ->placeholder(__('Select type'))
                            ->helperText(__('Credit increases qarz. Payment reduces qarz.')),
                        TextInput::make('amount')
                            ->label(__('Amount'))
                            ->placeholder(__('e.g. 500'))
                            ->helperText(__('How much money for this credit or payment.'))
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0.01)
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2)),
                        DatePicker::make('date')
                            ->label(__('Date'))
                            ->placeholder(__('Pick a date'))
                            ->helperText(__('When this credit or payment happened.'))
                            ->required()
                            ->default(now())
                            ->native(false),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->placeholder(__('e.g. Paid 500 AFN cash against old qarz'))
                            ->helperText(__('Optional note so you remember why this entry was made.'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

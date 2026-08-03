<?php

namespace App\Filament\Resources\WholesalerPayments\Schemas;

use App\Models\Wholesaler;
use App\Support\NumberFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WholesalerPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Payment Details'))
                    ->description(__('Record money paid to a wholesaler against their debt.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema(static::components()),
            ]);
    }

    /**
     * Fields used by the resource form and the Wholesaler table Pay modal.
     *
     * @return array<int, mixed>
     */
    public static function components(): array
    {
        return [
            Hidden::make('current_debt')
                ->dehydrated(false)
                ->default(0),
            Select::make('wholesaler_id')
                ->label(__('Wholesaler'))
                ->options(function (callable $get): array {
                    return Wholesaler::query()
                        ->where(function ($query) use ($get): void {
                            $query->where('total_debt', '>', 0);

                            // Keep the currently selected wholesaler visible when editing.
                            if (filled($get('wholesaler_id'))) {
                                $query->orWhere('id', $get('wholesaler_id'));
                            }
                        })
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (Wholesaler $record): array => [
                            $record->id => __(':name · Debt: :debt AFN', [
                                'name' => $record->name,
                                'debt' => NumberFormat::trim($record->total_debt, 2),
                            ]),
                        ])
                        ->all();
                })
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->placeholder(__('Select a wholesaler with unpaid debt'))
                ->helperText(function (callable $get): string {
                    $wholesalerId = $get('wholesaler_id');

                    if (! filled($wholesalerId)) {
                        return __('Only wholesalers with unpaid debt (total_debt > 0) are listed.');
                    }

                    $debt = NumberFormat::trim(
                        $get('current_debt') ?? Wholesaler::query()->find($wholesalerId)?->total_debt ?? 0,
                        2
                    );

                    return __('Current total debt: :amount AFN', ['amount' => $debt]);
                })
                ->afterStateUpdated(function ($state, callable $set): void {
                    $debt = filled($state)
                        ? (float) (Wholesaler::query()->find($state)?->total_debt ?? 0)
                        : 0;

                    $set('current_debt', $debt);
                }),
            TextInput::make('amount')
                ->label(__('Amount Paid'))
                ->placeholder(__('e.g. 5000'))
                ->helperText(function (callable $get): string {
                    if (! filled($get('wholesaler_id'))) {
                        return __('How much you paid to this wholesaler now.');
                    }

                    $debt = (float) ($get('current_debt') ?? 0);
                    $amount = (float) ($get('amount') ?? 0);
                    $remaining = max(0, round($debt - $amount, 2));

                    return __('Remaining balance after this payment: :amount AFN', [
                        'amount' => NumberFormat::trim($remaining, 2),
                    ]);
                })
                ->required()
                ->numeric()
                ->prefix('AFN')
                ->minValue(0.01)
                ->maxValue(function (callable $get): float {
                    if (filled($get('current_debt'))) {
                        return (float) $get('current_debt');
                    }

                    return (float) (Wholesaler::query()->find($get('wholesaler_id'))?->total_debt ?? 0);
                })
                ->validationMessages([
                    'max' => __('Payment amount cannot exceed the remaining debt.'),
                ])
                ->live(debounce: 300)
                ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2)),
            DatePicker::make('date')
                ->label(__('Payment Date'))
                ->placeholder(__('Pick a date'))
                ->helperText(__('When this payment was made.'))
                ->required()
                ->default(now())
                ->native(false),
            Textarea::make('note')
                ->label(__('Note'))
                ->placeholder(__('e.g. Paid 5000 AFN cash for rice bags'))
                ->helperText(__('Optional note so you remember why this payment was made.'))
                ->rows(3)
                ->columnSpanFull(),
        ];
    }
}

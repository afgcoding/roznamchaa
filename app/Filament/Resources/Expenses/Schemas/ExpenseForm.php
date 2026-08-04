<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Support\NumberFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Expense Details'))
                    ->description(__('Record a shop expense like rent, transport, or utilities.'))
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Expense Title'))
                            ->placeholder(__('e.g. کرایه، برق، ترانسپورت'))
                            ->helperText(__('Short name for this expense so you can find it later.'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        TextInput::make('amount')
                            ->label(__('Amount'))
                            ->placeholder(__('e.g. 2000'))
                            ->helperText(__('How much money was spent.'))
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0.01)
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2)),
                        DatePicker::make('date')
                            ->label(__('Expense Date'))
                            ->placeholder(__('Pick a date'))
                            ->helperText(__('When this expense was paid.'))
                            ->required()
                            ->default(now())
                            ->native(false),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->placeholder(__('e.g. Paid shop rent for this month'))
                            ->helperText(__('Optional note with more details about this expense.'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

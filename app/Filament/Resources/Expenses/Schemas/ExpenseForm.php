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
                Section::make('Expense Details')
                    ->description('Record a shop expense like rent, transport, or utilities.')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->label('Expense Title')
                            ->placeholder('e.g. کرایه، برق، ترانسپورت')
                            ->helperText('Short name for this expense so you can find it later.')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->placeholder('e.g. 2000')
                            ->helperText('How much money was spent.')
                            ->required()
                            ->numeric()
                            ->prefix('AFN')
                            ->minValue(0.01)
                            ->formatStateUsing(fn ($state) => $state === null || $state === '' ? $state : NumberFormat::trim($state, 2)),
                        DatePicker::make('date')
                            ->label('Expense Date')
                            ->placeholder('Pick a date')
                            ->helperText('When this expense was paid.')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('e.g. Paid shop rent for this month')
                            ->helperText('Optional note with more details about this expense.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Support\NumberFormat;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Details')
                    ->description('Shop expense information.')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Expense Title')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('danger'),
                        TextEntry::make('date')
                            ->label('Expense Date')
                            ->date()
                            ->badge()
                            ->color('info'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Record Info')
                    ->description('When this expense was created and last updated.')
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

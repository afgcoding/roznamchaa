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
                Section::make(__('Expense Details'))
                    ->description(__('Shop expense information.'))
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('Expense Title'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('amount')
                            ->label(__('Amount'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('danger'),
                        TextEntry::make('date')
                            ->label(__('Expense Date'))
                            ->date()
                            ->badge()
                            ->color('info'),
                        TextEntry::make('description')
                            ->label(__('Description'))
                            ->placeholder(__('No description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this expense was created and last updated.'))
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label(__('Last Updated'))
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

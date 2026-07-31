<?php

namespace App\Filament\Resources\WholesalerPayments\Schemas;

use App\Support\NumberFormat;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WholesalerPaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->description('Money paid to a wholesaler.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('wholesaler.name')
                            ->label('Wholesaler')
                            ->badge()
                            ->color('primary')
                            ->placeholder('—'),
                        TextEntry::make('amount')
                            ->label('Amount Paid')
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('success'),
                        TextEntry::make('date')
                            ->label('Payment Date')
                            ->date()
                            ->badge()
                            ->color('info'),
                        TextEntry::make('note')
                            ->label('Note')
                            ->placeholder('No note')
                            ->columnSpanFull(),
                    ]),

                Section::make('Record Info')
                    ->description('When this payment was created and last updated.')
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

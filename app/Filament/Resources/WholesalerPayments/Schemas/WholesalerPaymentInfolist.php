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
                Section::make(__('Payment Details'))
                    ->description(__('Money paid to a wholesaler.'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('wholesaler.name')
                            ->label(__('Wholesaler'))
                            ->badge()
                            ->color('primary')
                            ->placeholder('—'),
                        TextEntry::make('amount')
                            ->label(__('Amount Paid'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('success'),
                        TextEntry::make('date')
                            ->label(__('Payment Date'))
                            ->date()
                            ->badge()
                            ->color('info'),
                        TextEntry::make('note')
                            ->label(__('Note'))
                            ->placeholder(__('No note'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this payment was created and last updated.'))
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

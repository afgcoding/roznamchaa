<?php

namespace App\Filament\Resources\CustomerLedgers\Schemas;

use App\Support\NumberFormat;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CustomerLedgerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Ledger Entry'))
                    ->description(__('Credit or payment details for this customer.'))
                    ->icon(Heroicon::OutlinedBookOpen)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('customer.name')
                            ->label(__('Customer'))
                            ->badge()
                            ->color('primary')
                            ->placeholder('—'),
                        TextEntry::make('sale.id')
                            ->label(__('Related Bill'))
                            ->formatStateUsing(function ($state, $record): string {
                                return $record->sale?->ledgerBillLabel() ?? '—';
                            })
                            ->badge()
                            ->color('gray')
                            ->placeholder('—'),
                        TextEntry::make('type')
                            ->label(__('Entry Type'))
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'credit' => __('Credit (took goods on debt)'),
                                'payment' => __('Payment (paid debt back)'),
                                default => $state ?? '—',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'credit' => 'danger',
                                'payment' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('amount')
                            ->label(__('Amount'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color(fn ($record): string => $record->type === 'payment' ? 'success' : 'danger'),
                        TextEntry::make('date')
                            ->label(__('Date'))
                            ->date()
                            ->badge()
                            ->color('info'),
                        TextEntry::make('description')
                            ->label(__('Description'))
                            ->placeholder(__('No description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this ledger entry was created and last updated.'))
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

<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Support\NumberFormat;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Sale Header'))
                    ->description(__('Customer, payment, and bill totals.'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('Cashier'))
                            ->badge()
                            ->color('gray')
                            ->placeholder('—'),
                        TextEntry::make('customer.name')
                            ->label(__('Customer'))
                            ->badge()
                            ->color('gray')
                            ->placeholder(__('Walk-in')),
                        TextEntry::make('payment_status')
                            ->label(__('Payment Status'))
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'cash' => __('Cash'),
                                'credit' => __('Credit'),
                                'partial' => __('Partial'),
                                default => $state ?? '—',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'cash' => 'success',
                                'credit' => 'danger',
                                'partial' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('total_amount')
                            ->label(__('Grand Total'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('info'),
                        TextEntry::make('paid_amount')
                            ->label(__('Paid Amount'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color('success'),
                        TextEntry::make('due_amount')
                            ->label(__('Due Amount'))
                            ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                            ->badge()
                            ->color(fn ($state): string => (float) $state > 0 ? 'danger' : 'success'),
                    ]),

                Section::make(__('Sale Items'))
                    ->description(__('Products on this invoice.'))
                    ->icon(Heroicon::OutlinedShoppingCart)
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->columnSpanFull()
                            ->columns(4)
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('Product'))
                                    ->placeholder('—'),
                                TextEntry::make('quantity')
                                    ->label(__('Quantity'))
                                    ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 3)),
                                TextEntry::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2)),
                                TextEntry::make('subtotal')
                                    ->label(__('Subtotal'))
                                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ]),

                Section::make(__('Record Info'))
                    ->description(__('When this invoice was created and last updated.'))
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

<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('name'),
                TextEntry::make('barcode')
                    ->placeholder('-'),
                TextEntry::make('purchaseUnit.name')
                    ->label('Purchase unit'),
                TextEntry::make('saleUnit.name')
                    ->label('Sale unit'),
                TextEntry::make('unit_conversion')
                    ->numeric(),
                TextEntry::make('cost_price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money(),
                TextEntry::make('stock_quantity')
                    ->numeric(),
                TextEntry::make('min_stock_alert')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

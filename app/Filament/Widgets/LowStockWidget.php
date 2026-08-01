<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Support\NumberFormat;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Low Stock Products';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getLowStockQuery())
            ->heading('Low Stock Products')
            ->description('Products at or below their minimum stock alert.')
            ->emptyStateHeading('Stock looks good')
            ->emptyStateDescription('No products are below their minimum threshold.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->formatStateUsing(function ($state, Product $record): string {
                        $qty = NumberFormat::trim($state, 3);
                        $unit = $record->saleUnit?->short_name;

                        return $unit ? "{$qty} {$unit}" : $qty;
                    })
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('min_stock_alert')
                    ->label('Min Alert')
                    ->formatStateUsing(fn ($state): string => NumberFormat::trim($state, 0))
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('Sale Price')
                    ->formatStateUsing(fn ($state): string => 'AFN '.NumberFormat::trim($state, 2))
                    ->visible(fn (): bool => (bool) auth()->user()?->isAdmin())
                    ->toggleable(),
            ])
            ->defaultSort('stock_quantity')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->recordUrl(function (Product $record): ?string {
                if (! auth()->user()?->isAdmin()) {
                    return null;
                }

                return ProductResource::getUrl('view', ['record' => $record]);
            })
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->url(fn (Product $record): string => ProductResource::getUrl('view', ['record' => $record]))
                    ->visible(fn (): bool => (bool) auth()->user()?->isAdmin()),
            ]);
    }

    /**
     * @return Builder<Product>
     */
    protected function getLowStockQuery(): Builder
    {
        return Product::query()
            ->with(['category', 'saleUnit'])
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }
}

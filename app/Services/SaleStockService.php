<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaleStockService
{
    /**
     * Deduct sold quantities from product stock.
     *
     * @param  Collection<int, SaleItem|array{product_id: mixed, quantity: mixed}>  $items
     */
    public static function deduct(Collection $items): void
    {
        foreach ($items as $item) {
            $productId = data_get($item, 'product_id');
            $quantity = (float) data_get($item, 'quantity');

            if (! $productId || $quantity <= 0) {
                continue;
            }

            Product::query()
                ->whereKey($productId)
                ->decrement('stock_quantity', $quantity);
        }
    }

    /**
     * Put sold quantities back into product stock.
     *
     * @param  Collection<int, SaleItem|array{product_id: mixed, quantity: mixed}>  $items
     */
    public static function restore(Collection $items): void
    {
        foreach ($items as $item) {
            $productId = data_get($item, 'product_id');
            $quantity = (float) data_get($item, 'quantity');

            if (! $productId || $quantity <= 0) {
                continue;
            }

            Product::query()
                ->whereKey($productId)
                ->increment('stock_quantity', $quantity);
        }
    }

    /**
     * Restore previous lines, then deduct current lines (edit sync).
     *
     * @param  Collection<int, SaleItem>  $previousItems
     * @param  Collection<int, SaleItem>  $currentItems
     */
    public static function sync(Collection $previousItems, Collection $currentItems): void
    {
        DB::transaction(function () use ($previousItems, $currentItems): void {
            static::restore($previousItems);
            static::deduct($currentItems);
        });
    }

    /**
     * Restore stock for every line on a sale before it is deleted.
     */
    public static function restoreForSale(Sale $sale): void
    {
        static::restore($sale->items()->get(['product_id', 'quantity']));
    }
}

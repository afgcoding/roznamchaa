<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToStore;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_conversion' => 'decimal:3',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'stock_quantity' => 'decimal:3',
            'min_stock_alert' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            // Grocery (single-unit) plans may omit purchase unit / conversion in the form.
            if (blank($product->purchase_unit_id) && filled($product->sale_unit_id)) {
                $product->purchase_unit_id = $product->sale_unit_id;
            }

            if (blank($product->unit_conversion) || (float) $product->unit_conversion <= 0) {
                $product->unit_conversion = 1;
            }
        });
    }

    /**
     * Category of this product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Unit used when purchasing (e.g. carton, bag).
     */
    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    /**
     * Unit used when selling (e.g. piece, kg).
     */
    public function saleUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'sale_unit_id');
    }

    /**
     * Sale line items for this product.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * True when this product appears on any sale bill.
     */
    public function isUsedInSales(): bool
    {
        return $this->saleItems()->exists();
    }
}

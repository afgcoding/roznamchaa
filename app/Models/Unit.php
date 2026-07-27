<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    /**
     * Products that use this unit for purchase.
     */
    public function purchaseProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'purchase_unit_id');
    }

    /**
     * Products that use this unit for sale.
     */
    public function saleProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'sale_unit_id');
    }
}

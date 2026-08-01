<?php

namespace App\Models\Concerns;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Store;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        static::creating(function (Model $model): void {
            if (filled($model->getAttribute('store_id'))) {
                return;
            }

            // Nested sale lines often inherit store from parent sale.
            if ($model instanceof SaleItem && filled($model->getAttribute('sale_id'))) {
                $saleStoreId = Sale::query()
                    ->whereKey($model->getAttribute('sale_id'))
                    ->value('store_id');

                if (filled($saleStoreId)) {
                    $model->setAttribute('store_id', $saleStoreId);

                    return;
                }
            }

            $tenant = Filament::getTenant();

            if ($tenant instanceof Store) {
                $model->setAttribute('store_id', $tenant->getKey());
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}

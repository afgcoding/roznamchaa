<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wholesaler extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_debt' => 'decimal:2',
        ];
    }

    /**
     * Payments made to this wholesaler.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(WholesalerPayment::class);
    }
}

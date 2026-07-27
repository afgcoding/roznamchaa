<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
        ];
    }

    /**
     * Sales linked to this customer.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Credit and payment ledger entries.
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(CustomerLedger::class);
    }

    /**
     * Remaining unpaid credit (qarz): credits - payments.
     */
    public function getTotalDueAttribute(): float
    {
        $credits = (float) $this->ledgers()->where('type', 'credit')->sum('amount');
        $payments = (float) $this->ledgers()->where('type', 'payment')->sum('amount');

        return max(0, $credits - $payments);
    }

    /**
     * Customers with active unpaid credit (total_due > 0).
     */
    public function scopeWithUnpaidDebt(Builder $query): Builder
    {
        return $query->whereRaw($this->totalDueSql().' > 0');
    }

    /**
     * Customers whose unpaid credit is over their credit limit.
     */
    public function scopeOverCreditLimit(Builder $query): Builder
    {
        return $query->whereRaw($this->totalDueSql().' > customers.credit_limit');
    }

    protected function totalDueSql(): string
    {
        return '(
            COALESCE((SELECT SUM(amount) FROM customer_ledgers WHERE customer_id = customers.id AND type = \'credit\'), 0)
            - COALESCE((SELECT SUM(amount) FROM customer_ledgers WHERE customer_id = customers.id AND type = \'payment\'), 0)
        )';
    }
}

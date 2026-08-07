<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
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
     * Live unpaid balance (all plans):
     * open invoice dues + standalone credits − standalone payments.
     *
     * Sale-linked ledger credits are excluded so they are not double-counted
     * with sales.due_amount (those rows are kept for ledger history).
     * Sale-linked payments are also excluded because they already reduce sales.due_amount.
     */
    public function getTotalDueAttribute(): float
    {
        $invoiceDue = (float) $this->sales()->sum('due_amount');

        $standaloneCredits = (float) $this->ledgers()
            ->where('type', 'credit')
            ->whereNull('sale_id')
            ->sum('amount');

        $standalonePayments = (float) $this->ledgers()
            ->where('type', 'payment')
            ->whereNull('sale_id')
            ->sum('amount');

        return max(0, round($invoiceDue + $standaloneCredits - $standalonePayments, 2));
    }

    /**
     * Customers with active unpaid credit (total_due > 0).
     */
    public function scopeWithUnpaidDebt(Builder $query): Builder
    {
        return $query->whereRaw(static::totalDueSql().' > 0');
    }

    /**
     * Customers whose unpaid credit is over their credit limit.
     */
    public function scopeOverCreditLimit(Builder $query): Builder
    {
        return $query->whereRaw(static::totalDueSql().' > customers.credit_limit');
    }

    public static function totalDueSql(): string
    {
        return '(
            COALESCE((
                SELECT SUM(due_amount) FROM sales
                WHERE sales.customer_id = customers.id
            ), 0)
            + COALESCE((
                SELECT SUM(amount) FROM customer_ledgers
                WHERE customer_ledgers.customer_id = customers.id
                  AND customer_ledgers.type = \'credit\'
                  AND customer_ledgers.sale_id IS NULL
            ), 0)
            - COALESCE((
                SELECT SUM(amount) FROM customer_ledgers
                WHERE customer_ledgers.customer_id = customers.id
                  AND customer_ledgers.type = \'payment\'
                  AND customer_ledgers.sale_id IS NULL
            ), 0)
        )';
    }
}

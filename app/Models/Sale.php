<?php

namespace App\Models;

use App\Support\NumberFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'payable_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
        ];
    }

    /**
     * Label used in ledger forms/tables, e.g. #INV-1002 - 2026-07-29 - Due: 220 AFN
     */
    public function ledgerBillLabel(): string
    {
        $date = $this->created_at?->format('Y-m-d') ?? '—';
        $due = NumberFormat::trim($this->due_amount, 2);

        return "#INV-{$this->id} - {$date} - Due: {$due} AFN";
    }

    /**
     * Cashier/admin who created this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Customer (nullable for pure cash walk-in sales).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Line items inside this sale/bill.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Ledger entries linked to this sale (credit debt).
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(CustomerLedger::class);
    }
}

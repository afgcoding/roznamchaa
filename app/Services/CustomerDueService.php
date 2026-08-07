<?php

namespace App\Services;

use App\Models\CustomerLedger;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CustomerDueService
{
    protected static bool $syncing = false;

    /**
     * Keep the sale-linked credit ledger in sync with the invoice due amount.
     * Runs for every store plan (Grocery / Wholesale / Supermarket).
     */
    public static function syncSaleCreditLedger(Sale $sale): void
    {
        if (static::$syncing) {
            return;
        }

        static::$syncing = true;

        try {
            $sale->refresh();

            $customerId = $sale->customer_id;
            $due = round((float) $sale->due_amount, 2);

            $existing = CustomerLedger::query()
                ->where('sale_id', $sale->getKey())
                ->where('type', 'credit')
                ->first();

            if (! filled($customerId) || $due <= 0) {
                $existing?->delete();

                return;
            }

            $payload = [
                'customer_id' => $customerId,
                'sale_id' => $sale->getKey(),
                'type' => 'credit',
                'amount' => $due,
                'date' => $sale->created_at?->toDateString() ?? now()->toDateString(),
                'description' => __('Credit from invoice #:id', ['id' => $sale->getKey()]),
                'store_id' => $sale->store_id,
            ];

            if ($existing) {
                $existing->fill([
                    'customer_id' => $payload['customer_id'],
                    'amount' => $payload['amount'],
                    'store_id' => $payload['store_id'] ?? $existing->store_id,
                ])->save();

                return;
            }

            CustomerLedger::query()->create($payload);
        } finally {
            static::$syncing = false;
        }
    }

    /**
     * Remove sale-linked ledger rows when a sale is deleted
     * (credits + payments), so orphaned payments do not skew Total Due.
     */
    public static function deleteSaleCreditLedger(Sale $sale): void
    {
        if (static::$syncing) {
            return;
        }

        static::$syncing = true;

        try {
            CustomerLedger::query()
                ->where('sale_id', $sale->getKey())
                ->delete();
        } finally {
            static::$syncing = false;
        }
    }

    /**
     * Apply a payment ledger entry onto its related sale invoice.
     */
    public static function applyPaymentLedger(CustomerLedger $ledger): void
    {
        if (static::$syncing || $ledger->type !== 'payment' || blank($ledger->sale_id)) {
            return;
        }

        static::adjustSaleSettlement((int) $ledger->sale_id, (float) $ledger->amount);
    }

    /**
     * Reverse a payment ledger entry from its related sale invoice.
     */
    public static function reversePaymentLedger(CustomerLedger $ledger): void
    {
        if (static::$syncing || $ledger->type !== 'payment' || blank($ledger->sale_id)) {
            return;
        }

        static::adjustSaleSettlement((int) $ledger->sale_id, -1 * (float) $ledger->amount);
    }

    /**
     * Sync sale settlement when a payment ledger is edited.
     */
    public static function syncPaymentLedgerEdit(
        ?int $previousSaleId,
        string $previousType,
        float $previousAmount,
        CustomerLedger $ledger,
    ): void {
        if (static::$syncing) {
            return;
        }

        DB::transaction(function () use ($previousSaleId, $previousType, $previousAmount, $ledger): void {
            if ($previousType === 'payment' && filled($previousSaleId)) {
                static::adjustSaleSettlement($previousSaleId, -1 * $previousAmount);
            }

            if ($ledger->type === 'payment' && filled($ledger->sale_id)) {
                static::adjustSaleSettlement((int) $ledger->sale_id, (float) $ledger->amount);
            }
        });
    }

    protected static function adjustSaleSettlement(int $saleId, float $deltaPaid): void
    {
        if ($saleId <= 0 || abs($deltaPaid) < 0.00001) {
            return;
        }

        static::$syncing = true;

        try {
            $sale = Sale::query()->find($saleId);

            if (! $sale) {
                return;
            }

            $payable = round((float) $sale->payable_amount, 2);
            $paid = max(0, round((float) $sale->paid_amount + $deltaPaid, 2));
            $paid = min($paid, $payable);
            $due = max(0, round($payable - $paid, 2));

            $status = match (true) {
                $due <= 0 => 'cash',
                $paid > 0 => 'partial',
                default => 'credit',
            };

            $sale->forceFill([
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $status,
            ])->save();

            // Keep the sale-linked credit row aligned with remaining due.
            static::$syncing = false;
            static::syncSaleCreditLedger($sale);
        } finally {
            static::$syncing = false;
        }
    }
}

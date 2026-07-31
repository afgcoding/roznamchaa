<?php

namespace App\Services;

use App\Models\Wholesaler;
use App\Models\WholesalerPayment;
use Illuminate\Support\Facades\DB;

class WholesalerDebtService
{
    /**
     * Reduce wholesaler debt by the paid amount (never below zero).
     */
    public static function deduct(Wholesaler|int $wholesaler, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $model = $wholesaler instanceof Wholesaler
            ? $wholesaler
            : Wholesaler::query()->find($wholesaler);

        if (! $model) {
            return;
        }

        $model->update([
            'total_debt' => max(0, round((float) $model->total_debt - $amount, 2)),
        ]);
    }

    /**
     * Put paid amount back onto wholesaler debt.
     */
    public static function restore(Wholesaler|int $wholesaler, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $model = $wholesaler instanceof Wholesaler
            ? $wholesaler
            : Wholesaler::query()->find($wholesaler);

        if (! $model) {
            return;
        }

        $model->update([
            'total_debt' => round((float) $model->total_debt + $amount, 2),
        ]);
    }

    /**
     * Apply debt change for a newly created payment.
     */
    public static function applyPayment(WholesalerPayment $payment): void
    {
        static::deduct($payment->wholesaler_id, (float) $payment->amount);
    }

    /**
     * Sync debt when a payment is edited (amount / wholesaler may change).
     */
    public static function syncEdit(
        int $previousWholesalerId,
        float $previousAmount,
        WholesalerPayment $payment,
    ): void {
        DB::transaction(function () use ($previousWholesalerId, $previousAmount, $payment): void {
            static::restore($previousWholesalerId, $previousAmount);
            static::deduct($payment->wholesaler_id, (float) $payment->amount);
        });
    }

    /**
     * Undo debt change when a payment is deleted.
     */
    public static function reversePayment(WholesalerPayment $payment): void
    {
        static::restore($payment->wholesaler_id, (float) $payment->amount);
    }
}

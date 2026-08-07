<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Services\CustomerDueService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLedger extends Model
{
    use BelongsToStore;

    protected ?int $dueSyncPreviousSaleId = null;

    protected ?string $dueSyncPreviousType = null;

    protected ?float $dueSyncPreviousAmount = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (CustomerLedger $ledger): void {
            CustomerDueService::applyPaymentLedger($ledger);
        });

        static::updating(function (CustomerLedger $ledger): void {
            $ledger->dueSyncPreviousSaleId = $ledger->getOriginal('sale_id');
            $ledger->dueSyncPreviousType = (string) $ledger->getOriginal('type');
            $ledger->dueSyncPreviousAmount = (float) $ledger->getOriginal('amount');
        });

        static::updated(function (CustomerLedger $ledger): void {
            CustomerDueService::syncPaymentLedgerEdit(
                $ledger->dueSyncPreviousSaleId,
                $ledger->dueSyncPreviousType ?? 'payment',
                $ledger->dueSyncPreviousAmount ?? 0,
                $ledger,
            );
        });

        static::deleting(function (CustomerLedger $ledger): void {
            CustomerDueService::reversePaymentLedger($ledger);
        });
    }

    /**
     * Customer this ledger entry belongs to.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Related sale (when type is credit from a bill).
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}

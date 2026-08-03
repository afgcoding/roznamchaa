<?php

namespace App\Filament\Resources\Products\Concerns;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

trait ProtectsProductsUsedInSales
{
    public static function protectDeleteAction(DeleteAction $action): DeleteAction
    {
        return $action
            ->before(function (Action $action, Product $record): void {
                if (! $record->isUsedInSales()) {
                    return;
                }

                Notification::make()
                    ->title(__('Cannot delete this product'))
                    ->body(__('This product is used in sales invoices. Deleting it would break bill history.'))
                    ->danger()
                    ->send();

                $action->cancel();
            });
    }

    public static function protectDeleteBulkAction(DeleteBulkAction $action): DeleteBulkAction
    {
        return $action
            ->before(function (DeleteBulkAction $action, SupportCollection|Collection $records): void {
                $blocked = $records->filter(
                    fn (Product $record): bool => $record->isUsedInSales()
                );

                if ($blocked->isEmpty()) {
                    return;
                }

                Notification::make()
                    ->title(__('Cannot delete selected products'))
                    ->body(__(':count selected product(s) are used in sales invoices and cannot be deleted.', ['count' => $blocked->count()]))
                    ->danger()
                    ->send();

                $action->cancel();
            });
    }
}

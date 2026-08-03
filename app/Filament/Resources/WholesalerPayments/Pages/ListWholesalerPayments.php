<?php

namespace App\Filament\Resources\WholesalerPayments\Pages;

use App\Filament\Resources\WholesalerPayments\WholesalerPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListWholesalerPayments extends ListRecords
{
    protected static string $resource = WholesalerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('New Payment'))
                ->icon(Heroicon::OutlinedPlusCircle)
                ->tooltip(__('Record a payment to a wholesaler')),
        ];
    }
}

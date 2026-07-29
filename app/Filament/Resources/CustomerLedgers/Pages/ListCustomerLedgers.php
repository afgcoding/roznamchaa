<?php

namespace App\Filament\Resources\CustomerLedgers\Pages;

use App\Filament\Resources\CustomerLedgers\CustomerLedgerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListCustomerLedgers extends ListRecords
{
    protected static string $resource = CustomerLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Ledger Entry')
                ->icon(Heroicon::OutlinedPlusCircle)
                ->tooltip('Add a credit or payment entry'),
        ];
    }
}

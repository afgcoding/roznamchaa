<?php

namespace App\Filament\Resources\CustomerLedgers\Pages;

use App\Filament\Resources\CustomerLedgers\CustomerLedgerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerLedger extends CreateRecord
{
    protected static string $resource = CustomerLedgerResource::class;
}

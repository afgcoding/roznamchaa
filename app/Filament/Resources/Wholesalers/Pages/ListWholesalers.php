<?php

namespace App\Filament\Resources\Wholesalers\Pages;

use App\Filament\Resources\Wholesalers\WholesalerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListWholesalers extends ListRecords
{
    protected static string $resource = WholesalerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('New Wholesaler'))
                ->icon(Heroicon::OutlinedPlusCircle)
                ->tooltip(__('Add a new wholesaler / supplier')),
        ];
    }
}

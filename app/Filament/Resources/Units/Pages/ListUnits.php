<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('New Unit'))
                ->icon(Heroicon::OutlinedPlusCircle)
                ->tooltip(__('Add a new measuring unit')),
        ];
    }
}

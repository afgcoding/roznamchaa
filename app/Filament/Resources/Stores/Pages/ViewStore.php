<?php

namespace App\Filament\Resources\Stores\Pages;

use App\Filament\Resources\Stores\StoreResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewStore extends ViewRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to stores list')),
            EditAction::make()
                ->label(__('Edit'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->tooltip(__('Edit this store')),
            DeleteAction::make()
                ->label(__('Delete'))
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip(__('Delete this store')),
        ];
    }
}

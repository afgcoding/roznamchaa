<?php

namespace App\Filament\Resources\Wholesalers\Pages;

use App\Filament\Resources\Wholesalers\WholesalerResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewWholesaler extends ViewRecord
{
    protected static string $resource = WholesalerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip('Return to wholesalers list'),
            EditAction::make()
                ->label('Edit')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->tooltip('Edit this wholesaler'),
            DeleteAction::make()
                ->label('Delete')
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip('Delete this wholesaler'),
        ];
    }
}

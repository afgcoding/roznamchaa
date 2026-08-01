<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip('Return to users list'),
            EditAction::make()
                ->label('Edit')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->tooltip('Edit this user'),
            DeleteAction::make()
                ->label('Delete')
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip('Delete this user'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to users list')),
            ViewAction::make()
                ->label(__('View'))
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->tooltip(__('View user details')),
            DeleteAction::make()
                ->label(__('Delete'))
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip(__('Delete this user')),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label(__('Update User'))
            ->icon(Heroicon::OutlinedCheckCircle);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label(__('Cancel'))
            ->icon(Heroicon::OutlinedXMark);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

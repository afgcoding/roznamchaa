<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to units list')),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('Save Unit'))
            ->icon(Heroicon::OutlinedCheckCircle);
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label(__('Save & Add Another'))
            ->icon(Heroicon::OutlinedPlus);
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

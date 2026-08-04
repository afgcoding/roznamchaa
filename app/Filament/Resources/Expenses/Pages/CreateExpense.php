<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to expenses list')),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('Save Expense'))
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

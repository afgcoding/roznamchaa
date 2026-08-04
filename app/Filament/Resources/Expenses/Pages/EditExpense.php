<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use App\Support\NumberFormat;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditExpense extends EditRecord
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
            ViewAction::make()
                ->label(__('View'))
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->tooltip(__('View expense details')),
            DeleteAction::make()
                ->label(__('Delete'))
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip(__('Delete this expense')),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label(__('Update Expense'))
            ->icon(Heroicon::OutlinedCheckCircle);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label(__('Cancel'))
            ->icon(Heroicon::OutlinedXMark);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (array_key_exists('amount', $data) && $data['amount'] !== null && $data['amount'] !== '') {
            $data['amount'] = NumberFormat::trim($data['amount'], 2);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

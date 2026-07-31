<?php

namespace App\Filament\Resources\WholesalerPayments\Pages;

use App\Filament\Resources\WholesalerPayments\WholesalerPaymentResource;
use App\Services\WholesalerDebtService;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateWholesalerPayment extends CreateRecord
{
    protected static string $resource = WholesalerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip('Return to wholesaler payments list'),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Save Payment')
            ->icon(Heroicon::OutlinedCheckCircle);
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Save & Add Another')
            ->icon(Heroicon::OutlinedPlus);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancel')
            ->icon(Heroicon::OutlinedXMark);
    }

    protected function afterCreate(): void
    {
        WholesalerDebtService::applyPayment($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

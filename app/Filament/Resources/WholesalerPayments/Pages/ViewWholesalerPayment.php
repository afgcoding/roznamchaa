<?php

namespace App\Filament\Resources\WholesalerPayments\Pages;

use App\Filament\Resources\WholesalerPayments\WholesalerPaymentResource;
use App\Services\WholesalerDebtService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewWholesalerPayment extends ViewRecord
{
    protected static string $resource = WholesalerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to wholesaler payments list')),
            EditAction::make()
                ->label(__('Edit'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->tooltip(__('Edit this payment')),
            DeleteAction::make()
                ->label(__('Delete'))
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip(__('Delete this payment'))
                ->before(function (): void {
                    WholesalerDebtService::reversePayment($this->record);
                }),
        ];
    }
}

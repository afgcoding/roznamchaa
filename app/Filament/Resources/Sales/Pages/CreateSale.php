<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Enums\StoreFeature;
use App\Filament\Resources\Sales\SaleResource;
use App\Services\SaleStockService;
use App\Support\StoreFeatures;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to sales invoices list')),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('Save Invoice'))
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! StoreFeatures::enabled(StoreFeature::DiscountEngine)) {
            $data['discount'] = 0;
            $data['payable_amount'] = $data['total_amount'] ?? $data['payable_amount'] ?? 0;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        SaleStockService::deduct(
            $this->record->items()->get(['product_id', 'quantity'])
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

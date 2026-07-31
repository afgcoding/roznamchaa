<?php

namespace App\Filament\Resources\WholesalerPayments\Pages;

use App\Filament\Resources\WholesalerPayments\WholesalerPaymentResource;
use App\Services\WholesalerDebtService;
use App\Support\NumberFormat;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditWholesalerPayment extends EditRecord
{
    protected static string $resource = WholesalerPaymentResource::class;

    protected ?int $previousWholesalerId = null;

    protected ?float $previousAmount = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip('Return to wholesaler payments list'),
            ViewAction::make()
                ->label('View')
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->tooltip('View payment details'),
            DeleteAction::make()
                ->label('Delete')
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip('Delete this payment')
                ->before(function (): void {
                    WholesalerDebtService::reversePayment($this->record);
                }),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Update Payment')
            ->icon(Heroicon::OutlinedCheckCircle);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancel')
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

        if (! empty($data['wholesaler_id'])) {
            // For remaining-balance helper: show debt as if this payment were not yet applied.
            $currentDebt = (float) (\App\Models\Wholesaler::query()->find($data['wholesaler_id'])?->total_debt ?? 0);
            $data['current_debt'] = $currentDebt + (float) ($data['amount'] ?? 0);
        }

        return $data;
    }

    protected function beforeSave(): void
    {
        $this->previousWholesalerId = (int) $this->record->wholesaler_id;
        $this->previousAmount = (float) $this->record->amount;
    }

    protected function afterSave(): void
    {
        WholesalerDebtService::syncEdit(
            $this->previousWholesalerId ?? (int) $this->record->wholesaler_id,
            $this->previousAmount ?? (float) $this->record->amount,
            $this->record->fresh(),
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\Concerns\ProtectsProductsUsedInSales;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditProduct extends EditRecord
{
    use ProtectsProductsUsedInSales;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip('Return to products list'),
            ViewAction::make()
                ->label('View')
                ->icon(Heroicon::OutlinedEye)
                ->color('info')
                ->tooltip('View product details'),
            static::protectDeleteAction(
                DeleteAction::make()
                    ->label('Delete')
                    ->icon(Heroicon::OutlinedTrash)
                    ->tooltip('Delete this product')
            ),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Update Product')
            ->icon(Heroicon::OutlinedCheckCircle);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancel')
            ->icon(Heroicon::OutlinedXMark);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Prefill form-only helper fields from saved cost/stock + conversion.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $conversion = (float) ($data['unit_conversion'] ?? 0);

        if ($conversion > 0) {
            $data['purchase_unit_price'] = round((float) ($data['cost_price'] ?? 0) * $conversion, 2);
            $data['purchased_stock_units'] = round((float) ($data['stock_quantity'] ?? 0) / $conversion, 3);
        }

        return $data;
    }
}

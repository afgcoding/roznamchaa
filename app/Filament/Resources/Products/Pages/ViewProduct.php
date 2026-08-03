<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\Concerns\ProtectsProductsUsedInSales;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewProduct extends ViewRecord
{
    use ProtectsProductsUsedInSales;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to products list')),
            EditAction::make()
                ->label(__('Edit'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->tooltip(__('Edit this product')),
            static::protectDeleteAction(
                DeleteAction::make()
                    ->label(__('Delete'))
                    ->icon(Heroicon::OutlinedTrash)
                    ->tooltip(__('Delete this product'))
            ),
        ];
    }
}

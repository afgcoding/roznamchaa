<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewExpense extends ViewRecord
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
            EditAction::make()
                ->label(__('Edit'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->tooltip(__('Edit this expense')),
            DeleteAction::make()
                ->label(__('Delete'))
                ->icon(Heroicon::OutlinedTrash)
                ->tooltip(__('Delete this expense')),
        ];
    }
}

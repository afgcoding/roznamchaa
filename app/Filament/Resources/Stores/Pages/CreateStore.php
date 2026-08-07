<?php

namespace App\Filament\Resources\Stores\Pages;

use App\Filament\Resources\Stores\StoreResource;
use App\Models\Store;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to List'))
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->tooltip(__('Return to stores list')),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('Save Store'))
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
     * Create the store and its admin user in one transaction.
     * Admin fields are form-only (not columns on stores).
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $adminName = (string) $data['admin_name'];
            $adminEmail = (string) $data['admin_email'];
            $adminPassword = (string) $data['admin_password'];

            unset($data['admin_name'], $data['admin_email'], $data['admin_password']);

            /** @var Store $store */
            $store = static::getModel()::query()->create($data);

            $admin = User::query()->create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => $adminPassword,
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]);

            // Membership is via store_user pivot (not users.store_id).
            $store->users()->attach($admin);

            return $store;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

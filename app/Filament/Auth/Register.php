<?php

namespace App\Filament\Auth;

use App\Models\Store;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use SensitiveParameter;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent()
                    ->label('Your Name')
                    ->placeholder('e.g. Ahmad Khan')
                    ->helperText('Your personal name as the store owner.'),
                $this->getEmailFormComponent()
                    ->label('Email Address')
                    ->placeholder('e.g. owner@shop.com')
                    ->helperText('Used to log in to your store panel.'),
                $this->getStoreNameFormComponent(),
                $this->getPasswordFormComponent()
                    ->label('Password')
                    ->helperText('Choose a strong password for your account.'),
                $this->getPasswordConfirmationFormComponent()
                    ->label('Confirm Password'),
            ]);
    }

    protected function getStoreNameFormComponent(): Component
    {
        return TextInput::make('store_name')
            ->label('Store Name')
            ->placeholder('e.g. Ahmad Grocery')
            ->helperText('Your shop name. You will be redirected to this store after signup.')
            ->required()
            ->maxLength(255);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(#[SensitiveParameter] array $data): array
    {
        $data['role'] = User::ROLE_ADMIN;
        $data['is_active'] = true;

        return $data;
    }

    /**
     * Create the shopkeeper account and their store together.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(#[SensitiveParameter] array $data): Model
    {
        $storeName = (string) ($data['store_name'] ?? '');
        unset($data['store_name']);

        /** @var User $user */
        $user = $this->getUserModel()::query()->create($data);

        $store = Store::query()->create([
            'name' => $storeName,
            'slug' => Store::uniqueSlugFor($storeName),
            'is_active' => true,
        ]);

        $store->users()->attach($user);

        return $user;
    }

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Register your store';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Register Store';
    }
}

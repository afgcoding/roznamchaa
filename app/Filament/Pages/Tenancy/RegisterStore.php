<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Store;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegisterStore extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Create Store';
    }

    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        // Only authenticated users with no store yet may register a store.
        return $user instanceof User && $user->stores()->doesntExist();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Store Name')
                    ->placeholder('e.g. Ahmad Grocery, Branch Market')
                    ->helperText('This will be your shop name in the panel.')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                        if (filled($get('slug'))) {
                            return;
                        }

                        $set('slug', Store::uniqueSlugFor((string) $state));
                    }),
                TextInput::make('slug')
                    ->label('URL Slug')
                    ->placeholder('e.g. ahmad-grocery')
                    ->helperText('Used in your store URL, like /admin/your-slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'stores', column: 'slug')
                    ->alphaDash()
                    ->dehydrateStateUsing(fn (?string $state): string => Str::slug((string) $state)),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        /** @var User $user */
        $user = auth()->user();

        $store = Store::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Store::uniqueSlugFor($data['name']),
            'is_active' => true,
        ]);

        $store->users()->attach($user);

        // New shopkeeper is always the Admin of their own store.
        if (! $user->isAdmin()) {
            $user->forceFill([
                'role' => User::ROLE_ADMIN,
            ])->save();
        }

        return $store;
    }
}

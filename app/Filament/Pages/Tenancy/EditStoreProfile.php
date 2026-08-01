<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EditStoreProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Store Profile';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Store Name')
                    ->placeholder('e.g. Main Shop, Branch 2')
                    ->helperText('Display name for this store in the panel.')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (TextInput $component, ?string $state, callable $set, callable $get): void {
                        if (filled($get('slug'))) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    }),
                TextInput::make('slug')
                    ->label('URL Slug')
                    ->placeholder('e.g. main-shop')
                    ->helperText('Used in the admin URL. Keep it short and unique.')
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'stores', column: 'slug', ignoreRecord: true),
            ]);
    }
}

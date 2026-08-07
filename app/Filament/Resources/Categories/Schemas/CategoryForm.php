<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Category Details'))
                    ->description(__('Group your shop products into clear categories like Oils, Grains, or Beverages.'))
                    ->icon(Heroicon::OutlinedTag)
                    ->columnSpanFull()
                    ->schema(static::components()),
            ]);
    }

    /**
     * Category fields — reused by CategoryResource and ProductForm create-option modal.
     *
     * @return array<int, TextInput|Textarea>
     */
    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label(__('Category Name'))
                ->placeholder(__('e.g. Oils, Grains, Beverages, Biscuits'))
                ->helperText(__('Short name for this product group. Keep it simple and easy to find.'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),
            Textarea::make('description')
                ->label(__('Description'))
                ->placeholder(__('e.g. Cooking oils and ghee used in daily kitchen sales'))
                ->helperText(__('Optional note to explain what belongs in this category. Leave blank if not needed.'))
                ->rows(4)
                ->columnSpanFull(),
        ];
    }
}

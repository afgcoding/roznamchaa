<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Category Details'))
                    ->description(__('Basic information about this product group.'))
                    ->icon(Heroicon::OutlinedTag)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Category Name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('products_count')
                            ->label(__('Products in this Category'))
                            ->state(fn ($record): int => $record->products()->count())
                            ->badge()
                            ->color('info')
                            ->helperText(__('How many products are linked to this category.')),
                        TextEntry::make('description')
                            ->label(__('Description'))
                            ->placeholder(__('No description added')),
                    ]),
                Section::make(__('Record Info'))
                    ->description(__('When this category was created and last updated.'))
                    ->icon(Heroicon::OutlinedClock)
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->since()
                            ->color('info')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label(__('Last Updated'))
                            ->since()
                            ->color('warning')
                            ->badge()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}

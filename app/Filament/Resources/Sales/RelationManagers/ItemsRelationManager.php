<?php

namespace App\Filament\Resources\Sales\RelationManagers;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Sale Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if (! $state) {
                            return;
                        }

                        $product = Product::query()->find($state);

                        if ($product) {
                            $set('unit_price', $product->sale_price);
                            $set('cost_price', $product->cost_price);
                        }
                    }),
                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                        $set('subtotal', (float) $state * (float) $get('unit_price'));
                    }),
                TextInput::make('unit_price')
                    ->numeric()
                    ->required()
                    ->prefix('AFN')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                        $set('subtotal', (float) $get('quantity') * (float) $state);
                    }),
                TextInput::make('cost_price')
                    ->numeric()
                    ->required()
                    ->prefix('AFN'),
                TextInput::make('subtotal')
                    ->numeric()
                    ->required()
                    ->prefix('AFN'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric(decimalPlaces: 3),
                TextColumn::make('unit_price')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('cost_price')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subtotal')
                    ->numeric(decimalPlaces: 2),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Wholesalers;

use App\Filament\Resources\Wholesalers\Pages\CreateWholesaler;
use App\Filament\Resources\Wholesalers\Pages\EditWholesaler;
use App\Filament\Resources\Wholesalers\Pages\ListWholesalers;
use App\Filament\Resources\Wholesalers\Pages\ViewWholesaler;
use App\Filament\Resources\Wholesalers\Schemas\WholesalerForm;
use App\Filament\Resources\Wholesalers\Schemas\WholesalerInfolist;
use App\Filament\Resources\Wholesalers\Tables\WholesalersTable;
use App\Models\Wholesaler;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WholesalerResource extends Resource
{
    protected static ?string $model = Wholesaler::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('Wholesalers / Suppliers');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Purchases & Suppliers');
    }

    public static function getModelLabel(): string
    {
        return __('Wholesaler');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wholesalers');
    }

    public static function getNavigationBadge(): ?string
    {
        $withDebt = static::unpaidDebtCount();

        return $withDebt > 0 ? (string) $withDebt : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __(':count supplier(s) with unpaid debt', ['count' => static::unpaidDebtCount()]);
    }

    protected static function unpaidDebtCount(): int
    {
        return static::getModel()::query()
            ->where('total_debt', '>', 0)
            ->count();
    }

    public static function form(Schema $schema): Schema
    {
        return WholesalerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WholesalerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WholesalersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWholesalers::route('/'),
            'create' => CreateWholesaler::route('/create'),
            'view' => ViewWholesaler::route('/{record}'),
            'edit' => EditWholesaler::route('/{record}/edit'),
        ];
    }
}

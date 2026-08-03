<?php

namespace App\Filament\Resources\CustomerLedgers;

use App\Filament\Resources\CustomerLedgers\Pages\CreateCustomerLedger;
use App\Filament\Resources\CustomerLedgers\Pages\EditCustomerLedger;
use App\Filament\Resources\CustomerLedgers\Pages\ListCustomerLedgers;
use App\Filament\Resources\CustomerLedgers\Pages\ViewCustomerLedger;
use App\Filament\Resources\CustomerLedgers\Schemas\CustomerLedgerForm;
use App\Filament\Resources\CustomerLedgers\Schemas\CustomerLedgerInfolist;
use App\Filament\Resources\CustomerLedgers\Tables\CustomerLedgersTable;
use App\Models\CustomerLedger;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CustomerLedgerResource extends Resource
{
    protected static ?string $model = CustomerLedger::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('Customer Ledgers');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Sales & POS');
    }

    public static function getModelLabel(): string
    {
        return __('Ledger Entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Customer Ledgers');
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerLedgerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerLedgerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerLedgersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerLedgers::route('/'),
            'create' => CreateCustomerLedger::route('/create'),
            'view' => ViewCustomerLedger::route('/{record}'),
            'edit' => EditCustomerLedger::route('/{record}/edit'),
        ];
    }
}

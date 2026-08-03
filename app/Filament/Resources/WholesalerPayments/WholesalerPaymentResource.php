<?php

namespace App\Filament\Resources\WholesalerPayments;

use App\Filament\Resources\WholesalerPayments\Pages\CreateWholesalerPayment;
use App\Filament\Resources\WholesalerPayments\Pages\EditWholesalerPayment;
use App\Filament\Resources\WholesalerPayments\Pages\ListWholesalerPayments;
use App\Filament\Resources\WholesalerPayments\Pages\ViewWholesalerPayment;
use App\Filament\Resources\WholesalerPayments\Schemas\WholesalerPaymentForm;
use App\Filament\Resources\WholesalerPayments\Schemas\WholesalerPaymentInfolist;
use App\Filament\Resources\WholesalerPayments\Tables\WholesalerPaymentsTable;
use App\Models\WholesalerPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WholesalerPaymentResource extends Resource
{
    protected static ?string $model = WholesalerPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Wholesaler Payments');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Purchases & Suppliers');
    }

    public static function getModelLabel(): string
    {
        return __('Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wholesaler Payments');
    }

    public static function form(Schema $schema): Schema
    {
        return WholesalerPaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WholesalerPaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WholesalerPaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWholesalerPayments::route('/'),
            'create' => CreateWholesalerPayment::route('/create'),
            'view' => ViewWholesalerPayment::route('/{record}'),
            'edit' => EditWholesalerPayment::route('/{record}/edit'),
        ];
    }
}

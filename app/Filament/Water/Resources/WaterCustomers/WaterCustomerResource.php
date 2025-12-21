<?php

namespace App\Filament\Water\Resources\WaterCustomers;

use App\Filament\Water\Resources\WaterCustomers\Pages\CreateWaterCustomer;
use App\Filament\Water\Resources\WaterCustomers\Pages\EditWaterCustomer;
use App\Filament\Water\Resources\WaterCustomers\Pages\ListWaterCustomers;
use App\Filament\Water\Resources\WaterCustomers\Schemas\WaterCustomerForm;
use App\Filament\Water\Resources\WaterCustomers\Tables\WaterCustomersTable;
use App\Models\WaterCustomer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaterCustomerResource extends Resource
{
    protected static ?string $model = WaterCustomer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    public static function getModelLabel(): string
    {
        return 'গ্রাহক';
    }

    public static function getPluralModelLabel(): string
    {
        return 'গ্রাহকগণ';
    }

    public static function form(Schema $schema): Schema
    {
        return WaterCustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterCustomersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWaterCustomers::route('/'),
            'create' => CreateWaterCustomer::route('/create'),
            'edit' => EditWaterCustomer::route('/{record}/edit'),
        ];
    }
}

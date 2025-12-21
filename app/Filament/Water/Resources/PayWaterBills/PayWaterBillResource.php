<?php

namespace App\Filament\Water\Resources\PayWaterBills;

use App\Filament\Water\Resources\PayWaterBills\Pages\CreatePayWaterBill;
use App\Filament\Water\Resources\PayWaterBills\Pages\EditPayWaterBill;
use App\Filament\Water\Resources\PayWaterBills\Pages\ListPayWaterBills;
use App\Filament\Water\Resources\PayWaterBills\Pages\WaterBillDetails;
use App\Filament\Water\Resources\PayWaterBills\Schemas\PayWaterBillForm;
use App\Filament\Water\Resources\PayWaterBills\Tables\PayWaterBillsTable;
use App\Models\PayWaterBill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PayWaterBillResource extends Resource
{
    protected static ?string $model = PayWaterBill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyBangladeshi;

    public static function getModelLabel(): string
    {
        return 'পানি বিল পরিশোধ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'পানি বিলসমূহ পরিশোধ';
    }

    public static function form(Schema $schema): Schema
    {
        return PayWaterBillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayWaterBillsTable::configure($table);
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
            'index' => ListPayWaterBills::route('/'),
            'create' => CreatePayWaterBill::route('/create'),
            'edit' => EditPayWaterBill::route('/{record}/edit'),
            'details' => WaterBillDetails::route('/{record}/details'),
        ];
    }
}

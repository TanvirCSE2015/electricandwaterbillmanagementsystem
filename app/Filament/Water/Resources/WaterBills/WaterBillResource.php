<?php

namespace App\Filament\Water\Resources\WaterBills;

use App\Filament\Water\Resources\WaterBills\Pages\CreateWaterBill;
use App\Filament\Water\Resources\WaterBills\Pages\EditWaterBill;
use App\Filament\Water\Resources\WaterBills\Pages\ListWaterBills;
use App\Filament\Water\Resources\WaterBills\Pages\ViewWaterBill;
use App\Filament\Water\Resources\WaterBills\Pages\WaterCustomList;
use App\Filament\Water\Resources\WaterBills\Schemas\WaterBillForm;
use App\Filament\Water\Resources\WaterBills\Schemas\WaterBillInfolist;
use App\Filament\Water\Resources\WaterBills\Tables\WaterBillsTable;
use App\Models\WaterBill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaterBillResource extends Resource
{
    protected static ?string $model = WaterBill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::EyeDropper;

    public static function getModelLabel(): string
    {
        return ('পানি বিল');
    }

    public static function getPluralModelLabel(): string
    {
        return ('পানি বিলসমূহ');
    }

    public static function form(Schema $schema): Schema
    {
        return WaterBillForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WaterBillInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterBillsTable::configure($table);
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
            'index' => WaterCustomList::route('/'),
            'create' => CreateWaterBill::route('/create'),
            'view' => ViewWaterBill::route('/{record}'),
            'edit' => EditWaterBill::route('/{record}/edit'),
        ];
    }
}

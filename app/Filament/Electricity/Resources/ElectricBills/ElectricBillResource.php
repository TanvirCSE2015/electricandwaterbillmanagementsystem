<?php

namespace App\Filament\Electricity\Resources\ElectricBills;

use App\Filament\Electricity\Resources\ElectricBills\Pages\CreateElectricBill;
use App\Filament\Electricity\Resources\ElectricBills\Pages\CustomIndex;
use App\Filament\Electricity\Resources\ElectricBills\Pages\EditElectricBill;
use App\Filament\Electricity\Resources\ElectricBills\Pages\ListElectricBills;
use App\Filament\Electricity\Resources\ElectricBills\Pages\ViewElectricBill;
use App\Filament\Electricity\Resources\ElectricBills\Schemas\ElectricBillForm;
use App\Filament\Electricity\Resources\ElectricBills\Schemas\ElectricBillInfolist;
use App\Filament\Electricity\Resources\ElectricBills\Tables\ElectricBillsTable;
use App\Models\ElectricBill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ElectricBillResource extends Resource
{
    protected static ?string $model = ElectricBill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;

    protected static bool $shouldRegisterNavigation = true;

    public static function getModelLabel(): string
    {
        return 'বিদ্যুৎ বিল';
    }

    public static function getPluralModelLabel(): string
    {
        return 'বিদ্যুৎ বিলসমূহ';
    }


    public static function form(Schema $schema): Schema
    {
        return ElectricBillForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ElectricBillInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElectricBillsTable::configure($table);
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
            'index' => CustomIndex::route('/'),
            'create' => CreateElectricBill::route('/create'),
            'view' => ViewElectricBill::route('/{record}'),
            'edit' => EditElectricBill::route('/{record}/edit'),
        ];
    }
}

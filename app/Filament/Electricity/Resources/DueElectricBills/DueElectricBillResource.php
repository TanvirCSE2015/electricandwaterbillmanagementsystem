<?php

namespace App\Filament\Electricity\Resources\DueElectricBills;

use App\Filament\Electricity\Resources\DueElectricBills\Pages\CreateDueElectricBill;
use App\Filament\Electricity\Resources\DueElectricBills\Pages\DueElectricBillDetails;
use App\Filament\Electricity\Resources\DueElectricBills\Pages\EditDueElectricBill;
use App\Filament\Electricity\Resources\DueElectricBills\Pages\ListDueElectricBills;
use App\Filament\Electricity\Resources\DueElectricBills\Schemas\DueElectricBillForm;
use App\Filament\Electricity\Resources\DueElectricBills\Tables\DueElectricBillsTable;
use App\Models\DueElectricBill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DueElectricBillResource extends Resource
{
    protected static ?string $model = DueElectricBill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return 'বকেয়া বিদ্যুৎ বিল';
    }

    public static function getPluralModelLabel(): string
    {
        return 'বকেয়া বিদ্যুৎ বিলসমূহ';
    }

    public static function form(Schema $schema): Schema
    {
        return DueElectricBillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DueElectricBillsTable::configure($table);
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
            'index' => ListDueElectricBills::route('/'),
            'create' => CreateDueElectricBill::route('/create'),
            'edit' => EditDueElectricBill::route('/{record}/edit'),
            'details' => DueElectricBillDetails::route('/{record}/details'),
        ];
    }
}

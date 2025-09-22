<?php

namespace App\Filament\Resources\ElectricBillSettings;

use App\Filament\Resources\ElectricBillSettings\Pages\CreateElectricBillSetting;
use App\Filament\Resources\ElectricBillSettings\Pages\EditElectricBillSetting;
use App\Filament\Resources\ElectricBillSettings\Pages\ListElectricBillSettings;
use App\Filament\Resources\ElectricBillSettings\Schemas\ElectricBillSettingForm;
use App\Filament\Resources\ElectricBillSettings\Tables\ElectricBillSettingsTable;
use App\Models\ElectricBillSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ElectricBillSettingResource extends Resource
{
    protected static ?string $model = ElectricBillSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;


    public static function getModelLabel(): string
    {
        return ('বিদ্যুৎ বিল সেটিংস');
    }

    public static function getPluralModelLabel(): string
    {
        return ('বিদ্যুৎ বিল সেটিংস');
    }

    public static function form(Schema $schema): Schema
    {
        return ElectricBillSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElectricBillSettingsTable::configure($table);
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
            'index' => ListElectricBillSettings::route('/'),
            'create' => CreateElectricBillSetting::route('/create'),
            'edit' => EditElectricBillSetting::route('/{record}/edit'),
        ];
    }
}

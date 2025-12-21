<?php

namespace App\Filament\Water\Resources\WaterSettings;

use App\Filament\Water\Resources\WaterSettings\Pages\CreateWaterSetting;
use App\Filament\Water\Resources\WaterSettings\Pages\EditWaterSetting;
use App\Filament\Water\Resources\WaterSettings\Pages\ListWaterSettings;
use App\Filament\Water\Resources\WaterSettings\Schemas\WaterSettingForm;
use App\Filament\Water\Resources\WaterSettings\Tables\WaterSettingsTable;
use App\Models\WaterSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WaterSettingResource extends Resource
{
    protected static ?string $model = WaterSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog;

    protected static string | UnitEnum | null $navigationGroup = 'সেটংসমূহ';

    public static function getModelLabel(): string
    {
        return 'পানি বিলের সেটিং';
    }

    public static function getPluralModelLabel(): string
    {
        return 'পানি বিলের সেটিংস';
    }

    public static function form(Schema $schema): Schema
    {
        return WaterSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterSettingsTable::configure($table);
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
            'index' => ListWaterSettings::route('/'),
            'create' => CreateWaterSetting::route('/create'),
            'edit' => EditWaterSetting::route('/{record}/edit'),
        ];
    }
}

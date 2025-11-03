<?php

namespace App\Filament\Electricity\Resources\ElectricAreas;

use App\Filament\Electricity\Resources\ElectricAreas\Pages\CreateElectricArea;
use App\Filament\Electricity\Resources\ElectricAreas\Pages\EditElectricArea;
use App\Filament\Electricity\Resources\ElectricAreas\Pages\ListElectricAreas;
use App\Filament\Electricity\Resources\ElectricAreas\Schemas\ElectricAreaForm;
use App\Filament\Electricity\Resources\ElectricAreas\Tables\ElectricAreasTable;
use App\Models\ElectricArea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ElectricAreaResource extends Resource
{
    protected static ?string $model = ElectricArea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static string | UnitEnum | null $navigationGroup = 'সেটংসমূহ';

    public static function getModelLabel(): string
    {
        return 'এরিয়া';
    }
    public static function getPluralModelLabel(): string
    {
        return 'এরিয়াসমূহ';
    }

    public static function form(Schema $schema): Schema
    {
        return ElectricAreaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElectricAreasTable::configure($table);
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
            'index' => ListElectricAreas::route('/'),
            'create' => CreateElectricArea::route('/create'),
            'edit' => EditElectricArea::route('/{record}/edit'),
        ];
    }
}

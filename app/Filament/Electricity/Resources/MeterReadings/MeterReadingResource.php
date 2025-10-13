<?php

namespace App\Filament\Electricity\Resources\MeterReadings;

use App\Filament\Electricity\Resources\MeterReadings\Pages\CreateMeterReading;
use App\Filament\Electricity\Resources\MeterReadings\Pages\EditMeterReading;
use App\Filament\Electricity\Resources\MeterReadings\Pages\ListMeterReadings;
use App\Filament\Electricity\Resources\MeterReadings\Schemas\MeterReadingForm;
use App\Filament\Electricity\Resources\MeterReadings\Tables\MeterReadingsTable;
use App\Models\MeterReading;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MeterReadingResource extends Resource
{
    protected static ?string $model = MeterReading::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // protected static bool $shouldRegisterNavigation = false;

    public static function getModelLabel(): string
    {
        return 'বিল রিডিং';
    }

    public static function getPluralModelLabel(): string
    {
        return 'বিল রিডিংসমূহ';
    }

    public static function form(Schema $schema): Schema
    {
        return MeterReadingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeterReadingsTable::configure($table);
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
            'index' => ListMeterReadings::route('/'),
            'create' => CreateMeterReading::route('/create'),
            'edit' => EditMeterReading::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Electricity\Resources\Blocks;

use App\Filament\Electricity\Resources\Blocks\Pages\CreateBlocks;
use App\Filament\Electricity\Resources\Blocks\Pages\EditBlocks;
use App\Filament\Electricity\Resources\Blocks\Pages\ListBlocks;
use App\Filament\Electricity\Resources\Blocks\Schemas\BlocksForm;
use App\Filament\Electricity\Resources\Blocks\Tables\BlocksTable;
use App\Models\Blocks;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BlocksResource extends Resource
{
    protected static ?string $model = Blocks::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    protected static string | UnitEnum | null $navigationGroup = 'সেটংসমূহ';

    public static function getModelLabel(): string
    {
        return 'ব্লক';
    }
    public static function getPluralModelLabel(): string
    {
        return 'ব্লকসমূহ';
    }
    public static function form(Schema $schema): Schema
    {
        return BlocksForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlocksTable::configure($table);
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
            'index' => ListBlocks::route('/'),
            'create' => CreateBlocks::route('/create'),
            'edit' => EditBlocks::route('/{record}/edit'),
        ];
    }
}

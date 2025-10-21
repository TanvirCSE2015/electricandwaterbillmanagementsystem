<?php

namespace App\Filament\Electricity\Resources\PreviousDues;

use App\Filament\Electricity\Resources\PreviousDues\Pages\CreatePreviousDue;
use App\Filament\Electricity\Resources\PreviousDues\Pages\EditPreviousDue;
use App\Filament\Electricity\Resources\PreviousDues\Pages\ListPreviousDues;
use App\Filament\Electricity\Resources\PreviousDues\Schemas\PreviousDueForm;
use App\Filament\Electricity\Resources\PreviousDues\Tables\PreviousDuesTable;
use App\Models\PreviousDue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PreviousDueResource extends Resource
{
    protected static ?string $model = PreviousDue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return 'পূর্বের বকেয়া';
    }
    public static function getPluralModelLabel(): string
    {
        return 'পূর্বের বকেয়াসমূহ';
    }

    public static function form(Schema $schema): Schema
    {
        return PreviousDueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreviousDuesTable::configure($table);
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
            'index' => ListPreviousDues::route('/'),
            'create' => CreatePreviousDue::route('/create'),
            'edit' => EditPreviousDue::route('/{record}/edit'),
            'payment' => Pages\PayPreviousDue::route('/{record}/pay'),
        ];
    }
}

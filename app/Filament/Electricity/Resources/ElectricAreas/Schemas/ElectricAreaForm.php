<?php

namespace App\Filament\Electricity\Resources\ElectricAreas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ElectricAreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label(__('fields.name'))
                    ->required(),
            ]);
    }
}

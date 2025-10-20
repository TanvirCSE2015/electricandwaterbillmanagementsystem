<?php

namespace App\Filament\Electricity\Resources\Blocks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BlocksForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bolck_name')
                    ->required()
                    ->label(__('fields.block_name')),
            ]);
    }
}

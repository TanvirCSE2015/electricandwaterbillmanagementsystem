<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label(__('fields.name')),
                TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->required(),
                Select::make('panel_type')
                    ->label(__('fields.panel_type'))
                    ->options(['super_admin' => 'Super admin', 'electricity' => 'Electricity', 'water' => 'Water'])
                    ->default('electricity')
                 ->required(),
                // DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->label(__('fields.password'))
                    ->password()
                    ->required(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ElectricBillSettings\Schemas;

use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ElectricBillSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->required()
                    ->numeric()
                    ->suffix('৳/ইউনিট')
                    ->default(0),
                TextInput::make('system_loss')
                    ->label(__('fields.system_loss'))
                    ->required()
                    ->numeric()
                    ->suffix('ইউনিট')
                    ->default(0),
                TextInput::make('demand_charge')
                    ->label(__('fields.demand_charge'))
                    ->required()
                    ->numeric()
                    ->suffix('৳')
                    ->default(0),
                TextInput::make('service_charge')
                    ->label(__('fields.service_charge'))
                    ->required()
                    ->numeric()
                    ->suffix('৳')
                    ->default(0),
                TextInput::make('surcharge')
                    ->label(__('fields.surcharge'))
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->default(0),
                TextInput::make('vat')
                    ->label(__('fields.vat'))
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->default(0),
            ])->columns(3);
    }
}

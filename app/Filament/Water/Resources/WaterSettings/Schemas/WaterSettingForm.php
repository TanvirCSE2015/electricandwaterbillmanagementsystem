<?php

namespace App\Filament\Water\Resources\WaterSettings\Schemas;

use App\Helpers\ElectricBillHelper;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WaterSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('monthly_rate')
                    ->label(__('water_fields.monthly_rate'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->required(),
                TextInput::make('monthly_security')
                    ->label(__('water_fields.monthly_security'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->required(),
                TextInput::make('monthly_const_rate')
                    ->label(__('water_fields.monthly_const_rate'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->required(),
                TextInput::make('const_security')
                    ->label(__('water_fields.const_security'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->required(),
                TextInput::make('monthly_surcharge')
                    ->label(__('water_fields.monthly_surcharge'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->suffix('  %')
                    ->required()
            ])
            ->columns(3);
    }
}

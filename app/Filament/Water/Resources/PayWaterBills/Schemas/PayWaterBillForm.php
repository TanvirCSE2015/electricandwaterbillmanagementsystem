<?php

namespace App\Filament\Water\Resources\PayWaterBills\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PayWaterBillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_name')
                    ->required(),
                TextInput::make('customer_phone')
                    ->tel()
                    ->required(),
                TextInput::make('customer_email')
                    ->email()
                    ->default(null),
                Textarea::make('customer_address')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('holding_number')
                    ->required(),
                TextInput::make('flat_number')
                    ->required(),
                TextInput::make('total_flat')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('previous_due')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('type')
                    ->options(['flat' => 'Flat', 'construction' => 'Construction', 'complete' => 'Complete'])
                    ->default('flat')
                    ->required(),
            ]);
    }
}

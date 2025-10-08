<?php

namespace App\Filament\Electricity\Resources\DueElectricBills\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DueElectricBillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('meter_reading_id')
                    ->required()
                    ->numeric(),
                TextInput::make('electric_bill_setting_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('bill_date')
                    ->required(),
                TextInput::make('billing_month')
                    ->required()
                    ->numeric(),
                TextInput::make('billing_year')
                    ->required()
                    ->numeric(),
                TextInput::make('bill_month_name')
                    ->required(),
                TextInput::make('consumed_units')
                    ->required()
                    ->numeric(),
                TextInput::make('system_loss_units')
                    ->required()
                    ->numeric(),
                TextInput::make('base_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('demand_charge')
                    ->required()
                    ->numeric(),
                TextInput::make('service_charge')
                    ->required()
                    ->numeric(),
                TextInput::make('surcharge')
                    ->required()
                    ->numeric(),
                TextInput::make('vat')
                    ->required()
                    ->numeric(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Toggle::make('is_paid')
                    ->required(),
                DatePicker::make('payment_date'),
                TextInput::make('payment_method')
                    ->default(null),
                TextInput::make('transaction_id')
                    ->default(null),
                Textarea::make('remarks')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                TextInput::make('paid_by')
                    ->numeric()
                    ->default(null),
            ]);
    }
}

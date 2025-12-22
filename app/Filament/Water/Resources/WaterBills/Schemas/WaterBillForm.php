<?php

namespace App\Filament\Water\Resources\WaterBills\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WaterBillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('water_customer_id')
                    ->relationship('waterCustomer', 'id')
                    ->required(),
                TextInput::make('water_invoice_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('water_bill_month')
                    ->required()
                    ->numeric(),
                TextInput::make('water_bill_year')
                    ->required()
                    ->numeric(),
                TextInput::make('base_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('surcharge_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('surcharge_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('paid_amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('bill_creation_date')
                    ->required(),
                DatePicker::make('bill_due_date')
                    ->required(),
                Toggle::make('is_paid')
                    ->required(),
                DatePicker::make('paid_at'),
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

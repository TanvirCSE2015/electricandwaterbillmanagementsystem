<?php

namespace App\Filament\Water\Resources\WaterCustomers\Schemas;

use App\Helpers\ElectricBillHelper;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WaterCustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_name')
                    ->label(__('water_fields.customer_name'))
                    ->required(),
                TextInput::make('customer_phone')
                    ->label(__('water_fields.customer_phone'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->required(),
                TextInput::make('customer_email')
                    ->label(__('water_fields.customer_email'))
                    ->email()
                    ->default(null),
                TextInput::make('holding_number')
                    ->label(__('water_fields.holding_number'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->required(),
                // TextInput::make('flat_number')
                //     ->label(__('water_fields.flat_number'))
                //     ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                //     ->required(),
                TextInput::make('total_flat')
                    ->label(__('water_fields.total_flat'))
                    ->required()
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->default(1),
                TextInput::make('previous_due')
                    ->label(__('water_fields.previous_due'))
                    ->required()
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state))
                    ->default(0),
                Select::make('type')
                    ->label(__('water_fields.type'))
                    ->options([
                        'flat'=>'ফ্ল্যাট',
                        'construction'=>'নির্মাণাধীন',
                        'complete'=>'নির্মাণ সম্পন্ন',
                    ]),
                Textarea::make('customer_address')
                    ->label(__('water_fields.customer_address'))
                    ->required(),
            Section::make('')
                
                ->schema([
                    Repeater::make('flats')
                        ->label(__('water_fields.flats'))
                        ->relationship('flats')
                        ->schema([
                            TextInput::make('flat_number')
                                ->label(__('water_fields.flat_number'))
                                ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                                ->required(),
                            Select::make('is_occupied')
                                ->label(__('water_fields.is_occupied'))
                                ->options([
                                    1=>'আছে',
                                    0=>'নেই',
                                ])
                                ->reactive()
                                ->default(1)
                                ->required(),
                        ])
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $occupiedCount = collect($state ?? [])
                                ->where('is_occupied', 1)
                                ->count();

                            $set('total_flat', ElectricBillHelper::en2bn($occupiedCount));
                        })
                        ->columns(2)
                        ->columnSpanFull()
                ])->columnSpanFull(),
            ])
            ->columns(3);
    }
}

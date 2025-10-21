<?php

namespace App\Filament\Electricity\Resources\PreviousDues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PreviousDueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label(__('fields.name'))
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $customer = \App\Models\Customer::find($state);
                        if ($customer) {
                            $set('shop_no', $customer->shop_no);
                            $meter = \App\Models\Meter::where(['customer_id'=>$state,'status'=> 'active'])->first();
                            $set('meter_number', $meter?->meter_number);
                        } else {
                            $set('shop_no', null);
                            $set('meter_number', null);
                        }
                    })
                    ->afterStateHydrated(function (callable $set, $state) {
                        $customer = \App\Models\Customer::query()->whereHas('meters', function ($query) use ($state) {
                            $query->where(['customer_id'=> $state,'status' => 'active']);
                        })->where('id',$state)->first();
                        if ($customer) {
                            $set('shop_no', $customer->shop_no);
                            $meter = \App\Models\Meter::where(['customer_id'=>$state,'status'=> 'active'])->first();
                            $set('meter_number', $meter->meter_number);
                        }
                    })
                    ->required(),
                TextInput::make('shop_no')
                    ->label(__('fields.shop_no'))
                    ->disabled(),
                TextInput::make('meter_number')
                    ->label(__('fields.meter_number'))
                    ->disabled(),
                TextInput::make('amount')
                    ->label(__('fields.total_amount'))
                    ->required()
                    ->numeric(),
                Toggle::make('is_paid')
                    ->required(),
                Textarea::make('remarks')
                    ->label(__('fields.remarks'))
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

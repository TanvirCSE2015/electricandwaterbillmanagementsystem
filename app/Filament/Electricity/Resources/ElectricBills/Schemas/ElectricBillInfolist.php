<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ElectricBillInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.shop_no')
                    ->label(__('fields.shop_no') . 'ঃ'),
                TextEntry::make('customer.name')
                    ->label(__('fields.name'). 'ঃ'),
                TextEntry::make('bill_date')
                    ->label(__('fields.bill_date') . 'ঃ')
                    ->date(),
                // TextEntry::make('billing_month')
                //     ->label(__('fields.billing_month'))
                //     ->numeric(),
                TextEntry::make('billing_year')
                    ->label(__('fields.billing_year') . 'ঃ'),
                TextEntry::make('bill_month_name')
                    ->date('F')
                    ->label(__('fields.bill_month_name') . 'ঃ'),
                TextEntry::make('consumed_units')
                    ->label(__('fields.consume_unit') . 'ঃ')
                    ->numeric(),
                TextEntry::make('base_amount')
                    ->label(__('fields.base_amount') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳'),
                TextEntry::make('system_loss_units')
                    ->label(__('fields.system_loss') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳'),
                TextEntry::make('demand_charge')
                    ->label(__('fields.demand_charge') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳'),
                TextEntry::make('service_charge')
                    ->label(__('fields.service_charge') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳'),
                TextEntry::make('surcharge')
                    ->label(__('fields.surcharge') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳')
                    ->getStateUsing(function ($record) {
                        if ($record->surcharge > 0) {
                            return $record->surcharge;
                        } else {
                            return \App\Helpers\ElectricBillHelper::calculateSurcharge($record);
                        }
                    }),
                TextEntry::make('vat')
                    ->label(__('fields.vat') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳'),
                TextEntry::make('total_amount')
                    ->label(__('fields.total_amount') . 'ঃ')
                    ->numeric()
                    ->suffix(' ৳'),
                IconEntry::make('is_paid')
                    ->label(__('fields.is_paid') . 'ঃ')
                    ->boolean(),
                TextEntry::make('due_date')
                    ->label(__('fields.due_date') . 'ঃ')
                    ->date(),
                TextEntry::make('payment_date')
                    ->label(__('fields.payment_date') . 'ঃ')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->label(__('fields.payment_method') . 'ঃ')
                    ->placeholder('-'),
                TextEntry::make('transaction_id')
                    ->label(__('fields.transaction_id') . 'ঃ')
                    ->placeholder('-'),

                TextEntry::make('creator.name')
                    ->label(__('fields.created_by') . 'ঃ')
                    ->numeric(),
                TextEntry::make('paid_by')
                    ->label(__('fields.paid_by') . 'ঃ')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(__('fields.created_at') . 'ঃ')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('fields.updated_at') . 'ঃ')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('remarks')
                    ->label(__('fields.remarks') . 'ঃ')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ])->columns(4);
    }
}

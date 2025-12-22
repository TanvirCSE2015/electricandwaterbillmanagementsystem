<?php

namespace App\Filament\Water\Resources\WaterBills\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WaterBillInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('waterCustomer.id')
                    ->label('Water customer'),
                TextEntry::make('water_invoice_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('water_bill_month')
                    ->numeric(),
                TextEntry::make('water_bill_year')
                    ->numeric(),
                TextEntry::make('base_amount')
                    ->numeric(),
                TextEntry::make('surcharge_percent')
                    ->numeric(),
                TextEntry::make('surcharge_amount')
                    ->numeric(),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('paid_amount')
                    ->numeric(),
                TextEntry::make('bill_creation_date')
                    ->date(),
                TextEntry::make('bill_due_date')
                    ->date(),
                IconEntry::make('is_paid')
                    ->boolean(),
                TextEntry::make('paid_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                TextEntry::make('transaction_id')
                    ->placeholder('-'),
                TextEntry::make('remarks')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('paid_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

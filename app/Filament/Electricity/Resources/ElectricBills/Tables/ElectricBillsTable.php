<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ElectricBillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.shop_no')
                    ->label(__('fields.shop_no'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bill_date')
                    ->label(__('fields.bill_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('consumed_units')
                    ->label(__('fields.consume_unit'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ইউনিট'),
                TextColumn::make('system_loss_units')
                    ->label(__('fields.system_loss'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('demand_charge')
                    ->label(__('fields.demand_charge'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('service_charge')
                    ->label(__('fields.service_charge'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('surcharge')
                    ->label(__('fields.surcharge'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('vat')
                    ->label(__('fields.vat'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('total_amount')
                    ->label(__('fields.total_amount'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                IconColumn::make('is_paid')
                    ->label(__('fields.is_paid'))
                    ->boolean(),
                TextColumn::make('payment_date')
                    ->label(__('fields.payment_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(__('fields.payment_method'))
                    ->searchable(),
                TextColumn::make('transaction_id')
                    ->label(__('fields.transaction_id'))
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label(__('fields.created_by'))
                    ->sortable(),
                TextColumn::make('paid_by')
                    ->label(__('fields.paid_by'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

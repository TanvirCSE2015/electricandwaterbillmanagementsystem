<?php

namespace App\Filament\Water\Resources\WaterBills\Tables;

use App\Helpers\WaterBillHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WaterBillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('waterCustomer.customer_name')
                    ->label(__('water_fields.customer_name'))
                    ->searchable(),
                TextColumn::make('water_bill_month')
                    ->label(__('water_fields.water_bill_month'))
                    ->getStateUsing(fn ( $record) => \Carbon\Carbon::create()->month($record->water_bill_month)->translatedFormat('F'))
                    ->sortable(),
                TextColumn::make('water_bill_year')
                    ->label(__('water_fields.water_bill_year'))
                    ->formatStateUsing(fn ( $state) => WaterBillHelper::en2bn($state))
                    ->sortable(),
                TextColumn::make('surcharge_amount')
                    ->label(__('water_fields.surcharge_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(__('water_fields.total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bill_creation_date')
                    ->label(__('water_fields.bill_creation_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bill_due_date')
                    ->label(__('water_fields.bill_due_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_paid')
                    ->label(__('water_fields.is_paid'))
                    ->boolean(),
                TextColumn::make('paid_at')
                    ->label(__('water_fields.paid_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
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

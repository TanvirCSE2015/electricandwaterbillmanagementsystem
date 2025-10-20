<?php

namespace App\Filament\Electricity\Resources\DueElectricBills\Tables;

use App\Filament\Electricity\Resources\DueElectricBills\DueElectricBillResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DueElectricBillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable()
                    ->sortable(), 
                TextColumn::make('meters.meter_number')
                    ->label(__('fields.meter_number'))
                    ->searchable()
                    ->sortable(),  
                TextColumn::make('previousDue.amount')
                    ->label(__('fields.previous_due'))
                    ->getStateUsing(fn($record) => $record->previousDue?->amount ?? 0)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bills_sum_total_amount')
                    ->label(__('fields.total_amount'))
                    ->getStateUsing(function ($record) {
                        $bills=$record->bills;
                        $dueTotal=0;
                        foreach ($bills as $bill) {
                           if ($bill->is_paid) {
                               continue;
                            }else{
                                if($bill->surcharge > 0){
                                    $dueTotal += $bill->total_amount;
                                    continue;
                                }else{
                                        $surcharge= \App\Helpers\ElectricBillHelper::calculateSurcharge($bill);
                                        $dueTotal += $bill->total_amount + $surcharge + $record->previousDue?->amount ?? 0;
                                }
                            }
                        }
                        return round($dueTotal);
                    })
                    ->numeric()
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
                // EditAction::make(),
                Action::make('details')
                ->label('বিস্তারিত')
               ->url(fn ($record) => DueElectricBillResource::getUrl('details', ['record' => $record]))
                ->icon('heroicon-o-eye'),
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ElectricBillSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ElectricBillSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->numeric()
                    ->suffix('  ৳/ইউনিট')
                    ->sortable(),
                TextColumn::make('system_loss')
                    ->label(__('fields.system_loss'))
                    ->numeric()
                    ->suffix('  ইউনিট')
                    ->sortable(),
                TextColumn::make('demand_charge')
                    ->label(__('fields.demand_charge'))
                    ->numeric()
                    ->suffix('  ৳')
                    ->sortable(),
                TextColumn::make('service_charge')
                    ->label(__('fields.service_charge'))
                    ->numeric()
                    ->suffix('  ৳')
                    ->sortable(),
                TextColumn::make('surcharge')
                    ->label(__('fields.surcharge'))
                    ->numeric()
                    ->suffix('  %')
                    ->sortable(),
                TextColumn::make('vat')
                    ->label(__('fields.vat'))
                    ->numeric()
                    ->suffix('  %')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

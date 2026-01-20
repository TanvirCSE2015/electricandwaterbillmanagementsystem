<?php

namespace App\Filament\Water\Resources\WaterCustomers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WaterCustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label(__('water_fields.customer_name'))
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label(__('water_fields.customer_phone'))
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label(__('water_fields.customer_email'))
                    ->searchable(),
                TextColumn::make('holding_number')
                    ->label(__('water_fields.holding_number'))
                    ->searchable(),
                TextColumn::make('flats.flat_number')
                    ->label(__('water_fields.flat_number'))
                    ->searchable(),
                TextColumn::make('total_flat')
                    ->label(__('water_fields.total_flat'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('previous_due')
                    ->label(__('water_fields.previous_due'))
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

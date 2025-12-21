<?php

namespace App\Filament\Water\Resources\WaterSettings\Tables;

use App\Helpers\ElectricBillHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WaterSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('monthly_rate')
                    ->label(__('water_fields.monthly_rate'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state)),
                TextColumn::make('monthly_const_rate')
                    ->label(__('water_fields.monthly_const_rate'))
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state)),
                TextColumn::make('monthly_surcharge')
                    ->label(__('water_fields.monthly_surcharge'))
                    ->suffix('  %')
                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state)),
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

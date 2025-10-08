<?php

namespace App\Filament\Electricity\Resources\MeterReadings\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeterReadingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('meter.customer.shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable(),
                TextColumn::make('meter.meter_number')
                    ->label(__('fields.meter_number'))
                    ->searchable(),
                TextColumn::make('reading_date')
                    ->label(__('fields.reading_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('previous_reading')
                    ->label(__('fields.previous_reading'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_reading')
                    ->label(__('fields.current_reading'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('consume_unit')
                    ->label(__('fields.consume_unit'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ইউনিট'),
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

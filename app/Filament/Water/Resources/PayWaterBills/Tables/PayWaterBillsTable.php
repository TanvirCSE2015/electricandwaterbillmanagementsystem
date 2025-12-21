<?php

namespace App\Filament\Water\Resources\PayWaterBills\Tables;

use App\Filament\Water\Resources\PayWaterBills\PayWaterBillResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayWaterBillsTable
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
                TextColumn::make('holding_number')
                ->label(__('water_fields.holding_number'))
                    ->searchable(),
                TextColumn::make('total_flat')
                ->label(__('water_fields.total_flat'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('previous_due')
                ->label(__('water_fields.previous_due'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_due_amount')
                    ->label('মোট বকেয়া পরিমাণ')
                    ->numeric()
                    ->getStateUsing(fn ($record) => round($record->total_due_amount + $record->previous_due))
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
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
                ->url(fn ($record) => PayWaterBillResource::getUrl('details', ['record' => $record]))
                ->icon('heroicon-o-eye'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

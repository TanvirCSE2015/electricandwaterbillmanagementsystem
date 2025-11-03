<?php

namespace App\Filament\Electricity\Resources\Customers\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('fields.email'))
                    ->searchable(),
                TextColumn::make('shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('fields.phone'))
                    ->searchable(),
                TextColumn::make('electricArea.name')
                    ->label(__('fields.area'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('block.bolck_name')
                    ->label(__('fields.block_name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->label(__('fields.address'))
                    ->searchable(),
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

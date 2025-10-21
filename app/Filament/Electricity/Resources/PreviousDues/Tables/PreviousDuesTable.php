<?php

namespace App\Filament\Electricity\Resources\PreviousDues\Tables;

use App\Filament\Electricity\Resources\PreviousDues\Pages\PayPreviousDue;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PreviousDuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('fields.name'))
                    ->searchable(),
                TextColumn::make('customer.shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(__('fields.total_amount'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_paid')
                    ->label(__('fields.is_paid'))
                    ->boolean(),
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
                Action::make('pay')
                    ->label('পরিশোধ করুন')
                    ->url(fn ($record) => PayPreviousDue::getUrl(['record' => $record->id]))
                    ->icon('heroicon-o-currency-bangladeshi')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_paid),
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

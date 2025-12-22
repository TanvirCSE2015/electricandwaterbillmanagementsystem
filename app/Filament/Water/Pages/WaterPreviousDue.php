<?php

namespace App\Filament\Water\Pages;

use App\Helpers\WaterBillHelper;
use App\Models\WaterCustomer;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class WaterPreviousDue extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.water.pages.water-previous-due';

     protected static ?string $navigationLabel = 'পূর্বের বকেয়া সমুহ'; 
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';
    
    public function getTitle(): string
    {
        return 'পূর্বের বকেয়া সমূহ';
    }

    protected function getTableQuery(): Builder
    {
       return WaterCustomer::query()->where('previous_due','>',0);
    }

    protected function getTableColumns(): array
    {
        return [
           
            TextColumn::make('customer_name')
                ->label(__('water_fields.customer_name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('customer_phone')
                ->label(__('water_fields.customer_phone'))
                ->searchable()
                ->sortable(),
            TextColumn::make('holding_number')
                ->label(__('water_fields.holding_number'))
                ->searchable()
                ->sortable(),
            TextColumn::make('previous_due')
                ->label(__('water_fields.previous_due'))
                ->money('bdt', true)
                ->sortable(),
        ];
    }
    
    protected function getTableActions(): array
    {
        return[
            Action::make('payment')
            ->label('বকেয়া পরিশোধ')
            ->icon('heroicon-o-currency-bangladeshi')
            ->fillForm(fn (WaterCustomer $record) => [
                'previous_due' => $record->previous_due,
            ])
            ->schema([
                Grid::make(2)
                ->schema([
                    TextInput::make('previous_due')
                        ->label('বকেয়া পরিমাণ')
                        ->disabled(),
                    TextInput::make('payment_amount')  
                        ->label('পরিশোধের পরিমাণ')
                        ->required()
                        ->numeric(),
                ]),
            ])
            ->action(function (WaterCustomer $record, array $data): void {
                $paidAmount = $data['payment_amount'];
                $userId = auth()->id();

                WaterBillHelper::previousDueInvoice($record->id, $userId, $paidAmount);
            })
            ->modalHeading('পূর্বের বকেয়া পরিশোধ'),
        ];
    }
}
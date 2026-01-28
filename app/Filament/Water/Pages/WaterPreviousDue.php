<?php

namespace App\Filament\Water\Pages;

use App\Helpers\WaterBillHelper;
use App\Models\WaterCustomer;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class WaterPreviousDue extends Page implements HasTable,HasForms
{
    use InteractsWithTable, InteractsWithForms;
    protected string $view = 'filament.water.pages.water-previous-due';

     protected static ?string $navigationLabel = 'পূর্বের বকেয়া সমুহ'; 
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';
    
    public ?string $type = null;
    public function getTitle(): string
    {
        return 'পূর্বের বকেয়া সমূহ';
    }

    protected function getFormSchema(): array
    {
        return[
            Grid::make(4)
            ->schema([
                Select::make('type')
                    ->options([
                        'water' => 'পানি',
                        'security' => 'নিরাপত্তা',
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn()=>$this->resetTable())
                    ->required(),
            ]),
        ];
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
            TextColumn::make('s_previous_due')
                ->label(__('water_fields.s_previous_due'))
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
                's_previous_due' => $record->s_previous_due,
                'payment_amount' => 0,
                's_payment_amount' => 0,

            ])
            ->schema([
                Section::make('পানির বকেয়া পরিশোধ করুন')
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            TextInput::make('previous_due')
                                ->label('বকেয়া পরিমাণ')
                                ->disabled(),
                            TextInput::make('payment_amount')  
                                ->label('পরিশোধের পরিমাণ')
                                ->required()
                                ->numeric()
                                ->default(0),
                        ]),
                ]),
               Section::make('নিরাপত্তার বকেয়া পরিশোধ করুন')
               ->schema([
                   Grid::make(2)
                   ->schema([
                       TextInput::make('s_previous_due')
                           ->label('নিরাপত্তার বকেয়া পরিমাণ')
                           ->disabled(),
                       TextInput::make('s_payment_amount')  
                           ->label('নিরাপত্তার পরিশোধের পরিমাণ')
                           ->required()
                           ->default(0)
                           ->numeric(),
                   ]), 
               ]),
            ])
            ->action(function (WaterCustomer $record, array $data): void {
                $paidAmount = $data['payment_amount'];
                $s_paidAmount = $data['s_payment_amount'];
                $userId = auth()->id();

                WaterBillHelper::previousDueInvoice($record->id, $userId, $paidAmount, $s_paidAmount);
            })
            ->modalHeading('পূর্বের বকেয়া পরিশোধ'),
        ];
    }
}
<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Pages;

use App\Filament\Electricity\Resources\ElectricBills\ElectricBillResource;
use App\Models\ElectricBillSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class CustomIndex extends Page implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = ElectricBillResource::class;

    protected string $view = 'filament.electricity.resources.electric-bills.pages.custom-index';

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    public function getTitle(): string
    {
        return 'বিদ্যুৎ বিল সমূহ';
    }

    public ?int $month=null;
    public ?int $year=null;
    public function mount(): void
    {
        $this->month = date('m')-1;
        $this->year = date('Y'); 
    }
    protected function getFormSchema(): array
    {
        return [
            Grid::make(4)->schema([
                Select::make('month')
                ->label(__('fields.billing_month'))
                ->options(function () {
                    $months = [
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ];
                    $currentMonth = date('n'); // 1-12
                    // Only include months up to last month
                    return array_slice($months, 0, $currentMonth - 1, true);
                })
                ->reactive()
                ->afterStateUpdated(fn ()=> $this->resetTable())
                ->required(),
            Select::make('year')
                ->label(__('fields.billing_year'))
                ->options(function () {
                    $currentYear = date('Y');
                    $years = [];
                    for ($year = $currentYear; $year >= 2000; $year--) {
                        $years[$year] = $year;
                    }
                    return $years;
                })
                ->required(),
            ]),
        ];
    }

    protected function getTableQuery()
    {
    //     $query = \App\Models\ElectricBill::query()
    //     ->where(['billing_month'=> $this->month, 'billing_year' => $this->year,'is_paid' => false]);
    //     $settings = ElectricBillSetting::latest()->first();
    // // Process each record and apply surcharge if overdue
    //     $query->get()->each(function ($bill) use ($settings) {
    //     $dueDate = \Carbon\Carbon::parse($bill->payment_date);
    //     $now = now();

    //     // Only apply once
    //     if ($now->gt($dueDate) && $bill->surcharge == 0) {
    //         $surcharge = $bill->total_amount * ($settings->surcharge / 100);

    //         $bill->surcharge = $surcharge;
    //         $bill->total_amount += $surcharge;
    //         $bill->save();
    //     }
    // });

    // return $query;
        return \App\Models\ElectricBill::query()->where(['billing_month'=> $this->month,'billing_year'=> $this->year]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('customer.name')->label(__('fields.name'))->searchable()->sortable(),
            TextColumn::make('customer.shop_no')->label(__('fields.shop_no'))->searchable()->sortable(),
            TextColumn::make('customer.activeMeter.meter_number')->label(__('fields.meter_number'))->searchable()->sortable(),
            TextColumn::make('bill_month_name')->label(__('fields.billing_month'))
            ->formatStateUsing(fn ($state) => $this->en2bn($state))
            ->sortable(),
            TextColumn::make('billing_year')->label(__('fields.billing_year'))->sortable()
            ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('consumed_units')->label(__('fields.consume_unit'))->sortable()
            ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('total_amount')->label(__('fields.total_amount'))
            ->getStateUsing(function ($record) {
                if($record->surcharge > 0){
                    return $record->total_amount;
                }else{
                    $surcharge= \App\Helpers\ElectricBillHelper::calculateSurcharge($record);
                    return $record->total_amount + $surcharge;
                }
            })
            ->sortable()
            ->formatStateUsing(fn ($state) => $this->en2bn(number_format($state, 2))),
            IconColumn::make('is_paid')->label(__('fields.is_paid'))->boolean(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')
                ->label('View')
                ->url(fn (\App\Models\ElectricBill $record): string => route('filament.electricity.resources.electric-bills.view', $record))
                ->icon('heroicon-o-eye'),
            Action::make('edit')
                ->label('Edit')
                // ->url(fn (\App\Models\ElectricBill $record): string => route('filament.electricity.resources.meter-readings.edit', $record->reading->id))
                ->icon('heroicon-o-pencil')
                ->schema([
                    TextInput::make('previous_reading')
                        ->label(__('fields.previous_reading'))
                        ->disabled()
                        ->dehydrated(true)
                        ->default(fn (\App\Models\ElectricBill $record) => $record->reading->previous_reading),
                    TextInput::make('current_reading')
                        ->label(__('fields.current_reading'))
                        ->default(fn (\App\Models\ElectricBill $record) => optional($record->reading)->current_reading),
                       
                ])
                ->action(function (array $data, \App\Models\ElectricBill $record) {
                    $record->reading->update([
                        'previous_reading' => $data['previous_reading'],
                        'current_reading' => $data['current_reading'],
                        'consume_unit' => $data['current_reading']-$data['previous_reading'],
                    ]);
                    $this->resetTable();
                })->modalWidth('md')
                ->successNotification(
                    \Filament\Notifications\Notification::make()
                        ->title('বিল সফলভাবে হালনাগাদ হয়েছে') 
                        ->success()
                )
                ->failureNotification(
                    \Filament\Notifications\Notification::make()
                        ->title('বিল হালনাগাদ করতে ব্যর্থ হয়েছে')
                        ->danger()
                )
                ->modalHeading('বিল হালনাগাদ করুন'),
        ];
    }
    protected function getTableBulkActions(): array
    {
        return [];
    }

   protected function getHeaderActions(): array
   {
         return [
              Action::make('generateBills')
                ->label('Generate Bills')
                ->requiresConfirmation()
                ->url(fn () => route('filament.electricity.pages.billgenerate'))
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),
            
         ];
   }


}

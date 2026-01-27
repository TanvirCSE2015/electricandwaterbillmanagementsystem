<?php

namespace App\Filament\Water\Pages;

use App\Helpers\WaterBillHelper;
use App\Models\SecurityBill;
use App\Models\WaterBill;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use UnitEnum;

class LeaserReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected string $view = 'filament.water.pages.leaser-report';
    protected static ?string $title = 'লেজার রিপোর্ট';
    protected static ?string $navigationLabel = 'লেজার রিপোর্ট';
    protected static string | UnitEnum | null $navigationGroup = 'রিপোর্ট সমূহ';
    protected static ?int $navigationSort = 2;

    public ?int $month=null;
    public ?int $year=null;
    public ?string $type = null;

    public function mount(){
        $this->year=date('Y');
        $this->month=date('m');
        $this->type='water';
    }

    protected function getFormSchema(): array
    {
        return[
            Grid::make(4)
                ->schema([
                    Select::make('month')
                        ->label('মাস')
                        ->options([
                            1 => 'জানুয়ারি',
                            2 => 'ফেব্রুয়ারি',
                            3 => 'মার্চ',
                            4 => 'এপ্রিল',
                            5 => 'মে',
                            6 => 'জুন',
                            7 => 'জুলাই',
                            8 => 'আগস্ট',
                            9 => 'সেপ্টেম্বর',
                            10 => 'অক্টোবর',
                            11 => 'নভেম্বর',
                            12 => 'ডিসেম্বর',
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->resetTable()),
                    Select::make('year')
                        ->label('বছর')
                        ->options(function () {
                            $currentYear = date('Y');
                            $years = [];
                            for ($year = $currentYear; $year >= 2023; $year--) {
                                $years[$year] = $year;
                            }
                            return $years;
                        })
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->resetTable()),
                    Select::make('type')
                        ->label('রিপোর্টের ধরন')
                        ->options([
                            'water' => 'পানি বিল রিপোর্ট',
                            'security' => 'নিরাপত্তা বিল রিপোর্ট',
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->resetTable()),
                ]),
        ];
    }

    protected function getTableQuery()
    {
        $query=null;
        if($this->type=='water'){
                $query=WaterBill::query()
                ->select('*')
                ->selectRaw("
                CASE
                    WHEN water_bills.bill_due_date < CURDATE()
                    THEN ROUND(
                        water_bills.total_amount + (water_bills.total_amount * surcharge_percent / 100) ,
                        2
                    )
                    ELSE water_bills.total_amount
                END AS payable_amount
            ")

            ->selectRaw("
                CASE
                    WHEN water_bills.bill_due_date < CURDATE()
                    THEN ROUND(water_bills.total_amount * surcharge_percent / 100, 2)
                    ELSE 0
                END AS calculated_surcharge
            ")
            ->when($this->month, fn ($q) => $q->where('water_bill_month', $this->month))
            ->when($this->year, fn ($q) => $q->where('water_bill_year', $this->year));
        }else{
            // Security Bill Query

            $query=SecurityBill::query()
            ->when($this->month, fn ($q) => $q->where('s_bill_month', $this->month))
            ->when($this->year, fn ($q) => $q->where('s_bill_year', $this->year));
        }
        return $query;
    }

    protected function getTableColumns(): array
    {
        if($this->type=='water'){
            return[
                 TextColumn::make('waterCustomer.customer_name')
                    ->label(__('water_fields.customer_name'))
                    ->searchable(),
                TextColumn::make('water_bill_month')
                    ->label(__('water_fields.water_bill_month'))
                    ->getStateUsing(fn ( $record) => \Carbon\Carbon::create()->month($record->water_bill_month)->translatedFormat('F') 
                    . '-' . WaterBillHelper::en2bn($record->water_bill_year))
                    ->sortable(),
                // TextColumn::make('water_bill_year')
                //     ->label(__('water_fields.water_bill_year'))
                //     ->formatStateUsing(fn ( $state) => WaterBillHelper::en2bn($state))
                //     ->sortable(),
                TextColumn::make('base_amount')
                    ->label('ফ্ল্যাট বিল')
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                    TextColumn::make('cons_amount')
                    ->label('নির্মাধীন বিল')
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('total_amount')
                    ->label(__('water_fields.total_amount'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('surcharge_percent')
                    ->label(__('water_fields.surcharge_percent'))
                    ->formatStateUsing(fn ( $state) => WaterBillHelper::en2bn($state) . '%')
                    ->sortable(),
                TextColumn::make('calculated_surcharge')
                    ->label(__('water_fields.surcharge_amount'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                TextColumn::make('payable_amount')
                    ->label('পানি বিল')
                    ->numeric()
                    ->sortable()
                    ->suffix(' ৳'),
                IconColumn::make('is_paid')
                    ->label('বিল পরিশোধিত')
                    ->boolean()
                    ->sortable(),
            ];
        }else if($this->type=='security'){
            return[
                TextColumn::make('waterCustomer.customer_name')
                   ->label(__('water_fields.customer_name'))
                   ->searchable(),
               TextColumn::make('s_bill_month')
                   ->label('নিরাপত্তা বিল মাস')
                   ->getStateUsing(fn ( $record) => \Carbon\Carbon::create()->month($record->s_bill_month)->translatedFormat('F') 
                   . '-' . WaterBillHelper::en2bn($record->s_bill_year))
                   ->sortable(),
                TextColumn::make('base_amount')
                   ->label('ফ্ল্যাট নিরাপত্তা বিল')
                   ->numeric()
                   ->sortable()
                   ->suffix(' ৳'),
                TextColumn::make('s_cons_amount')
                   ->label('নির্মাধীন নিরাপত্তা বিল')
                   ->numeric()
                   ->sortable()
                   ->suffix(' ৳'),
               TextColumn::make('total_amount')
                   ->label('মোট বিল')
                   ->numeric()
                   ->sortable()
                   ->suffix(' ৳'),
               IconColumn::make('is_paid')
                   ->label('বিল পরিশোধিত')
                   ->boolean()
                   ->sortable(),
           ];
        }
        return[];
    }

    protected function getTableHeaderActions(): array
    {
        return[
            Action::make('print')
                ->label('প্রিন্ট করুন')
                ->url(fn () => route('water-laser-report.print', [
                    'month' => $this->month,
                    'year' => $this->year,
                    'type' => $this->type,
                ]))
                ->openUrlInNewTab(),
        ];
    }


}

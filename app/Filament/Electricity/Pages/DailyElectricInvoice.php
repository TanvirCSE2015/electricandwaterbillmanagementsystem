<?php

namespace App\Filament\Electricity\Pages;

use Dom\Text;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Rakibhstu\Banglanumber\NumberToBangla;
use UnitEnum;

class DailyElectricInvoice extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    protected string $view = 'filament.electricity.pages.daily-electric-invoice';

    protected static ?string $navigationLabel = 'বকেয়া আদাইয়ের রিপোর্ট';

    protected static string | UnitEnum | null $navigationGroup = 'রিপোর্ট সমূহ';
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    public ?string $date=null;

    public ?string $type=null;

    public ?int $month=null;
    public ?string $year=null;

    public ?int $block_id=null;

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->type='daily';
        $this->month=date('n');
        $this->year=date('Y');
    }

    public function getTitle(): string
    {
        return 'বকেয়া আদাইয়ের রিপোর্ট';
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(4)
                ->schema([
                    Select::make('type')
                    ->label('রিপোর্টের ধরন')
                    ->options([
                        'daily'=>'দৈনিক',
                        'monthly'=>'মাসিক',
                        'yearly'=>'বার্ষিক',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function($state, callable $set){
                        if ($state === 'daily') {
                                $set('date', now()->format('Y-m-d'));
                                $set('month', null);
                                $set('year', null);
                        }

                        if ($state === 'monthly') {
                            $set('date', null);
                            $set('month', now()->month);
                            $set('year', now()->year);
                        }

                        if ($state === 'yearly') {
                            $set('date', null);
                            $set('month', null);
                            $set('year', now()->year);
                        }
                        $this->resetTable();
                    }),
                    DatePicker::make('date')
                        ->label('তারিখ')
                        ->default(now()->format('Y-m-d'))
                        ->format('Y-m-d')
                        ->displayFormat('Y-m-d')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->reactive()
                        ->afterStateUpdated(function ($set, $state) {
                            $this->date = is_string($state) ? $state : $state->format('Y-m-d');
                            $this->resetTable();
                        })
                        ->visible(fn (callable $get) => $get('type') === 'daily')
                        ->required(),
                    Select::make('month')
                    ->label('মাস নির্বাচন করুন')
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
                    return $months;
                })
                ->visible(fn (callable $get) => $get('type') === 'monthly')
                ->default(date('n'))
                ->reactive()
                ->afterStateUpdated(function($state){
                    return $this->month=$state;
                }),

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
                ->visible(fn (callable $get) => $get('type') === 'yearly' ||  $get('type') === 'monthly')
                ->reactive()
                ->required()
                ->afterStateUpdated(function($state){
                    return $this->year=$state;
                }),
                Select::make('block_id')
                    ->label('ব্লক')
                    ->searchable()
                    ->placeholder('ব্লক নির্বাচন করুন')
                    ->options(\App\Models\Blocks::query()->pluck('bolck_name','id'))
                    ->reactive()
                    ->afterStateUpdated(fn()=>$this->resetTable()),
            ]),

                
        ];
    }


    protected function getTableQuery(): Builder
    {
        // return \App\Models\ElectricInvoice::query()
        //     ->when($this->date, function ($query) {
        //         $query->whereDate('invoice_date', $this->date);
        //     })
        //     ->when($this->month && $this->year, function($query){
        //         $query->where(['invoice_month' => $this->month, 'invoice_year' => $this->year]);
        //     })
        //     ->when($this->year , function($query){
        //         $query->where(['invoice_year' => $this->year]);
        //     });
        $type=$this->form->getState()['type'];
        $date=$this->form->getState()['date'] ?? null;
        $month=$this->form->getState()['month'] ?? null;
        $year=$this->form->getState()['year'] ?? null;
        $blockId=$this->form->getState()['block_id'] ?? null;
        $query = \App\Models\ElectricInvoice::query();

        if ($this->form->getState()['type'] === 'daily' && $date) {
            $query->whereDate('invoice_date', $date)
            ->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)));
        }

        if ($this->form->getState()['type'] === 'monthly' && $month && $year) {
            return $query
                ->where(['invoice_month'=> $month,'invoice_year'=>$year])
                ->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)))
                
               ->selectRaw('ROW_NUMBER() OVER() as id,
                    invoice_date,
                    SUM(total_amount) as total_amount,
                    invoice_month as month,
                    invoice_month_name as month_name,
                    invoice_year as year
                ')
                ->groupByRaw('invoice_date,month,month_name,year');
        }

        if ($this->form->getState()['type'] === 'yearly' && $year) {
            return $query->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)))
             ->selectRaw('
                ROW_NUMBER() OVER() as id,
                 invoice_month,
                 invoice_year,
                 invoice_month_name,
                SUM(total_amount) as total_amount
            ')
            ->where('invoice_year', $year)
            ->groupByRaw('invoice_month,invoice_year,invoice_month_name');
        }

        return $query;
        
    }

    protected function getTableColumns(): array
    {
        

         if ($this->form->getState()['type'] === 'daily') {
            return [
                TextColumn::make('invoice_number')->label('রশিদ নং')->formatStateUsing(fn($state) => $this->en2bn($state))->searchable(),
                TextColumn::make('invoice_date')->label('রশিদ তারিখ')->date()->formatStateUsing(fn($state) => $this->en2bn($state))->searchable(),
                TextColumn::make('customer.name')->label('গ্রাহক নাম')->searchable(),
                TextColumn::make('customer.shop_no')->label('দোকান নং')->searchable(),
                TextColumn::make('Month')->label('বিলের মাস')->getStateUsing(function ($record){
                    if ($record->to_month){
                        return $record->from_month . ' হতে '. $record->to_month;
                    }
                    return $record->from_month;
                })->formatStateUsing(fn($state)=>$this->en2bn($state)),
                TextColumn::make('total_amount')->label('পরিশোধিত পরিমাণ')
                    ->formatStateUsing(fn($state) => (new NumberToBangla())->bnCommaLakh($state))
                    ->suffix(' ৳')
                    ->searchable(),
                // TextColumn::make('total_amount')
                // ->label('পরিশোধিত পরিমাণ')
                // ->summarize(Sum::make()->label('মোট পরিশোধিত')),
            ];
        }

        if ($this->form->getState()['type'] === 'monthly') {
            return [
                TextColumn::make('invoice_date')->label('তারিখ')->formatStateUsing(fn($state) => $this->en2bn($state))
                    ->searchable(),
                TextColumn::make('month_name')->label('মাস')->formatStateUsing(fn($state) => $this->en2bn($state))
                    ->searchable(),
                TextColumn::make('year')->label('বছর')->formatStateUsing(fn($state) => $this->en2bn($state)),
                TextColumn::make('total_amount')->label('মোট আদায়')
                    ->formatStateUsing(fn($state) => (new NumberToBangla())->bnCommaLakh($state))->suffix(' ৳')
                    ->searchable(),
            ];
        }

        if ($this->form->getState()['type'] === 'yearly') {
            return [
                TextColumn::make('invoice_month_name')->label('মাস')
                    ->formatStateUsing(fn($state) => $this->en2bn($state)),
                TextColumn::make('invoice_year')->label('বছর')->formatStateUsing(fn($state) => $this->en2bn($state)),
                TextColumn::make('total_amount')->label('মোট আদায়')
                    ->formatStateUsing(fn($state) => (new NumberToBangla())->bnCommaLakh($state))->suffix(' ৳')
                    ->searchable(),
            ];
        }

        return [];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('print')
            ->label('প্রিন্ট')
            ->url(fn ($record) => route('electric-receipt.print', [
                'id'=>$record->id,
            ]))
            ->icon('heroicon-o-printer')
            ->openUrlInNewTab()
            ->hidden(fn()=>$this->form->getState()['type']==='monthly' || $this->form->getState()['type']==='yearly')
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            ExportAction::make('Export')
                ->label('এক্সেলে ডাউনলোড')
                ->color('success')
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename('বিদ্যুৎ বিল রিপোর্ট-' . now()->format('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),          
                ]),

            Action::make('printReport')
            ->label('প্রিন্ট রিপোর্ট')
            ->icon('heroicon-o-printer')
            ->url(fn () => route('daily-electric-invoice.print', [
                'type' => $this->form->getState()['type'] ?? 'daily',
                'date' => $this->form->getState()['date'] ?? null,
                'month' => $this->form->getState()['month'] ?? null,
                'year' => $this->form->getState()['year'] ?? null,
                'block_id' => $this->form->getState()['block_id'] ?? null,
            ]))
            ->openUrlInNewTab(),

            
        ];
    }

    public function getTotalAmount(): string
    {
        $type = $this->form->getState()['type'] ?? 'daily';
        $date = $this->form->getState()['date'] ?? null;
        $month = $this->form->getState()['month'] ?? null;
        $year = $this->form->getState()['year'] ?? null;
        $blockId=$this->form->getState()['block_id'] ?? null;

        $query = \App\Models\ElectricInvoice::query();

        if ($type === 'daily' && $date) {
            $query->whereDate('invoice_date', $date)
            ->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)));
        }

        if ($type === 'monthly' && $month && $year) {
            $query->where(['invoice_month'=> $month, 'invoice_year'=> $year])
            ->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)));
        }

        if ($type === 'yearly' && $year) {
            $query->where('invoice_year', $year)
            ->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)));
        }

        $sum = $query->sum('total_amount');

        return (new NumberToBangla())->bnCommaLakh($sum) . ' /=';
    }


}

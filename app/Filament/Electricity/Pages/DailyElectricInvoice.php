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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Rakibhstu\Banglanumber\NumberToBangla;

class DailyElectricInvoice extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    protected string $view = 'filament.electricity.pages.daily-electric-invoice';

    protected static ?string $navigationLabel = 'দৈনিক বকেয়া আদায়';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

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

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->type='daily';
        $this->month=date('n');
        $this->year=date('Y');
    }

    public function getTitle(): string
    {
        return 'দৈনিক বকেয়া আদায়';
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
            ]),
                
        ];
    }


    protected function getTableQuery(): Builder
    {
        return \App\Models\ElectricInvoice::query()
            ->when($this->date, function ($query) {
                $query->whereDate('invoice_date', $this->date);
            })
            ->when($this->month && $this->year, function($query){
                $query->where(['invoice_month' => $this->month, 'invoice_year' => $this->year]);
            })
            ->when($this->year , function($query){
                $query->where(['invoice_year' => $this->year]);
            });
        
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('invoice_number')
                ->label('রশিদ নং')
                ->formatStateUsing(fn($state) => $this->en2bn($state))
                ->searchable(),
            TextColumn::make('invoice_date')
                ->label('রশিদ তারিখ')
                ->date()
                ->formatStateUsing(fn($state) => $this->en2bn($state))
                ->searchable(),
            TextColumn::make('customer.name')->label('গ্রাহক নাম')->searchable(),
            TextColumn::make('customer.shop_no')->label('দোকান নং')->searchable(),
            TextColumn::make('Month')->label('বিলের মাস')
            ->getStateUsing(function ($record){
                if ($record->to_month){
                    return $record->from_month . ' হতে '. $record->to_month;
                }
                return $record->from_month;
            })
            ->formatStateUsing(fn($state)=>$this->en2bn($state)),
            
             TextColumn::make('total_amount')->label('পরিশোধিত পরিমাণ')
                ->formatStateUsing(function($state){
                    $numto=$numto = new NumberToBangla();
                    return $numto->bnCommaLakh($state);
                })->searchable(),
            ];
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
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('printReport')
            ->label('প্রিন্ট রিপোর্ট')
            ->icon('heroicon-o-printer')
            ->url(fn () => route('daily-electric-invoice.print', [
                'type' => $this->form->getState()['type'] ?? 'daily',
                'date' => $this->form->getState()['date'] ?? null,
                'month' => $this->form->getState()['month'] ?? null,
                'year' => $this->form->getState()['year'] ?? null,
            ]))
            ->openUrlInNewTab()
        ];
    }

}

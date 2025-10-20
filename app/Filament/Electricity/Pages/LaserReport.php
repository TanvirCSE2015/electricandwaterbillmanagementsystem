<?php

namespace App\Filament\Electricity\Pages;

use App\Models\Customer;
use App\Models\ElectricBill;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use UnitEnum;

class LaserReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable,InteractsWithForms;
    protected string $view = 'filament.electricity.pages.laser-report';

    protected static ?string $navigationLabel = 'লেজার';

    protected static string | UnitEnum | null $navigationGroup = 'লেজার সমূহ';
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    public ?int $customer_id=null;
    public ?int $month=null;
    public ?int $year=null;
    public ?int $block_id=null;

    public function getTitle(): string
    {
        return 'লেজার রিপোর্ট';
    }

    

    public function mount(){
        $this->year=now()->format('Y');
    }


    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    protected function getFormSchema(): array
    {
        return[
             Grid::make(4)
             ->schema([
                Select::make('customer_id')
                  ->label('গ্রাহক')
                  ->placeholder('গ্রাহক নির্বাচন করুন')
                  ->options(Customer::query()->pluck('shop_no','id'))
                  ->searchable()
                  ->reactive(),
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
                // ->visible(fn (callable $get) => $get('type') === 'monthly')
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
                // ->visible(fn (callable $get) => $get('type') === 'yearly' ||  $get('type') === 'monthly')
                ->reactive()
                ->required()
                ->afterStateUpdated(function($state){
                    return $this->year=$state;
                }),

                Select::make('block_id')
                    ->label('ব্লক')
                    ->placeholder('ব্লক নির্বাচন করুন')
                    ->options(\App\Models\Blocks::query()->pluck('bolck_name','id'))
                    ->reactive()
                    ->afterStateUpdated(fn()=>$this->resetTable()),
             ])
            ];
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $customerId=$this->form->getState()['customer_id'];
        $month=$this->form->getState()['month'];
        $year=$this->form->getState()['year'];
        $blockId=$this->form->getState()['block_id'];
        return ElectricBill::query()
            ->with('customer')
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($month, fn($q) => $q->where('billing_month', $month))
            ->when($year, fn($q) => $q->where('billing_year', $year))
            ->when($blockId, fn($q) => $q->whereHas('customer', fn($q2) => $q2->where('block_id', $blockId)))
            ->select('*')
            ->selectRaw("
                ROUND(
                    CASE 
                        WHEN CURDATE() > due_date 
                        THEN total_amount * surcharge_percentage
                        ELSE 0 
                    END) AS calculated_surcharge
            ")
            ->selectRaw("
            ROUND(total_amount + 
                CASE 
                    WHEN CURDATE() > due_date 
                    THEN total_amount * surcharge_percentage
                    ELSE 0 
                END) AS grand_total
            
            ");
    }

    protected function getTableColumns(): array
    {
        return[
            TextColumn::make('customer.name')
                        ->label(__('fields.name'))
                        ->searchable()
                        ->sortable(),
            TextColumn::make('customer.shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable()
                    ->sortable(),
            TextColumn::make('bill_month_name')
                    ->label(__('fields.bill_month_name'))
                    ->formatStateUsing(fn($state)=>$this->en2bn($state)),
            TextColumn::make('reading')
                    ->label('মিটার রিডিং')
                    ->getStateUsing(function ($record){
                        return $record->reading->previous_reading . ' - ' . $record->reading->current_reading;
                })->formatStateUsing(fn($state)=>$this->en2bn($state)),
            TextColumn::make('consumed_units')
                    ->label(__('fields.consume_unit'))
                    ->formatStateUsing(fn($state)=>$this->en2bn($state)),
            TextColumn::make('calculated_surcharge')
                    ->label(__('fields.surcharge'))
                    ->formatStateUsing(fn($state)=>$this->en2bn($state)),
            TextColumn::make('grand_total')
                    ->label(__('fields.total_amount'))
                    ->formatStateUsing(fn($state)=>$this->en2bn($state)),
            IconColumn::make('is_paid')
                    ->label(__('fields.is_paid'))
                    ->boolean(),
        ];
    }


    protected function getTableHeaderActions(): array
    {
        return[
            ExportAction::make('ExportExcel')
                ->label('এক্সেলে ডাউনলোড')
                ->color('success')
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename('লেজার রিপোর্ট_' . now()->format('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),          
                ]),
            
             Action::make('printReport')
            ->label('প্রিন্ট লেজার')
            ->icon('heroicon-o-printer')
            ->url(fn () => route('electric-laser-report.print', [
                'customer_id' => $this->form->getState()['customer_id'] ?? null,
                'month' => $this->form->getState()['month'] ?? null,
                'year' => $this->form->getState()['year'] ?? null,
                'block_id' => $this->form->getState()['block_id'] ?? null,
            ]))
            ->openUrlInNewTab(),
        ];
    }

}

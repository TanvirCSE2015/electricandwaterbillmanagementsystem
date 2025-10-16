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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use UnitEnum;

class UnpaidElectricBills extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;
    protected string $view = 'filament.electricity.pages.unpaid-electric-bills';

    protected static ?string $navigationLabel = 'অনাদায় রিপোর্ট';

    protected static string | UnitEnum | null $navigationGroup = 'রিপোর্ট সমূহ';
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    public ?string $type=null;
    public ?int $customer_id=null;

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    public function getTitle(): string
    {
        return 'অনাদায় রিপোর্ট';
    }

    public function mount(){
        $this->type='short';
    }

    protected function getFormSchema(): array
    {
        return [

            Grid::make(4)
                ->schema([
                    Select::make('type')
                        ->label('রিপোর্টের ধরন')
                        ->options([
                            'short'=>'সংক্ষিপ্ত',
                            'detailts'=>'বিস্তারিত'
                        ])
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn()=>$this->resetTable()),
                    
                    Select::make('customer_id')
                        ->label('গ্রাহক')
                        ->placeholder('গ্রাহক নির্বাচন করুন')
                        ->options(Customer::query()->pluck('shop_no','id'))
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn()=>$this->resetTable()),
                ])
            

        ];
    }

    protected function getTableQuery(): Builder
    {
        $customerId=$this->form->getState()['customer_id'];
        if($this->form->getState()['type']==='short'){
            return Customer::query()
            ->whereHas('bills', fn($q) => $q->where('is_paid', false))
            ->when($customerId, fn($q) => $q->where('id', $customerId))
            ->addSelect([
                // Total unpaid amount
                'total_amount' => ElectricBill::selectRaw('SUM(total_amount)')
                    ->whereColumn('customer_id', 'customers.id')
                    ->where('is_paid', false),
                // Total surcharge dynamically
                'total_surcharge' => ElectricBill::selectRaw("
                    SUM(
                        CASE 
                            WHEN CURDATE() > due_date 
                            THEN total_amount * surcharge_percentage
                            ELSE 0 
                        END
                    )
                ")
                ->whereColumn('customer_id', 'customers.id')
                ->where('is_paid', false),
                // Grand total (amount + surcharge)
                'grand_total' => ElectricBill::selectRaw("
                    SUM(total_amount + 
                        CASE 
                            WHEN CURDATE() > due_date 
                            THEN total_amount * surcharge_percentage
                            ELSE 0 
                        END
                    )
                ")
                ->whereColumn('customer_id', 'customers.id')
                ->where('is_paid', false),
            ]);
        }

        return ElectricBill::query()
            ->where('is_paid', false)
            ->with('customer')
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->select('*')
            ->selectRaw("
                CASE 
                    WHEN CURDATE() > due_date 
                    THEN total_amount * surcharge_percentage / 100 * DATEDIFF(CURDATE(), due_date)
                    ELSE 0 
                END AS calculated_surcharge
            ")
            ->selectRaw("
                total_amount + 
                CASE 
                    WHEN CURDATE() > due_date 
                    THEN total_amount * surcharge_percentage / 100 * DATEDIFF(CURDATE(), due_date)
                    ELSE 0 
                END AS grand_total
            ");
    }

    protected function getTableColumns(): array
    {
        if($this->form->getState()['type']==='short'){
            return[
                TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shop_no')
                    ->label(__('fields.shop_no'))
                    ->searchable()
                    ->sortable(), 
                TextColumn::make('meters.meter_number')
                    ->label(__('fields.meter_number'))
                    ->searchable()
                    ->sortable(),  
                TextColumn::make('grand_total')
                    ->label(__('fields.total_amount'))
                    // ->getStateUsing(function ($record) {
                    //     $bills=$record->bills;
                    //     $dueTotal=0;
                    //     foreach ($bills as $bill) {
                    //        if ($bill->is_paid) {
                    //            continue;
                    //         }else{
                    //             if($bill->surcharge > 0){
                    //                 $dueTotal += $bill->total_amount;
                    //                 continue;
                    //             }else{
                    //                     $surcharge= \App\Helpers\ElectricBillHelper::calculateSurcharge($bill);
                    //                     $dueTotal += $bill->total_amount + $surcharge;
                    //             }
                    //         }
                    //     }
                    //     return $dueTotal;
                    // })
                    ->formatStateUsing(fn($state)=>$this->en2bn($state))
                    ->sortable(),
                ];
            
        }

        return [
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
                TextColumn::make('consumed_units')
                        ->label(__('fields.consume_unit'))
                        ->formatStateUsing(fn($state)=>$this->en2bn($state)),
                TextColumn::make('grand_total')
                        ->label(__('fields.total_amount'))
                        // ->formatStateUsing(fn($state)=>$this->en2bn($state))
                        // ->getStateUsing(function($record){
                        //      $dueTotal=0;
                        //      if($record->surcharge > 0){
                        //         $dueTotal += $record->total_amount;
                        //     }else{
                        //             $surcharge= \App\Helpers\ElectricBillHelper::calculateSurcharge($record);
                        //             $dueTotal += $record->total_amount + $surcharge;
                        //     }
                        //     return $dueTotal;
                        // })
                        ->formatStateUsing(fn($state)=>$this->en2bn($state)),
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
                        ->withFilename('বিদ্যুৎ বিলের বকেয়া রিপোর্ট_' . now()->format('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),          
                ]),
            
             Action::make('printReport')
            ->label('প্রিন্ট রিপোর্ট')
            ->icon('heroicon-o-printer')
            ->url(fn () => route('unpaid-electric-bills-report.print', [
                'type' => $this->form->getState()['type'] ?? 'daily',
                'customer_id' => $this->form->getState()['customer_id'] ?? null,
            ]))
            ->openUrlInNewTab(),
        ];
    }


}

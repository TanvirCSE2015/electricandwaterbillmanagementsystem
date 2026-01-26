<?php

namespace App\Filament\Water\Resources\PayWaterBills\Pages;

use App\Filament\Water\Resources\PayWaterBills\PayWaterBillResource;
use App\Helpers\WaterBillHelper;
use App\Models\WaterBill;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class WaterBillDetails extends Page implements HasTable,HasForms
{
    use InteractsWithRecord, InteractsWithTable, InteractsWithForms;

    protected static string $resource = PayWaterBillResource::class;

    protected string $view = 'filament.water.resources.pay-water-bills.pages.water-bill-details';

    public ?int $record_id=null;
    public ?int $count=null;
    public ?int $row=null;
    public $bill;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->bill=WaterBill::where(['water_customer_id'=>$this->record->id,'is_paid'=>false])
                    ->get();
        $this->count=$this->bill->count();  
        $this->row=$this->bill->count();
    }

    public function getTitle(): string
    {
        return 'বকেয়া বিলের বিস্তারিত';
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(4)->schema([
                TextEntry::make('customer_name')
                    ->label(__('water_fields.customer_name'))
                    ->default(fn () => $this->record->customer_name)
                    ->disabled(),
                TextEntry::make('customer_phone')
                    ->label(__('water_fields.customer_phone'))
                    ->default(fn () => $this->record->customer_phone)
                    ->disabled(),
                TextEntry::make('holding_number')
                    ->label(__('water_fields.holding_number'))
                    ->default(fn () => $this->record->holding_number)
                    ->disabled(),
                Select::make('count')
                    ->label('বকেয়া বিলের সংখ্যা')
                    ->options(function () {
                        $options = [];
                        for ($i = 1; $i <= $this->row; $i++) {
                            $options[$i] = $i;
                        }
                        return $options;
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        
                        $this->count = $state;
                        $this->record_id = $state;
                        // dd($this->count);
                        //$this->row = ElectricBill::where('customer_id', $this->record->id)->where('is_paid', false)->count();
                        // $this->tableQueryKey = uniqid();
                        $this->resetTable();
                    }),
            ])
        ];
    }

    protected function getTableQuery():Builder
    {
        // return WaterBill::query()
        // ->leftJoin('security_bills as sb', function ($join) {
        //     $join->on('sb.water_customer_id', '=', 'water_bills.water_customer_id')
        //         ->on('sb.s_bill_month', '=', 'water_bills.water_bill_month')
        //         ->on('sb.s_bill_year', '=', 'water_bills.water_bill_year');
        // })
        // ->select('water_bills.*')

        // // security amount
        // ->selectRaw('COALESCE(sb.total_amount, 0) + s_cons_amount as security_amount')
        
        // ->where(['water_customer_id'=>$this->record->id,'is_paid'=>false])
        // ->select('water_bills.*')
        // ->selectRaw("
        //     CASE
        //         WHEN bill_due_date < CURDATE()
        //         THEN ROUND(
        //             base_amount + (base_amount * surcharge_percent / 100),
        //             2
        //         )
        //         ELSE base_amount
        //     END AS payable_amount
        // ")
        // ->selectRaw("
        //     CASE
        //         WHEN bill_due_date < CURDATE()
        //         THEN ROUND(base_amount * surcharge_percent / 100, 2)
        //         ELSE 0
        //     END AS calculated_surcharge
        // ")

        // ->limit($this->count ?? 1);
       return WaterBill::query()

        ->leftJoin('security_bills as sb', function ($join) {
            $join->on('sb.water_customer_id', '=', 'water_bills.water_customer_id')
                ->on('sb.s_bill_month', '=', 'water_bills.water_bill_month')
                ->on('sb.s_bill_year', '=', 'water_bills.water_bill_year');
        })
        ->select('water_bills.*')
        ->selectRaw('COALESCE(sb.total_amount, 0)  AS security_amount, sb.id as security_id, sb.s_cons_amount as cons_security,sb.base_amount as s_base_amount')
        ->selectRaw("
            CASE
                WHEN water_bills.bill_due_date < CURDATE()
                THEN ROUND(
                    water_bills.total_amount +
                    (water_bills.total_amount * water_bills.surcharge_percent / 100),
                    2
                )
                ELSE water_bills.total_amount
            END AS payable_amount
        ")

        ->selectRaw("
            CASE
                WHEN water_bills.bill_due_date < CURDATE()
                THEN ROUND(
                    water_bills.total_amount * water_bills.surcharge_percent / 100,
                    2
                )
                ELSE 0
            END AS calculated_surcharge
        ")
        ->selectRaw("
        (
            CASE
                WHEN water_bills.bill_due_date < CURDATE()
                THEN ROUND(
                    water_bills.total_amount +
                    (water_bills.total_amount * water_bills.surcharge_percent / 100),
                    2
                )
                ELSE water_bills.total_amount
            END
            +
            COALESCE(sb.total_amount, 0) 
        ) AS total_payable
    ")
        ->where('water_bills.water_customer_id', $this->record->id)
        ->where('water_bills.is_paid', false)

        ->limit($this->count ?? 1);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('water_bill_month')
                ->label(__('water_fields.water_bill_month'))
                ->formatStateUsing(fn ($state) =>
                    \Carbon\Carbon::create()->month($state)->translatedFormat('F')
                ),
            TextColumn::make('water_bill_year')
                ->label(__('water_fields.water_bill_year')),
            TextColumn::make('base_amount')
                ->label('বেসিক বিল')
                ->numeric(),
            TextColumn::make('cons_amount')
                ->label('নির্মাণাধীন বিল')
                ->numeric(),
            TextColumn::make('surcharge_percent')
                ->label(__('water_fields.surcharge_percent'))
                ->numeric(),
            TextColumn::make('calculated_surcharge')
                ->label(__('water_fields.surcharge_amount'))
                ->money('BDT')
                ->numeric(),
            TextColumn::make('payable_amount')
                ->label(__('water_fields.total_amount'))
                ->money('BDT')
                ->summarize(Sum::make()->money('BDT')
                ->label('মোট বকেয়া'))
                ->numeric(),
            
            TextColumn::make('s_base_amount')
                ->label('বেসিক বিল')
                ->money('BDT')
                ->numeric(),
            TextColumn::make('cons_security')
                ->label('নির্মাণাধীন সিকিউরিটি')
                ->money('BDT')
                ->numeric(),
            TextColumn::make('security_amount')
                ->label('সিকিউরিটি বিল')
                ->money('BDT')
                ->summarize(Sum::make()->money('BDT')
                ->label('সিকিউরিটি বিল'))
                ->numeric(),
            TextColumn::make('total_payable')
                ->label('মোট')
                ->money('BDT')
                ->summarize(Sum::make()->money('BDT')
                ->label('মোট পরিশোধযোগ্য'))
                ->getStateUsing(function (WaterBill $record) {
                    return $record->payable_amount + $record->security_amount;
                })
                ->numeric(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payment')
                ->label('পরিশোধ করুন')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    $response = WaterBillHelper::createInvoice(
                        customerId: $this->record->id,
                        securityId: $this->record->security_id,
                        count: $this->count ?? 1,
                        userId: auth()->id()
                    );

                    // $this->notify($response['status'], $response['message']);
                }),
        ];
    }

}

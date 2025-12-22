<?php

namespace App\Filament\Water\Resources\PayWaterBills\Pages;

use App\Filament\Water\Resources\PayWaterBills\PayWaterBillResource;
use App\Helpers\WaterBillHelper;
use App\Models\WaterBill;
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
        return WaterBill::query()->where(['water_customer_id'=>$this->record->id,'is_paid'=>false])
        ->select('*')
        ->selectRaw("
            CASE
                WHEN bill_due_date < CURDATE()
                THEN ROUND(
                    base_amount + (base_amount * surcharge_percent / 100),
                    2
                )
                ELSE base_amount
            END AS payable_amount
        ")
        ->selectRaw("
            CASE
                WHEN bill_due_date < CURDATE()
                THEN ROUND(base_amount * surcharge_percent / 100, 2)
                ELSE 0
            END AS calculated_surcharge
        ")
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
                ->label(__('water_fields.base_amount'))
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
                        count: $this->count ?? 1,
                        userId: auth()->id()
                    );

                    // $this->notify($response['status'], $response['message']);
                }),
        ];
    }

}

<?php

namespace App\Filament\Electricity\Resources\DueElectricBills\Pages;

use App\Filament\Electricity\Resources\DueElectricBills\DueElectricBillResource;
use App\Helpers\ElectricBillHelper;
use App\Models\ElectricBill;
use App\Models\ElectricBillSetting;
use App\Models\Meter;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class DueElectricBillDetails extends Page implements HasForms, HasTable
{
    use InteractsWithRecord, InteractsWithForms, InteractsWithTable;

    protected static string $resource = DueElectricBillResource::class;

    protected string $view = 'filament.electricity.resources.due-electric-bills.pages.due-electric-bill-details';

    public function getTitle(): string
    {
        return 'বকেয়া বিলের বিস্তারিত';
    }

    public ?int $count=null; 

    public ?int $row=null;

    public ?int $record_id=null;

    public  $data;

    public $meter;
    

    protected $tableQueryKey;

    // public ?float $total_due_amount = null;

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->data = ElectricBill::where('customer_id', $this->record->id)->where('is_paid', false)->get();
        $this->meter=Meter::where(['customer_id'=> $this->record->id, 'status'=>'active'])->first();
        $this->row = $this->data->count();

        $this->count = $this->data->count();
        // $this->total_due_amount = ElectricBill::where('customer_id', $this->record->id)->where('is_paid', false)->sum('total_amount');
    }

    protected function getFormSchema(): array
    {
        return [
           
            Grid::make(4)->schema([
               
                TextEntry::make('customer_name')
                    ->label('গ্রাহকের নামঃ ')
                    ->default($this->record->name)
                    ->disabled(),
                TextEntry::make('customer_number')
                    ->label(__('fields.shop_no').'ঃ')
                    ->default($this->record->shop_no)
                    ->disabled(),
                TextEntry::make('meter_number')
                    ->label('মিটার নম্বরঃ ')
                    ->default($this->meter->meter_number)
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
                        //$this->row = ElectricBill::where('customer_id', $this->record->id)->where('is_paid', false)->count();
                        // $this->tableQueryKey = uniqid();
                        $this->resetTable();
                    }),
            ]),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Models\ElectricBill::query()
            ->where('customer_id', $this->record->id)
            ->where('is_paid', false)
            ->limit($this->count ?? 1); 
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('bill_month_name')
                ->label('বিলের মাস')
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('billing_year')
                ->label('বিলের বছর')
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('reading.previous_reading')
                ->label('পূর্বের রিডিং')
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('reading.current_reading')
                ->label('বর্তমান রিডিং')
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('consumed_units')
                ->label('ব্যবহৃত ইউনিট')
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('surcharge')
                ->label('সারচার্জ')
                ->getStateUsing(function ($record) {
                    return ElectricBillHelper::calculateSurcharge($record);
                })
                ->formatStateUsing(function ($state) {

                    return $state ? $this->en2bn($state) : $this->en2bn('0');
                }),
            TextColumn::make('total_amount')
                ->label('মোট বিল')
                ->getStateUsing(function ($record) {
                    $surcharge = ElectricBillHelper::calculateSurcharge($record);
                    return $record->total_amount + $surcharge;
                })
                ->formatStateUsing(fn ($state) => $this->en2bn(number_format($state, 2)))
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
                ->action(function () {
                    $response = ElectricBillHelper::createInvoice(
                        customerId: $this->record->id,
                        count: $this->count ?? 1,
                        userId: auth()->id()
                    );

                    // $this->notify($response['status'], $response['message']);
                }),
        ];
    }

}

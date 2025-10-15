<?php

namespace App\Filament\Electricity\Pages;

use App\Models\ElectricBill;
use App\Models\ElectricBillSetting;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Services\ElectricBillingService;
use Carbon\Carbon;
use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Billgenerate extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    protected string $view = 'filament.electricity.pages.billgenerate';

    protected static ?string $navigationLabel = 'বিদ্যুৎ প্রস্তুতকরণ';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';
    
    public function getTitle(): string
    {
        return 'বিদ্যুৎ বিল প্রস্তুতকরণ';
    }

    public ?int $month = null;
    public ?int $year = null;

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    public function mount(): void
    {
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
                    ->afterStateUpdated(fn () => $this->resetTable()),

                Select::make('year')
                    ->label(__('fields.billing_year'))
                    ->options(function () {
                        $current = date('Y');
                        $years = [];
                        for ($i = $current; $i >= 2023; $i--) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->resetTable()),
            ]),
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (!$this->month || !$this->year) {
            return ElectricBill::query()->whereNull('id');
        }

        $existing = ElectricBill::where('billing_month', $this->month)
            ->where('billing_year', $this->year)
            ->exists();

        if (!$existing) {
            $this->generateBills();
        }

        return ElectricBill::query()
            ->where('billing_month', $this->month)
            ->where('billing_year', $this->year);
    }

    /***
     * Auto-generate bills for all active meters
     */
    protected function generateBills(): void
    {
        DB::transaction(function () {
            $activeMeters = Meter::where('status', 'active')->get();
            $previousReading=0;

            foreach ($activeMeters as $meter) {
                // Find last reading
                $lastReading = MeterReading::where('meter_id', $meter->id)
                    ->latest('reading_date')
                    ->first();
                // if(!$lastReading){
                //     $previousReading=$meter->current_reading;
                // }else{
                //     $previousReading = $lastReading?->current_reading;
                // }
                $previousReading = $lastReading?->current_reading ?? $meter->current_reading;
                $setting = ElectricBillSetting::query()->latest()->first();
                // Create a new meter reading
                $reading = MeterReading::create([
                    'meter_id' => $meter->id,
                    'reading_date' => Carbon::create($this->year, $this->month, 1),
                    'previous_reading' => $previousReading,
                    'current_reading' => 0,
                    'consume_unit' => 0,
                ]);
            }
        });
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('reading.meter.customer.name')
                ->label(__('fields.name')),
            TextColumn::make('reading.meter.customer.shop_no')
                ->label(__('fields.shop_no')),
            TextColumn::make('bill_month_name')
                ->label(__('fields.billing_month'))
                ->searchable()
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('reading.meter.meter_number')->label(__('fields.meter_number'))
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('reading.previous_reading')->label(__('fields.previous_reading'))
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextInputColumn::make('reading.current_reading')
                ->label(__('fields.current_reading'))
                ->afterStateUpdated(function ($state, ElectricBill $record) {
                    $record->reading->current_reading = $state;
                    $record->reading->consume_unit = $state - $record->reading->previous_reading;
                    $reading=$record->reading;
                    $reading->update(
                        [
                            'current_reading' => $state,
                            'consume_unit' => $state - $record->reading->previous_reading,
                        ]
                        );

                    
                }),
            TextColumn::make('reading.consume_unit')->label(__('fields.consume_unit'))
                ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('total_amount')->label(__('fields.total_amount'))
                ->formatStateUsing(fn ($state) => $this->en2bn(number_format($state, 2))),
        ];
    }
}

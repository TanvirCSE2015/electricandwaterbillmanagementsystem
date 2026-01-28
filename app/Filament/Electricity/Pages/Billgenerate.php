<?php

namespace App\Filament\Electricity\Pages;

use App\Models\ElectricArea;
use App\Models\ElectricBill;
use App\Models\ElectricBillSetting;
use App\Models\ElectricCalculation;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Services\ElectricBillingService;
use Carbon\Carbon;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
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
    public ?int $area_id = null;

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
                Select::make('area_id')
                    ->label(__('fields.area'))
                    ->options(ElectricArea::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('month', null);
                        $this->resetTable();
                    }),
                Select::make('year')
                    ->label(__('fields.billing_year'))
                    ->options(function () {
                        $current = date('Y');
                        $years = [];
                        for ($i = $current; $i >= 2025; $i--) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->reactive()
                    ->visible(fn () => $this->area_id !== null),
                Select::make('month')
                    ->label(__('fields.billing_month'))
                    ->options(function (callable $get) {
                        $months = [
                            1 => 'জানুয়ারি',
                            2 => 'ফেব্রুয়ারি',
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
                        ];
                        $selectedYear = $get('year');
                        $now = now();

                        // If no year selected → show all months
                        if (! $selectedYear) {
                            return $months;
                        }

                        // Current year → only past months
                        if ((int) $selectedYear === $now->year) {
                            return array_slice($months, 0, $now->month - 1, true);
                        }

                        // Past or future year → all months
                        return $months;
                    })
                    ->reactive()
                    ->visible(fn () => $this->area_id !== null)
                    ->afterStateUpdated(fn () => $this->resetTable()),

                
            ]),
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (!$this->month || !$this->year) {
            return ElectricBill::query()->whereNull('id');
        }

        $existing = ElectricBill::query()
            ->whereHas('customer', function (Builder $query) {
                $query->where('electric_area_id', $this->area_id);
            })
            ->where('billing_month', $this->month)
            ->where('billing_year', $this->year)
            ->exists();

        if (!$existing) {
            // CanRequireConfirmation::requireConfirmation(true);
           return ElectricBill::query()->whereNull('id');
        }

        return ElectricBill::query()
            ->whereHas('customer', function (Builder $query) {
                $query->where('electric_area_id', $this->area_id);
            })
            ->where('billing_month', $this->month)
            ->where('billing_year', $this->year);
    }

    /***
     * Auto-generate bills for all active meters
     */
    protected function generateBills(): void
    {
        DB::transaction(function () {
            $activeMeters = Meter::whereHas('customer', function (Builder $query) {
                $query->where('electric_area_id', $this->area_id);
            })
            ->where('status', 'active')->get();
            $previousReading=0;
           
            foreach ($activeMeters as $meter) {
                // Find last reading
                $lastReading = MeterReading::where('meter_id', $meter->id)
                    ->latest('reading_date')
                    ->first();
                if(!$lastReading){
                    $previousReading=$meter->current_reading;
                }else{
                    $previousReading = $lastReading?->current_reading;
                }
                // $previousReading = $lastReading?->current_reading ?? $meter->current_reading;
                $setting = ElectricBillSetting::query()->where('electric_area_id',$this->area_id)->latest()->first();
                // Create a new meter reading
                $reading = MeterReading::create([
                    'meter_id' => $meter->id,
                    'reading_date' => Carbon::now(),
                    'previous_reading' => $previousReading,
                    'current_reading' => 0,
                    'consume_unit' => 0,
                ]);
                ElectricBillingService::generateBill($reading, $setting, auth()->id(),$this->month,$this->year);
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

    protected function getTableHeaderActions(): array
    {
        return [

            Action::make('generate_bills')
                ->label('বিল তৈরি করুন')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(function () {
                    if (!$this->area_id || !$this->month || !$this->year) {
                        return false;
                    }

                    return ! ElectricBill::query()
                        ->whereHas('customer', fn ($q) =>
                            $q->where('electric_area_id', $this->area_id)
                        )
                        ->where('billing_month', $this->month)
                        ->where('billing_year', $this->year)
                        ->exists();
                })
                ->requiresConfirmation()
                ->modalHeading('বিল তৈরি নিশ্চিত করুন')
                ->modalDescription('এই মাসের জন্য এখনো কোনো বিল নেই। আপনি কি নতুন বিল তৈরি করতে চান?')
                ->modalSubmitActionLabel('হ্যাঁ, তৈরি করুন')
                ->action(function () {
                    $this->generateBills();

                    Notification::make()
                        ->title('বিল সফলভাবে তৈরি হয়েছে')
                        ->success()
                        ->send();

                    $this->resetTable();
                }),
            Action::make('complete_bills')
                ->label('পূর্ণাঙ্গ বিল তৈরি করুন')
                ->icon('heroicon-o-check')
                ->color('success')
                ->schema([
                Grid::make(3)->schema([
                    TextInput::make('cantral_ac_rate')
                        ->label(__(('fields.cantral_ac_rate')))
                        ->default(0)
                        ->numeric()
                        ->required(),

                    TextInput::make('common_area_rate')
                       ->label(__(('fields.common_area_rate')))
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('water_area_rate')
                        ->label(__(('fields.water_area_rate')))
                        ->numeric()
                        ->default(0)
                        ->required(),
                    DatePicker::make('due_date')
                        ->label(__(('fields.due_date')))
                        ->default(0)
                        ->required(),
                ]),
            ])
            ->fillForm(function () {

                if (!$this->area_id || !$this->month || !$this->year) {
                    return [];
                }

                $record = \App\Models\ElectricCalculation::where('elecric_area_id', $this->area_id)
                    ->where('bill_month', $this->month)
                    ->where('bill_year', $this->year)
                    ->first();

                return $record
                    ? [
                        'cantral_ac_rate' => $record->cantral_ac_rate,
                        'common_area_rate' => $record->common_area_rate,
                        'water_area_rate' => $record->water_area_rate,
                    ]
                    : [];
            })

            ->action(function (array $data) {

                if (!$this->area_id || !$this->month || !$this->year) {
                    return;
                }

                $calc=ElectricCalculation::updateOrCreate(
                    [
                        'elecric_area_id' => $this->area_id,
                        'bill_month' => $this->month,
                        'bill_year' => $this->year,
                    ],
                    [
                        'cantral_ac_rate' => $data['cantral_ac_rate'],
                        'common_area_rate' => $data['common_area_rate'],
                        'water_area_rate' => $data['water_area_rate'],
                        'due_date' => $data['due_date'],
                    ]
                );

                $rows = DB::table('electric_bills as b')
                        ->join('customers as c', 'b.customer_id', '=', 'c.id')
                        ->select([
                            'b.id',
                            'b.consumed_units',
                            'b.system_loss_units',
                            'b.service_charge',
                            'b.demand_charge',
                            'c.central_ac_area',
                            'c.common_ac_area',
                            'c.water_area',

                        ])
                        ->where('b.billing_month', $this->month)
                        ->where('b.billing_year', $this->year)
                        ->where('c.electric_area_id', $this->area_id)
                        ->get();

                    if ($rows->isEmpty()) {
                        return;
                    }

                    $setting=ElectricBillSetting::query()->where('electric_area_id',$this->area_id)->latest()->first();

                    $acRate     = (float) $calc->cantral_ac_rate;
                    $commonRate = (float) $calc->common_area_rate;
                    $waterRate  = (float) $calc->water_area_rate;
                    $due_date= Carbon::parse($calc->due_date)->format('Y-m-d');

                    $ids = [];
                    
                    // $AcRateCase='';
                    // $commonRateCase='';
                    // $waterRateCase='';
                    $unitAcCases = '';
                    $unitCommonCases = '';
                    $unitTotalCase='';
                    $baseAmountCase='';
                    $totalAmountCase='';
                    $acAmountCases = '';
                    $commonAmountCases = '';
                    $waterAmountCases = '';
                    $vatCase='';

                    foreach ($rows as $row) {
                        $ids[] = $row->id;

                        $unitAc     = $acRate * ((float) $row->central_ac_area ?? 0);
                        $acAmount = round($unitAc * $setting->unit_price);
                        $unitCommon = $commonRate * ((float) $row->common_ac_area ?? 0);
                        $commonAmount=round($unitCommon *  $setting->unit_price);
                        $waterAmt   = round($waterRate * ((float) $row->water_area ?? 0));

                        $unitTotal=$row->consumed_units + $row->system_loss_units + $unitAc + $unitCommon;
                        $baseAmount=$unitTotal * $setting->unit_price;
                        $vatAmount=round(($baseAmount + $row->service_charge + $row->demand_charge ) * ($setting->vat/100));

                        $totalAmount = round($baseAmount + $row->service_charge + $row->demand_charge + $waterAmt + $vatAmount);

                        $unitAcCases     .= "WHEN {$row->id} THEN {$unitAc} ";
                        $unitCommonCases .= "WHEN {$row->id} THEN {$unitCommon} ";
                        $unitTotalCase .= "WHEN {$row->id} THEN {$unitTotal} ";
                        $acAmountCases   .= "WHEN {$row->id} THEN {$acAmount} ";
                        $commonAmountCases .= "WHEN {$row->id} THEN {$commonAmount} ";
                        $waterAmountCases .= "WHEN {$row->id} THEN {$waterAmt} ";
                        $baseAmountCase .= "WHEN {$row->id} THEN {$baseAmount} ";
                        $vatCase .= "WHEN {$row->id} THEN {$vatAmount} ";
                        $totalAmountCase .= "WHEN {$row->id} THEN {$totalAmount} ";
                    }

                    $idsList = implode(',', $ids);

                    DB::statement("
                        UPDATE electric_bills SET

                            unit_rate_ac     = {$acRate},
                            unit_rate_common = {$commonRate},
                            unit_rate_water  = {$waterRate},
                            due_date         = '{$due_date}',

                            unit_ac      = CASE id {$unitAcCases} END,
                            unit_common  = CASE id {$unitCommonCases} END,
                            unit_total   = CASE id {$unitTotalCase} END,

                            ac_amount     = CASE id {$acAmountCases} END,
                            common_amount = CASE id {$commonAmountCases} END,
                            water_amount  = CASE id {$waterAmountCases} END,

                            base_amount  = CASE id {$baseAmountCase} END,
                            vat          = CASE id {$vatCase} END,
                            total_amount = CASE id {$totalAmountCase} END

                        WHERE id IN ({$idsList})
                    ");

            }),
    ];

        
    }

    /**
     * Mark generated bills as completed (paid) for the selected area/month/year.
     * This implementation marks unpaid bills that have consume_unit > 0 as paid,
     * and sets `paid_by` and `payment_date`. Adjust to your business rules if needed.
     */
    public function completeBills(): void
    {
        
    }
}

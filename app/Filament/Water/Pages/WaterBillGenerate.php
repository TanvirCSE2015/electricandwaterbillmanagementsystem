<?php

namespace App\Filament\Water\Pages;

use App\Models\WaterBill;
use App\Services\WaterBillingService;
use Carbon\Carbon;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class WaterBillGenerate extends Page implements HasForms,HasTable
{
    use InteractsWithForms,InteractsWithTable;
    protected string $view = 'filament.water.pages.water-bill-generate';

    protected static ?string $navigationLabel = 'বিল প্রস্তুতকরণ'; 
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';
    
    public function getTitle(): string
    {
        return 'পানি বিল প্রস্তুতকরণ'; 
    }

    public ?int $month=null;
    public ?int $year=null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    protected function getFormSchema(): array
    {
        return [

            Grid::make(4)->schema([
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
                Select::make('month')
                    ->label(__('fields.billing_month'))
                    ->options(function (callable $get) {
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
                    ->afterStateUpdated(fn () => $this->resetTable()),

                
            ]),
            
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (!$this->month || !$this->year) {
            return WaterBill::query()->whereNull('id');
        }

        $existing = WaterBill::query()
            ->where('water_bill_month', $this->month)
            ->where('water_bill_year', $this->year)
            ->exists();
            
        if (!$existing) {
            // $this->generateBills();
             return WaterBill::query()->whereNull('id');
        }

        return WaterBill::query()
            ->where('water_bill_month', $this->month)
            ->where('water_bill_year', $this->year);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('waterCustomer.customer_name')->label('গ্রাহকের নাম'),
            TextColumn::make('water_bill_month')->label('বিল মাস')
            ->formatStateUsing(fn ($state) =>
                Carbon::create()->month($state)->translatedFormat('F')
            ),
            TextColumn::make('water_bill_year')->label('বিল বছর')
            ->formatStateUsing(fn ($state) => $this->en2bn($state)),
            TextColumn::make('base_amount')->label('মূল পরিমাণ')
            ->numeric(),
            TextColumn::make('surcharge_amount')->label('সারচার্জ')
            ->numeric(),
            TextColumn::make('total_amount')->label('মোট পরিমাণ')
            ->numeric(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return[
            Action::make('generate_bills')
                ->label('বিল তৈরি করুন')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(function () {
                    if (!$this->month || !$this->year) {
                        return false;
                    }

                    return ! WaterBill::query()
                        ->where('water_bill_month', $this->month)
                        ->where('water_bill_year', $this->year)
                        ->exists();
                })
                ->requiresConfirmation()
                ->modalHeading('বিল তৈরি নিশ্চিত করুন')
                ->modalDescription('এই মাসের জন্য এখনো কোনো বিল নেই। আপনি কি নতুন বিল তৈরি করতে চান?')
                ->modalSubmitActionLabel('হ্যাঁ, তৈরি করুন')
                ->schema([
                    Grid::make(2)
                    ->schema([
                        DatePicker::make('creation_date')
                        ->label('বিলের তৈরির তারিখ')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->displayFormat('Y-m-d')
                        ->format('Y-m-d')
                        ->default(Carbon::now())
                        ->required(),
                        DatePicker::make('last_date')
                        ->label('বিল পরিশোধের তারিখ')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->displayFormat('Y-m-d')
                        ->format('Y-m-d')
                        ->default(Carbon::now())
                        ->required(),
                    ])
                ])
                ->action(function (array $data) {
                        // dd($data['confirm_date']);
                    $this->generateBills($data['creation_date'],$data['last_date']);

                    Notification::make()
                        ->title('বিল সফলভাবে তৈরি হয়েছে')
                        ->success()
                        ->send();

                    $this->resetTable();
                }),
                Action::make('refresh')
                ->label('রিফ্রেশ করুন')
                ->icon(Heroicon::ReceiptRefund)
                ->color('info')
                ->action(function () {
                    $this->resetTable();
                }),
        ];
    }

    protected function generateBills($creationDate, $lastBillDate): void
    {
        // Call the service method to generate bills

        WaterBillingService::generateBulkBills($this->month, $this->year, auth()->id(), $creationDate, $lastBillDate);
    }
}

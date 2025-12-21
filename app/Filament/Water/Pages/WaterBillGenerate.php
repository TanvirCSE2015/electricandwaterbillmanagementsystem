<?php

namespace App\Filament\Water\Pages;

use App\Models\WaterBill;
use App\Services\WaterBillingService;
use Carbon\Carbon;
use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

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
            return WaterBill::query()->whereNull('id');
        }

        $existing = WaterBill::query()
            ->where('water_bill_month', $this->month)
            ->where('water_bill_year', $this->year)
            ->exists();

        if (!$existing) {
            $this->generateBills();
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

    protected function generateBills(): void
    {
        // Call the service method to generate bills
        WaterBillingService::generateBulkBills($this->month, $this->year, auth()->id());
    }
}

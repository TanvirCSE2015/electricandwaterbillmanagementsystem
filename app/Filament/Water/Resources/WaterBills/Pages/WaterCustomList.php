<?php

namespace App\Filament\Water\Resources\WaterBills\Pages;

use App\Filament\Water\Resources\WaterBills\WaterBillResource;
use App\Helpers\WaterBillHelper;
use App\Models\WaterBill;
use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class WaterCustomList extends Page implements HasTable,HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $resource = WaterBillResource::class;

    protected string $view = 'filament.water.resources.water-bills.pages.water-custom-list';

    public ?int $record_id=null;
     public ?int $month=null;
    public ?int $year=null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function getTitle(): string
    {
        return 'পানি বিল তালিকা';
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
       return WaterBill::query()
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
        ->when($this->month, fn (Builder $query) => $query->where('water_bill_month', $this->month))
        ->when($this->year, fn (Builder $query) => $query->where('water_bill_year', $this->year));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('waterCustomer.customer_name')
                    ->label(__('water_fields.customer_name'))
                    ->searchable(),
            TextColumn::make('water_bill_month')
                ->label(__('water_fields.water_bill_month'))
                ->getStateUsing(fn ( $record) => \Carbon\Carbon::create()->month($record->water_bill_month)->translatedFormat('F'))
                ->sortable(),
            TextColumn::make('water_bill_year')
                ->label(__('water_fields.water_bill_year'))
                ->formatStateUsing(fn ( $state) => WaterBillHelper::en2bn($state))
                ->sortable(),
            TextColumn::make('calculated_surcharge')
                ->label(__('water_fields.surcharge_amount'))
                ->numeric()
                ->sortable(),
            TextColumn::make('payable_amount')
                ->label(__('water_fields.total_amount'))
                ->numeric()
                ->sortable(),
            TextColumn::make('bill_creation_date')
                ->label(__('water_fields.bill_creation_date'))
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('bill_due_date')
                ->label(__('water_fields.bill_due_date'))
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            IconColumn::make('is_paid')
                ->label(__('water_fields.is_paid'))
                ->boolean(),
            TextColumn::make('paid_at')
                ->label(__('water_fields.paid_at'))
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ViewAction::make(),
            // EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Water\Pages;

use App\Models\WaterBill;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use UnitEnum;

class WaterInvoiceReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;
    
    protected string $view = 'filament.water.pages.water-invoice-report';

    protected static ?string $title = 'আদায় রিপোর্ট';
    protected static ?string $navigationLabel = 'আদায় রিপোর্ট';
    protected static string | UnitEnum | null $navigationGroup = 'রিপোর্ট সমূহ';

    public ?Carbon $date = null;
    public ?Carbon $end_date = null;
    public ?string $type = null;

    public function mount(): void
    {
        $this->date = now();
        $this->end_date = null;
        $this->type='water';
        
    }

    function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(4)
                ->schema([
                   DatePicker::make('date')
                        ->label('তারিখ থেকে')
                        ->displayFormat('Y-m-d')
                        ->native(false)
                        ->required()
                        ->reactive()
                        ->closeOnDateSelection()
                        ->afterStateUpdated(fn () => $this->resetTable()),

                    DatePicker::make('end_date')
                        ->label('তারিখ পর্যন্ত')
                        ->displayFormat('Y-m-d')
                        ->native(false)
                        ->reactive()
                        ->closeOnDateSelection()
                        ->afterStateUpdated(fn () => $this->resetTable()),
                    Select::make('type')
                        ->label('বিলের ধরন')
                        ->options([
                            'water' => 'পানি বিল',
                            'security' => 'নিরাপত্তা বিল',
                            'previous' => 'পূর্বের বকেয়া বিল',
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->resetTable()),
                ]),
        ];
    }
    protected function getTableQuery()
    {
        $query = null;
        if ($this->type === 'water' || $this->type === 'previous') {
            $query = \App\Models\WaterInvoice::query()
            ->when($this->date && $this->end_date, function (Builder $query) {
                $query->whereBetween('w_invoice_date', [
                    $this->date ? $this->date->startOfDay() : null,
                    $this->end_date ? $this->end_date->endOfDay() : null,
                ]);
            })
            ->when($this->date && ! $this->end_date, function (Builder $query) {
                $query->whereDate('w_invoice_date', '=', $this->date->toDateString());
            })
            ->where('w_due_type', $this->type === 'previous' ? 'previous' : 'current');
        } elseif ($this->type === 'security') {
            $query = \App\Models\SecurityInvoice::query()
            ->when($this->date && $this->end_date, function (Builder $query) {
                $query->whereBetween('s_invoice_date', [
                    $this->date ? $this->date->startOfDay() : null,
                    $this->end_date ? $this->end_date->endOfDay() : null,
                ]);
            })
            ->when($this->date && ! $this->end_date, function (Builder $query) {
                $query->whereDate('s_invoice_date', '=', $this->date->toDateString());
            });
        } else {
            return null; // or handle invalid type as needed
        }
        return $query;
    }

    protected function getTableColumns(): array
    {
        if ($this->type === 'water' || $this->type === 'previous') {
            return [
                TextColumn::make('invoice_number')->label('রশিদ নং')
                    ->formatStateUsing(fn (string $state): string => $this->en2bn($state)),
                TextColumn::make('waterCustomer.customer_name')->label('গ্রাহকের নাম'),
                TextColumn::make('waterCustomer.holding_number')->label('হোল্ডিং নং'),
                TextColumn::make('waterCustomer.activeFlats.flat_number')->label('ফ্ল্যাট নং'),
                TextColumn::make('waterCustomer.total_flat')->label('ফ্ল্যাট সংখ্যা')->numeric(),
                TextColumn::make('w_invoice_date')->label('তারিখ')->date('d-m-Y')
                    ->formatStateUsing(fn (string $state): string => $this->en2bn(date('d-m-Y', strtotime($state)))),
                TextColumn::make('w_total_amount')->label('মোট টাকা')->money('BDT', true),
            ];
        } elseif ($this->type === 'security') {
            return [
                TextColumn::make('s_invoice_number')->label('রশিদ নং')
                    ->formatStateUsing(fn (string $state): string => $this->en2bn($state)),
                TextColumn::make('waterCustomer.customer_name')->label('গ্রাহকের নাম'),
                TextColumn::make('waterCustomer.holding_number')->label('হোল্ডিং নং'),
                TextColumn::make('waterCustomer.activeFlats.flat_number')->label('ফ্ল্যাট নং'),
                TextColumn::make('waterCustomer.total_flat')->label('ফ্ল্যাট সংখ্যা')->numeric(),
                TextColumn::make('s_invoice_date')->label('তারিখ')->date('d-m-Y')
                    ->formatStateUsing(fn (string $state): string => $this->en2bn(date('d-m-Y', strtotime($state)))),
                TextColumn::make('s_total_amount')->label('মোট টাকা')->money('BDT', true),
            ];
        } else {
            return [
            ];
        }
    }

    protected function getTableHeaderActions(): array
    {
        return[
            Action::make('print_report')
            ->label('প্রিন্ট রিপোর্ট')
            ->icon(Heroicon::Printer)
            ->url(fn () => route('water-invoice-report.print', [
                'type' => $this->type,
                'date' => $this->date->format('Y-m-d'),
                'end_date' => $this->end_date?->format('Y-m-d'),
            ]))
            ->openUrlInNewTab(),
        ];
    }
}

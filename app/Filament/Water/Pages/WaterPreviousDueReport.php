<?php

namespace App\Filament\Water\Pages;

use App\Models\WaterBill;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use UnitEnum;

class WaterPreviousDueReport extends Page implements HasTable,HasForms
{
    use InteractsWithTable,InteractsWithForms;
    protected string $view = 'filament.water.pages.water-previous-due-report';
    protected static ?string $navigationLabel = 'পানি ও নিরাপত্তা বকেয়া রিপোর্ট';
    protected static string | UnitEnum | null  $navigationGroup = 'রিপোর্ট সমূহ';
    protected static ?int $navigationSort = 3;

    public ?string $type = null;
    public ?int $payment_status= null;
    public ?Carbon $date = null;
    public ?Carbon $end_date = null;

     public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }
    public function getTitle(): string
    {
        return 'পানি ও নিরাপত্তা বকেয়া রিপোর্ট';
    }

    public function mount(): void
    {
        $this->date = now();
        $this->end_date = null;
        $this->payment_status=1;
        $this->type='water';
        
    }

    protected function getFormSchema(): array
    {
        return[
            Grid::make(4)
            ->schema([
                
                Select::make('payment_status')
                    ->label('বকেয়া রিপোর্টের ধরন')
                    ->options([
                        1 => 'পরিশোধকৃত বকেয়া রিপোর্ট',
                        0 => 'অপরিশোধকৃত বকেয়া রিপোর্ট',
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn()=>$this->resetTable())
                    ->required(),
                DatePicker::make('date')
                        ->label('তারিখ থেকে')
                        ->displayFormat('Y-m-d')
                        ->native(false)
                        ->required()
                        ->visible(fn() => $this->payment_status == 1)
                        ->reactive()
                        ->closeOnDateSelection()
                        ->afterStateUpdated(fn () => $this->resetTable()),

                DatePicker::make('end_date')
                    ->label('তারিখ পর্যন্ত')
                    ->displayFormat('Y-m-d')
                    ->native(false)
                    ->visible(fn() => $this->payment_status == 1)
                    ->reactive()
                    ->closeOnDateSelection()
                    ->afterStateUpdated(fn () => $this->resetTable()),
                Select::make('type')
                    ->label('বকেয়া রিপোর্টের ধরন')
                    ->options([
                        'water' => 'পানি বকেয়া রিপোর্ট',
                        'security' => 'নিরাপত্তা বকেয়া রিপোর্ট',
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn()=>$this->resetTable())
                    ->required(),
            ]),
        ];
    } 
    
    protected function getTableQuery(): Builder|Relation|null
    {
        return WaterBill::query();
    }
}
<?php

namespace App\Filament\Water\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use UnitEnum;

class LeaserReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected string $view = 'filament.water.pages.leaser-report';
    protected static ?string $title = 'লেজার রিপোর্ট';
    protected static ?string $navigationLabel = 'লেজার রিপোর্ট';
    protected static string | UnitEnum | null $navigationGroup = 'রিপোর্ট সমূহ';
    protected static ?int $navigationSort = 2;

    public ?int $month=null;
    public ?int $year=null;
    public ?string $type = null;

    protected function getFormSchema(): array
    {
        return[
            Grid::make(4)
                ->schema([
                    
                ]),
        ];
    }
}

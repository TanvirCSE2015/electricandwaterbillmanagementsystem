<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Pages;

use App\Filament\Electricity\Resources\ElectricBills\ElectricBillResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListElectricBills extends ListRecords
{
    protected static string $resource = ElectricBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }


}

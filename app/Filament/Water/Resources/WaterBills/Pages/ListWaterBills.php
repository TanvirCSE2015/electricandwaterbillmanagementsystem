<?php

namespace App\Filament\Water\Resources\WaterBills\Pages;

use App\Filament\Water\Resources\WaterBills\WaterBillResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaterBills extends ListRecords
{
    protected static string $resource = WaterBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

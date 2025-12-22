<?php

namespace App\Filament\Water\Resources\WaterBills\Pages;

use App\Filament\Water\Resources\WaterBills\WaterBillResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWaterBill extends ViewRecord
{
    protected static string $resource = WaterBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

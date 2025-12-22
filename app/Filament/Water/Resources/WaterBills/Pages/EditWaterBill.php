<?php

namespace App\Filament\Water\Resources\WaterBills\Pages;

use App\Filament\Water\Resources\WaterBills\WaterBillResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWaterBill extends EditRecord
{
    protected static string $resource = WaterBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

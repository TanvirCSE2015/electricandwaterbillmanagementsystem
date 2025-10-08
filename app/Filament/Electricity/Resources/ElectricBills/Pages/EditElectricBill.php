<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Pages;

use App\Filament\Electricity\Resources\ElectricBills\ElectricBillResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditElectricBill extends EditRecord
{
    protected static string $resource = ElectricBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

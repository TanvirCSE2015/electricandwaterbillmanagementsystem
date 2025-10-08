<?php

namespace App\Filament\Electricity\Resources\DueElectricBills\Pages;

use App\Filament\Electricity\Resources\DueElectricBills\DueElectricBillResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDueElectricBill extends EditRecord
{
    protected static string $resource = DueElectricBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

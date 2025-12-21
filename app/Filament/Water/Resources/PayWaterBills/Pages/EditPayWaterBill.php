<?php

namespace App\Filament\Water\Resources\PayWaterBills\Pages;

use App\Filament\Water\Resources\PayWaterBills\PayWaterBillResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayWaterBill extends EditRecord
{
    protected static string $resource = PayWaterBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

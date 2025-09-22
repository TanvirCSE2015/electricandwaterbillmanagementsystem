<?php

namespace App\Filament\Resources\ElectricBillSettings\Pages;

use App\Filament\Resources\ElectricBillSettings\ElectricBillSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditElectricBillSetting extends EditRecord
{
    protected static string $resource = ElectricBillSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

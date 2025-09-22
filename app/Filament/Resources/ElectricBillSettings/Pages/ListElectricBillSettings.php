<?php

namespace App\Filament\Resources\ElectricBillSettings\Pages;

use App\Filament\Resources\ElectricBillSettings\ElectricBillSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListElectricBillSettings extends ListRecords
{
    protected static string $resource = ElectricBillSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}

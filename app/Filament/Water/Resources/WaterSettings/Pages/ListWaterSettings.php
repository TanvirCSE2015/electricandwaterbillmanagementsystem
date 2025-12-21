<?php

namespace App\Filament\Water\Resources\WaterSettings\Pages;

use App\Filament\Water\Resources\WaterSettings\WaterSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaterSettings extends ListRecords
{
    protected static string $resource = WaterSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

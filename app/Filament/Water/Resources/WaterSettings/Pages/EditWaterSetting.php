<?php

namespace App\Filament\Water\Resources\WaterSettings\Pages;

use App\Filament\Water\Resources\WaterSettings\WaterSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWaterSetting extends EditRecord
{
    protected static string $resource = WaterSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

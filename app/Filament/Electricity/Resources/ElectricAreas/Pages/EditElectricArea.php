<?php

namespace App\Filament\Electricity\Resources\ElectricAreas\Pages;

use App\Filament\Electricity\Resources\ElectricAreas\ElectricAreaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditElectricArea extends EditRecord
{
    protected static string $resource = ElectricAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

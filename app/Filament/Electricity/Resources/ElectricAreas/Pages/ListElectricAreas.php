<?php

namespace App\Filament\Electricity\Resources\ElectricAreas\Pages;

use App\Filament\Electricity\Resources\ElectricAreas\ElectricAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListElectricAreas extends ListRecords
{
    protected static string $resource = ElectricAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

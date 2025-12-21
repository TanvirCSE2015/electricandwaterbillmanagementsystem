<?php

namespace App\Filament\Water\Resources\WaterCustomers\Pages;

use App\Filament\Water\Resources\WaterCustomers\WaterCustomerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListWaterCustomers extends ListRecords
{
    protected static string $resource = WaterCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus),
        ];
    }
}

<?php

namespace App\Filament\Water\Resources\WaterCustomers\Pages;

use App\Filament\Water\Resources\WaterCustomers\WaterCustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWaterCustomer extends EditRecord
{
    protected static string $resource = WaterCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

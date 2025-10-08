<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Pages;

use App\Filament\Electricity\Resources\ElectricBills\ElectricBillResource;
use Filament\Resources\Pages\CreateRecord;

class CreateElectricBill extends CreateRecord
{
    protected static string $resource = ElectricBillResource::class;
}

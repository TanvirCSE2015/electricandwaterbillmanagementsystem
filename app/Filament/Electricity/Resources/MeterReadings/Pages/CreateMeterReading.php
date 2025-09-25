<?php

namespace App\Filament\Electricity\Resources\MeterReadings\Pages;

use App\Filament\Electricity\Resources\MeterReadings\MeterReadingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeterReading extends CreateRecord
{
    protected static string $resource = MeterReadingResource::class;
}

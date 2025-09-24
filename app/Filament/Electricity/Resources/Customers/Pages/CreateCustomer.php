<?php

namespace App\Filament\Electricity\Resources\Customers\Pages;

use App\Filament\Electricity\Resources\Customers\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}

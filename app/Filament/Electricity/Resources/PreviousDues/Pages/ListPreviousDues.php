<?php

namespace App\Filament\Electricity\Resources\PreviousDues\Pages;

use App\Filament\Electricity\Resources\PreviousDues\PreviousDueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPreviousDues extends ListRecords
{
    protected static string $resource = PreviousDueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

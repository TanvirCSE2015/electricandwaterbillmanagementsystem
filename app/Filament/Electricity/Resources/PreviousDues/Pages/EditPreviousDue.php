<?php

namespace App\Filament\Electricity\Resources\PreviousDues\Pages;

use App\Filament\Electricity\Resources\PreviousDues\PreviousDueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPreviousDue extends EditRecord
{
    protected static string $resource = PreviousDueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

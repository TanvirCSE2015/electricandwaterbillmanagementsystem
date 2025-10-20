<?php

namespace App\Filament\Electricity\Resources\Blocks\Pages;

use App\Filament\Electricity\Resources\Blocks\BlocksResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlocks extends EditRecord
{
    protected static string $resource = BlocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

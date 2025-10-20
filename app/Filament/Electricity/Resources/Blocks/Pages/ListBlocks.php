<?php

namespace App\Filament\Electricity\Resources\Blocks\Pages;

use App\Filament\Electricity\Resources\Blocks\BlocksResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlocks extends ListRecords
{
    protected static string $resource = BlocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->icon('heroicon-o-plus'),
        ];
    }
}

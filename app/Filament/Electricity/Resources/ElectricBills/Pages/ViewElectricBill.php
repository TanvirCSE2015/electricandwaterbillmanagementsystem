<?php

namespace App\Filament\Electricity\Resources\ElectricBills\Pages;

use App\Filament\Electricity\Resources\ElectricBills\ElectricBillResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewElectricBill extends ViewRecord
{
    protected static string $resource = ElectricBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('Edit')
                ->label('Edit')
                ->url(route('filament.electricity.resources.meter-readings.edit', ['record' => $this->record->meter_reading_id ]))
                ->icon('heroicon-o-arrow-left')
                ->color('primary')
                ->button(),
        ];
    }

    // public function getBreadcrumbs(): array
    // {
    //     return [
    //         route('filament.electricity.pages.electric-bill-page') => 'বিদ্যুৎ বিলসমূহ',
    //         url()->current() => 'বিল বিস্তারিত',
    //     ];
    // }
}

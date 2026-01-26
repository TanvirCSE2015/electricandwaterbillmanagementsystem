<?php

namespace App\Filament\Water\Resources\PayWaterBills\Pages;

use App\Filament\Water\Resources\PayWaterBills\PayWaterBillResource;
use App\Models\WaterCustomer;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListPayWaterBills extends ListRecords
{
    protected static string $resource = PayWaterBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return WaterCustomer::query()
        ->whereHas('waterBills',function ($query) {
            $query->where('is_paid',false);
        })
        ->withSum(['waterBills as total_due_amount' => function ($query) {
                $query->where('is_paid', false)
                    ->select(DB::raw("
                        SUM(
                            CASE
                                WHEN bill_due_date < CURDATE()
                                THEN total_amount 
                                     + (total_amount * surcharge_percent / 100)
                                ELSE total_amount
                            END
                        )
                    "));
            }], 'total_amount')
            ->withSum(['securityBills as total_security_amount' => function ($query) {
                $query->select(DB::raw("SUM(total_amount)"));
            }], 'total_amount');
    }

}

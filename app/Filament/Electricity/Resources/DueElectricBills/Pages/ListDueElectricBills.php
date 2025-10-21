<?php

namespace App\Filament\Electricity\Resources\DueElectricBills\Pages;

use App\Filament\Electricity\Resources\DueElectricBills\DueElectricBillResource;
use App\Models\Customer;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListDueElectricBills extends ListRecords
{
    protected static string $resource = DueElectricBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // return \App\Models\ElectricBill::query()
        //     ->join('customers', 'electric_bills.customer_id', '=', 'customers.id')
        //     ->join('meters', 'customers.id', '=', 'meters.customer_id')
        //     ->select('electric_bills.customer_id', 'customers.name as customer_name', 'customers.shop_no','meters.meter_number', DB::raw('SUM(electric_bills.total_amount) as total_amount'))
        //     ->where('electric_bills.is_paid', false)
        //     ->groupBy('electric_bills.customer_id', 'customers.name', 'customers.shop_no', 'meters.meter_number');


        return Customer::query()
            ->whereHas('bills', function ($query) {
                $query->where('is_paid', false);
            })
            ->withSum(['bills' => function ($query) {
                $query->where('is_paid', false);
            }], 'total_amount')
            ->with([
            // Load previous_due where unpaid
            'previousDue' => fn($q) => $q->where('is_paid', false),
        ]);
    }
}

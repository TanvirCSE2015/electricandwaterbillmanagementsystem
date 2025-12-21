<?php

namespace App\Services;

use App\Models\WaterBill;
use App\Models\WaterCustomer;
use App\Models\WaterSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WaterBillingService
{
    
    public static function generateBulkBills(int $month, int $year, int $userId): void 
    {
        // if ($this->billsExist($month, $year)) {
        //     return;
        // }

        $setting = WaterSetting::latest()->firstOrFail();
        $now = now();

        DB::transaction(function () use ($setting, $month, $year, $userId, $now) {

            WaterCustomer::select('id', 'total_flat', 'previous_due')
                ->chunk(500, function ($customers) use (
                    $setting,
                    $month,
                    $year,
                    $userId,
                    $now
                ) {

                    $rows = [];

                    foreach ($customers as $customer) {
                        if($customer->type!='complete'){
                            $baseAmount = $customer->type=='flat' ?
                                ($customer->total_flat * $setting->monthly_rate)
                                : $setting->monthly_const_rate;

                            $surchargeAmount = 0;

                            $totalAmount = $baseAmount + $surchargeAmount;

                            $rows[] = [
                                'water_customer_id'  => $customer->id,
                                'water_bill_month'   => $month,
                                'water_bill_year'    => $year,
                                'base_amount'        => round($baseAmount, 2),
                                'surcharge_percent'  => $setting->monthly_surcharge,
                                'surcharge_amount'   => round($surchargeAmount, 2),
                                'total_amount'       => round($totalAmount, 2),
                                'paid_amount'        => 0,
                                'bill_creation_date' => Carbon::create($year, $month, 1),
                                'bill_due_date'      => Carbon::create($year, $month, 1)->endOfMonth(),
                                'is_paid'            => false,
                                'created_by'         => $userId,
                                'created_at'         => $now,
                                'updated_at'         => $now,
                            ];
                        }
                    }

                    if (!empty($rows)) {
                        WaterBill::insert($rows); // 🚀 SQL BULK INSERT
                    }
                });
        });
    }
}

<?php

namespace App\Services;

use App\Models\SecurityBill;
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

            WaterCustomer::select('id', 'total_flat', 'previous_due', 'type', 'total_security_flat')
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
                            // Calculate Water bills amounts
                            $baseAmount = $customer->type=='flat' || $customer->type=='combine' ?
                                ($customer->total_flat * $setting->monthly_rate)
                                : 0;
                            $cons_amount = $customer->type=='construction' || $customer->type=='combine' ?
                                 $setting->monthly_const_rate : 0;
                            $surchargeAmount = 0;

                            $totalAmount = $baseAmount + $surchargeAmount+ $cons_amount;

                            // Calculate Security bills amounts

                            $securityBaseAmount = $customer->type=='flat' || $customer->type=='combine' ? 
                            $customer->total_security_flat * $setting->monthly_security : 0;
                            $s_cons_amount = $customer->type=='construction' || $customer->type=='combine' ?
                              $setting->const_security : 0;
                            $securityTotalAmount = $securityBaseAmount + $s_cons_amount;

                            $water_rows[] = [
                                'water_customer_id'  => $customer->id,
                                'water_bill_month'   => $month,
                                'water_bill_year'    => $year,
                                'flat_numbers'       => $customer->flats->where('is_occupied',true)->pluck('flat_number')->implode(', '),
                                'total_flats'        => $customer->flats->where('is_occupied',true)->count(),
                                'base_amount'        => round($baseAmount, 2),
                                'cons_amount'       => round($cons_amount, 2),
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
                            $security_rows[] = [
                                'water_customer_id'  => $customer->id,
                                's_bill_month'   => $month,
                                's_bill_year'    => $year,
                                's_flat_numbers'       => '',
                                's_total_flats'        => $customer->total_security_flat,
                                'base_amount'        => round($securityBaseAmount, 2),
                                's_cons_amount'     => round($s_cons_amount, 2),
                                'total_amount'       => round($securityTotalAmount, 2),
                                'paid_amount'        => 0,
                                'bill_creation_date' => Carbon::create($year, $month, 1),
                                'bill_due_date'      => Carbon::create($year, $month, 1)->endOfMonth(),
                                'is_paid'            => false,
                                'created_by'         => $userId,
                                's_invoice_number'   => '',
                            ];
                        }
                    }

                    if (!empty($water_rows)) {
                        WaterBill::insert($water_rows); // 🚀 SQL BULK INSERT
                         
                    }
                    if (!empty($security_rows)) {
                        SecurityBill::insert($security_rows); // 🚀 SQL BULK INSERT
                    }
                });
        });
    }
}

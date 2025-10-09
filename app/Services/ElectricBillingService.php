<?php

namespace App\Services;

use App\Models\MeterReading;
use App\Models\ElectricBill;
use App\Models\ElectricBillSetting;
use Illuminate\Support\Carbon;

class ElectricBillingService
{

    public static function generateBill(MeterReading $reading, ElectricBillSetting $setting, int $userId): ElectricBill
    {
        $consumedUnits = $reading->consume_unit;

        $baseAmount = $consumedUnits * $setting->unit_price;
        $systemLossAmount = $setting->system_loss * $setting->unit_price;

        $surcharge = 0; // surcharge is applied later if unpaid
        $vat = ($baseAmount + $setting->demand_charge + $systemLossAmount + $setting->service_charge) * ($setting->vat / 100);

        $totalAmount = $baseAmount
            + $systemLossAmount
            + $setting->demand_charge
            + $setting->service_charge
            + $surcharge
            + $vat;

        return ElectricBill::create([
            'customer_id'              => $reading->meter->customer_id,
            'meter_reading_id'         => $reading->id,
            'electric_bill_setting_id' => $setting->id,
            'bill_date'                => Carbon::now(),
            'billing_month'            => $reading->reading_date->month,
            'billing_year'             => $reading->reading_date->year,
            'bill_month_name'          => $reading->reading_date->format('F'),
            'consumed_units'           => $consumedUnits,
            'system_loss_units'        => $systemLossAmount,
            'base_amount'              => $baseAmount,
            'demand_charge'            => $setting->demand_charge,
            'service_charge'           => $setting->service_charge,
            'surcharge'                => $surcharge,
            'vat'                      => $vat,
            'total_amount'             => $totalAmount,
            'is_paid'                  => false,
            'due_date'                 => Carbon::parse($reading->reading_date)->addDays(15),
            'created_by'               => $userId,
        ]);
    }


    public static function updateBill(MeterReading $reading, ElectricBillSetting $setting, int $userId): ?ElectricBill
    {
        $bill = $reading->bill; // hasOne relation (MeterReading -> ElectricBill)

        if (! $bill) {
            // If bill does not exist yet, create one instead
            return self::generateBill($reading, $setting, $userId);
        }

        $consumedUnits    = $reading->consume_unit;
        $baseAmount       = $consumedUnits * $setting->unit_price;
        $systemLossAmount = $setting->system_loss * $setting->unit_price;

        $surcharge = $bill->surcharge; // keep previous surcharge, recalc later if unpaid
        $vat = ($baseAmount + $setting->demand_charge + $systemLossAmount + $setting->service_charge)
            * ($setting->vat / 100);

        $totalAmount = $baseAmount
            + $systemLossAmount
            + $setting->demand_charge
            + $setting->service_charge
            + $surcharge
            + $vat;

        $bill->update([
            'customer_id'              => $reading->meter->customer_id,
            'electric_bill_setting_id' => $setting->id,
            'bill_date'                => $reading->reading_date,
            'billing_month'            => $reading->reading_date->month,
            'billing_year'             => $reading->reading_date->year,
            'bill_month_name'          => $reading->reading_date->format('F'),
            'consumed_units'           => $consumedUnits,
            'system_loss_units'        => $systemLossAmount,
            'base_amount'              => $baseAmount,
            'demand_charge'            => $setting->demand_charge,
            'service_charge'           => $setting->service_charge,
            'surcharge'                => $surcharge,
            'vat'                      => $vat,
            'total_amount'             => $totalAmount,
            'due_date'             => Carbon::parse($reading->reading_date)->addDays(15),
        ]);

        return $bill;
    }
}

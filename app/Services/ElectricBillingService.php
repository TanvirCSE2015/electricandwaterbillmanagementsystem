<?php

namespace App\Services;

use App\Models\MeterReading;
use App\Models\ElectricBill;
use App\Models\ElectricBillSetting;
use DateTime;
use Illuminate\Support\Carbon;

class ElectricBillingService
{

    public static function generateBill(MeterReading $reading, ElectricBillSetting $setting, int $userId, int $month): ElectricBill
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
            + round($vat);

        return ElectricBill::create([
            'customer_id'              => $reading->meter->customer_id,
            'meter_reading_id'         => $reading->id,
            'electric_bill_setting_id' => $setting->id,
            'bill_date'                => Carbon::now(),
            'billing_month'            => $month,
            'billing_year'             => $reading->reading_date->year,
            'bill_month_name'          =>  DateTime::createFromFormat('!m', $month)->format('F'),
            'consumed_units'           => $consumedUnits,
            'system_loss_units'        => $systemLossAmount,
            'base_amount'              => $baseAmount,
            'demand_charge'            => $setting->demand_charge,
            'service_charge'           => $setting->service_charge,
            'surcharge_percentage'     =>$setting->surcharge/100,
            'surcharge'                => $surcharge,
            'vat'                      => round($vat),
            'total_amount'             => round($totalAmount),
            'is_paid'                  => false,
            'due_date'                 => (function() use ($reading) {
                $due = Carbon::parse($reading->reading_date)->addDays(15);
                if ($due->isFriday()) {
                    $due->addDays(2); // Fri -> Sun
                } elseif ($due->isSaturday()) {
                    $due->addDay(); // Sat -> Sun
                }
                return $due;
            })(),
            'created_by'               => $userId,
        ]);
    }


    public static function updateBill(MeterReading $reading, ElectricBillSetting $setting, int $userId): ?ElectricBill
    {
        $bill = $reading->bill; // hasOne relation (MeterReading -> ElectricBill)

        if (! $bill) {
            // If bill does not exist yet, create one instead
            return self::generateBill($reading, $setting, $userId, $reading->reading_date->month);
        }

        $consumedUnits    = $reading->consume_unit < 0 ? 0 : $reading->consume_unit;
        $baseAmount       = $consumedUnits * $setting->unit_price;
        $systemLossAmount = $setting->system_loss * $setting->unit_price;

        $surcharge = round($bill->surcharge); // keep previous surcharge, recalc later if unpaid
        $vat = ($baseAmount + $setting->demand_charge + $systemLossAmount + $setting->service_charge)
            * ($setting->vat / 100);

        $totalAmount = $baseAmount
            + $systemLossAmount
            + $setting->demand_charge
            + $setting->service_charge
            + $surcharge
            + round($vat);

        $bill->update([
            'customer_id'              => $reading->meter->customer_id,
            'electric_bill_setting_id' => $setting->id,
            // 'bill_date'                => $reading->reading_date,
            // 'billing_month'            => $reading->reading_date->month,
            // 'billing_year'             => $reading->reading_date->year,
            // 'bill_month_name'          => $reading->reading_date->format('F'),
            'consumed_units'           => $consumedUnits,
            'system_loss_units'        => $systemLossAmount,
            'base_amount'              => $baseAmount,
            'demand_charge'            => $setting->demand_charge,
            'service_charge'           => $setting->service_charge,
            'surcharge'                => round($surcharge),
            'vat'                      => round($vat),
            'total_amount'             => round($totalAmount),
            // 'due_date'             => Carbon::parse($reading->reading_date)->addDays(15),
        ]);

        return $bill;
    }
}

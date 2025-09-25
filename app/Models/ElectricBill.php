<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricBill extends Model
{
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reading()
    {
        return $this->belongsTo(MeterReading::class, 'meter_reading_id');
    }

    public function billSetting()
    {
        return $this->belongsTo(ElectricBillSetting::class, 'electric_bill_setting_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaterBill extends Model
{
    public function waterCustomer(): BelongsTo
    {
        return $this->belongsTo(WaterCustomer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function securityBill(): HasOne
    {
        return $this->hasOne(SecurityBill::class, 'water_customer_id', 'water_customer_id')
            ->where('s_bill_month', $this->month);
    }

    // public function getSecurityBillAttribute()
    // {
    //     return $this->securityBills
    //         ->where('s_bill_month', $this->water_bill_month)
    //         ->first();
    // }
}

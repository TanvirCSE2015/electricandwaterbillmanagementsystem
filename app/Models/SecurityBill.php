<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityBill extends Model
{
    public  function waterCustomer():BelongsTo
    {
        return $this->belongsTo(WaterCustomer::class);
    }

    public function waterBill(): BelongsTo
    {
        return $this->belongsTo(WaterBill::class,'water_customer_id','water_customer_id');
    }   
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterCustomerFlat extends Model
{
    public function waterCustomer():BelongsTo
    {
        return $this->belongsTo(WaterCustomer::class);
    }
}

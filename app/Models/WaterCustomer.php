<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaterCustomer extends Model
{
    public function waterBills():HasMany
    {
        return $this->hasMany(WaterBill::class);
    }
}

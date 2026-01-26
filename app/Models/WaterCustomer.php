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

    public function flats():HasMany
    {
        return $this->hasMany(WaterCustomerFlat::class);
    }

    public function activeFlats():HasMany
    {
        return $this->hasMany(WaterCustomerFlat::class)->where('is_occupied', true);
    }

    public function unpaidWaterBills():HasMany
    {
        return $this->hasMany(WaterBill::class)->where('is_paid', false);
    }

    public function securityBills():HasMany
    {
        return $this->hasMany(SecurityBill::class);
    }
}

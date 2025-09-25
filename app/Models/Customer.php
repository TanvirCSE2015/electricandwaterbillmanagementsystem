<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    public function meters():HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function readings(): HasManyThrough
    {
        return $this->hasManyThrough(MeterReading::class, Meter::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(ElectricBill::class);
    }
}

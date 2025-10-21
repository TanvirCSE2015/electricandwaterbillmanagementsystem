<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    public function meters():HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function activeMeter(): HasOne
    {
        return $this->hasOne(Meter::class)->where('status', 'active');
    }

    public function readings(): HasManyThrough
    {
        return $this->hasManyThrough(MeterReading::class, Meter::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(ElectricBill::class);
    }

    public function dueBills(): HasMany
    {
        return $this->hasMany(DueElectricBill::class);
    }

    public function previousDue(): HasOne
    {
        return $this->hasOne(PreviousDue::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Blocks::class);
    }
}

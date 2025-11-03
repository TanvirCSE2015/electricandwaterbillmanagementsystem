<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectricBillSetting extends Model
{

    public function electricArea(): BelongsTo
    {
        return $this->belongsTo(ElectricArea::class);
    }
    
}

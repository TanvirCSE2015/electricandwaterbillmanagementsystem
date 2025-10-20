<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PreviousDue extends Model
{
   
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

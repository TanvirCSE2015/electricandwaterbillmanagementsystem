<?php

namespace App\Models;

use App\Services\ElectricBillingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MeterReading extends Model
{
    protected $casts = [
        'reading_date' => 'date', // <--- This makes it a Carbon object
    ];


    protected static function booted()
    {
        // static::creating(function ($reading) {
        //     // $lastReading = self::where('meter_id', $reading->meter_id)
        //     //     ->latest('reading_date')
        //     //     ->first();
        //     // $reading->previous_reading = $lastReading?->current_reading ?? 0;
        //     if ($reading->current_reading !=0) {
        //         $reading->consume_unit = $reading->current_reading - $reading->previous_reading;
        //     }
            
        // });

        static::created(function ($reading) {
            $setting = ElectricBillSetting::latest()->first();
            if ($setting) {
                ElectricBillingService::generateBill($reading, $setting, auth()->id());
            }
        });

        static::updated(function ($reading) {
            $setting = ElectricBillSetting::latest()->first();
            $nextReading = self::where('meter_id', $reading->meter_id)
                ->where('reading_date', '>', $reading->reading_date)
                ->orderBy('reading_date', 'asc')
                ->first();
            // $nextReading=$reading;
            if ($nextReading) {
                $nextReading->previous_reading = $reading->current_reading;
                $nextReading->consume_unit = $nextReading->current_reading - $nextReading->previous_reading;
                $nextReading->save(); // This will also trigger the updated event
                 ElectricBillingService::updateBill($nextReading, $setting, auth()->id());
            }
            if ($setting) {
                ElectricBillingService::updateBill($reading, $setting, auth()->id());
            }
        });
        
    }


    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function bill(): HasOne
    {
        return $this->hasOne(ElectricBill::class, 'meter_reading_id');
    }
}

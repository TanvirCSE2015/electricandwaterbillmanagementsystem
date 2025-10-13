<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectricInvoice extends Model
{
    protected static function booted()
    {
        static::creating(function ($invoice) {
            // Generate a unique invoice number, e.g., INV202507230001
            $prefix = 'INV-' . now()->format('Y-m') . '-';
            $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')->orderByDesc('invoice_number')->first();
            $number = $lastInvoice
                ? ((int)substr($lastInvoice->invoice_number, -4)) + 1
                : 1;
            $invoice->invoice_number = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(ElectricBill::class);
    }

}

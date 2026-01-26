<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityInvoice extends Model
{
    protected static function booted()
    {
        static::creating(function ($invoice) {
            // Generate a unique invoice number, e.g., INV202507230001
            $prefix = 'SI-' . now()->format('Y-m') . '-';
            $lastInvoice = self::where('s_invoice_number', 'like', $prefix . '%')->orderByDesc('s_invoice_number')->first();
            $number = $lastInvoice
                ? ((int)substr($lastInvoice->s_invoice_number, -4)) + 1
                : 1;
            $invoice->s_invoice_number = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function waterCustomer():BelongsTo
    {
        return $this->belongsTo(WaterCustomer::class, 'water_customer_id');
    }
}

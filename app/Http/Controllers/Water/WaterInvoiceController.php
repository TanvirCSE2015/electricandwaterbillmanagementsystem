<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use App\Models\SecurityInvoice;
use App\Models\WaterInvoice;
use Illuminate\Http\Request;

class WaterInvoiceController extends Controller
{

    public function PrintWaterReceipt(Request $request)
    {
        $billIds = $request->input('bill_ids', []);
        // Fetch the water bills based on the provided IDs
        $receipt=WaterInvoice::with('waterCustomer')->find($request->id);
        $s_receipt=SecurityInvoice::with('waterCustomer')->find($request->s_id);

        // Return a view to print the water receipt
        return view('invoice.print_water_invoice', compact('receipt', 's_receipt'));
    }
    
}

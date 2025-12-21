<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaterInvoiceController extends Controller
{

    public function PrintWaterReceipt(Request $request)
    {
        $billIds = $request->input('bill_ids', []);
        // Fetch the water bills based on the provided IDs
        $waterBills = \App\Models\WaterBill::whereIn('id', $billIds)->get();

        // Return a view to print the water receipt
        return view('invoice.print_water_invoice', compact('waterBills'));
    }
    
}

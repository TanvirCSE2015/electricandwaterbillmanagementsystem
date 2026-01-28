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
        $receipt=$request->id ? WaterInvoice::with('waterCustomer')->find($request->id) : null;
        $s_receipt=$request->s_id ? SecurityInvoice::with('waterCustomer')->find($request->s_id) : null;
        $type=$request->type;

        // Return a view to print the water receipt

        return $type == 'previous' ? view('invoice.print_water_previous_due_invoice', compact('receipt', 's_receipt', 'type')) : view('invoice.print_water_invoice', compact('receipt', 's_receipt', 'type'));
    }
    
}

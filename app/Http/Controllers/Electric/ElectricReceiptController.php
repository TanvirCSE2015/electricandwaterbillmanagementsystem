<?php

namespace App\Http\Controllers\Electric;

use App\Http\Controllers\Controller;
use App\Models\ElectricInvoice;
use Illuminate\Http\Request;

class ElectricReceiptController extends Controller
{
    public function PrintElectricReceipt(Request $request){
        $receipt=ElectricInvoice::with('customer')->find($request->id);
        return view('invoice.printelectricinvoice', compact('receipt'));
    }
}

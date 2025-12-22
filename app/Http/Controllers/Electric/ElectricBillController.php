<?php

namespace App\Http\Controllers\Electric;

use App\Models\Customer;
use App\Models\ElectricBill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ElectricBillController extends Controller
{
    // search electric bill
    public function search(Request $request)
    {
        $request->validate([
            'shop_no' => 'required'
        ]);

        $customer = Customer::with([
                'electricArea',
                'block',
                'activeMeter',
                'bills.reading'
            ])
            ->where('shop_no', $request->shop_no)
            ->first();

        if (!$customer || $customer->bills->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No bills found'
            ]);
        }

        $html = view('pay_bill.search_electric_bill', compact('customer'))->render();

        return response()->json([
            'status' => true,
            'html' => $html
        ]);
    }



}

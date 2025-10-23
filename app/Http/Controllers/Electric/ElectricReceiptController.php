<?php

namespace App\Http\Controllers\Electric;

use App\Http\Controllers\Controller;
use App\Models\ElectricBill;
use App\Models\ElectricInvoice;
use Illuminate\Http\Request;

class ElectricReceiptController extends Controller
{
    public function PrintElectricReceipt(Request $request){
        $type = $request->query('type');
        $receipt=ElectricInvoice::with('customer')->find($request->id);
        return view('invoice.printelectricinvoice', compact('receipt','type'));
    }

    public function PrintElectricBillCopy(Request $request){
        // $bill=ElectricInvoice::with('customer')->find($request->id);
        $month=$request->query('month');
        $year=$request->query('year');
        $records=ElectricBill::query()->with('customer','customer.activeMeter','billSetting','reading',
        'customer.unpaidBills','customer.previousDue')
           ->where(['billing_month'=>$request->month, 'billing_year'=>$request->year,])->get();
        return view('invoice.printelectricbillcopy',compact('records','month','year'));
    }
}

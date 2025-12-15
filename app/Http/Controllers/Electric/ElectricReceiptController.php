<?php

namespace App\Http\Controllers\Electric;

use App\Http\Controllers\Controller;
use App\Models\ElectricBill;
use App\Models\ElectricCalculation;
use App\Models\ElectricInvoice;
use Illuminate\Database\Eloquent\Builder;
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
        $area_id=$request->query('area_id');
        $records=ElectricBill::query()->with('customer','customer.activeMeter','billSetting','reading',
        'customer.unpaidBills','customer.previousDue')
        ->whereHas('customer', function (Builder $query) use($area_id) {
                $query->where('electric_area_id', $area_id);
        })
        ->where(['billing_month'=>$request->month, 'billing_year'=>$request->year,])->get();

        $calculaton=ElectricCalculation::query()
        ->where(['bill_month'=>$month,'bill_year'=>$year,'elecric_area_id'=>$area_id])
        ->latest()->first();
       

        return view('invoice.printelectricbillcopy',compact('records','month','year','calculaton'));
    }
}

<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaterReportController extends Controller
{
    public function PrintWaterInvoiceReport(Request $request)
    {
        $date=$request->query('date');
        $endDate=$request->query('end_date');
        $type=$request->query('type');
        $records='';
        if($type=='water'){
            $records=\App\Models\WaterInvoice::query()
            ->when($date && $endDate, function ($query) use ($date, $endDate) {
                $query->whereBetween('w_invoice_date', [$date, $endDate]);
            })
            ->when($date && ! $endDate, function ($query) use ($date) {
                $query->whereDate('w_invoice_date', '=', $date);
            })->get();
        }elseif($type=='security'){
            $records=\App\Models\SecurityInvoice::query()->
            when($date && $endDate, function ($query) use ($date, $endDate) {
                $query->whereBetween('s_invoice_date', [$date, $endDate]);
            })
            ->when($date && ! $endDate, function ($query) use ($date) {
                $query->whereDate('s_invoice_date', '=', $date);
            })
            ->get();
        }
        return view('water.reports.print_water_invoice_report',compact('records','type','date','endDate'));
    }
}

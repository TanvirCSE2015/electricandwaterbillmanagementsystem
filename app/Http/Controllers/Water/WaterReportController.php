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
        if($type=='water' || $type=='w_previous'){
            $records=\App\Models\WaterInvoice::query()
            ->when($date && $endDate, function ($query) use ($date, $endDate) {
                $query->whereBetween('w_invoice_date', [$date, $endDate]);
            })
            ->when($date && ! $endDate, function ($query) use ($date) {
                $query->whereDate('w_invoice_date', '=', $date);
            })
            ->where('w_due_type', $type === 'w_previous' ? 'previous' : 'current')->get();
        }elseif($type=='security' || $type=='s_previous'){
            $records=\App\Models\SecurityInvoice::query()->
            when($date && $endDate, function ($query) use ($date, $endDate) {
                $query->whereBetween('s_invoice_date', [$date, $endDate]);
            })
            ->when($date && ! $endDate, function ($query) use ($date) {
                $query->whereDate('s_invoice_date', '=', $date);
            })
            ->where('due_type', $type == 's_previous' ? 'pre_due' : 'current')->get();
        }
        // dd($records);
        return view('water.reports.print_water_invoice_report',compact('records','type','date','endDate'));
    }

    public function PrintWaterLaserReport(Request $request)
    {
        $month=$request->query('month');
        $year=$request->query('year');
        $type=$request->query('type');
        $records='';
        if($type=='water'){
            $records=\App\Models\WaterBill::query()
            ->select('*')
                ->selectRaw("
                CASE
                    WHEN water_bills.bill_due_date < CURDATE()
                    THEN ROUND(
                        water_bills.total_amount + (water_bills.total_amount * surcharge_percent / 100) ,
                        2
                    )
                    ELSE water_bills.total_amount
                END AS payable_amount
            ")

            ->selectRaw("
                CASE
                    WHEN water_bills.bill_due_date < CURDATE()
                    THEN ROUND(water_bills.total_amount * surcharge_percent / 100, 2)
                    ELSE 0
                END AS calculated_surcharge
            ")
            ->when($month, fn ($q) => $q->where('water_bill_month', $month))
            ->when($year, fn ($q) => $q->where('water_bill_year', $year))->get();
        }elseif($type=='security'){
            $records=\App\Models\SecurityBill::query()
            ->when($month, fn ($q) => $q->where('s_bill_month', $month))
            ->when($year, fn ($q) => $q->where('s_bill_year', $year))->get();
        }
        return view('water.reports.print_water_laser_report',compact('records','type','month','year'));
    }
}

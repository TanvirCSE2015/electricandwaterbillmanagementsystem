<?php

namespace App\Http\Controllers\Electric;

use App\Http\Controllers\Controller;
use App\Models\ElectricInvoice;
use Illuminate\Http\Request;

class ElectricReporController extends Controller
{

    public function PrintDailyElectricInvoice(Request $request)
    {
        $date  =  $request->query('date');
        $month = $request->query('month');
        $year = $request->query('year');
        $type = $request->query('type');

        // Fetch the data for the report based on the date
        $records = ElectricInvoice::when($date, function($query) use($date){
                $query->whereDate('invoice_date',$date);
            })
            ->when($month && $year , function($query) use($month,$year){
                $query->where(['invoice_month' => $month, 'invoice_year' => $year]);
            })
            ->when($year , function($query) use($year){
                $query->where(['invoice_year' => $year]);
            })
            ->with('customer')
            ->get();
        $total = $records->sum('total_amount');

        // Return a view or a PDF with the data
        return view('reports.daily_electric_invoice', compact('records', 'date', 'total','type','month','year'));
    }
    
}

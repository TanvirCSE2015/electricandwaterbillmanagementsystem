<?php

namespace App\Http\Controllers\Electric;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ElectricBill;
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
                $query->where(['invoice_month' => $month, 'invoice_year' => $year])
                ->selectRaw('ROW_NUMBER() OVER() as id,
                    invoice_date, invoice_month,invoice_month_name, invoice_year, SUM(total_amount) as total_amount')
                ->groupByRaw('invoice_date');
            })
            ->when($year , function($query) use($year){
                $query->where(['invoice_year' => $year])
                    ->selectRaw('ROW_NUMBER() OVER() as id, invoice_month, SUM(total_amount) as total_amount,
                    invoice_month_name, invoice_year');
            })
            ->with('customer')
            ->get();
        $total = $records->sum('total_amount');

        // Return a view or a PDF with the data
        return view('reports.daily_electric_invoice', compact('records', 'date', 'total','type','month','year'));
    }

    public function PrintUnpaidElectricBillsReport(Request $request){
        $type=$request->query('type');
        $customer_id=$request->query('customer_id');
        if($type==='short'){
             $records=Customer::query()
                ->whereHas('bills', function($query){
                    $query->where('is_paid', false);
                })
                ->withSum(['bills' => function($query){
                    $query->where('is_paid', false);
                }], 'total_amount')
                ->when($customer_id, function($query) use($customer_id){
                    $query->where('id', $customer_id);
                })->get()
                ;
                $total=$records->sum('bills_sum_total_amount');
            return view('reports.unpaid_electric_bills', compact('records','type','total'));
        }

         $records=ElectricBill::query()
                ->where('is_paid', false)->with('customer')
                ->when($customer_id, function($query) use($customer_id){
                            $query->where('id', $customer_id);
                })->get();
        $total=$records->sum('total_amount');
       return view('reports.unpaid_electric_bills', compact('records','type','total'));
    }
    
}

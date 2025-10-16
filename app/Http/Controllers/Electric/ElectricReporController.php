<?php

namespace App\Http\Controllers\Electric;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ElectricBill;
use App\Models\ElectricInvoice;
use Carbon\Carbon;
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
             $records = Customer::query()
                ->whereHas('bills', fn($q) => $q->where('is_paid', false))
                ->with(['bills' => fn($q) => $q->where('is_paid', false)])
                ->when($customer_id, fn($q) => $q->where('id', $customer_id))
                ->get()
                ->map(function ($customer) {
                    $totalAmount = $customer->bills->sum('total_amount');
                    $totalSurcharge = 0;

                    foreach ($customer->bills as $bill) {
                        if (now()->gt($bill->due_date)) {
                            $daysLate = now()->diffInDays(Carbon::parse($bill->due_date));
                            $totalSurcharge += $bill->total_amount * $bill->surcharge_percentage;
                        }
                    }

                    // Attach computed totals for easy access in Blade
                    $customer->total_surcharge = $totalSurcharge;
                    $customer->grand_total = $totalAmount + $totalSurcharge;

                    return $customer;
                });

            $total = $records->sum('grand_total');
            return view('reports.unpaid_electric_bills', compact('records','type','total'));
        }

         $records = ElectricBill::query()
            ->where('is_paid', false)
            ->with('customer')
            ->when($customer_id, fn($q) => $q->where('customer_id', $customer_id))
            ->get()
            ->map(function ($bill) {
                // Auto add surcharge if overdue
                if (now()->gt($bill->due_date)) {
                    $daysLate = now()->diffInDays(Carbon::parse($bill->due_date));
                    $bill->calculated_surcharge = $bill->total_amount * $bill->surcharge_percentage ;
                } else {
                    $bill->calculated_surcharge = 0;
                }

                $bill->grand_total = $bill->total_amount + $bill->calculated_surcharge;
                return $bill;
            });

        $total = $records->sum('grand_total');
       return view('reports.unpaid_electric_bills', compact('records','type','total'));
    }

    public function PrintElectricLaserReport(Request $request){
        date_default_timezone_set('Asia/Dhaka');
        $customer_id=$request->query('customer_id');
        $month=$request->query('month');
        $year=$request->query('year');

        $records = ElectricBill::query()
            ->with('customer')
            ->when($customer_id, fn($q) => $q->where('customer_id', $customer_id))
            ->when($month, fn($q) => $q->where('billing_month', $month))
            ->when($year, fn($q) => $q->where('billing_year', $year))
            ->get()
            ->map(function ($bill) {
                $today = Carbon::today();
                $dueDate = Carbon::parse($bill->due_date)->startOfDay();
                // Auto add surcharge if overdue
                if ($dueDate->lt($today)) {
                    $daysLate = now()->diffInDays(Carbon::parse($bill->due_date));
                    $bill->calculated_surcharge = $bill->total_amount * $bill->surcharge_percentage ;
                } else {
                    $bill->calculated_surcharge = 0;
                }

                $bill->grand_total = $bill->total_amount + $bill->calculated_surcharge;
                return $bill;
            });

        return view('reports.electric_laser', compact('records','customer_id','month','year'));

    }
    
}

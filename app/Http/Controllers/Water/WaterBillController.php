<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use App\Models\WaterBill;
use App\Models\WaterSetting;
use Illuminate\Http\Request;

class WaterBillController extends Controller
{
    public function PrintWaterBillCopy(Request $request)
    {
       $motnth = $request->input('month');
       $year = $request->input('year');

        // Fetch the water bill from the database
     $records = WaterBill::query()
            ->where('water_bill_month', $motnth)
            ->where('water_bill_year', $year)
            ->with('waterCustomer','waterCustomer.flats')
            ->get();
        //   dd($records);
        // Return the view with the water bill data
        $setting = WaterSetting::latest()->firstOrFail();
        return view('water.print_water_bill_copy', compact('records','motnth','year','setting'));
    }
}

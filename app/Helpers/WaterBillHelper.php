<?php

namespace App\Helpers;

use App\Models\WaterBill;
use App\Models\WaterCustomer;
use App\Models\WaterInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WaterBillHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }

    public static function bn2en($number)
    {
         $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
         $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        return str_replace($bn, $en, $number);
    }

    public static function createInvoice(int $customerId, int $count, int $userId)
    {
        return DB::transaction(function () use ($customerId, $count, $userId) {
            // Fetch unpaid bills
            $bills = WaterBill::query()->where(['water_customer_id'=>$customerId,'is_paid'=>false])
                ->select('*')
                ->selectRaw("
                    CASE
                        WHEN bill_due_date < CURDATE()
                        THEN ROUND(
                            base_amount + (base_amount * surcharge_percent / 100),
                            2
                        )
                        ELSE base_amount
                    END AS payable_amount
                ")
                ->selectRaw("
                    CASE
                        WHEN bill_due_date < CURDATE()
                        THEN ROUND(base_amount * surcharge_percent / 100, 2)
                        ELSE 0
                    END AS calculated_surcharge
                ")
                ->limit($count)
                ->get();

            if ($bills->isEmpty()) {
                return ['status' => 'warning', 'message' => 'কোনো বকেয়া বিল পাওয়া যায়নি।'];
            }

            // Calculate total amount with surcharge
            $totalAmount = $bills->sum('payable_amount');
            //Carbon::create()->month(3)->translatedFormat('F')
            // Determine invoice range and metadata
            $fromMonth = Carbon::create()->month($bills->first()->water_bill_month)->translatedFormat('F') . '-' . $bills->first()->water_bill_year ?? '';
            $toMonth   = $count ==1 ? '' : Carbon::create()->month($bills->last()->water_bill_month)->translatedFormat('F') . '-' . $bills->last()->water_bill_year ?? '';
            $invoiceYear =  now()->year;

            $invoiceNumber = 'INV-' . now()->format('YmdHis') . '-' . $customerId;

            // Create invoice
            $invoice = WaterInvoice::create([
                'water_customer_id' => $customerId,
                'w_invoice_date' => now(),
                'w_invoice_month' => now()->month,
                'w_invoice_month_name' => now()->format('F'),
                'w_invoice_year' => $invoiceYear,
                'w_from_month' => $fromMonth,
                'w_to_month' => $toMonth,
                'w_total_amount' => $totalAmount,
                'w_created_by' => $userId,
            ]);

            foreach ($bills as $bill) {
                WaterBill::where('id', $bill->id)->update([
                    'surcharge_amount' =>  $bill->calculated_surcharge ?? 0,
                    'is_paid' => true,
                    'paid_by' => $userId,
                    'paid_at' => now(),
                    'payment_method' => 'Offline',
                    'paid_amount' => $bill->payable_amount,
                    'surcharge_amount' => $bill->calculated_surcharge,
                    'water_invoice_id' => $invoice->id,
                ]);
            }

            // return [
            //     'status' => 'success',
            //     'message' => 'ইনভয়েস সফলভাবে তৈরি হয়েছে!',
            //     'invoice' => $invoice,
            // ];
            return redirect()->route('water-receipt.print',['id'=>$invoice->id,'type'=>'current']);
        });
    }

    public static function previousDueInvoice(int $customerId, int $userId, float $paidAmount)
    {
        return DB::transaction(function () use ($customerId, $userId, $paidAmount) {
            $previousDue = WaterCustomer::find($customerId);
            $dueTotal= $previousDue->previous_due - $paidAmount;

            if (!$previousDue) {
                return ['status' => 'warning', 'message' => 'কোনো পূর্বের বকেয়া পাওয়া যায়নি।'];
            }

            $invoiceNumber = 'INV-PD-' . now()->format('YmdHis') . '-' . $customerId;

            // Create invoice
            $invoice = WaterInvoice::create([
                'water_customer_id' => $customerId,
                'w_invoice_date' => now(),
                'w_invoice_month' => now()->month,
                'w_invoice_month_name' => now()->format('F'),
                'w_invoice_year' => now()->year,
                'w_from_month' => 'পূর্বের বকেয়া',
                'w_to_month' => '',
                'w_total_amount' => $paidAmount,
                'w_created_by' => $userId,
            ]);

            // Update paid status
           
            $previousDue->previous_due = $dueTotal;
            $previousDue->save();

            return redirect()->route('water-receipt.print',['id'=>$invoice->id,'type'=>'previous']);
        });
    }
}

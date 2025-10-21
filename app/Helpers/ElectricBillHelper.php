<?php

namespace App\Helpers;

use App\Models\ElectricBill;
use App\Models\ElectricBillSetting;
use App\Models\ElectricInvoice;
use Illuminate\Support\Facades\DB;

class ElectricBillHelper
{
   public static function calculateSurcharge($bill): float
    {
        $today = now();
        $dueDate = $bill->due_date ?? null;

        if ($bill->surcharge > 0) {
            return $bill->surcharge;
        }

        if ($dueDate && $today->gt($dueDate)) {
            $surcharge = round($bill->total_amount * $bill->surcharge_percentage);
            return $surcharge;
        }

        return 0;
    }

    public static function createInvoice(int $customerId, int $count, int $userId)
    {
        return DB::transaction(function () use ($customerId, $count, $userId) {
            // Fetch unpaid bills
            $bills = ElectricBill::where('customer_id', $customerId)
                ->where('is_paid', false)
                ->orderBy('billing_year')
                ->orderBy('billing_month')
                ->limit($count)
                ->get();

            if ($bills->isEmpty()) {
                return ['status' => 'warning', 'message' => 'কোনো বকেয়া বিল পাওয়া যায়নি।'];
            }

            // Calculate total amount with surcharge
            $totalAmount = 0;
            $surcharge = 0;
            foreach ($bills as $bill) {
                if($bill->surcharge > 0){
                    $totalAmount =   $bill->total_amount;
                }else{
                    $surcharge = self::calculateSurcharge($bill);
                    $totalAmount += $bill->total_amount + $surcharge;
                }
            }

            // Determine invoice range and metadata
            $fromMonth = $bills->first()->bill_month_name . '-' . $bills->first()->billing_year ?? '';
            $toMonth   = $count ==1 ? '' : $bills->last()->bill_month_name . '-' . $bills->last()->billing_year ?? '';
            $invoiceYear =  now()->year;

            $invoiceNumber = 'INV-' . now()->format('YmdHis') . '-' . $customerId;

            // Create invoice
            $invoice = ElectricInvoice::create([
                'customer_id' => $customerId,
                'invoice_date' => now(),
                'invoice_month' => now()->month,
                'invoice_month_name' => now()->format('F'),
                'invoice_year' => $invoiceYear,
                'from_month' => $fromMonth,
                'to_month' => $toMonth,
                'total_amount' => $totalAmount,
                'created_by' => $userId,
            ]);

            // Update paid status
            ElectricBill::whereIn('id', $bills->pluck('id'))->update([
                'surcharge' => $surcharge,
                'is_paid' => true,
                'paid_by' => $userId,
                'payment_date' => now(),
                'payment_method' => 'Offline',
                'electric_invoice_id' => $invoice->id,
            ]);

            // return [
            //     'status' => 'success',
            //     'message' => 'ইনভয়েস সফলভাবে তৈরি হয়েছে!',
            //     'invoice' => $invoice,
            // ];
            return redirect()->route('electric-receipt.print',['id'=>$invoice->id,'type'=>'current']);
        });
    }

    public static function previousDueInvoice(int $customerId, int $userId, float $paidAmount)
    {
        return DB::transaction(function () use ($customerId, $userId, $paidAmount) {
            $previousDue = \App\Models\PreviousDue::where('customer_id', $customerId)
                ->where('is_paid', false)
                ->first();
            $dueTotal= $previousDue->amount - $paidAmount;

            if (!$previousDue) {
                return ['status' => 'warning', 'message' => 'কোনো পূর্বের বকেয়া পাওয়া যায়নি।'];
            }

            $invoiceNumber = 'INV-PD-' . now()->format('YmdHis') . '-' . $customerId;

            // Create invoice
            $invoice = ElectricInvoice::create([
                'customer_id' => $customerId,
                'invoice_date' => now(),
                'invoice_month' => now()->month,
                'invoice_month_name' => now()->format('F'),
                'invoice_year' => now()->year,
                'from_month' => 'পূর্বের বকেয়া',
                'to_month' => '',
                'total_amount' => $paidAmount,
                'due_type' => 'previous_due',
                'created_by' => $userId,
            ]);

            // Update paid status
            $previousDue->is_paid = $dueTotal == 0 ? true : false;
            $previousDue->amount= $dueTotal;
            // $previousDue->paid_by = $userId;
            // $previousDue->payment_date = now();
            // $previousDue->electric_invoice_id = $invoice->id;
            $previousDue->save();

            return redirect()->route('electric-receipt.print',['id'=>$invoice->id,'type'=>'previous']);
        });
    }
}

<?php

use App\Http\Controllers\Electric\ElectricReceiptController;
use App\Http\Controllers\Electric\ElectricReporController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('gateway');
});

Route::get('/print-daily-electric-invoices',[ElectricReporController::class,'PrintDailyElectricInvoice'])
->name('daily-electric-invoice.print')->middleware('auth');
Route::get('/print-unpaid-electric-bills-report',[ElectricReporController::class,'PrintUnpaidElectricBillsReport'])
->name('unpaid-electric-bills-report.print')->middleware('auth');
Route::get('/print-electric-laser-report',[ElectricReporController::class,'PrintElectricLaserReport'])
->name('electric-laser-report.print')->middleware('auth');




Route::get('/print-electric-receipt', [ElectricReceiptController::class, 'PrintElectricReceipt'])
->name('electric-receipt.print');

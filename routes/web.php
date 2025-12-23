<?php

use App\Http\Controllers\Electric\ElectricReceiptController;
use App\Http\Controllers\Electric\ElectricReporController;
use App\Http\Controllers\Water\WaterInvoiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Electric\ElectricBillController;

Route::get('/gateway', function () {
    return view('gateway');
});

Route::get('/print-daily-electric-invoices',[ElectricReporController::class,'PrintDailyElectricInvoice'])
->name('daily-electric-invoice.print')->middleware('auth');
Route::get('/print-unpaid-electric-bills-report',[ElectricReporController::class,'PrintUnpaidElectricBillsReport'])
->name('unpaid-electric-bills-report.print')->middleware('auth');
Route::get('/print-electric-laser-report',[ElectricReporController::class,'PrintElectricLaserReport'])
->name('electric-laser-report.print')->middleware('auth');
Route::get('/print-electric-invoice-pre-due',[ElectricReporController::class,'PrintElectricPreDueInvoice'])
->name('electric-invoice-pre-due.print')->middleware('auth');




Route::get('/print-electric-receipt', [ElectricReceiptController::class, 'PrintElectricReceipt'])
->name('electric-receipt.print');
Route::get('/print-electric-bill-copy', [ElectricReceiptController::class, 'PrintElectricBillCopy'])
->name('electric-bill-copy.print');

// home
Route::get('/', function () {
    return view('home');
});

// bill pay
Route::get('/electric-bill', function () {
    return view('pay_bill.electric_bill');
})->name('electic-bill');
Route::get('/water-bill', function () {
    return view('pay_bill.water_bill');
})->name('water-bill');

// Electric bill search
Route::get('/electric-bill/search', [ElectricBillController::class, 'search'])->name('electric.bill.search');


Route::get('/print-water-receipt', [WaterInvoiceController::class, 'PrintWaterReceipt'])
->name('water-receipt.print');

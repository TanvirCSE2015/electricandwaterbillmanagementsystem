
@extends('invoice.layout.invoice_layout')
@php
use Rakibhstu\Banglanumber\NumberToBangla;
$numto = new NumberToBangla();
function en2bn($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
    return str_replace($en, $bn, $number);
}
@endphp
@section('main_content')
<div class="row mb-2">
    <div class="col-6">
      ফরম নং ক্যান্ট ৪-বি <br>
      [ধারা ২৪ (১)]
    </div>
    <div class="col-6 text-end">
      তারিখ: <span class="dashed">{{ en2bn($receipt->invoice_date) }}{{ 'ইং' }}</span>
    </div>
  </div>

<div class="row align-items-center mb-3">
  <!-- Left: Logo -->
  <div class="col-2 text-start">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 60px;">
  </div>

  <!-- Center: Titles -->
  <div class="col-8 text-center">
    <h5 class="fw-bold mb-1">ক্যান্টনমেন্ট বোর্ড, ঢাকা ক্যান্টনমেন্ট</h5>
    <p class="fw-bold mb-0">মূল রশিদ</p>
  </div>

  <!-- Right: QR Code -->
  <div class="col-2 text-end">
    {!! QrCode::size(60)
    ->encoding('UTF-8')
    ->generate(
        'রশিদ নং: ' . en2bn($receipt->invoice_number) . "\n" .
        'মোট টাকা: ' . en2bn($receipt->total_amount) . '/='
    )
!!}
  </div>
</div>


  <div class="row mb-2">
    <div class="col-6">
      রশিদ নং: <span class="fw-bold">{{ en2bn($receipt->invoice_number) }}</span>
    </div>
  </div>

  <div class="mb-2">
    <p>
        <span class="label">জনাব</span>
        <span class="d-inline-block dashed text-center" style="min-width: 35%;">{{ en2bn($receipt->customer->name) }}</span>
        <span class="label">দোকান নং</span>
        <span class="d-inline-block dashed text-center" style="min-width: 20%;">{{ en2bn($receipt->customer->shop_no) }}</span>
        <span>এর বিপরীতে প্রাপ্ত</span>
  </p>
  </div>

  <div class="mb-2">
   <span class=""> টাকা (কথায়):</span>
    <span class="d-inline-block dashed text-center" style="min-width: 82%;">{{$numto->bnWord($receipt->total_amount )}}{{ ' টাকা মাত্র' }}</span>
  </div>

  <div class="mb-2">
    বিষয়: <span class="d-inline-block dashed text-center" style="min-width: 91%;">{{ $receipt->to_month ? en2bn($receipt->from_month) . ' হতে ' .en2bn($receipt->to_month) :  en2bn($receipt->from_month) }} বিদ্যুৎ বিল</span>
  </div>

  {{-- <div class="mb-3">
    <div class="dashed w-100"></div>
  </div> --}}

  <div class="mb-4">
    টাকা: <div class="dashed w-25 d-inline-block text-center">{{ $numto->bnCommaLakh($receipt->total_amount )}}{{ '/=' }}</div>
  </div>

<div class="col-sm-12 d-flex justify-content-end align-items-end mb-5">

    <div class="text-end">
        <p class="text-center" style="margin-bottom: 0rem;">ক্যান্টনমেন্ট এক্সিকিউটিভ অফিসার<br>
        ঢাকা ক্যান্টনমেন্ট</p>
        <p class="fs-6" style="font-size: 12px!important;">( সিস্টেম থেকে প্রিন্টকৃত কপি,কোন স্বাক্ষরের প্রয়োজন নেই )</p>
    </div>
</div>
@endsection
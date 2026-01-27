@extends('water.reports.layouts.water_report_layout')
@php
use Rakibhstu\Banglanumber\NumberToBangla;

$numto = new NumberToBangla();
function en2bn($number): string
{
    $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
    return str_replace($en, $bn, $number);
}
@endphp
@section('main_content')
    <div class="text-center">
        <img src="{{ asset('images/logo.png') }}" alt="" srcset="">
        <h4>ঢাকা ক্যান্টনমেন্ট বোর্ড</h4>
        <h6>{{ $type === 'water' ? 'পানি লেজার' : 'নিরাপত্তা লেজার' }}</h6>
        <h6 class="text-decoration-underline">সময়ঃ {{ en2bn($month ? date("F", mktime(0, 0, 0, $month, 10)) . '-' . $year . ' ইং' : $year ) }}</h6>
    </div>
    <div class="text-end">
        <h6>মোট বিলঃ {{ $numto->bnCommaLakh(round($records->where('is_paid', 1)->sum($type === 'water' ? 'payable_amount' : 'total_amount'))) . '/='  }}</h6>
        <h6>মোট আদায়ঃ {{ $numto->bnCommaLakh(round($records->where('is_paid', 1)->sum('paid_amount'))) . '/='  }}</h6>
        <h6>মোট বাকিঃ {{ $numto->bnCommaLakh(round($records->where('is_paid', 0)->sum($type === 'water' ? 'payable_amount' : 'total_amount'))) . '/='  }}</h6>
    </div>
    <div class="p-1">
        <table class="table table-bordered table-striped">
            <thead class="text-center">
                @if ($type === 'water')
                <th>গ্রাহক</th>
                <th>হোল্ডিং</th>
                <th>ফ্ল্যাট সংখ্যা</th>
                <th>মাস</th>
                <th>ফ্ল্যাট বিল</th>
                <th>নির্মাধীন বিল</th>
                <th>মোট বিল</th>
                <th>সার্চার্জ</th>
                <th>মোট</th>
                @else
                <th>গ্রাহক</th>
                <th>হোল্ডিং</th>
                <th>ফ্ল্যাট সংখ্যা</th>
                <th>মাস</th>
                <th>ফ্ল্যাট বিল</th>
                <th>নির্মাধীন বিল</th>   
                <th>মোট বিল</th>
                @endif
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr class="text-center" style="font-size: 12px">
                        <td>{{ $record->waterCustomer->customer_name }}</td>
                        <td>{{ en2bn($record->waterCustomer->holding_number) }}</td>
                        <td>{{ en2bn($record->waterCustomer->total_flat) }}</td>
                        <td>{{ en2bn(date("F", mktime(0, 0, 0, $type === 'water' ? $record->water_bill_month : $record->s_bill_month, 10)) . '-' . $year) }}</td>
                        @if ($type === 'water')
                        <td>{{ $numto->bnCommaLakh(round($record->base_amount)) . '/=' }}</td>
                        <td>{{ $numto->bnCommaLakh(round($record->cons_amount)) . '/=' }}</td>
                        <td>{{ $numto->bnCommaLakh(round($record->total_amount)) . '/=' }}</td>
                        {{-- <td>{{ WaterBillHelper::en2bn($record->surcharge_percent) }}%</td> --}}
                        <td>{{ $numto->bnCommaLakh($record->calculated_surcharge) }}</td>
                        <td>{{ $numto->bnCommaLakh(round($record->payable_amount)) . '/=' }}</td>
                        @else
                        <td>{{ $numto->bnCommaLakh(round($record->base_amount)) . '/=' }}</td>
                        <td>{{ $numto->bnCommaLakh(round($record->s_cons_amount)) . '/=' }}</td>
                        <td>{{ $numto->bnCommaLakh(round($record->total_amount)) . '/=' }}</td>
                        @endif
                    </tr>
                @endforeach
        </table>
    </div>
@endsection
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
    <h6>{{ $type === 'water' ? 'পানি বকেয়া আদায়' : 'নিরাপত্তা বকেয়া আদায়' }}</h6>
    <h6 class="text-decoration-underline">তারিখঃ {{ en2bn($endDate ? $date . ' ইং থেকে ' . $endDate . ' ইং' : $date ) }}</h6>
</div>
<div class="text-end">
    <h6>মোট আদায়ঃ {{ $type=='water' || $type=='previous' ? $numto->bnCommaLakh(round($records->sum('w_total_amount'))) . '/=' : $numto->bnCommaLakh(round($records->sum('s_total_amount'))) . '/=' }}</h6>
</div>
<div class="p-1">
    <table class="table table-bordered table-striped">
        <thead class="text-center">
            <th>রশিদ নং</th>
            <th>গ্রাহকের নাম</th>
            <th>হোল্ডিং নং</th>
            <th>ফ্ল্যাট নং</th>
            <th>ফ্ল্যাট সংখ্যা</th>
            <th>মোট</th>
        </thead>
        <tbody>
            @foreach ($records as $record)

                <tr class="text-center" style="font-size: 12px">
                    <td>{{ en2bn($type=='water' ? $record->invoice_number : $record->s_invoice_number) }}</td>
                    <td>{{ $record->waterCustomer->customer_name }}</td>
                    <td>{{ en2bn($record->waterCustomer->holding_number) }}</td>
                    <td>{{ $record->waterCustomer->activeFlats->pluck('flat_number')->implode(', ') }}</td>
                    <td>{{ en2bn($record->waterCustomer->total_flat) }}</td>
                    <td>{{ $numto->bnCommaLakh(round($type=='water' || $type=='previous' ? $record->w_total_amount : $record->s_total_amount)) . '/=' }}</td>
                </tr>
            @endforeach
            <tr style="font-size: 12px">
                <td colspan="4" class="text-end"><strong>মোটঃ</strong></td>
                <td class="text-end" colspan="2"><strong>{{ $type=='water' || $type=='previous' ? $numto->bnCommaLakh(round($records->sum('w_total_amount'))) . '/=' : $numto->bnCommaLakh(round($records->sum('s_total_amount'))) . '/=' }}</strong></td>
            </tr>
            <tr style="font-size: 12px">
                <td colspan="3" class="text-end"><strong>কথায়ঃ</strong></td>
                <td class="text-end" colspan="3"><strong>{{ ($type=='water' || $type=='previous' ? $numto->bnWord(round($records->sum('w_total_amount')))  : $numto->bnWord(round($records->sum('s_total_amount')))) . 'টাকা মাত্র' }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
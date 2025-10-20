
@extends('reports.layouts.report_layout')
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
        <h6>বিদ্যুৎ বকেয়া আদায়</h6>
        @if($type==='daily')
            <h6>দৈনিক রিপোর্ট</h6>
        @elseif ($type === 'monthly')
            <h6>মাসিক রিপোর্ট</h6>
        @else
            <h6>বার্ষিক রিপোর্ট</h6>
        @endif

        @if ($type==='daily')
            <h6>{{ $block_name ? $block_name : '' }}</h6>
            <h6 class="text-decoration-underline" >তারিখঃ {{ en2bn($date) }} ইং</h6>
        @elseif ($type === 'monthly')
            <h6>{{ $block_name ? $block_name : '' }}</h6>
            <h6 class="text-decoration-underline" >মাসঃ {{ $numto->bnMonth($month) .'-'. en2bn($year)}} ইং</h6>
         @else
            <h6>{{ $block_name ? $block_name : '' }}</h6>
            <h6 class="text-decoration-underline" >বছরঃ {{ en2bn($year) }} ইং</h6>
        @endif


    </div>
    <div class="d-flex flex-row-reverse bd-highlight">
        <h6 class="p-2 bd-highlight">মোট আদায়: {{ $numto->bnCommaLakh($total) }} /= </h6>
    </div>

    <table class="table table-bordered table-striped" id="sales-table">
    <thead>
        <tr class="text-center" style="font-size: 14px">
            @if ($type==='daily')
                <th>রশিদ নং</th>
                <th>তারিখ</th>
                <th>গ্রাহকের নাম</th>
                <th>দোকান নং</th>
                <th>বিলের মাস</th>
                <th>মোট বিল</th>
            @elseif ($type==='monthly')
                <th>তারিখ</th>
                <th>মাস</th>
                <th>বছর</th>
                <th>মোট বিল</th>
            @elseif ($type==='yearly')
                <th>মাস</th>
                <th>বছর</th>
                <th>মোট বিল</th>
            @endif
            
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $record)
            @if ($type==='daily')
                <tr class="text-center" style="font-size: 12px">
                    <td>{{ en2bn($record->invoice_number) }}</td>
                    <td>{{ en2bn($record->invoice_date)}}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td>{{ $record->customer->shop_no }}</td>
                    <td>{{ en2bn( $record->to_month ? $record->from_month . ' থেকে ' . $record->to_month : $record->from_month) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->total_amount) }}</td>
                    
                </tr>
            @elseif ($type==='monthly')
                <tr class="text-center" style="font-size: 14px">
                    <td>{{ en2bn($record->invoice_date)}}</td>
                    <td>{{ en2bn($record->invoice_month_name) }}</td>
                    <td>{{ en2bn($record->invoice_year) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->total_amount) }}</td>
                </tr>
            @elseif ($type==='yearly')
                <tr class="text-center" style="font-size: 14px">
                    <td>{{ en2bn($record->invoice_month_name) }}</td>
                    <td>{{ en2bn($record->invoice_year) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->total_amount) }}</td>
                </tr>
            @endif
            
        @empty
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        @endforelse
        @if ($type==='daily')
            <tr>
                <td colspan="5" class="text-end">মোট আদায়</td>
                <td>{{ $numto->bnCommaLakh($total) }}</td>
            </tr>
             <tr>
                <td colspan="2" class="text-end">মোট আদায় কথায়</td> 
                <td colspan="4" class="text-end">{{ $numto->bnMoney($total) . ' মাত্র'}}</td>
            </tr>
         @elseif ($type==='monthly')
            <tr class="text-center">
                <td colspan="3" class="text-end">মোট আদায়</td>
                <td class="text-center">{{ $numto->bnCommaLakh($total) }}</td>
            </tr>
             <tr>
                <td colspan="1" class="text-end">মোট আদায় কথায়</td> 
                <td colspan="3" class="text-end">{{ $numto->bnMoney($total) . ' মাত্র'}}</td>
            </tr>
        @elseif ($type==='yearly')
            <tr>
                <td colspan="2" class="text-end">মোট আদায়</td>
                <td class="text-center">{{ $numto->bnCommaLakh($total) }}</td>
            </tr>
             <tr>
                <td colspan="1" class="text-end">মোট আদায় কথায়</td> 
                <td colspan="2" class="text-end">{{ $numto->bnMoney($total) . ' মাত্র'}}</td>
            </tr>
        @endif
         
    </table>
@endsection
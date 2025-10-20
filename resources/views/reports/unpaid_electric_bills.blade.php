
@extends('reports.layouts.report_layout')
@php
use Rakibhstu\Banglanumber\NumberToBangla;
use App\Helpers\ElectricBillHelper;

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
        <h6>বিদ্যুৎ বকেয়া রিপোর্ট</h6>
        
        <h6>{{ $block_name ?? '' }}</h6>
    

        @if ($type==='short')
            <h6 class="text-decoration-underline" >সামারি রিপোর্ট</h6>
            
         @else
            <h6 class="text-decoration-underline" >বিস্তারিত রিপোর্ট</h6>
        @endif

    </div>
    <div class="d-flex flex-row-reverse bd-highlight">
        <h6 class="p-2 bd-highlight">মোট বকেয়া: {{ $numto->bnCommaLakh($total) }} /= </h6>
    </div>

    <table class="table table-bordered table-striped" id="sales-table">
    <thead>
        <tr class="text-center" style="font-size: 14px">
            @if ($type==='short')
                <th>গ্রাহকের নাম</th>
                <th>দোকান নং</th>
                <th>পূর্বের বকেয়া</th> 
                <th>মোট বিল</th>
            
            @else
                <th>গ্রাহকের নাম</th>
                <th>দোকান নং</th>
                <th>মাস</th>
                <th>বছর</th>
                <th>ব্যবহৃত ইউনিট</th>
                <th>মোট বিল</th>
            @endif
            
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $record)
            @if ($type==='short')
                <tr class="text-center" style="font-size: 12px">
                    
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->shop_no }}</td>
                    <td>{{ $numto->bnCommaLakh($record->previousDue->is_paid ? 0 : $record->previousDue->amount)}}</td>
                    <td>{{ $numto->bnCommaLakh($record->grand_total) }}</td>
                    
                </tr>
            @else 
                <tr class="text-center" style="font-size: 14px">
                    <td>{{ $record->customer->name }}</td>
                    <td>{{ $record->customer->shop_no }}</td>
                    <td>{{ en2bn($record->bill_month_name) }}</td>
                    <td>{{ en2bn($record->billing_year) }}</td>
                    <td>{{ en2bn($record->consumed_units) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->grand_total) }}</td>
                </tr>
            @endif
            
        @empty
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        @endforelse
        @if ($type==='short')
            <tr>
                <td colspan="3" class="text-end">মোট আদায়</td>
                <td class="text-center">{{ $numto->bnCommaLakh($total) }}</td>
            </tr>
             <tr>
                <td colspan="1" class="text-end">মোট আদায় কথায়</td> 
                <td colspan="3" class="text-end">{{ $numto->bnMoney($total) . ' মাত্র'}}</td>
            </tr>
        
        @else
            <tr>
                <td colspan="5" class="text-end">মোট আদায়</td>
                <td class="text-center">{{ $numto->bnCommaLakh($total) }}</td>
            </tr>
             <tr>
                <td colspan="2" class="text-end">মোট আদায় কথায়</td> 
                <td colspan="4" class="text-end">{{ $numto->bnMoney($total) . ' মাত্র'}}</td>
            </tr>
        @endif
         
    </table>
@endsection
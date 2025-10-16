
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
        <h6>লেজার  রিপোর্ট</h6>
        @if ($month)
        <h6 class="text-decoration-underline">{{ $numto->bnMonth($month) .' - ' . en2bn($year) . 'ইং'}}</h6>
            
        @endif

    </div>
    <table class="table table-bordered table-striped" id="sales-table">
    <thead>
        <tr class="text-center" style="font-size: 14px">
           
                <th>গ্রাহকের নাম</th>
                <th>দোকান নং</th>
                <th>মাস</th>
                {{-- <th>বছর</th> --}}
                <th>মিটার রিডিং</th>
                <th>ইউনিট</th>
                <th>সারচার্জ</th>
                <th>মোট বিল</th>
                <th>স্ট্যাটাস</th>
           
            
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $record)
            
                <tr class="text-center" style="font-size: 14px">
                    <td>{{ $record->customer->name }}</td>
                    <td>{{ $record->customer->shop_no }}</td>
                    <td>{{ en2bn($record->bill_month_name) .'-' .en2bn($record->billing_year) }}</td>
                    {{-- <td>{{ en2bn($record->billing_year) }}</td> --}}
                    <td>{{ en2bn($record->reading->previous_reading . ' - ' . $record->reading->current_reading) }}</td>
                    <td>{{ en2bn($record->consumed_units) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->calculated_surcharge) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->grand_total) }}</td>
                    @if ($record->is_paid)
                        <td><i class="bi bi-check-circle-fill text-success fs-3"></i></td>
                    @else
                        <td><i class="bi bi-x-circle-fill text-danger fs-3"></i></td>
                    
                    @endif
                   
                </tr>
            
            
        @empty
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        @endforelse
        
    </table>
@endsection
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bill Print</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    body {
      font-family: 'Noto Sans Bengali', sans-serif;
      font-size: 10px;
      background: #fff;
    }
    p {
        margin-top: 0;
        margin-bottom: .3rem!important;
    }

    .h-tag{
         margin-bottom: .3rem!important;
    }
    .bill-wrapper {
      max-width: 800px;
      margin: 0 auto 30px auto;
      border: 1px solid #000;
      padding: 0;
      page-break-after: always;
      background: #fff;
    }

    .bill-container {
      padding: 15px 20px 15px 20px;
      border-bottom: 1px dashed #000; /* dashed separator between copies */
    }

    .bill-container:last-child {
      border-bottom: none;
    }

    .bill-table th, .bill-table td {
      border: 1px solid #000 !important;
      vertical-align: middle;
      text-align: center;
      padding: 3px;
    }

    .signature {
      margin-top: 30px;
      text-align: right;
    }

    @media print {
      @page {
        margin: 5mm;
      }
      body {
        background: #fff;
        -webkit-print-color-adjust: exact !important;
      }
      .bill-wrapper {
        border: 1px solid #000 !important;
        page-break-inside: avoid;
      }
      .bill-container {
        border-bottom: 1px dashed #000 !important;
      }
    }
  </style>
</head>
<body>
   @php
    use Rakibhstu\Banglanumber\NumberToBangla;
    $numto = new NumberToBangla();
    function en2bn($number) {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
        
        
                
    }
    
    @endphp
<!-- ======= One Full Bill (Customer Copy + Office Copy) ======= -->
{{-- <p>strong>মোট বিল কপি সংখ্যা: {{ $records }}</strong></p> --}}
@foreach ($records as $record)
    @php
        // $unpaidTotal=0;
        $flatNumbers = '';

        foreach ($record->waterCustomer->flats->where('is_occupied',true) as $flat) {
            $flatNumbers .= $flat->flat_number . ', ';
        }
        $unpaidBills = $record->waterCustomer->unpaidWaterBills->where('water_bill_month', '<', $record->water_bill_month)
                                                        ->where('water_bill_year', '<=', $record->water_bill_year);
    @endphp
    <div class="bill-wrapper">

    <!-- === Customer Copy === -->
    <div class="bill-container">
        <div class="text-center">
            <img src="images/logo.png" height="60px">
            <h5 class="h-tag"><strong>ঢাকা ক্যান্টনমেন্ট বোর্ড</strong></h5>
            <h6 class="h-tag"><strong>পানির বিল</strong></h6>
            <p><strong>(গ্রাহক কপি)</strong></p>
        </div>
        <div class="row mt-0">
            <div class="col-6">
                <p><strong>গ্রাহক নাম: </strong>{{ $record->waterCustomer->customer_name }}</p>
                <p><strong>মোবাইল: </strong>{{ en2bn($record->waterCustomer->customer_phone) }}</p>
                <p><strong>ঠিকানা: </strong>{{ $record->waterCustomer->customer_address }}</p>
                <p><strong>হোল্ডিং নাম্বার: </strong>{{ en2bn($record->waterCustomer->holding_number) }}</p>
            </div>
            <div class="col-6 d-flex flex-column align-items-end justify-content-end text-end">
                {{-- <p><strong>বিল নম্বর: </strong>{{ en2bn($record->bill_number) }}</p> --}}
                <p><strong>বিল মাস: </strong>{{ en2bn(date('F', mktime(0, 0, 0, $record->water_bill_month, 1)) . '-'. $record->water_bill_year) }}</p>
                <p><strong>বিল প্রস্তুতের তারিখ: </strong>{{ en2bn(date('d F, Y', strtotime($record->bill_creation_date))) }}</p>
                <p><strong>বিল শেষের তারিখ: </strong>{{ en2bn(date('d F, Y', strtotime($record->bill_due_date))) }}</p>
            </div>
        </div>
        <table class="table table-bordered bill-table mb-0">
            <thead>
                <tr>
                    <th>বিলের বিবরণ</th>
                    <th>ফ্ল্যাট নাম্বার</th>
                    <th>ফ্ল্যাট সংখ্যা</th>
                    <th>প্রতি এককের মূল্য</th>
                    <th>টাকার পরিমান</th>
                </tr>

            </thead>
            <tbody>
                <tr>
                    <td>ফ্ল্যাটের বিবরণ</td>
                    <td>{{ en2bn($record->flat_numbers) }}</td>
                    <td>{{ en2bn($record->total_flats) }}</td>
                    <td>{{ $numto->bnCommaLakh($record->waterCustomer->type=='flat' ?
                                 $setting->monthly_rate : $setting->monthly_const_rate) }}</td>
                    <td>{{  $numto->bnCommaLakh($record->total_amount) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-start"><strong>বর্তমান মাসের বিল</strong></td>
                    <td><strong>{{  $numto->bnCommaLakh($record->total_amount) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-start"><strong>বিলম্ব মাসুল ৫%</strong></td>
                    <td><strong>{{  $numto->bnCommaLakh(round($record->total_amount * ($record->surcharge_percent / 100))) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-start"><strong>বিলম্ব মাসুল সহ মোট বিল</strong></td>
                    <td><strong>{{ $numto->bnCommaLakh($record->total_amount + round($record->total_amount * ($record->surcharge_percent / 100))) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center"><strong>বকেয়া বিল</strong></td>
                   
                </tr>
                <tr>
                   
                    <td colspan="4" class="text-center">
                        <div class="row">
                            @php
                                $total_unpaid=0;
                            @endphp
                            @foreach ($unpaidBills as $unpaid )
                                @php
                                    if (\Carbon\Carbon::parse($unpaid->bill_due_date)->lt(today())) {
                                        $unpaidTotal = round($unpaid->total_amount + ($unpaid->total_amount * $unpaid->surcharge_percent / 100));
                                        $total_unpaid += $unpaidTotal;
                                    } else {
                                        $unpaidTotal = $unpaid->total_amount;
                                        $total_unpaid += $unpaidTotal;
                                    }
                                @endphp
                                <div class="col">{{ en2bn(date('F', mktime(0, 0, 0, $unpaid->water_bill_month, 1)) . '-'. $unpaid->water_bill_year)}}<br>
                                {{ $numto->bnCommaLakh($unpaidTotal) .'/=' }}
                            </div>
                            @endforeach
                          
                        </div>
                    </td>
                     <td class="" rowspan=""><strong>{{  $numto->bnCommaLakh($total_unpaid)  }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-start"><strong>পূর্বের বকেয়া</strong></td>
                    <td class="" rowspan=""><strong>{{ $numto->bnCommaLakh($record->waterCustomer->previous_due) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-start"><strong>মোট বিল</strong></td>
                    <td class="" rowspan=""><strong>{{ $numto->bnCommaLakh($record->total_amount + $total_unpaid + $record->waterCustomer->previous_due) }}</strong></td>
                </tr>
            </tbody>
        </table>
        <div class="signature">
        <p class="mb-0">------------------------------</p>
        <p class="mb-0"><strong>উপ-সহকারী প্রকৌশলী</strong><br>(পানি ও বিদ্যুৎ)</p>
        </div>
    </div>
@endforeach
 
</body>
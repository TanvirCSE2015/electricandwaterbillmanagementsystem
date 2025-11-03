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
        margin-bottom: .5rem!important;
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
      padding: 20px;
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
<body onload="printReport()">
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
        $unpaidTotal=0;
    @endphp
    <div class="bill-wrapper">

    <!-- === Customer Copy === -->
    <div class="bill-container">
        <div class="text-center">
        <img src="images/logo.png" height="60px">
        <h5><strong>ঢাকা ক্যান্টনমেন্ট বোর্ড</strong></h5>
        <h6><strong>বিদ্যুৎ বিল</strong></h6>
        <p><strong>(গ্রাহক কপি)</strong></p>
        </div>

        <div class="row">
        <div class="col-6">
            <p>জনাব/জনাবা: <strong>{{ $record->customer->name }}</strong></p>
            <p>ঠিকানা: <strong>{{ $record->customer->address }}</strong></p>
        </div>
        <div class="col-6 text-end">
            <p>বাড়ি/দোকান নম্বর: <strong>{{ $record->customer->shop_no }}</strong></p>
            <p>মিটার নম্বর: <strong>{{ $record->customer->activeMeter->meter_number }}</strong></p>
        </div>
        </div>

        <table class="table table-bordered bill-table mb-0">
        <thead>
            <tr>
            <th>বিলের বিবরণ</th>
            <th>ইউনিট</th>
            <th>প্রতি এককে মূল্য</th>
            <th>টাকার পরিমান</th>
            <th>মন্তব্য</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td>বর্তমান একক</td>
            <td>{{ $numto->bnNum($record->reading->current_reading) }}</td>
            <td rowspan="5">{{ $numto->bnNum($record->billSetting->unit_price) }}</td>
            @php
                $base_amount=($record->reading->consume_unit + $record->billSetting->system_loss) * $record->billSetting->unit_price;
            @endphp
            <td rowspan="5" class="text-end">{{ $numto->bnNum($base_amount) }}</td>
            <td rowspan="6" style="font-size: 12px;"><strong>প্রস্তুতের তারিখঃ<br>{{ en2bn(date("d-m-Y", strtotime($record->bill_date))) }} ইং</strong></td>
            </tr>
            <tr>
                <td>বিগত একক</td>
                <td>{{ $numto->bnNum($record->reading->previous_reading) }}</td>
            </tr>
            <tr>
                <td>প্রাপ্ত খরচ</td>
                <td>{{ $numto->bnNum($record->reading->consume_unit) }}</td>
            </tr>
            <tr>
                <td>সিস্টেম লস</td>
                <td>{{ $numto->bnNum($record->billSetting->system_loss) }}</td>
            </tr>
            <tr>
                <td>মোট</td>
                <td>{{ $numto->bnNum($record->reading->consume_unit +$record->billSetting->system_loss) }}</td>
            </tr>
            <tr>
            <td>সার্ভিস চার্জ</td>
            <td colspan="3" class="text-end">{{ $numto->bnNum($record->billSetting->service_charge) }}</td>
            </tr>
            <tr>
            <td>ডিমান্ড চার্জ</td>
            <td colspan="3" class="text-end">{{ $numto->bnNum($record->billSetting->demand_charge) }}</td>
            <td rowspan="7" style="font-size: 12px;"><strong>পরিশোধের শেষ তারিখঃ<br>{{ en2bn(date("d-m-Y", strtotime($record->due_date))) }} ইং</strong></td>
            </tr>
            <tr>
            <td>মোট টাকা উপর ভ্যাট ৫%</td>
            
            <td colspan="3" class="text-end">{{ $numto->bnCommaLakh($record->total_amount)}}</td>
            </tr>
            <tr>
            <td colspan="4"><strong>বকেয়া বিল</strong></td>
            </tr>
            @php
                $unpaidBills = $record->customer->unpaidBills;
            @endphp
            <tr>
            <td colspan="3">
                <div class="row">
                
                    @foreach ($unpaidBills as $unpaid)
                        @php
                            $unpaidAmount=$unpaid->surcharge > 0 ? 
                            $unpaid->total_amount : $unpaid->total_amount +
                                (round($unpaid->total_amount * $unpaid->surcharge_percentage));
                            $unpaidTotal += $unpaidAmount;
                        @endphp
                        <div class="col">{{ en2bn($unpaid->bill_month_name . '-' . $unpaid->billing_year)}}<br>
                            {{ $numto->bnCommaLakh($unpaidAmount) .'/=' }}
                        </div>
                        {{-- <div class="col">জুলাই-২০২৫<br>১৫০০</div>
                        <div class="col">আগস্ট-২০২৫<br>১৮০০</div> --}}
                    @endforeach
                
                </div>
            </td>
            <td class="text-end">{{ $numto->bnCommaLakh($unpaidTotal) }}</td>
            </tr>
            <tr><td>পূর্বের বকেয়া</td><td class="text-end" colspan="3">{{ $numto->bnCommaLakh($record->customer->previousDue->amount) }}</td></tr>
            <tr>
                <td>মোট বিল</td>
                <td class="text-end" colspan="3">{{ $numto->bnCommaLakh($record->total_amount+$unpaidTotal+
                    $record->customer->previousDue->amount)}}
                </td>
            </tr>
            @php
                $current_bill_surcharge=$record->total_amount+round($record->total_amount*$record->surcharge_percentage);
            @endphp
            <tr>
                <td>বিলম্ব মাসুল ৫% সহ মোট বিল</td><td class="text-end" colspan="3">{{ $numto->bnCommaLakh($current_bill_surcharge+$unpaidTotal+
                    $record->customer->previousDue->amount)}}
                </td>
            </tr>
        </tbody>
        </table>

        <div class="signature">
        <p class="mb-0">------------------------------</p>
        <p class="mb-0"><strong>উপ-সহকারী প্রকৌশলী</strong><br>(পানি ও বিদ্যুৎ)</p>
        </div>
        <div class="text-center mt-3">
            <p class="fw-bold" style="font-size: 12px;">
                <strong>বিঃদ্রঃ দুই মাসের অধিক সময় বিল বকেয়া থাকলে বিদ্যুৎ সংযোগ বিচ্ছিন্ন করা হবে এবং এটি বিচ্ছিন্নকরণের চূড়ান্ত নোটিশ হিসাবে গণ্য হবে।</strong>
            </p>
        </div>
    </div>

    <!-- === Office Copy (part of same bill) === -->
    <div class="bill-container" style="border-bottom: none!important">
        <div class="text-center">
        <h5 class="d-inline"><strong>ঢাকা ক্যান্টনমেন্ট বোর্ড</strong></h5>
        <h6 class="d-inline"><strong> (বিদ্যুৎ বিল)</strong></h6>
        <p><strong>(অফিস কপি)</strong></p>
        </div>

        <div class="row">
        <div class="col-6">
            <p>জনাব/জনাবা: <strong>{{ $record->customer->name }}</strong></p>
            <p>ঠিকানা: <strong>{{ $record->customer->address }}</strong></p>
        </div>
        <div class="col-6 text-end">
            <p>বাড়ি/দোকান নম্বর: <strong>{{ $record->customer->shop_no }}</strong></p>
            <p>মিটার নম্বর: <strong>{{ $record->customer->activeMeter->meter_number }}</strong></p>
        </div>
        </div>

        <table class="table table-bordered bill-table">
        <thead>
            <tr>
            <th>মোট বকেয়া</th>
            <th>আদায়ের বিবরন (বকেয়া/পূর্বের বকেয়া/অন্যান্য)</th> 
            <th>আদায়ের পরিমান</th>
            <th>আদায়কারীর স্বাক্ষর</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td style="padding: 15px;">{{ $numto->bnCommaLakh($current_bill_surcharge+$unpaidTotal+
                    $record->customer->previousDue->amount) . '/='}}</td>
            <td></td>
            <td></td>
            <td></td>
            </tr>
        </tbody>
        </table>

        <div class="signature">
        <p class="mb-0">------------------------------</p>
        <p class="mb-0"><strong>উপ-সহকারী প্রকৌশলী</strong><br>(পানি ও বিদ্যুৎ)</p>
        </div>
    </div>

    </div>
@endforeach


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    function printReport() {
        var type = '{{ $type ?? '' }}';
         window.print();
            window.onafterprint = function () {
                if(type==='current'){
                    window.location.href = '/electricity/due-electric-bills';
                } else if(type==='previous'){
                 window.location.href = '/electricity/previous-dues';
                } else {
                    window.close();
                }
            };
    }
 </script>
</body>
</html>

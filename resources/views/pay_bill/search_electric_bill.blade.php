{{-- DEBUG FRIENDLY --}}
{{-- @dd($customer) --}}

<div class="card-body">
        <h3 class="h4 text-center mb-4">বিল বিবরণ</h3>
        <h5 class="mb-3">দোকান তথ্য</h5>
        <p>
            <strong>দোকান নং:</strong> {{ $customer->shop_no }} <br>
            <strong>নাম:</strong> {{ $customer->name }} <br>
            <strong>এলাকা:</strong> {{ $customer->electricArea->name ?? '' }} <br>
            <strong>ব্লক:</strong> {{ $customer->block->bolck_name ?? '' }} <br>
            <strong>মিটার:</strong> {{ $customer->activeMeter->meter_number ?? '' }}
        </p>

        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>মাস</th>
                        <th>পূর্বের রিডিং</th>
                        <th>বর্তমান রিডিং</th>
                        <th>ব্যবহৃত ইউনিট</th>
                        <th>সারচার্জ</th>
                        <th>মোট বিল</th>
                        <th>স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                    $total_bill = 0;
                    @endphp
                    @foreach ($customer->bills as $bill)
                        <tr>
                            <td>{{ \App\Helpers\ElectricBillHelper::en2bn($bill->bill_month_name) }} {{ \App\Helpers\ElectricBillHelper::en2bn($bill->billing_year) }}</td>
                            <td>{{ \App\Helpers\ElectricBillHelper::en2bn($bill->reading->previous_reading) ?? 0 }}</td>
                            <td>{{ \App\Helpers\ElectricBillHelper::en2bn($bill->reading->current_reading) ?? 0 }}</td>
                            <td>{{ \App\Helpers\ElectricBillHelper::en2bn($bill->consumed_units) }}</td>
                            <td>
                                @php
                                $surcharge = \App\Helpers\ElectricBillHelper::calculateSurcharge($bill);
                                @endphp
                            {{ \App\Helpers\ElectricBillHelper::en2bn($surcharge) }}
                            </td>
                            <td>@php
                                $total_amount = 0;
                                if($bill->surcharge == 0){
                                    $surcharge = \App\Helpers\ElectricBillHelper::calculateSurcharge($bill);
                                    $total_amount = round($bill->total_amount + $surcharge, 2);
                                }else{
                                    $total_amount = $bill->total_amount;
                                }
                                @endphp
                                {{ \App\Helpers\ElectricBillHelper::en2bn(number_format($total_amount,2)) }}
                            </td>
                            <td>
                                {!! $bill->is_paid
                                    ? '<span class="badge bg-success">Paid</span>'
                                    : '<span class="badge bg-danger">Unpaid</span>' !!}
                            </td>
                        </tr>
                        @php
                        $total_bill += $total_amount;
                        @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-light rounded p-2 text-center my-2">
            <p class="text-danger mb-2">পূর্বের বকেয়া </p>
            @php
            $previousDue = DB::table('previous_dues')->where(['customer_id' => $bill->customer_id,'is_paid' =>false])->first();
            @endphp
            <h2 class="amount-display text-danger fw-bold mb-0">৳ <span id="totalAmount">{{ \App\Helpers\ElectricBillHelper::en2bn(number_format($previousDue?->amount,2)) }}</span></h2>
        </div>
        <div class="bg-light rounded p-2 text-center my-2">
            <p class="text-muted mb-2">মোট বকেয়া</p>
            <h2 class="amount-display text-primary fw-bold mb-0">৳ <span id="totalAmount">{{ \App\Helpers\ElectricBillHelper::en2bn(number_format($total_bill+$previousDue?->amount,2)) }}</span></h2>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-grid">
                    <button class="btn btn-success btn-lg" onclick="payBill()">পেমেন্ট করুন</button>
                </div>
            </div>
        </div>
    </div>

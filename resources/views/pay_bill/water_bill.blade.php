@extends('layouts.gateway_layout')
@section('main_content')


    <div class="container py-1">
        <a href="#" class="btn btn-secondary mb-3" onclick="goBack()">← ফিরে যান</a>
        
        <!-- Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo-centered" style="width:100px;">
                <h1 class="h2 fw-bold mb-2">ঢাকা ক্যান্টনমেন্ট বোর্ড</h1>
                <p class="text-muted fs-5 mb-0 fw-bold" id="pageTitle">পানি বিল পেমেন্ট</p>
            </div>
        </div>
        
        <!-- Search Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h3 class="h4 mb-0">🔍 দোকান আইডি দিয়ে খুঁজুন</h3>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" id="shopId" placeholder="দোকান আইডি লিখুন (যেমন: 12345)" />
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" onclick="searchBill()">খুঁজুন</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- No Bill Alert -->
        <div class="alert alert-danger text-center d-none" role="alert" id="noBill">
            <strong>⚠️ দুঃখিত!</strong> এই দোকান আইডির জন্য কোন বিল পাওয়া যায়নি।
        </div>
        
        <!-- Bill Info Card -->
        <div class="card shadow-sm d-none" id="billInfo">
            <div class="card-body p-4">
                <h3 class="h4 text-center mb-4">বিল বিবরণ</h3>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">দোকান আইডি:</td>
                                <td class="text-end fw-medium" id="displayShopId">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">দোকানের নাম:</td>
                                <td class="text-end fw-medium" id="shopName">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">মালিকের নাম:</td>
                                <td class="text-end fw-medium" id="ownerName">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">ঠিকানা:</td>
                                <td class="text-end fw-medium" id="address">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">বিলের মাস:</td>
                                <td class="text-end fw-medium" id="billMonth">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">মিটার নম্বর:</td>
                                <td class="text-end fw-medium" id="meterNumber">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">বর্তমান রিডিং:</td>
                                <td class="text-end fw-medium" id="currentReading">-</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-muted">পূর্ববর্তী রিডিং:</td>
                                <td class="text-end fw-medium" id="previousReading">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">ব্যবহৃত ইউনিট:</td>
                                <td class="text-end fw-medium" id="unitsUsed">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-light rounded p-4 text-center my-4">
                    <p class="text-muted mb-2">মোট বিল</p>
                    <h2 class="amount-display text-primary fw-bold mb-0">৳ <span id="totalAmount">0</span></h2>
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-success btn-lg" onclick="payBill()">পেমেন্ট করুন</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        
    </script>
@endsection
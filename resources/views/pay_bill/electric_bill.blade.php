@extends('layouts.gateway_layout')
@section('main_content')


    <div class="container py-1">
        <a href="#" class="btn btn-secondary mb-3" onclick="goBack()">← ফিরে যান</a>
        
        <!-- Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo-centered" style="width:100px;">
                <h1 class="h2 fw-bold mb-2">ঢাকা ক্যান্টনমেন্ট বোর্ড</h1>
                <p class="text-muted fs-5 mb-0 fw-bold" id="pageTitle">বিদ্যুৎ বিল পেমেন্ট</p>
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
                            <input type="text" class="form-control form-control-lg" id="shop_no" placeholder="দোকান আইডি লিখুন" />
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" id="search-bill">খুঁজুন</button>
                        </div>
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center my-4 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">লোড হচ্ছে...</p>
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
        <div class="card shadow-sm mt-4" id="billInfo">
            
        </div>
    </div>
    
    <script>
        document.getElementById('search-bill').addEventListener('click', function () {
            let shopInput = document.getElementById('shop_no');
            let shop_no = shopInput.value.trim();

            if (!shop_no) {
                alert('দোকান আইডি লিখুন');
                shopInput.focus();
                return;
            }

            shop_no = encodeURIComponent(shop_no);

            const spinner = document.getElementById('loadingSpinner');
            const billInfo = document.getElementById('billInfo');

            // Reset UI
            billInfo.innerHTML = '';
            billInfo.classList.add('d-none');

            // Show spinner
            spinner.classList.remove('d-none');

            fetch(`{{ route('electric.bill.search') }}?shop_no=${shop_no}`)
                .then(res => res.json())
                .then(res => {
                    spinner.classList.add('d-none'); // ❌ hide spinner

                    if (!res.status) {
                        alert(res.message);
                        return;
                    }

                    // ✅ Inject HTML
                    billInfo.innerHTML = res.html;
                    billInfo.classList.remove('d-none');

                    // 🎯 Smooth scroll to bill info
                    billInfo.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                })
                .catch(err => {
                    spinner.classList.add('d-none');
                    console.error(err);
                    alert('সার্ভার সমস্যা হয়েছে');
                });
        });

    </script>
@endsection
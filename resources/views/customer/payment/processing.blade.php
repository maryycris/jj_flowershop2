@extends('layouts.customer_app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow text-center">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <h4 class="mb-3">Processing your payment...</h4>
                    <p class="text-muted">Please wait while we process your payment. Do not close or refresh this page.</p>
                    <form id="processingForm" action="{{ route('customer.payment.process', $order->id) }}" method="POST">
                        @csrf
                        @foreach($paymentData as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(function() {
        document.getElementById('processingForm').submit();
    }, 2000); // 2 seconds delay
</script>
@endsection 
@extends('layouts.customer_app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('customer.checkout.index') }}" class="btn btn-sm btn-link text-decoration-none text-dark">
            <i class="fas fa-arrow-left me-2"></i>Back to Checkout
        </a>
        <h4 class="mb-0 ms-2">GCash Payment</h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-mobile-alt me-2"></i>
                        <h5 class="mb-0">GCash Payment Gateway</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-mobile-alt text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5>Order #{{ $order->id }}</h5>
                        <p class="text-muted">Total Amount: ₱{{ number_format($order->total_price, 2) }}</p>
                    </div>

                    <div class="alert" style="background-color: #e8f5e8; border-color: #7bb47b; color: #2d5a2d;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> This is a simulation of the GCash payment gateway. 
                        In a real implementation, you would be redirected to the actual GCash payment page.
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.payment.processing', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="gcash_number" class="form-label">GCash Number</label>
                            <input type="text" class="form-control" id="gcash_number" name="gcash_number" 
                                   placeholder="09XX XXX XXXX" required maxlength="11" minlength="11" pattern="09[0-9]{9}" inputmode="numeric" value="{{ old('gcash_number') }}">
                            <div class="form-text">Enter the GCash number you want to pay from</div>
                        </div>

                        <div class="mb-3">
                            <label for="payment_pin" class="form-label">Payment PIN</label>
                            <input type="password" class="form-control" id="payment_pin" name="payment_pin" 
                                   placeholder="Enter your GCash PIN" required maxlength="4" minlength="4" pattern="[0-9]{4}" inputmode="numeric" value="{{ old('payment_pin') }}">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="payment_status" value="success" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Pay ₱{{ number_format($order->total_price, 2) }}
                            </button>
                            
                            <button type="submit" name="payment_status" value="failed" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>Simulate Payment Failure
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <div class="d-flex align-items-center text-muted">
                            <i class="fas fa-shield-alt me-2"></i>
                            <small>Your payment information is secure and encrypted</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush
@endsection 
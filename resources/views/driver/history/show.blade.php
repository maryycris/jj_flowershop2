@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('driver.history.index') }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">Completed Delivery #{{ $delivery->id }}</h4>
</div>

<div class="card shadow-sm mb-3 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Delivery Completed</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Completed Date:</small><br>
                <strong>{{ $delivery->updated_at->format('M d, Y') }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Completed Time:</small><br>
                <strong>{{ $delivery->updated_at->format('g:i A') }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Order ID:</small><br>
            <strong>#{{ $delivery->order->id }}</strong>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Customer Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Name:</small><br>
                <strong>{{ $delivery->order->user->name }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Contact:</small><br>
                <strong>{{ $delivery->order->user->contact_number }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Email:</small><br>
            <strong>{{ $delivery->order->user->email }}</strong>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Delivery Details</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Scheduled Date:</small><br>
                <strong>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Scheduled Time:</small><br>
                <strong>{{ $delivery->delivery_time ?? 'Not specified' }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Delivery Address:</small><br>
            <strong>{{ $delivery->delivery_address ?? 'Address not specified' }}</strong>
        </div>
        
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Recipient Name:</small><br>
                <strong>{{ $delivery->recipient_name ?? 'Same as customer' }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Recipient Phone:</small><br>
                <strong>{{ $delivery->recipient_phone ?? 'Same as customer' }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Delivered Items</h5>
    </div>
    <div class="card-body">
        @if($delivery->order->products)
            @foreach($delivery->order->products as $product)
            <div class="d-flex align-items-center mb-2">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" 
                     alt="{{ $product->name }}" 
                     class="rounded me-2" 
                     style="width: 50px; height: 50px; object-fit: cover;">
                <div class="flex-fill">
                    <strong>{{ $product->name }}</strong><br>
                    <small class="text-muted">Quantity: {{ $product->pivot->quantity ?? 1 }}</small>
                </div>
                <div class="text-end">
                    <strong>₱{{ number_format($product->price, 2) }}</strong>
                </div>
            </div>
            @endforeach
        @else
            <p class="text-muted mb-0">No product details available</p>
        @endif
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-6">
                <small class="text-muted">Order Date:</small><br>
                <strong>{{ $delivery->order->created_at->format('M d, Y g:i A') }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Order Status:</small><br>
                <span class="badge bg-{{ $delivery->order->status === 'completed' ? 'success' : ($delivery->order->status === 'processing' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($delivery->order->status) }}
                </span>
            </div>
        </div>
        
        <div class="mb-2">
            <small class="text-muted">Order Type:</small><br>
            <strong>{{ ucfirst($delivery->order->type ?? 'Standard') }}</strong>
        </div>
        
        <div class="mb-0">
            <small class="text-muted">Total Amount:</small><br>
            <strong class="text-success fs-5">₱{{ number_format($delivery->order->total_amount, 2) }}</strong>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('driver.history.index') }}" class="btn btn-outline-secondary flex-fill">
        <i class="bi bi-arrow-left me-1"></i>Back to History
    </a>
    <button class="btn btn-outline-primary" onclick="printDelivery()">
        <i class="bi bi-printer me-1"></i>Print
    </button>
</div>

<script>
function printDelivery() {
    window.print();
}
</script>

<style>
@media print {
    .btn, nav, .driver-menu { display: none !important; }
    .card { border: 1px solid #000 !important; }
}
</style>
@endsection 
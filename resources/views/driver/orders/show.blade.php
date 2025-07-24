@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('driver.orders.index') }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">Order #{{ $delivery->order->id }}</h4>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Order Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
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
        
        <div class="mb-3">
            <small class="text-muted">Order Type:</small><br>
            <strong>{{ ucfirst($delivery->order->type ?? 'Standard') }}</strong>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Total Amount:</small><br>
            <strong class="text-success">₱{{ number_format($delivery->order->total_amount, 2) }}</strong>
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
        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Delivery Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Delivery Date:</small><br>
                <strong>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Delivery Time:</small><br>
                <strong>{{ $delivery->delivery_time ?? 'Not specified' }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Delivery Address:</small><br>
            <strong>{{ $delivery->delivery_address ?? 'Address not specified' }}</strong>
        </div>
        @if($delivery->order && $delivery->order->address)
            @if($delivery->order->address->landmark)
                <div class="mb-2">
                    <strong>Landmark:</strong> {{ $delivery->order->address->landmark }}
                </div>
            @endif
            @if($delivery->order->address->special_instructions)
                <div class="mb-2">
                    <strong>Special Instructions:</strong> {{ $delivery->order->address->special_instructions }}
                </div>
            @endif
        @endif
        
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
        
        <div class="mb-3">
            <small class="text-muted">Current Status:</small><br>
            <span class="badge bg-{{ $delivery->status === 'completed' ? 'success' : ($delivery->status === 'in_progress' ? 'warning' : 'secondary') }} fs-6">
                {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
            </span>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Order Items</h5>
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

@if($delivery->status !== 'completed')
<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Update Status</h5>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2">
            @if($delivery->status === 'pending')
            <button class="btn btn-warning flex-fill" onclick="updateStatus('in_progress')">
                <i class="bi bi-play me-1"></i>Start Delivery
            </button>
            @elseif($delivery->status === 'in_progress')
            <button class="btn btn-success flex-fill" onclick="updateStatus('completed')">
                <i class="bi bi-check-circle me-1"></i>Mark as Completed
            </button>
            @endif
        </div>
    </div>
</div>
@endif

<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to update this delivery status?')) {
        fetch(`/driver/deliveries/{{ $delivery->id }}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error updating status');
        });
    }
}
</script>
@endsection 
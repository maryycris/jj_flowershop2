@extends('layouts.admin_app')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Order Details #{{ $order->id }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Status: <strong>{{ ucfirst($order->status) }}</strong></span>
            <span>Date: <strong>{{ $order->created_at->format('M d, Y') }}</strong></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Customer Details:</h5>
                    <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                    <p><strong>Contact:</strong> {{ $order->user->contact_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Delivery Information:</h5>
                    <p><strong>Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                    <p><strong>Date:</strong> {{ $order->delivery ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Receipt / Order Slip / Invoice Options -->
    @php
        $isCOD = strtoupper($order->payment_method) === 'COD';
        $isOnDelivery = $order->order_status === 'on_delivery' || $order->order_status === 'completed';
    @endphp
    
    @if($isOnDelivery)
        <!-- Invoice Options - Only show when order is on delivery or completed -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Invoice Options</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Invoice
                </a>
                <a href="{{ route('admin.orders.invoice.download', $order->id) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    @elseif($isCOD)
        <!-- Order Slip - Show when COD payment method -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Order Slip</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Order Slip
                </a>
                <a href="{{ route('admin.orders.invoice.download', $order->id) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Download Order Slip
                </a>
            </div>
        </div>
    @else
        <!-- Payment Receipt - Show for other payment methods -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Payment Receipt</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Receipt
                </a>
                <a href="{{ route('admin.orders.invoice.download', $order->id) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Download Receipt
                </a>
            </div>
        </div>
    @endif
    <!-- Payment Proof Section -->
    @php
        $latestProof = $order->paymentProofs()->latest()->first();
    @endphp
    @if($latestProof)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Payment Proof</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Reference Number:</strong> {{ $latestProof->reference_number ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>Payment Method:</strong> {{ strtoupper($latestProof->payment_method) }}
            </div>
            <div class="mb-3">
                <strong>Status:</strong> <span class="badge bg-{{ $latestProof->status === 'approved' ? 'success' : ($latestProof->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($latestProof->status) }}</span>
            </div>
            <div class="mb-3">
                <strong>Screenshot/Receipt:</strong><br>
                <img src="{{ asset('storage/' . $latestProof->image_path) }}" alt="Payment Proof" class="img-fluid rounded" style="max-width: 300px; max-height: 300px;">
            </div>
        </div>
    </div>
    @endif
    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Products</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Payment Method:</strong>
                @if($order->payment_method === 'gcash')
                    GCASH (Processed via PAY MONGGO)
                @elseif($order->payment_method === 'paymaya')
                    PAYMAYA (Processed via PAY MONGGO)
                @elseif($order->payment_method === 'cod')
                    Cash on Delivery (COD)
                @else
                    {{ strtoupper($order->payment_method ?? 'N/A') }}
                @endif
            </div>
            <ul class="list-group list-group-flush">
                @foreach($order->products as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $product->name }}</strong>
                            <small class="d-block text-muted">SKU: {{ $product->code ?? 'N/A' }}</small>
                        </div>
                        <div>
                            <span>{{ $product->pivot->quantity }} x ₱{{ number_format($product->price, 2) }}</span>
                            <strong class="ms-3">₱{{ number_format($product->price * $product->pivot->quantity, 2) }}</strong>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-footer text-end">
            <h4>Total: ₱{{ number_format($order->total_price, 2) }}</h4>
        </div>
    </div>
</div>
@endsection 
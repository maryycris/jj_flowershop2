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
                    @if($order->delivery && $order->delivery->driver_decision)
                        <p><strong>Driver Decision:</strong> 
                            <span class="badge bg-{{ $order->delivery->driver_decision === 'accepted' ? 'success' : 'danger' }}">
                                {{ ucfirst($order->delivery->driver_decision) }}
                            </span>
                            @if($order->delivery->driver_decision === 'declined' && $order->delivery->decline_reason)
                                <br><small class="text-muted">Reason: {{ $order->delivery->decline_reason }}</small>
                            @endif
                        </p>
                    @endif
                    @if($order->delivery && $order->delivery->proof_of_delivery_image)
                        <p><strong>Proof of Delivery:</strong> 
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#proofOfDeliveryModal">
                                <i class="fas fa-camera me-1"></i>View Photo
                            </button>
                            <br><small class="text-muted">Delivered on: {{ $order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A' }}</small>
                        </p>
                    @endif
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

<!-- Proof of Delivery Modal -->
@if($order->delivery && $order->delivery->proof_of_delivery_image)
<div class="modal fade" id="proofOfDeliveryModal" tabindex="-1" aria-labelledby="proofOfDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfDeliveryModalLabel">
                    <i class="fas fa-camera me-2"></i>Proof of Delivery - Order #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $order->delivery->proof_of_delivery_image) }}" 
                         alt="Proof of Delivery" 
                         class="img-fluid rounded shadow" 
                         style="max-height: 500px; border: 2px solid #dee2e6;">
                </div>
                <div class="row text-start">
                    <div class="col-md-6">
                        <p><strong>Order #:</strong> {{ $order->id }}</p>
                        <p><strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><strong>Delivered on:</strong> {{ $order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Confirmed by:</strong> {{ $order->delivery->driver->name ?? 'Delivery Driver' }}</p>
                        <p><strong>Delivery Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Delivered</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ asset('storage/' . $order->delivery->proof_of_delivery_image) }}" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Download Photo
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection 
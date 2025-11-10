@extends('layouts.clerk_app')

@section('title', 'Order Details')

@push('styles')
<style>
/* Order Details Styling - matching invoice page hierarchy */
.card-title {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.card-header h5 {
    font-size: 0.95rem !important;
    font-weight: 600;
}

.card-body p {
    font-size: 0.85rem;
}

.card-body strong {
    font-size: 0.85rem;
    font-weight: 600;
}

/* Table styling */
.table {
    font-size: 0.85rem;
}

.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    background-color: #e6f4ea;
}

.table tbody td {
    font-size: 0.85rem;
}

/* Section headers */
h6 {
    font-size: 0.9rem !important;
    font-weight: 600;
}

/* Form labels */
.form-label {
    font-size: 0.85rem;
    font-weight: 600;
}

.form-control {
    font-size: 0.85rem;
}

/* Button sizing */
.btn {
    font-size: 0.85rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid" style="margin-top: 1rem; padding-top: 0.5rem;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">Order Details - #{{ $order->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Order Number:</strong> #{{ $order->id }}</p>
                                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                                            <p><strong>Status:</strong> 
                                                @if($order->order_status)
                                                    @if($order->order_status === 'completed')
                                                        <span class="badge" style="background-color: #28a745; color: white;">Completed</span>
                                                    @elseif($order->order_status === 'on_delivery')
                                                        <span class="badge" style="background-color: #4caf50; color: white;">On Delivery</span>
                                                    @elseif($order->order_status === 'approved')
                                                        <span class="badge" style="background-color: #90ee90; color: black;">Approved</span>
                                                    @else
                                                        <span class="badge" style="background-color: #ffc107; color: black;">{{ ucfirst($order->order_status) }}</span>
                                                    @endif
                                                @else
                                                    <span class="badge" style="background-color: #ffc107; color: black;">{{ ucfirst($order->status ?? 'Pending') }}</span>
                                                @endif
                                            </p>
                                            <p><strong>Payment Method:</strong>
                                                @if($order->payment_method === 'gcash')
                                                    <span class="badge" style="background-color: #4caf50; color: white;">GCASH</span>
                                                @elseif($order->payment_method === 'paymaya')
                                                    <span class="badge" style="background-color: #4caf50; color: white;">PAYMAYA</span>
                                                @elseif($order->payment_method === 'cod')
                                                    <span class="badge" style="background-color: #66bb6a; color: white;">COD</span>
                                                @else
                                                    <span class="badge" style="background-color: #66bb6a; color: white;">{{ strtoupper($order->payment_method ?? 'N/A') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                                            <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                                            <p><strong>Contact:</strong> {{ $order->user->contact_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Products -->
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Products</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->products as $product)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($product->image)
                                                                <img src="{{ asset('storage/' . $product->image) }}" 
                                                                     alt="{{ $product->name }}" 
                                                                     class="img-thumbnail" 
                                                                     style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                            @endif
                                                            <div>
                                                                <strong>{{ $product->name }}</strong>
                                                                @if($product->code)
                                                                    <br><small class="text-muted">SKU: {{ $product->code }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $product->pivot->quantity }}</td>
                                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                                    <td>₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                @foreach($order->customBouquets ?? [] as $bouquet)
                                                @php
                                                    $orderQty = $bouquet->pivot->quantity ?? 1;
                                                    $customData = $bouquet->customization_data ?? [];
                                                    $freshFlowerQty = $customData['freshFlowerQuantity'] ?? 1;
                                                    $artificialFlowerQty = $customData['artificialFlowerQuantity'] ?? 1;
                                                    
                                                    $components = [];
                                                    
                                                    if ($bouquet->wrapper) {
                                                        $components[] = "Wrapper: {$bouquet->wrapper} (x{$orderQty})";
                                                    }
                                                    
                                                    $freshFlowers = [];
                                                    if ($bouquet->focal_flower_1) {
                                                        $freshFlowers[] = $bouquet->focal_flower_1;
                                                    }
                                                    if ($bouquet->focal_flower_2) {
                                                        $freshFlowers[] = $bouquet->focal_flower_2;
                                                    }
                                                    if ($bouquet->focal_flower_3) {
                                                        $freshFlowers[] = $bouquet->focal_flower_3;
                                                    }
                                                    if (!empty($freshFlowers)) {
                                                        $totalFreshQty = $freshFlowerQty * $orderQty;
                                                        $components[] = "Fresh Flowers: " . implode(', ', $freshFlowers) . " (x{$totalFreshQty})";
                                                    }
                                                    
                                                    if ($bouquet->greenery) {
                                                        $components[] = "Greenery: {$bouquet->greenery} (x{$orderQty})";
                                                    }
                                                    
                                                    if ($bouquet->filler) {
                                                        $totalArtificialQty = $artificialFlowerQty * $orderQty;
                                                        $components[] = "Artificial Flowers: {$bouquet->filler} (x{$totalArtificialQty})";
                                                    }
                                                    
                                                    if ($bouquet->ribbon) {
                                                        $components[] = "Ribbon: {$bouquet->ribbon} (x{$orderQty})";
                                                    }
                                                    
                                                    $componentDescription = !empty($components) ? implode('<br>', $components) : '';
                                                    $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($orderQty, 1));
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>Custom Bouquet</strong>
                                                            @if(!empty($componentDescription))
                                                                <div style="font-size: 0.8rem; color: #666; margin-top: 4px; line-height: 1.6;">
                                                                    {!! $componentDescription !!}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $orderQty }}</td>
                                                    <td>₱{{ number_format($unitPrice, 2) }}</td>
                                                    <td>₱{{ number_format($unitPrice * $orderQty, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                    <td><strong class="text-success">₱{{ number_format($order->total_price, 2) }}</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Information -->
                            @if($order->delivery)
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Delivery Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Delivery Date:</strong> {{ $order->delivery->delivery_date ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') : 'N/A' }}</p>
                                            <p><strong>Delivery Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                                            @if($order->delivery->driver_decision)
                                                <p><strong>Driver Decision:</strong> 
                                                    <span class="badge" style="background-color: {{ $order->delivery->driver_decision === 'accepted' ? '#28a745' : '#dc3545' }}; color: white;">
                                                        {{ ucfirst($order->delivery->driver_decision) }}
                                                    </span>
                                                    @if($order->delivery->driver_decision === 'declined' && $order->delivery->decline_reason)
                                                        <br><small class="text-muted">Reason: {{ $order->delivery->decline_reason }}</small>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            @if($order->delivery->proof_of_delivery_image)
                                                <p><strong>Proof of Delivery:</strong>
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#proofOfDeliveryModal" style="font-size: 0.75rem; padding: 0.2rem 0.4rem;">
                                                        <i class="fas fa-camera"></i> View Photo
                                                    </button>
                                                </p>
                                                <p><strong>Delivered on:</strong> 
                                                    <small class="text-muted">
                                                        {{ $order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A' }}
                                                    </small>
                                                </p>
                                            @else
                                                <p><strong>Proof of Delivery:</strong> <span class="text-muted">Not available</span></p>
                                            @endif
                                            @if($order->delivery->driver)
                                                <p><strong>Driver:</strong> {{ $order->delivery->driver->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Payment Proof -->
                            @php
                                $latestProof = $order->paymentProofs()->latest()->first();
                            @endphp
                            @if($latestProof)
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Payment Proof</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference Number:</strong> {{ $latestProof->reference_number ?? 'N/A' }}</p>
                                            <p><strong>Payment Method:</strong> 
                                                <span class="badge" style="background-color: #4caf50; color: white;">{{ strtoupper($latestProof->payment_method) }}</span>
                                            </p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge" style="background-color: {{ $latestProof->status === 'approved' ? '#28a745' : ($latestProof->status === 'pending' ? '#ffc107' : '#dc3545') }}; color: {{ $latestProof->status === 'pending' ? 'black' : 'white' }};">
                                                    {{ ucfirst($latestProof->status) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($latestProof->image_path)
                                                <p><strong>Screenshot/Receipt:</strong></p>
                                                <img src="{{ asset('storage/' . $latestProof->image_path) }}" alt="Payment Proof" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Sidebar Actions -->
                        <div class="col-md-4">
                            <!-- Invoice/Order Slip Options -->
                            @php
                                $isCOD = strtoupper($order->payment_method) === 'COD';
                                $isOnDelivery = $order->order_status === 'on_delivery' || $order->order_status === 'completed';
                            @endphp
                            
                            @if($isOnDelivery || $isCOD)
                            <div class="card">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>@if($isOnDelivery) Invoice Options @else Order Slip @endif</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('clerk.orders.invoice.view', $order->id) }}" class="btn btn-primary btn-sm mb-2 w-100" target="_blank">
                                        <i class="fas fa-eye"></i> View {{ $isOnDelivery ? 'Invoice' : 'Order Slip' }}
                                    </a>
                                    <a href="{{ route('clerk.orders.invoice.download', $order->id) }}" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-download"></i> Download {{ $isOnDelivery ? 'PDF' : 'Order Slip' }}
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="card">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Payment Receipt</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('clerk.orders.invoice.view', $order->id) }}" class="btn btn-primary btn-sm mb-2 w-100" target="_blank">
                                        <i class="fas fa-eye"></i> View Receipt
                                    </a>
                                    <a href="{{ route('clerk.orders.invoice.download', $order->id) }}" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-download"></i> Download Receipt
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Proof of Delivery Modal -->
@if($order->delivery && $order->delivery->proof_of_delivery_image)
<div class="modal fade" id="proofOfDeliveryModal" tabindex="-1" aria-labelledby="proofOfDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfDeliveryModalLabel" style="font-size: 0.95rem;">
                    <i class="fas fa-camera me-2"></i>Proof of Delivery - Order #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding: 1rem;">
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $order->delivery->proof_of_delivery_image) }}" 
                         alt="Proof of Delivery" 
                         class="img-fluid rounded shadow" 
                         style="max-height: 300px; max-width: 100%; border: 2px solid #dee2e6;">
                </div>
                <div class="row text-start" style="font-size: 0.85rem; margin-top: 0.5rem;">
                    <div class="col-md-6">
                        <p style="margin-bottom: 0.25rem;"><strong>Order #:</strong> {{ $order->id }}</p>
                        <p style="margin-bottom: 0.25rem;"><strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p style="margin-bottom: 0.25rem;"><strong>Delivered on:</strong> {{ $order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin-bottom: 0.25rem;"><strong>Confirmed by:</strong> {{ $order->delivery->driver->name ?? 'Delivery Driver' }}</p>
                        <p style="margin-bottom: 0.25rem;"><strong>Delivery Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                        <p style="margin-bottom: 0.25rem;"><strong>Status:</strong> <span class="badge bg-success" style="font-size: 0.75rem;">Delivered</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

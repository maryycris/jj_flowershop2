@extends('layouts.clerk_app')

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
    
    <!-- Recipient Details Validation Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-user-check me-2"></i>Recipient Details Validation
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('clerk.orders.validate-recipient', $order->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Recipient Name</label>
                            <input type="text" class="form-control" name="recipient_name" value="{{ $order->delivery->recipient_name ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Recipient Phone</label>
                            <input type="text" class="form-control" name="recipient_phone" value="{{ $order->delivery->recipient_phone ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Relationship to Recipient</label>
                            <input type="text" class="form-control" value="{{ ucfirst($order->delivery->recipient_relationship ?? 'Not specified') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Delivery Address</label>
                            <textarea class="form-control" name="delivery_address" rows="3" required>{{ $order->delivery->delivery_address ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Special Instructions</label>
                            <textarea class="form-control" name="special_instructions" rows="2" placeholder="Any special delivery instructions...">{{ $order->delivery->special_instructions ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced Recipient Information Display -->
                @if($order->delivery->delivery_message || $order->delivery->recipient_relationship)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Additional Recipient Information
                            </h6>
                            @if($order->delivery->delivery_message)
                                <div class="mb-2">
                                    <strong>Delivery Message:</strong>
                                    <p class="mb-0 mt-1">{{ $order->delivery->delivery_message }}</p>
                                </div>
                            @endif
                            @if($order->delivery->recipient_relationship)
                                <div>
                                    <strong>Relationship:</strong> {{ ucfirst($order->delivery->recipient_relationship) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="recipient_verified" name="recipient_verified" required>
                            <label class="form-check-label fw-bold" for="recipient_verified">
                                I have verified all recipient details are correct
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="address_verified" name="address_verified" required>
                            <label class="form-check-label fw-bold" for="address_verified">
                                I have confirmed the delivery address is valid and accessible
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="contact_verified" name="contact_verified" required>
                            <label class="form-check-label fw-bold" for="contact_verified">
                                I have verified the contact number is reachable
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle me-2"></i>Validate Recipient Details
                    </button>
                </div>
            </form>
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
                <a href="{{ route('clerk.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Invoice
                </a>
                <a href="{{ route('clerk.orders.invoice.download', $order->id) }}" class="btn btn-success">
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
                <a href="{{ route('clerk.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Order Slip
                </a>
                <a href="{{ route('clerk.orders.invoice.download', $order->id) }}" class="btn btn-success">
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
                <a href="{{ route('clerk.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Receipt
                </a>
                <a href="{{ route('clerk.orders.invoice.download', $order->id) }}" class="btn btn-success">
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

    <!-- Register Payment Button for COD Orders -->
    @if(strtolower($order->payment_method) === 'cod' && $order->invoice_status === 'ready')
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-credit-card me-2"></i>
                Payment Registration Required
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-3">This COD order requires payment registration. Click the button below to register the payment received.</p>
            <a href="{{ route('clerk.payment.form', $order->id) }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Register Payment
            </a>
        </div>
    </div>
    @endif

    <!-- Payment Tracking Section -->
    @if($order->paymentTracking->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Payment History
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Recorded By</th>
                            <th>Memo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->paymentTracking as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-primary">{{ strtoupper($payment->payment_method) }}</span>
                            </td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>{{ $payment->recordedBy->name ?? 'System' }}</td>
                            <td>{{ $payment->memo ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
        <div class="card-footer">
            @php
                $subtotal = $order->products->sum(function($product) {
                    return $product->pivot->quantity * $product->price;
                });
                $shippingFee = $order->delivery->shipping_fee ?? 0;
                if ($shippingFee == 0 && $order->total_price > $subtotal) {
                    $shippingFee = $order->total_price - $subtotal;
                }
                $total = $subtotal + $shippingFee;
            @endphp
            <div class="row">
                <div class="col-6 text-start">
                    <div class="mb-2">
                        <strong>Subtotal:</strong> ₱{{ number_format($subtotal, 2) }}
                    </div>
                    <div class="mb-2">
                        <strong>Shipping Fee:</strong> ₱{{ number_format($shippingFee, 2) }}
                    </div>
                </div>
                <div class="col-6 text-end">
                    <h4 class="mb-0">Total: ₱{{ number_format($total, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
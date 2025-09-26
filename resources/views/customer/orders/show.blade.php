@extends('layouts.customer_app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Order Details #{{ $order->id }}</h2>
        <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to My Orders
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Order ID:</strong> #{{ $order->id }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> 
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Pending Approval</span>
                            @else
                                <span class="badge bg-{{ 
                                    $order->status === 'approved' ? 'info' : 
                                    ($order->status === 'processing' ? 'primary' : 
                                    ($order->status === 'completed' ? 'success' : 
                                    ($order->status === 'cancelled' ? 'danger' : 'secondary'))) 
                                }}">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Payment Status:</strong> 
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Payment Method:</strong> 
                            <span class="badge bg-info">{{ strtoupper($order->payment_method ?? 'N/A') }}</span>
                        </div>
                        @if($order->notes)
                            <div class="col-md-12 mb-3">
                                <strong>Special Instructions:</strong> {{ $order->notes }}
                            </div>
                        @endif
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
                        <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Invoice
                        </a>
                        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download PDF
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
                        <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Order Slip
                        </a>
                        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download Order Slip
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
                        <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-primary me-2" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Receipt
                        </a>
                        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download Receipt
                        </a>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Products in Order</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($order->products as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $product->name }}">
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <small class="text-muted d-block">Quantity: {{ $product->pivot->quantity }}</small>
                                    </div>
                                </div>
                                <span>₱{{ number_format($product->price * $product->pivot->quantity, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($order->delivery)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Delivery Status:</strong> 
                                <span class="badge bg-{{ 
                                    $order->delivery->status === 'pending' ? 'warning' : 
                                    ($order->delivery->status === 'in_transit' ? 'info' : 
                                    ($order->delivery->status === 'delivered' ? 'success' : 
                                    ($order->delivery->status === 'cancelled' ? 'danger' : 'secondary'))) 
                                }}">{{ ucfirst($order->delivery->status) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Delivery Date:</strong> {{ $order->delivery->delivery_date ? date('M d, Y', strtotime($order->delivery->delivery_date)) : 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Delivery Time:</strong> {{ $order->delivery->delivery_time ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Recipient Name:</strong> {{ $order->delivery->recipient_name ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Recipient Phone:</strong> {{ $order->delivery->recipient_phone ?? 'N/A' }}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Delivery Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}
                            </div>
                            @if($order->delivery->notes)
                                <div class="col-md-12 mb-3">
                                    <strong>Delivery Notes:</strong> {{ $order->delivery->notes }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Name:</strong> {{ $order->user->name }}</p>
                    <p class="mb-2"><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p class="mb-0"><strong>Phone:</strong> {{ $order->user->contact_number ?? 'N/A' }}</p>
                </div>
            </div>

            @if(in_array($order->payment_method, ['gcash','paymaya']) && $order->payment_status === 'unpaid')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Upload Payment Proof</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('customer.orders.uploadPaymentProof', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <input type="hidden" name="payment_method" value="{{ $order->payment_method }}">
                            <input type="text" class="form-control" value="{{ strtoupper($order->payment_method) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number (optional)</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Upload Screenshot/Receipt <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Submit Payment Proof</button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Order Actions -->
            @if ($order->status === 'pending' && $order->payment_status !== 'paid')
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Order Actions</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            You can only cancel orders that are still pending and within 24 hours of placement.
                        </p>
                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
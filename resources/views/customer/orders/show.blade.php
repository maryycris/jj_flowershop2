@extends('layouts.customer_app')

@section('content')
<div class="pt-0 pb-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">


        <div class="row justify-content-center">
            <!-- Left Box - Order Details -->
            <div class="col-12 col-lg-8 col-xl-6" style="max-width: 1200px;">
                <!-- Header Section for Left Box -->
                <div class="mb-2">
                    <div class="rounded-2 p-2" style="background: linear-gradient(135deg, #8ACB88, #7bb47b); box-shadow: 0 2px 10px rgba(0,0,0,0.08); border: none; display: inline-block;">
                        <h4 class="mb-0" style="color: white; font-weight: 600;">
                            Order Details #{{ $order->id }}
                        </h4>
                    </div>
    </div>

                <div class="bg-white rounded-3 p-4 mb-4 scrollable-content" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; max-height: 85vh; overflow-y: auto;">
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #8ACB88, #7bb47b); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shopping-bag text-white" style="font-size: 1.5rem;"></i>
        </div>
        </div>
                        <div>
                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700;">Order Information</h4>
                            <p class="text-muted mb-0">Order #{{ $order->id }} • {{ $order->created_at->format('M d, Y') }}</p>
                </div>
                        </div>
                    
                    <!-- Order Status Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-info-circle me-3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Order Status</small>
                            @if($order->status === 'pending')
                                        <span class="badge bg-warning px-3 py-2">Pending Approval</span>
                            @else
                                <span class="badge bg-{{ 
                                    $order->status === 'approved' ? 'info' : 
                                    ($order->status === 'processing' ? 'primary' : 
                                    ($order->status === 'completed' ? 'success' : 
                                    ($order->status === 'cancelled' ? 'danger' : 'secondary'))) 
                                        }} px-3 py-2">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-credit-card me-3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Payment Status</small>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success px-3 py-2">Paid</span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="badge bg-warning px-3 py-2">Pending</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2">Unpaid</span>
                            @endif
                        </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Amount & Payment Method -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #8ACB88, #7bb47b); border-radius: 8px; color: white;">
                                <i class="fas fa-dollar-sign me-3"></i>
                                <div>
                                    <small class="opacity-75 d-block">Total Amount</small>
                                    <strong style="font-size: 1.2rem;">₱{{ number_format($order->total_price, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-wallet me-3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Payment Method</small>
                                    <span class="badge bg-info px-3 py-2">{{ strtoupper($order->payment_method ?? 'N/A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products in Order -->
                    <div class="mb-4">
                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-box me-2 text-primary"></i>Products in Order
                        </h5>
                        <div class="row g-3">
                            @foreach ($order->products as $product)
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #8ACB88;">
                                        <img src="{{ asset('storage/' . $product->image) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" alt="{{ $product->name }}">
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1" style="color: #2c3e50;">{{ $product->name }}</h6>
                                            <small class="text-muted">Quantity: {{ $product->pivot->quantity }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong style="color: #8ACB88; font-size: 1.1rem;">₱{{ number_format($product->price * $product->pivot->quantity, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    @if($order->delivery)
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-truck me-2 text-primary"></i>Delivery Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <i class="fas fa-map-marker-alt me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Delivery Address</small>
                                            <strong>{{ $order->delivery->delivery_address ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <i class="fas fa-user me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Recipient</small>
                                            <strong>{{ $order->delivery->recipient_name ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <i class="fas fa-calendar me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Delivery Date</small>
                                            <strong>{{ $order->delivery->delivery_date ? date('M d, Y', strtotime($order->delivery->delivery_date)) : 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <i class="fas fa-clock me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Delivery Time</small>
                                            <strong>{{ $order->delivery->delivery_time ?? 'N/A' }}</strong>
                                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                    @endif

                    <!-- Special Instructions -->
                        @if($order->notes)
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-sticky-note me-2 text-primary"></i>Special Instructions
                            </h5>
                            <div class="p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #8ACB88;">
                                <p class="mb-0">{{ $order->notes }}</p>
                            </div>
                            </div>
                        @endif
                </div>
            </div>

            <!-- Right Box - Actions & Customer Info -->
            <div class="col-12 col-lg-4 col-xl-4">
                <!-- Header Section for Right Box -->
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to My Orders
                    </a>
                </div>
                
                <div class="bg-white rounded-3 p-4 mb-4 scrollable-content" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; max-height: 85vh; overflow-y: auto;">
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6c757d, #495057); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700;">Customer Information</h4>
                            <p class="text-muted mb-0">Account Details</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center p-3 mb-3" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-user me-3 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Full Name</small>
                                <strong>{{ $order->user->name }}</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 mb-3" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-envelope me-3 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <strong>{{ $order->user->email }}</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-phone me-3 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Phone Number</small>
                                <strong>{{ $order->user->contact_number ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Payment Receipt / Order Slip / Invoice Options -->
            @php
                $isCOD = strtoupper($order->payment_method) === 'COD';
                $isOnDelivery = $order->order_status === 'on_delivery' || $order->order_status === 'completed';
            @endphp
            
            @if($isOnDelivery)
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-file-invoice me-2 text-primary"></i>Invoice Options
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Invoice
                        </a>
                        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download PDF
                        </a>
                    </div>
                </div>
                
                <!-- Mark as Received Button -->
                <div class="mb-4">
                    <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                        <i class="fas fa-check-circle me-2 text-success"></i>Order Actions
                    </h5>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Click the button below when you have received your order to complete the delivery process.
                    </p>
                    <form action="{{ route('customer.orders.mark-received', $order->id) }}" method="POST" onsubmit="return confirm('Have you received your order? This will mark the order as completed.');">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i>Mark as Received
                        </button>
                    </form>
                </div>
            @elseif($isCOD)
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-receipt me-2 text-primary"></i>Order Slip
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Order Slip
                        </a>
                        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download Order Slip
                        </a>
                    </div>
                </div>
            @else
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-receipt me-2 text-primary"></i>Payment Receipt
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Receipt
                        </a>
                        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download Receipt
                        </a>
                    </div>
                </div>
            @endif

                    <!-- Upload Payment Proof -->
            @if(in_array($order->payment_method, ['gcash','paymaya']) && $order->payment_status === 'unpaid')
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-upload me-2 text-primary"></i>Upload Payment Proof
                            </h5>
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
            @endif

            <!-- Order Actions -->
            @if ($order->status === 'pending' && $order->payment_status !== 'paid')
                        <div>
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Order Actions
                            </h5>
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
            @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom scrollbar styling for the content areas */
    .scrollable-content::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .scrollable-content::-webkit-scrollbar-thumb {
        background: #8ACB88;
        border-radius: 4px;
    }

    .scrollable-content::-webkit-scrollbar-thumb:hover {
        background: #7bb47b;
    }

    /* For Firefox */
    .scrollable-content {
        scrollbar-width: thin;
        scrollbar-color: #8ACB88 #f1f1f1;
    }
</style>
@endpush
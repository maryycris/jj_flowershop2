@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Success Alert -->
            <div class="alert alert-success d-flex align-items-center mb-4">
                <i class="fas fa-check-circle me-3"></i>
                <div>
                    <h5 class="mb-1">Order Validated Successfully!</h5>
                    <p class="mb-0">Order #{{ $order->id }} has been validated, invoice generated, and driver assigned for delivery.</p>
                </div>
            </div>

            <!-- Invoice Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Generated Invoice Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Invoice Details</h6>
                            <p><strong>Invoice Number:</strong> {{ $invoiceData['invoice_number'] ?? 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p><strong>Generated Date:</strong> {{ $invoiceData['generated_date'] ?? now()->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $order->invoice_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->invoice_status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Client Information</h6>
                            <p><strong>Name:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Products to be Delivered ({{ $order->products->count() }} items)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
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
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td>₱{{ number_format($product->price, 2) }}</td>
                                        <td>₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Assignment Card -->
            @if($order->assigned_driver_id)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Driver Assignment
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Assigned Driver</h6>
                            <p><strong>Name:</strong> {{ $order->assignedDriver->name ?? 'N/A' }}</p>
                            <p><strong>Contact:</strong> {{ $order->assignedDriver->contact_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Delivery Status</h6>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-info">On Delivery</span>
                            </p>
                            <p><strong>Assigned:</strong> {{ $order->on_delivery_at ? \Carbon\Carbon::parse($order->on_delivery_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Driver Assignment Required
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">No driver was automatically assigned. Please manually assign a driver for delivery.</p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('clerk.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
                <div>
                    <a href="{{ route('clerk.orders.show', $order->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-eye me-2"></i>View Full Invoice
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



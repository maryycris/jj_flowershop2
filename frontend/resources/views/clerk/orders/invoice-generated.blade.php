@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Invoice Generated - Order #{{ $order->id }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Invoice Details</h5>
                            <p><strong>Invoice Number:</strong> {{ $invoiceData['invoice_number'] }}</p>
                            <p><strong>Generated Date:</strong> {{ $invoiceData['generated_date'] }}</p>
                            <p><strong>Order Status:</strong> 
                                <span class="badge bg-{{ $order->invoice_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->invoice_status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Client Information</h5>
                            <p><strong>Name:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p><strong>Phone:</strong> {{ $order->user->contact_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    @if($order->delivery)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Delivery Address</h5>
                            <p>{{ $order->delivery->delivery_address }}</p>
                            <p><strong>Recipient:</strong> {{ $order->delivery->recipient_name ?? $order->user->name }}</p>
                            <p><strong>Phone:</strong> {{ $order->delivery->recipient_phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Delivery Schedule</h5>
                            <p><strong>Date:</strong> {{ $order->delivery->delivery_date ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Time:</strong> {{ $order->delivery->delivery_time ?? 'N/A' }}</p>
                            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Products Table -->
                    <div class="mb-4">
                        <h5>Products to be Delivered</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
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
                                                         class="img-thumbnail me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                                         alt="{{ $product->name }}">
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
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><strong>₱{{ number_format($invoiceData['subtotal'], 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Shipping Fee:</strong></td>
                                        <td><strong>₱{{ number_format($invoiceData['shipping_fee'], 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong>₱{{ number_format($invoiceData['total'], 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    @if($order->paymentTracking->count() > 0)
                    <div class="mb-4">
                        <h5>Payment History</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Recorded By</th>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('clerk.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                        <div>
                            <button class="btn btn-primary me-2" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Invoice
                            </button>
                            <a href="{{ route('clerk.orders.show', $order->id) }}" class="btn btn-success">
                                <i class="fas fa-eye me-2"></i>View Order Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .card-header {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Invoice {{ $invoiceData['invoice_number'] }}
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('clerk.invoices.index') }}" class="btn btn-outline-light">
                                <i class="bi bi-arrow-left me-2"></i>Back to Invoices
                            </a>
                            <button class="btn btn-light" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Invoice Details</h5>
                            <p><strong>Invoice Number:</strong> {{ $invoiceData['invoice_number'] }}</p>
                            <p><strong>Generated Date:</strong> {{ $invoiceData['generated_date'] }}</p>
                            <p><strong>Status:</strong> 
                                @php
                                    $statusClass = 'bg-warning text-dark';
                                    switch($order->invoice_status) {
                                        case 'ready':
                                            $statusClass = 'bg-warning text-dark';
                                            break;
                                        case 'paid':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'overdue':
                                            $statusClass = 'bg-danger';
                                            break;
                                        case 'draft':
                                            $statusClass = 'bg-secondary';
                                            break;
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($order->invoice_status) }}</span>
                            </p>
                            <p><strong>Source Document:</strong> SO-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
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
                        <h5>Products</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>SKU</th>
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
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $product->code ?? 'N/A' }}</td>
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td>₱{{ number_format($product->price, 2) }}</td>
                                        <td>₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><strong>₱{{ number_format($invoiceData['subtotal'], 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Shipping Fee:</strong></td>
                                        <td><strong>₱{{ number_format($invoiceData['shipping_fee'], 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong>₱{{ number_format($invoiceData['total'], 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Processing Section -->
                    @if(strtolower($order->payment_method) === 'cod' && $order->invoice_status === 'ready')
                    <div class="mb-4">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Payment Registration Required
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">This COD order requires payment registration. Click the button below to register the payment received.</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('clerk.payment.form', $order->id) }}" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Register Payment
                                    </a>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickPaymentModal">
                                        <i class="fas fa-bolt me-2"></i>Quick Register
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

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
                                        <th>Memo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->paymentTracking as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
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
                    @endif

                    <!-- Order Status Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Status</h6>
                            <p><strong>Current Status:</strong> 
                                @php
                                    $orderStatusClass = 'bg-warning text-dark';
                                    switch($order->order_status) {
                                        case 'approved':
                                            $orderStatusClass = 'bg-success';
                                            break;
                                        case 'on_delivery':
                                            $orderStatusClass = 'bg-info';
                                            break;
                                        case 'completed':
                                            $orderStatusClass = 'bg-primary';
                                            break;
                                        case 'cancelled':
                                            $orderStatusClass = 'bg-danger';
                                            break;
                                    }
                                @endphp
                                <span class="badge {{ $orderStatusClass }}">{{ ucfirst($order->order_status) }}</span>
                            </p>
                            @if($order->approved_at)
                                <p><strong>Approved:</strong> {{ $order->approved_at ? \Carbon\Carbon::parse($order->approved_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                            @endif
                            @if($order->on_delivery_at)
                                <p><strong>On Delivery:</strong> {{ $order->on_delivery_at ? \Carbon\Carbon::parse($order->on_delivery_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                            @endif
                            @if($order->completed_at)
                                <p><strong>Completed:</strong> {{ $order->completed_at ? \Carbon\Carbon::parse($order->completed_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Invoice Timeline</h6>
                            <p><strong>Generated:</strong> {{ $order->invoice_generated_at ? \Carbon\Carbon::parse($order->invoice_generated_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                            @if($order->invoice_paid_at)
                                <p><strong>Paid:</strong> {{ $order->invoice_paid_at ? \Carbon\Carbon::parse($order->invoice_paid_at)->format('M d, Y g:i A') : 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Payment Registration Modal -->
<div class="modal fade" id="quickPaymentModal" tabindex="-1" aria-labelledby="quickPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="quickPaymentModalLabel">
                    <i class="fas fa-credit-card me-2"></i>
                    Quick Payment Registration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickPaymentForm">
                    @csrf
                    <div class="mb-3">
                        <label for="quick_payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick_payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="quick_amount" name="amount" 
                                   step="0.01" min="0.01" value="{{ $invoiceData['total'] }}" required>
                        </div>
                        <div class="form-text">Maximum: ₱{{ number_format($invoiceData['total'], 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="quick_payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="quick_payment_date" name="payment_date" 
                               value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick_memo" class="form-label">Memo (Optional)</label>
                        <textarea class="form-control" id="quick_memo" name="memo" rows="2" 
                                  placeholder="Additional notes about this payment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="quickPaymentBtn">
                    <i class="fas fa-check me-2"></i>Register Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="paymentSuccessModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Payment Registered Successfully
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <p class="lead">Payment for Invoice {{ $invoiceData['invoice_number'] }} has been successfully registered.</p>
                <p>The invoice status has been updated to 'Paid'.</p>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    You will be redirected to the invoice page in 3 seconds...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="fas fa-refresh me-2"></i>Refresh Page
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .btn, .card-header .btn {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background: #f8f9fa !important;
        color: #000 !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quickPaymentBtn = document.getElementById('quickPaymentBtn');
    const quickPaymentForm = document.getElementById('quickPaymentForm');
    const paymentSuccessModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
    
    quickPaymentBtn.addEventListener('click', function() {
        // Disable button and show loading
        quickPaymentBtn.disabled = true;
        quickPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        const formData = new FormData(quickPaymentForm);
        const url = `{{ route('clerk.payment.register', $order->id) }}`;
        
        console.log('Submitting quick payment to URL:', url);
        console.log('Form data:', Object.fromEntries(formData));
        
        // Get CSRF token safely
        let csrfTokenValue = '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            csrfTokenValue = csrfToken.getAttribute('content');
        } else {
            const csrfInput = quickPaymentForm.querySelector('input[name="_token"]');
            if (csrfInput) {
                csrfTokenValue = csrfInput.value;
            }
        }
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfTokenValue,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Close quick payment modal
                const quickPaymentModal = bootstrap.Modal.getInstance(document.getElementById('quickPaymentModal'));
                quickPaymentModal.hide();
                
                // Show success modal
                paymentSuccessModal.show();
                
                // Auto redirect after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                alert('Error: ' + (data.message || 'Failed to register payment'));
                quickPaymentBtn.disabled = false;
                quickPaymentBtn.innerHTML = '<i class="fas fa-check me-2"></i>Register Payment';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the payment: ' + error.message);
            quickPaymentBtn.disabled = false;
            quickPaymentBtn.innerHTML = '<i class="fas fa-check me-2"></i>Register Payment';
        });
    });
});
</script>
@endpush
@endsection

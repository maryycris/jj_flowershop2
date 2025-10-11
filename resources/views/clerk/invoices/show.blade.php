@extends('layouts.clerk_app')

@section('title', 'Invoice Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Invoice Details - {{ $invoice->invoice_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('clerk.invoices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                        @if($invoice->status === 'ready' && $invoice->payment_type === 'cod')
                            <button type="button" class="btn btn-success ml-2" onclick="openPaymentWizard({{ $invoice->id }})">
                                <i class="fas fa-credit-card"></i> Register Payment
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Invoice Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Invoice Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                                            <p><strong>Order Number:</strong> 
                                                <a href="{{ route('clerk.orders.show', $invoice->order_id) }}">#{{ $invoice->order_id }}</a>
                                            </p>
                                            <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                                            <p><strong>Payment Type:</strong> 
                                                @if($invoice->payment_type === 'online')
                                                    <span class="badge badge-info">Online</span>
                                                @else
                                                    <span class="badge badge-primary">COD</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                @if($invoice->status === 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif($invoice->status === 'ready')
                                                    <span class="badge badge-warning">Ready</span>
                                                @elseif($invoice->status === 'draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                @else
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </p>
                                            <p><strong>Customer:</strong> {{ $invoice->order->user->name }}</p>
                                            <p><strong>Email:</strong> {{ $invoice->order->user->email }}</p>
                                            <p><strong>Phone:</strong> {{ $invoice->order->user->contact_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Products -->
                            <div class="card mt-3">
                                <div class="card-header">
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
                                                @foreach($invoice->order->products as $product)
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
                                                                @if($product->pivot->rating)
                                                                    <br><small class="text-muted">
                                                                        Rating: {{ $product->pivot->rating }}/5
                                                                    </small>
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
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Information -->
                            @if($invoice->order->delivery)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Delivery Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Delivery Date:</strong> {{ $invoice->order->delivery->delivery_date }}</p>
                                            <p><strong>Delivery Time:</strong> {{ $invoice->order->delivery->delivery_time }}</p>
                                            <p><strong>Recipient:</strong> {{ $invoice->order->delivery->recipient_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Phone:</strong> {{ $invoice->order->delivery->recipient_phone }}</p>
                                            <p><strong>Address:</strong> {{ $invoice->order->delivery->delivery_address }}</p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge badge-info">{{ ucfirst($invoice->order->delivery->status) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Payment Summary -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Subtotal:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p>₱{{ number_format($invoice->subtotal, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Shipping Fee:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p>₱{{ number_format($invoice->shipping_fee, 2) }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Total Amount:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p class="text-success font-weight-bold">₱{{ number_format($invoice->total_amount, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment History -->
                            @if($invoice->payments->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Payment History</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($invoice->payments as $payment)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span><strong>{{ ucfirst($payment->mode_of_payment) }}</strong></span>
                                            <span class="text-success">₱{{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $payment->payment_date->format('M d, Y') }}
                                            @if($payment->memo)
                                                - {{ $payment->memo }}
                                            @endif
                                        </small>
                                    </div>
                                    @endforeach
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

<!-- Payment Wizard Modal -->
<div class="modal fade" id="paymentWizardModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Payment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mode_of_payment">Mode of Payment <span class="text-danger">*</span></label>
                                <select class="form-control" id="mode_of_payment" name="mode_of_payment" required>
                                    <option value="">Select Payment Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="card">Card Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0.01" value="{{ $invoice->total_amount }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="memo">Memo (Optional)</label>
                                <input type="text" class="form-control" id="memo" name="memo" 
                                       placeholder="Payment reference or notes">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="validatePayment()">
                    <i class="fas fa-check"></i> Validate Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openPaymentWizard(invoiceId) {
    $('#paymentWizardModal').modal('show');
}

function validatePayment() {
    const formData = new FormData(document.getElementById('paymentForm'));
    
    // Show loading state
    const validateBtn = document.querySelector('button[onclick="validatePayment()"]');
    const originalText = validateBtn.innerHTML;
    validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    validateBtn.disabled = true;

    fetch(`/clerk/invoices/{{ $invoice->id }}/register-payment`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment registered successfully!');
            $('#paymentWizardModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while registering payment');
    })
    .finally(() => {
        validateBtn.innerHTML = originalText;
        validateBtn.disabled = false;
    });
}
</script>
@endsection
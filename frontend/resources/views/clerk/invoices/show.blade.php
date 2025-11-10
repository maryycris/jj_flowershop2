@extends('layouts.clerk_app')

@section('title', 'Invoice Details')

@push('styles')
<style>
/* Invoice Details Styling - matching invoice index hierarchy */
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

/* Order link styling */
.card-body a[href*="orders"] {
    color: #7bb47b !important;
    text-decoration: none;
    transition: all 0.2s ease;
}

.card-body a[href*="orders"]:hover {
    color: #5aa65a !important;
    text-decoration: underline;
}

/* Amount styling */
.text-success {
    font-size: 0.85rem;
}

/* Button sizing in header */
.card-header .card-tools .btn {
    font-size: 0.75rem !important;
    padding: 0.25rem 0.5rem !important;
    line-height: 1.3 !important;
}

.card-header .card-tools .btn i {
    font-size: 0.7rem;
    margin-right: 0.25rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">Invoice Details - {{ $invoice->invoice_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('clerk.invoices.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                        @if($invoice->status === 'ready' && $invoice->payment_type === 'cod')
                            <button type="button" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#paymentWizardModal">
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
                                <div class="card-header" style="background: #e6f4ea;">
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
                                                    <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                                                @else
                                                    <span class="badge" style="background-color: #66bb6a; color: white;">COD</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                @if($invoice->status === 'paid')
                                                    <span class="badge" style="background-color: #28a745; color: white;">Paid</span>
                                                @elseif($invoice->status === 'ready')
                                                    <span class="badge" style="background-color: #90ee90; color: black;">Ready</span>
                                                @elseif($invoice->status === 'draft')
                                                    <span class="badge" style="background-color: #c8e6c9; color: black;">Draft</span>
                                                @else
                                                    <span class="badge" style="background-color: #2d5016; color: white;">Cancelled</span>
                                                @endif
                                            </p>
                                            <p><strong>Customer:</strong> {{ $invoice->order->user->name }}</p>
                                            <p><strong>Email:</strong> {{ $invoice->order->user->email }}</p>
                                            @php
                                                // Get phone - prefer delivery recipient phone, fallback to user contact
                                                $phone = null;
                                                if ($invoice->order->delivery && $invoice->order->delivery->recipient_phone) {
                                                    $phone = $invoice->order->delivery->recipient_phone;
                                                } elseif ($invoice->order->user->contact_number) {
                                                    $phone = $invoice->order->user->contact_number;
                                                }
                                            @endphp
                                            <p><strong>Phone:</strong> {{ $phone ?: 'N/A' }}</p>
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
                                                @foreach($invoice->order->customBouquets as $bouquet)
                                                @php
                                                    $orderQty = $bouquet->pivot->quantity;
                                                    $customData = $bouquet->customization_data ?? [];
                                                    $freshFlowerQty = $customData['freshFlowerQuantity'] ?? 1;
                                                    $artificialFlowerQty = $customData['artificialFlowerQuantity'] ?? 1;
                                                    
                                                    $components = [];
                                                    
                                                    // Wrapper
                                                    if ($bouquet->wrapper) {
                                                        $components[] = "Wrapper: {$bouquet->wrapper} (x{$orderQty})";
                                                    }
                                                    
                                                    // Fresh Flowers
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
                                                    
                                                    // Greenery
                                                    if ($bouquet->greenery) {
                                                        $components[] = "Greenery: {$bouquet->greenery} (x{$orderQty})";
                                                    }
                                                    
                                                    // Artificial Flowers (Filler)
                                                    if ($bouquet->filler) {
                                                        $totalArtificialQty = $artificialFlowerQty * $orderQty;
                                                        $components[] = "Artificial Flowers: {$bouquet->filler} (x{$totalArtificialQty})";
                                                    }
                                                    
                                                    // Ribbon
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
                                                @if($invoice->order->products->isEmpty() && $invoice->order->customBouquets->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        No products found
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Information -->
                            @if($invoice->order->delivery)
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Delivery Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Delivery Date:</strong> {{ $invoice->order->delivery->delivery_date ?? 'N/A' }}</p>
                                            <p><strong>Delivery Time:</strong> {{ $invoice->order->delivery->delivery_time ?? 'N/A' }}</p>
                                            <p><strong>Recipient:</strong> {{ $invoice->order->delivery->recipient_name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                // Get phone - prefer delivery recipient phone, fallback to user contact
                                                $deliveryPhone = null;
                                                if ($invoice->order->delivery && $invoice->order->delivery->recipient_phone) {
                                                    $deliveryPhone = $invoice->order->delivery->recipient_phone;
                                                } elseif ($invoice->order->user->contact_number) {
                                                    $deliveryPhone = $invoice->order->user->contact_number;
                                                }
                                                
                                                // Get address - ensure it's not empty
                                                $deliveryAddress = $invoice->order->delivery->delivery_address ?? null;
                                                if (empty($deliveryAddress) && $invoice->order->delivery && $invoice->order->delivery->recipient_name) {
                                                    $deliveryAddress = 'Address not specified for ' . $invoice->order->delivery->recipient_name;
                                                }
                                                
                                                // Get delivery status - check multiple sources
                                                $deliveryStatus = null;
                                                if ($invoice->order->delivery && isset($invoice->order->delivery->status) && !empty($invoice->order->delivery->status)) {
                                                    $deliveryStatus = $invoice->order->delivery->status;
                                                } elseif ($invoice->order->order_status) {
                                                    $deliveryStatus = $invoice->order->order_status;
                                                } else {
                                                    $deliveryStatus = 'pending';
                                                }
                                                
                                                // Format status for display
                                                $statusBadgeClass = 'badge-info';
                                                if (in_array(strtolower($deliveryStatus), ['delivered', 'completed'])) {
                                                    $statusBadgeClass = 'badge-success';
                                                } elseif (in_array(strtolower($deliveryStatus), ['on_delivery', 'on delivery'])) {
                                                    $statusBadgeClass = 'badge-warning';
                                                } elseif (in_array(strtolower($deliveryStatus), ['cancelled', 'returned'])) {
                                                    $statusBadgeClass = 'badge-danger';
                                                }
                                            @endphp
                                            <p><strong>Phone:</strong> {{ $deliveryPhone ?: 'N/A' }}</p>
                                            <p><strong>Address:</strong> {{ $deliveryAddress ?: 'N/A' }}</p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge {{ $statusBadgeClass }}">{{ ucfirst(str_replace('_', ' ', $deliveryStatus)) }}</span>
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
                                <div class="card-header" style="background: #e6f4ea;">
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
                                <div class="card-header" style="background: #e6f4ea;">
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
<div class="modal fade" id="paymentWizardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
// No opener needed: using data-bs-toggle="modal" on the button

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
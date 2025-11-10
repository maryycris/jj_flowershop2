@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success">
                <i class="fas fa-undo me-2"></i>Return Details - Order #{{ $order->id }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Return Management</a></li>
                    <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Returns
            </a>
            @if($order->return_status === 'pending')
                <button class="btn btn-success" onclick="showReturnAction({{ $order->id }}, '{{ $order->return_status }}')">
                    <i class="fas fa-cog me-1"></i>Take Action
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Order Status:</strong> 
                                <span class="badge bg-warning text-dark">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                            </p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Return Date:</strong> {{ $order->returned_at ? $order->returned_at->format('M d, Y H:i') : 'N/A' }}</p>
                            <p><strong>Return Status:</strong> 
                                @switch($order->return_status)
                                    @case('pending')
                                        <span class="badge bg-info">Pending Review</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-success">Approved</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                        @break
                                    @case('resolved')
                                        <span class="badge bg-secondary">Resolved</span>
                                        @break
                                @endswitch
                            </p>
                            <p><strong>Return Reason:</strong> {{ $order->return_reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p><strong>Phone:</strong> {{ $order->user->contact_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                            @if($order->delivery && $order->delivery->special_instructions)
                                <p><strong>Special Instructions:</strong> {{ $order->delivery->special_instructions }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Driver Information</h5>
                </div>
                <div class="card-body">
                    @if($order->returnedByDriver)
                        <p><strong>Driver Name:</strong> {{ $order->returnedByDriver->name }}</p>
                        <p><strong>Driver Email:</strong> {{ $order->returnedByDriver->email }}</p>
                        <p><strong>Driver Phone:</strong> {{ $order->returnedByDriver->contact_number ?? 'N/A' }}</p>
                    @else
                        <p class="text-muted">Driver information not available</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    @if($order->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
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
                    @else
                        <p class="text-muted">No products found for this order.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Return Actions & Notes -->
        <div class="col-md-4">
            <!-- Return Notes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Return Notes</h5>
                </div>
                <div class="card-body">
                    @if($order->return_notes)
                        <p><strong>Driver Notes:</strong></p>
                        <p class="text-muted">{{ $order->return_notes }}</p>
                    @else
                        <p class="text-muted">No additional notes provided.</p>
                    @endif
                </div>
            </div>

            <!-- Admin Actions -->
            @if($order->return_status === 'pending')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Admin Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="showReturnAction({{ $order->id }}, '{{ $order->return_status }}')">
                            <i class="fas fa-check me-1"></i>Review Return
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Refund Processing -->
            @if($order->return_status === 'approved' && !$order->refund_processed_at)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Refund Processing</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning" onclick="showRefundModal({{ $order->id }}, {{ $order->total_price }})">
                            <i class="fas fa-money-bill-wave me-1"></i>Process Refund
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Status History -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Status History</h5>
                </div>
                <div class="card-body">
                    @if($order->statusHistories->count() > 0)
                        <div class="timeline">
                            @foreach($order->statusHistories->sortByDesc('created_at') as $history)
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle p-2">
                                            <i class="fas fa-circle text-white" style="font-size: 0.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</h6>
                                        <p class="text-muted small mb-1">{{ $history->notes }}</p>
                                        <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No status history available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the same modals from index page -->
@include('admin.returns.partials.modals')

<script>
// Include the same JavaScript functions from index page
function showReturnAction(orderId, currentStatus) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('return_status').value = '';
    document.getElementById('admin_notes').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('returnActionModal'));
    modal.show();
}

function showRefundModal(orderId, maxAmount) {
    document.getElementById('refundOrderId').value = orderId;
    document.getElementById('refund_amount').max = maxAmount;
    document.getElementById('refund_amount').value = maxAmount;
    
    const modal = new bootstrap.Modal(document.getElementById('refundModal'));
    modal.show();
}

function submitReturnAction() {
    const form = document.getElementById('returnActionForm');
    const formData = new FormData(form);
    const orderId = formData.get('order_id');
    
    fetch(`/admin/returns/${orderId}/update-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Action Submitted!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while submitting the action.'
        });
    });
}

function processRefund() {
    const form = document.getElementById('refundForm');
    const formData = new FormData(form);
    const orderId = formData.get('order_id');
    
    fetch(`/admin/returns/${orderId}/process-refund`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Refund Processed!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while processing the refund.'
        });
    });
}
</script>

<style>
/* Ensure proper positioning without sidebar overlap */
.container-fluid {
    margin-left: 0 !important;
    padding-left: 20px !important;
    padding-right: 20px !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* Ensure content doesn't get cut off */
.row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.col-12, .col-md-6, .col-md-4, .col-md-8 {
    padding-left: 10px !important;
    padding-right: 10px !important;
}
</style>
@endsection

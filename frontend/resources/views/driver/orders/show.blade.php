@extends('layouts.driver_mobile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">Order Details</h4>
                <a href="{{ route('driver.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Orders
                </a>
            </div>

            <!-- Order Status Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Order #{{ $order->id }}</h5>
                        @if($order->order_status === 'returned')
                            <span class="badge bg-warning fs-6">
                                <i class="bi bi-arrow-return-left me-1"></i>Returned
                            </span>
                        @elseif($order->order_status === 'completed')
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle me-1"></i>Delivered
                            </span>
                        @elseif($order->order_status === 'on_delivery')
                            <span class="badge bg-info fs-6">
                                <i class="bi bi-truck me-1"></i>On Delivery
                            </span>
                        @elseif($order->order_status === 'pending')
                            <span class="badge bg-warning fs-6">
                                <i class="bi bi-clock me-1"></i>Pending
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6">{{ ucfirst($order->order_status) }}</span>
                        @endif
                    </div>
                    
                    @if($order->order_status === 'returned')
                        <div class="mt-2">
                            <small class="text-muted">Returned on:</small>
                            <strong class="text-warning">{{ $order->returned_at ? $order->returned_at->format('M d, Y H:i') : 'N/A' }}</strong>
                        </div>
                        @if($order->return_reason)
                            <div class="mt-1">
                                <small class="text-muted">Return Reason:</small>
                                <strong class="text-warning">{{ $order->return_reason }}</strong>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Order Information (Consolidated) -->
            <div class="card mb-3">
                <div class="card-header" style="background: #e6f4ea; border-bottom: 1px solid #bbf7d0;">
                    <h6 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #385E42;">
                        <i class="bi bi-info-circle me-2"></i>Order Information
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Customer Information Section -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600; color: #385E42;">
                            <i class="bi bi-person me-2"></i>Customer Information
                        </h6>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <small class="text-muted" style="font-size: 0.8rem;">Name:</small><br>
                                <strong style="font-size: 0.85rem;">{{ $order->user->name }}</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted" style="font-size: 0.8rem;">Contact:</small><br>
                                <strong style="font-size: 0.85rem;">{{ $order->user->contact_number ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information Section -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600; color: #385E42;">
                            <i class="bi bi-geo-alt me-2"></i>Delivery Information
                        </h6>
                        <div class="mb-2">
                            <small class="text-muted" style="font-size: 0.8rem;">Address:</small><br>
                            <strong style="font-size: 0.85rem;">{{ $order->delivery->delivery_address ?? 'N/A' }}</strong>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <small class="text-muted" style="font-size: 0.8rem;">Delivery Date:</small><br>
                                <strong style="font-size: 0.85rem;">{{ $order->delivery->delivery_date ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') : 'N/A' }}</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted" style="font-size: 0.8rem;">Delivery Time:</small><br>
                                <strong style="font-size: 0.85rem;">{{ $order->delivery->delivery_time ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Section -->
                    <div>
                        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600; color: #385E42;">
                            <i class="bi bi-box me-2"></i>Order Items
                        </h6>
                        @foreach($order->products as $product)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong style="font-size: 0.85rem;">{{ $product->name }}</strong>
                                <br><small class="text-muted" style="font-size: 0.75rem;">Qty: {{ $product->pivot->quantity }}</small>
                            </div>
                            <div class="text-end">
                                <strong style="font-size: 0.85rem;">₱{{ number_format($product->price * $product->pivot->quantity, 2) }}</strong>
                            </div>
                        </div>
                        @endforeach
                        
                        <hr style="margin: 1rem 0; border-color: #e6f4ea;">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong style="font-size: 0.9rem;">Total Amount:</strong>
                            <strong class="text-success" style="font-size: 0.9rem;">₱{{ number_format($order->total_price, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            @if($order->statusHistories && $order->statusHistories->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Order Timeline</h6>
                </div>
                <div class="card-body">
                    @foreach($order->statusHistories->sortBy('created_at') as $history)
                    <div class="d-flex mb-2">
                        <div class="flex-shrink-0">
                            <i class="bi bi-circle-fill text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</small>
                            <br><strong>{{ ucfirst($history->status) }}</strong>
                            @if($history->message)
                                <br><small class="text-muted">{{ $history->message }}</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($order->order_status === 'on_delivery')
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-success flex-fill" onclick="showCompleteModal({{ $order->id }})">
                    <i class="bi bi-check-circle me-1"></i>Mark Complete
                </button>
            </div>
            @endif

            {{-- Return status messages removed --}}

            @if($order->order_status === 'completed')
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Order Delivered</strong><br>
                This order has been successfully delivered to the customer.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Complete Delivery Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">Complete Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="completeDeliveryForm">
                    @csrf
                    <div class="mb-3">
                        <label for="deliveryNotes" class="form-label">Delivery Notes (Optional)</label>
                        <textarea class="form-control" id="deliveryNotes" name="delivery_notes" rows="3" placeholder="Any notes about the delivery..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="completeDeliveryBtn">Complete Delivery</button>
            </div>
        </div>
    </div>
</div>

<script>
function showCompleteModal(orderId) {
    const modal = new bootstrap.Modal(document.getElementById('completeModal'));
    modal.show();
}

document.getElementById('completeDeliveryBtn').addEventListener('click', function() {
    const orderId = {{ $order->id }};
    const deliveryNotes = document.getElementById('deliveryNotes').value;
    
    // Show loading state
    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Completing...';
    this.disabled = true;
    
    fetch(`/driver/orders/${orderId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            delivery_notes: deliveryNotes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Delivery Completed!',
                text: 'Order has been marked as completed.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to complete delivery'
            });
            this.innerHTML = 'Complete Delivery';
            this.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while completing the delivery'
        });
        this.innerHTML = 'Complete Delivery';
        this.disabled = false;
    });
});

function returnOrder(orderId) {
    if (confirm('Are you sure you want to return this order?')) {
        // Show loading state
        const returnBtn = document.querySelector(`button[onclick="returnOrder(${orderId})"]`);
        const originalText = returnBtn.innerHTML;
        returnBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Returning...';
        returnBtn.disabled = true;
        
        fetch(`/driver/orders/${orderId}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reason: 'Order returned by driver - customer not available'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Returned!',
                    text: 'Order has been marked as returned.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to return order'
                });
                returnBtn.innerHTML = originalText;
                returnBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while returning the order'
            });
            returnBtn.innerHTML = originalText;
            returnBtn.disabled = false;
        });
    }
}
</script>
@endsection
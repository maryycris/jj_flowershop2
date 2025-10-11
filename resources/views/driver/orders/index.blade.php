@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">My Orders</h4>
    <span class="badge bg-primary">{{ $orders->count() }} assigned</span>
</div>

@if($orders->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-inbox display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No orders assigned yet</h5>
    <p class="text-muted">You'll see your delivery orders here when they're assigned to you.</p>
</div>
@else
<div class="row">
    @foreach($orders as $order)
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #{{ $order->id }}</h6>
                    <span class="badge bg-{{ $order->order_status === 'completed' ? 'success' : ($order->order_status === 'on_delivery' ? 'info' : ($order->order_status === 'assigned' ? 'warning' : 'secondary')) }}">
                        {{ $order->order_status === 'assigned' ? 'For Delivery' : ucwords(str_replace('_', ' ', $order->order_status)) }}
                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong>{{ $order->user->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Contact:</small><br>
                        <strong>{{ $order->user->contact_number ?? 'N/A' }}</strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong>{{ $order->delivery->delivery_address ?? 'Address not specified' }}</strong>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Total Amount:</small><br>
                        <strong>₱{{ number_format($order->total_price, 2) }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Order Date:</small><br>
                        <strong>{{ $order->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
                
                @if($order->delivery && $order->delivery->special_instructions)
                <div class="mb-3">
                    <small class="text-muted">Special Instructions:</small><br>
                    <strong class="text-info">{{ $order->delivery->special_instructions }}</strong>
                </div>
                @endif
                
                <div class="d-flex gap-2">
                    <a href="{{ route('driver.orders.show', $order->id) }}" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    @if($order->order_status === 'assigned')
                        @if($order->delivery && $order->delivery->driver_decision === 'accepted')
                            <span class="badge bg-success">Accepted</span>
                        @elseif($order->delivery && $order->delivery->driver_decision === 'declined')
                            <span class="badge bg-danger">Declined</span>
                        @else
                            <button class="btn btn-success btn-sm me-1" onclick="acceptOrder({{ $order->id }})">
                                <i class="bi bi-check-circle me-1"></i>Accept
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="declineOrder({{ $order->id }})">
                                <i class="bi bi-x-circle me-1"></i>Decline
                            </button>
                        @endif
                    @elseif($order->order_status === 'on_delivery')
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" onclick="updateOrderStatus({{ $order->id }}, 'completed')">
                            <i class="bi bi-check me-1"></i>Mark Complete
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="showReturnModal({{ $order->id }}, '{{ $order->user->first_name }} {{ $order->user->last_name }}', '{{ $order->delivery_address }}', '₱{{ number_format($order->total_price, 2) }}', '{{ $order->created_at->format('M d, Y') }}')">
                            <i class="bi bi-arrow-return-left me-1"></i>Return
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- Decline Reason Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineModalLabel">Decline Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="declineForm">
                    @csrf
                    <div class="mb-3">
                        <label for="declineReason" class="form-label">Reason for declining:</label>
                        <select class="form-select" id="declineReason" name="reason" required>
                            <option value="">Select a reason...</option>
                            <option value="Busy with other deliveries">Busy with other deliveries</option>
                            <option value="Unavailable today">Unavailable today</option>
                            <option value="Location too far">Location too far</option>
                            <option value="Vehicle issues">Vehicle issues</option>
                            <option value="Personal emergency">Personal emergency</option>
                            <option value="Weather conditions">Weather conditions</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3" id="customReasonDiv" style="display: none;">
                        <label for="customReason" class="form-label">Please specify:</label>
                        <textarea class="form-control" id="customReason" name="custom_reason" rows="3" placeholder="Enter your reason..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitDecline()">Decline Order</button>
            </div>
        </div>
    </div>
</div>

<!-- Return Order Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Order Details -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Order Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order ID:</strong> <span id="returnOrderId"></span></p>
                                <p><strong>Customer:</strong> <span id="returnCustomerName"></span></p>
                                <p><strong>Total Amount:</strong> <span id="returnTotalAmount"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Order Date:</strong> <span id="returnOrderDate"></span></p>
                                <p><strong>Delivery Address:</strong> <span id="returnDeliveryAddress"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Reason -->
                <div class="mb-3">
                    <label for="returnReason" class="form-label">Reason for returning the order:</label>
                    <select class="form-select" id="returnReason" onchange="toggleCustomReturnReason()">
                        <option value="">Select a reason</option>
                        <option value="Customer not available">Customer not available</option>
                        <option value="Customer refused delivery">Customer refused delivery</option>
                        <option value="Wrong address provided">Wrong address provided</option>
                        <option value="Package damaged during delivery">Package damaged during delivery</option>
                        <option value="Customer requested return">Customer requested return</option>
                        <option value="Delivery time not suitable">Delivery time not suitable</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3" id="customReturnReasonDiv" style="display: none;">
                    <label for="customReturnReason" class="form-label">Please specify:</label>
                    <textarea class="form-control" id="customReturnReason" rows="3" placeholder="Enter your reason here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="confirmReturn()">
                    <i class="bi bi-arrow-return-left me-1"></i>Send Return Notification
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentOrderId = null;

function acceptOrder(orderId) {
    if (confirm('Are you sure you want to accept this order?')) {
        fetch(`/driver/orders/${orderId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Accepted!',
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
                text: 'An error occurred while accepting the order'
            });
        });
    }
}

function declineOrder(orderId) {
    currentOrderId = orderId;
    const modal = new bootstrap.Modal(document.getElementById('declineModal'));
    modal.show();
}

// Handle custom reason input
document.getElementById('declineReason').addEventListener('change', function() {
    const customReasonDiv = document.getElementById('customReasonDiv');
    if (this.value === 'Other') {
        customReasonDiv.style.display = 'block';
        document.getElementById('customReason').required = true;
    } else {
        customReasonDiv.style.display = 'none';
        document.getElementById('customReason').required = false;
    }
});

function submitDecline() {
    const reason = document.getElementById('declineReason').value;
    const customReason = document.getElementById('customReason').value;
    
    if (!reason) {
        Swal.fire({
            icon: 'warning',
            title: 'Required Field',
            text: 'Please select a reason for declining'
        });
        return;
    }
    
    if (reason === 'Other' && !customReason.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Required Field',
            text: 'Please specify your reason'
        });
        return;
    }
    
    const finalReason = reason === 'Other' ? customReason : reason;
    
    fetch(`/driver/orders/${currentOrderId}/decline`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reason: finalReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Order Declined',
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
            text: 'An error occurred while declining the order'
        });
    });
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('declineModal'));
    modal.hide();
}

function updateOrderStatus(orderId, status) {
    if (confirm('Are you sure you want to mark this order as completed?')) {
        fetch(`/driver/orders/${orderId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating order status');
            }
        });
    }
}

// Return Order Functions
let returnOrderId = null;

function showReturnModal(orderId, customerName, deliveryAddress, totalAmount, orderDate) {
    returnOrderId = orderId;
    
    // Populate order details
    document.getElementById('returnOrderId').textContent = '#' + orderId;
    document.getElementById('returnCustomerName').textContent = customerName;
    document.getElementById('returnDeliveryAddress').textContent = deliveryAddress;
    document.getElementById('returnTotalAmount').textContent = totalAmount;
    document.getElementById('returnOrderDate').textContent = orderDate;
    
    // Reset form
    document.getElementById('returnReason').value = '';
    document.getElementById('customReturnReason').value = '';
    document.getElementById('customReturnReasonDiv').style.display = 'none';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    modal.show();
}

function toggleCustomReturnReason() {
    const reason = document.getElementById('returnReason').value;
    const customDiv = document.getElementById('customReturnReasonDiv');
    
    if (reason === 'Other') {
        customDiv.style.display = 'block';
        document.getElementById('customReturnReason').required = true;
    } else {
        customDiv.style.display = 'none';
        document.getElementById('customReturnReason').required = false;
    }
}

function confirmReturn() {
    const reason = document.getElementById('returnReason').value;
    const customReason = document.getElementById('customReturnReason').value;
    
    if (!reason) {
        Swal.fire({
            icon: 'warning',
            title: 'Required Field',
            text: 'Please select a reason for returning the order'
        });
        return;
    }
    
    if (reason === 'Other' && !customReason.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Required Field',
            text: 'Please specify your reason'
        });
        return;
    }
    
    const finalReason = reason === 'Other' ? customReason : reason;
    
    // Show loading
    Swal.fire({
        title: 'Sending Return Notification...',
        text: 'Please wait while we notify the admin',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`/driver/orders/${returnOrderId}/return`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reason: finalReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Return Notification Sent!',
                text: 'The admin has been notified about the order return.',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                // Close modal and reload page
                const modal = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
                modal.hide();
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to send return notification'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while sending the return notification'
        });
    });
}
</script>
@endsection 
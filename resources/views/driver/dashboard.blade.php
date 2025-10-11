@extends('layouts.driver_mobile')

@section('content')
<div class="text-center mb-4">
    <!-- Banner/UI Reference -->
    <img src="/images/rider_UI.png" alt="Driver UI Reference" style="max-width: 100%; border-radius: 15px; margin-bottom: 24px; box-shadow: 0 4px 16px #dbe7db;">
    <h4 class="fw-bold mt-3" style="color: #356e35; letter-spacing: 1px;">Welcome, {{ Auth::user()->name ?? 'Driver' }}!</h4>
    <p class="text-muted">Your delivery dashboard is ready.</p>
</div>
<div class="card shadow-lg mb-4" style="border: none; border-radius: 16px; background: #f7faf7;">
    <div class="card-body text-center">
        <div style="font-size: 2.3rem; color: #2a7e2a;"><i class="bi bi-truck"></i></div>
        <h5 class="card-title mt-2 mb-2" style="color: #3a5d37; font-weight: 600;">Today's Deliveries</h5>
        <p class="display-5 fw-bold mb-1" style="color: #216f21;">{{ isset($toDeliver) ? $toDeliver->count() : 0 }}</p>
        <div class="small text-muted mb-0">Deliveries assigned to you today</div>
    </div>
</div>

@if(isset($pendingAcceptance) && $pendingAcceptance->count() > 0)
<div class="card shadow-lg mb-4" style="border: none; border-radius: 16px; border-left: 4px solid #ffc107;">
    <div class="card-header" style="background: #fff3cd; border-bottom: 1px solid #dee2e6;">
        <h6 class="mb-0" style="color: #856404; font-weight: 600;">
            <i class="bi bi-clock me-2"></i>Pending Acceptance
        </h6>
    </div>
    <div class="card-body p-0">
        @foreach($pendingAcceptance as $order)
        <div class="border-bottom p-3" style="border-color: #e9ecef !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="color: #2c3e50; font-weight: 600;">
                        Order #{{ $order->id }}
                    </h6>
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-person me-1"></i>{{ $order->user->name ?? 'N/A' }}
                    </p>
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-geo-alt me-1"></i>{{ $order->delivery->delivery_address ?? 'Address not specified' }}
                    </p>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-currency-dollar me-1"></i>₱{{ number_format($order->total_price, 2) }}
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning text-dark mb-2">Pending</span>
                    <br>
                    <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                    <br>
                    <div class="mt-2">
                        @if($order->delivery && $order->delivery->driver_decision === 'accepted')
                            <span class="badge bg-success">Accepted</span>
                        @elseif($order->delivery && $order->delivery->driver_decision === 'declined')
                            <span class="badge bg-danger">Declined</span>
                        @else
                            <button class="btn btn-success btn-sm me-1" onclick="acceptOrder({{ $order->id }})">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="declineOrder({{ $order->id }})">
                                <i class="bi bi-x-circle"></i> Decline
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(isset($toDeliver) && $toDeliver->count() > 0)
<div class="card shadow-lg mb-4" style="border: none; border-radius: 16px;">
    <div class="card-header" style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <h6 class="mb-0" style="color: #3a5d37; font-weight: 600;">
            <i class="bi bi-list-ul me-2"></i>On Delivery
        </h6>
    </div>
    <div class="card-body p-0">
        @foreach($toDeliver as $order)
        <div class="border-bottom p-3" style="border-color: #e9ecef !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="color: #2c3e50; font-weight: 600;">
                        Order #{{ $order->id }}
                    </h6>
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-person me-1"></i>{{ $order->user->name ?? 'N/A' }}
                    </p>
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-geo-alt me-1"></i>{{ $order->delivery->delivery_address ?? 'Address not specified' }}
                    </p>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-currency-dollar me-1"></i>₱{{ number_format($order->total_price, 2) }}
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-info text-white mb-2">On Delivery</span>
                    <br>
                    <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@elseif(!isset($pendingAcceptance) || $pendingAcceptance->count() == 0)
<div class="card shadow-lg mb-4" style="border: none; border-radius: 16px; background: #f8f9fa;">
    <div class="card-body text-center">
        <div style="font-size: 2rem; color: #6c757d;"><i class="bi bi-inbox"></i></div>
        <h6 class="mt-2 mb-1" style="color: #6c757d;">No deliveries assigned</h6>
        <p class="small text-muted mb-0">You have no orders assigned to you at the moment.</p>
    </div>
</div>
@endif
<ul class="list-group mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center" style="font-size: 1.08rem;">
        <span class="fw-semibold"><i class="bi bi-truck me-2"></i>Go to your Orders</span>
        <a href="{{ route('driver.orders.index') }}" class="btn btn-success btn-sm px-3"><i class="bi bi-chevron-right"></i></a>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-person me-2"></i>Account/Profile</span>
        <a href="{{ route('driver.profile') }}" class="btn btn-outline-success btn-sm px-3"><i class="bi bi-chevron-right"></i></a>
    </li>
</ul>

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
</script>
@endsection

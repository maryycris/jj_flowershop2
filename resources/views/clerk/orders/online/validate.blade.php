@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #e6f0e6;">
                        <span class="badge bg-warning text-dark">Pending</span>
                        <div class="ms-2 small text-muted">{{ sprintf('%05d', $order->id) }}</div>
                        <div class="ms-2 small text-muted">OUT / 0001</div>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-light d-flex align-items-center" style="border:1px solid #e6e6e6;" onclick="showMoves()">
                                <i class="bi bi-list me-1"></i>
                                Moves
                            </button>
                        </div>
                    </div>

                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="border-bottom:1px solid #e6f0e6;">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">Validate</button>
                        <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                            <a href="{{ route('clerk.orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" id="readyBtn" onclick="markAsReady()">Ready</button>
                            <button type="button" class="btn btn-sm btn-light" id="doneBtn" onclick="markAsDone()" style="display: none;">Done</button>
                        </div>
                    </div>

                    <div class="px-3 pt-3 pb-4">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="p-3 fw-semibold">INVENTORY / OUT / 0001</div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 fw-semibold">Delivery Address</div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 small"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 small">
                                    @if($order->delivery)
                                        <div class="mb-1">{{ $order->delivery->delivery_address }}</div>
                                    @endif
                                    <div class="mt-2"><span class="fw-semibold">Schedule Date:</span> <span class="text-muted">{{ optional($order->created_at)->format('m/d/Y') }}</span></div>
                                    <div class="mt-1"><span class="fw-semibold">Order Number:</span> <span class="text-muted">{{ sprintf('%05d', $order->id) }}</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Operations</div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th>Product</th>
                                            <th>Demand</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $product)
                                            @php
                                                $demand = (int) ($product->pivot->quantity ?? 0);
                                                $stockAvailable = (int) ($product->stock ?? 0);
                                                $quantityToProvide = max(0, min($demand, $stockAvailable));
                                                $isInsufficientStock = $stockAvailable < $demand;
                                            @endphp
                                            <tr class="{{ $isInsufficientStock ? 'table-warning' : '' }}">
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $demand }}</td>
                                                <td>
                                                    <span class="{{ $isInsufficientStock ? 'text-warning' : '' }}">
                                                        {{ $quantityToProvide }}
                                                        @if($isInsufficientStock)
                                                            <small class="text-muted">(Insufficient stock: {{ $stockAvailable }} available)</small>
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="confirmModalLabel">Are you sure you want to proceed?</div>
                <div class="mb-3 text-muted">
                    <small>This will validate the order and assign a driver for delivery.</small>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <form method="POST" action="{{ route('clerk.orders.online.validate.confirm', $order) }}" class="m-0" id="validateForm">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="showValidateAlert()">Confirm</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Moves Modal -->
<div class="modal fade" id="movesModal" tabindex="-1" aria-labelledby="movesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movesModalLabel">Order History & Moves</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="timeline">
                    @if($order->statusHistories)
                        @foreach($order->statusHistories->sortBy('created_at') as $history)
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ ucfirst($history->status) }}</div>
                                        <div class="text-muted small">{{ $history->created_at->format('M d, Y g:i A') }}</div>
                                        @if($history->notes)
                                            <div class="text-muted small">{{ $history->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted">No history available for this order.</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Ready Confirmation Modal -->
<div class="modal fade" id="readyModal" tabindex="-1" aria-labelledby="readyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="readyModalLabel">Mark Order as Ready?</div>
                <div class="mb-3 text-muted">
                    <small>This will mark the order as ready for delivery and assign a driver.</small>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <form method="POST" action="{{ route('clerk.orders.mark-ready', $order) }}" class="m-0" id="readyForm">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="showReadyAlert()">Confirm Ready</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Done Confirmation Modal -->
<div class="modal fade" id="doneModal" tabindex="-1" aria-labelledby="doneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="doneModalLabel">Complete Order Processing?</div>
                <div class="mb-3 text-muted">
                    <small>This will complete the order processing and mark it as done.</small>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <form method="POST" action="{{ route('clerk.orders.mark-done', $order) }}" class="m-0" id="doneForm">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="showDoneAlert()">Complete</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showMoves() {
    const movesModal = new bootstrap.Modal(document.getElementById('movesModal'));
    movesModal.show();
}

function markAsReady() {
    const readyModal = new bootstrap.Modal(document.getElementById('readyModal'));
    readyModal.show();
}

function markAsDone() {
    const doneModal = new bootstrap.Modal(document.getElementById('doneModal'));
    doneModal.show();
}

function showValidateAlert() {
    event.preventDefault(); // Prevent form submission
    
    Swal.fire({
        title: 'Validating Order...',
        text: 'Order is being validated and moved to Ready status',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Submit the form after the alert
        document.getElementById('validateForm').submit();
    });
}

function showReadyAlert() {
    event.preventDefault(); // Prevent form submission
    
    Swal.fire({
        title: 'Marking as Ready...',
        text: 'Order is being marked as ready for delivery',
        icon: 'success',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Submit the form after the alert
        document.getElementById('readyForm').submit();
    });
}

function showDoneAlert() {
    event.preventDefault(); // Prevent form submission
    
    Swal.fire({
        title: 'Completing Order...',
        text: 'Order processing is being completed',
        icon: 'success',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Submit the form after the alert
        document.getElementById('doneForm').submit();
    });
}

// Update button states based on order status
document.addEventListener('DOMContentLoaded', function() {
    const orderStatus = '{{ $order->order_status ?? $order->status }}';
    const readyBtn = document.getElementById('readyBtn');
    const doneBtn = document.getElementById('doneBtn');
    
    if (orderStatus === 'approved') {
        readyBtn.style.display = 'inline-block';
        readyBtn.disabled = false;
    } else if (orderStatus === 'on_delivery') {
        readyBtn.style.display = 'none';
        doneBtn.style.display = 'inline-block';
        doneBtn.disabled = false;
    } else if (orderStatus === 'completed') {
        readyBtn.style.display = 'none';
        doneBtn.style.display = 'inline-block';
        doneBtn.disabled = true;
        doneBtn.textContent = 'Completed';
    }
});
</script>

<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 18px;
    bottom: -12px;
    width: 2px;
    background: #e9ecef;
}

.timeline-marker {
    flex-shrink: 0;
}
</style>
@endsection




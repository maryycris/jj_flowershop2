@extends('layouts.admin_app')

@section('content')
<div class="container-fluid" style="margin-left: 0; padding-left: 20px; padding-right: 20px;">
    <!-- Centered Header -->
    <div class="text-center mb-4">
        <h2 class="fw-bold text-success display-4">
            <i class="fas fa-undo me-3"></i>Return Management
        </h2>
        <p class="text-muted fs-5">Monitor and manage returned orders efficiently</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <button class="btn btn-outline-success btn-lg" onclick="refreshReturns()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <button class="btn btn-success btn-lg" onclick="exportReturns()">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Return Analytics Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-chart-line me-2"></i>Return Analytics Overview
                    </h5>
                    <p class="card-text text-muted">
                        View statistics, process refunds, and track return patterns to improve customer satisfaction.
                    </p>
                    <div class="row text-center">
                        <div class="col-3">
                            <h6 class="text-muted">This Month</h6>
                            <h4 class="text-success">{{ $returnStats['total_returned'] }}</h4>
                        </div>
                        <div class="col-3">
                            <h6 class="text-muted">Pending Review</h6>
                            <h4 class="text-warning">{{ $returnStats['pending_review'] }}</h4>
                        </div>
                        <div class="col-3">
                            <h6 class="text-muted">Approved</h6>
                            <h4 class="text-info">{{ $returnStats['approved'] }}</h4>
                        </div>
                        <div class="col-3">
                            <h6 class="text-muted">Resolved</h6>
                            <h4 class="text-success">{{ $returnStats['resolved'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-3" id="returnTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-returns" type="button" role="tab">
                All Returns ({{ $returnStats['total_returned'] }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-returns" type="button" role="tab">
                Pending Review ({{ $returnStats['pending_review'] }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved-returns" type="button" role="tab">
                Approved ({{ $returnStats['approved'] }})
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="returnTabsContent">
        <!-- All Returns -->
        <div class="tab-pane fade show active" id="all-returns" role="tabpanel">
            @include('admin.returns.partials.returns_table', ['orders' => $returnedOrders, 'filter' => 'all'])
        </div>
        
        <!-- Pending Returns -->
        <div class="tab-pane fade" id="pending-returns" role="tabpanel">
            @include('admin.returns.partials.returns_table', ['orders' => $pendingOrders, 'filter' => 'pending'])
        </div>
        
        <!-- Approved Returns -->
        <div class="tab-pane fade" id="approved-returns" role="tabpanel">
            @include('admin.returns.partials.returns_table', ['orders' => $approvedOrders, 'filter' => 'approved'])
        </div>
    </div>

    <!-- Status Cards at Bottom -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="text-center text-success mb-4">
                <i class="fas fa-chart-bar me-2"></i>Return Status Overview
                <small class="text-muted d-block mt-1">Click on any status card to filter returns</small>
            </h4>
            <div class="row justify-content-center">
                <div class="col-md-2 col-6 mb-3">
                    <div class="card status-card-clickable" style="background-color: #ffc107; border: 2px solid #ffc107; cursor: pointer;" onclick="filterByStatus('all')" data-status="all">
                        <div class="card-body text-center">
                            <h4 class="mb-1 text-dark fw-bold">{{ $returnStats['total_returned'] }}</h4>
                            <small class="text-dark fw-semibold">Total Returned</small>
                            <div class="mt-1">
                                <i class="fas fa-mouse-pointer text-dark" style="font-size: 0.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card status-card-clickable" style="background-color: #17a2b8; border: 2px solid #17a2b8; cursor: pointer;" onclick="filterByStatus('pending')" data-status="pending">
                        <div class="card-body text-center">
                            <h4 class="mb-1 text-white fw-bold">{{ $returnStats['pending_review'] }}</h4>
                            <small class="text-white fw-semibold">Pending Review</small>
                            <div class="mt-1">
                                <i class="fas fa-mouse-pointer text-white" style="font-size: 0.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card status-card-clickable" style="background-color: #28a745; border: 2px solid #28a745; cursor: pointer;" onclick="filterByStatus('approved')" data-status="approved">
                        <div class="card-body text-center">
                            <h4 class="mb-1 text-white fw-bold">{{ $returnStats['approved'] }}</h4>
                            <small class="text-white fw-semibold">Approved</small>
                            <div class="mt-1">
                                <i class="fas fa-mouse-pointer text-white" style="font-size: 0.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card status-card-clickable" style="background-color: #dc3545; border: 2px solid #dc3545; cursor: pointer;" onclick="filterByStatus('rejected')" data-status="rejected">
                        <div class="card-body text-center">
                            <h4 class="mb-1 text-white fw-bold">{{ $returnStats['rejected'] }}</h4>
                            <small class="text-white fw-semibold">Rejected</small>
                            <div class="mt-1">
                                <i class="fas fa-mouse-pointer text-white" style="font-size: 0.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card status-card-clickable" style="background-color: #6c757d; border: 2px solid #6c757d; cursor: pointer;" onclick="filterByStatus('resolved')" data-status="resolved">
                        <div class="card-body text-center">
                            <h4 class="mb-1 text-white fw-bold">{{ $returnStats['resolved'] }}</h4>
                            <small class="text-white fw-semibold">Resolved</small>
                            <div class="mt-1">
                                <i class="fas fa-mouse-pointer text-white" style="font-size: 0.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Action Modal -->
<div class="modal fade" id="returnActionModal" tabindex="-1" aria-labelledby="returnActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnActionModalLabel">Return Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnActionForm">
                    @csrf
                    <input type="hidden" id="orderId" name="order_id">
                    
                    <div class="mb-3">
                        <label for="return_status" class="form-label">Action</label>
                        <select class="form-select" id="return_status" name="return_status" required>
                            <option value="">Select action...</option>
                            <option value="approved">Approve Return</option>
                            <option value="rejected">Reject Return</option>
                            <option value="resolved">Mark as Resolved</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                  placeholder="Add notes about this return action..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitReturnAction()">Submit Action</button>
            </div>
        </div>
    </div>
</div>

<!-- Refund Processing Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="refundForm">
                    @csrf
                    <input type="hidden" id="refundOrderId" name="order_id">
                    
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="refund_amount" name="refund_amount" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_method" class="form-label">Refund Method</label>
                        <select class="form-select" id="refund_method" name="refund_method" required>
                            <option value="">Select method...</option>
                            <option value="original_payment">Original Payment Method</option>
                            <option value="store_credit">Store Credit</option>
                            <option value="cash">Cash Refund</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">Refund Reason</label>
                        <input type="text" class="form-control" id="refund_reason" name="refund_reason" 
                               placeholder="Reason for refund amount..." required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processRefund()">Process Refund</button>
            </div>
        </div>
    </div>
</div>

<script>
function refreshReturns() {
    location.reload();
}

function exportReturns() {
    // Implement export functionality
    alert('Export functionality will be implemented in Phase 3');
}

// Filter returns by status
function filterByStatus(status) {
    // Remove active class from all cards
    document.querySelectorAll('.status-card-clickable').forEach(card => {
        card.classList.remove('status-card-active');
    });
    
    // Add active class to clicked card
    event.currentTarget.classList.add('status-card-active');
    
    // Show/hide appropriate tab content
    if (status === 'all') {
        // Show all returns tab
        document.getElementById('all-tab').click();
    } else if (status === 'pending') {
        // Show pending returns tab
        document.getElementById('pending-tab').click();
    } else if (status === 'approved') {
        // Show approved returns tab
        document.getElementById('approved-tab').click();
    } else {
        // For rejected and resolved, show all tab for now
        // You can add more tabs later if needed
        document.getElementById('all-tab').click();
    }
    
    // Scroll to the table
    document.querySelector('.nav-tabs').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

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
@endsection

<style>
/* Fix sidebar overlap issue - Use existing layout structure */
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

.col-12, .col-md-2, .col-md-4, .col-md-8, .col-6, .col-3 {
    padding-left: 10px !important;
    padding-right: 10px !important;
}

/* Ensure text visibility in status cards */
.card h4 {
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    font-size: 1.8rem;
    font-weight: 700;
}

.card small {
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    font-size: 1rem;
    font-weight: 600;
}

/* Status cards at bottom */
.mt-5 .card {
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.mt-5 .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

/* Clickable status cards */
.status-card-clickable {
    transition: all 0.3s ease;
    cursor: pointer;
}

.status-card-clickable:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    opacity: 0.9;
}

.status-card-clickable:active {
    transform: translateY(-1px) scale(0.98);
}

.status-card-active {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    border-width: 3px !important;
}

/* Add pulse animation for active cards */
@keyframes pulse-active {
    0% { box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    50% { box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
    100% { box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
}

.status-card-active {
    animation: pulse-active 2s infinite;
}

/* Improve badge visibility */
.badge {
    font-weight: 600;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

/* Ensure table text is readable */
.table td {
    vertical-align: middle;
}

.table th {
    font-weight: 600;
    background-color: #d4edda !important;
    color: #155724 !important;
}

/* Improve button visibility */
.btn-group .btn {
    font-weight: 500;
}

/* Ensure nav tabs are readable */
.nav-tabs .nav-link {
    font-weight: 500;
    color: #495057;
}

.nav-tabs .nav-link.active {
    color: #000;
    font-weight: 600;
}

/* Responsive adjustments - work with existing layout */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    
    .col-md-2 {
        margin-bottom: 15px;
    }
}

/* Ensure proper spacing */
.text-center {
    padding: 0 10px;
}

/* Fix any overflow issues */
.card {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Let the existing admin layout handle positioning */
</style>

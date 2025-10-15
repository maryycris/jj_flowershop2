@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Reports <span class="badge bg-warning" id="pendingCountBadge" style="background-color: #ff8c00 !important;">0 Pending</span></h2>
    </div>

    @if($pendingChanges->count() > 0)
        <div class="row">
            @foreach($pendingChanges as $change)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                @if($change->action === 'edit')
                                    <i class="fas fa-edit text-primary"></i> Edit Request
                                @else
                                    <i class="fas fa-trash text-danger"></i> Delete Request
                                @endif
                            </h6>
                            <small class="text-muted">{{ $change->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $change->product->name ?? 'Product Deleted' }}</h6>
                            <p class="card-text">
                                <strong>Product Code:</strong> {{ $change->product->code ?? $change->product->id ?? 'N/A' }}<br>
                                <strong>Category:</strong> {{ $change->product->category ?? 'N/A' }}<br>
                                <strong>Submitted by:</strong> {{ $change->submittedBy->name ?? 'Unknown' }}
                            </p>
                            
                            @if(!$change->product)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> This product has been deleted and cannot be processed.
                                </div>
                            @elseif($change->action === 'edit' && $change->changes)
                                <div class="mt-3">
                                    <h6>Changes:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Old Value</th>
                                                    <th>New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($change->changes as $field => $newValue)
                                                    @php
                                                        $oldValue = $change->product->$field ?? '-';
                                                        $fieldNames = [
                                                            'name' => 'Name',
                                                            'category' => 'Category',
                                                            'price' => 'Selling Price',
                                                            'cost_price' => 'Cost Price',
                                                            'reorder_min' => 'Reorder Min',
                                                            'reorder_max' => 'Reorder Max',
                                                            'stock' => 'Qty On Hand',
                                                            'qty_consumed' => 'Qty Consumed',
                                                            'qty_damaged' => 'Qty Damaged',
                                                            'qty_sold' => 'Qty Sold'
                                                        ];
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $fieldNames[$field] ?? ucfirst($field) }}</td>
                                                        <td>{{ $oldValue }}</td>
                                                        <td class="text-success">{{ $newValue }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            @if($change->product)
                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-success btn-sm w-100" onclick="approveChange({{ $change->id }})">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-danger btn-sm w-100" onclick="rejectChange({{ $change->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-ban"></i> Cannot Process - Product Deleted
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Pending Changes</h4>
            <p class="text-muted">All inventory changes have been reviewed.</p>
        </div>
    @endif
</div>

<!-- Admin Notes Modal -->
<div class="modal fade" id="adminNotesModal" tabindex="-1" aria-labelledby="adminNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminNotesModalLabel">Add Admin Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminNotesForm">
                    <input type="hidden" id="changeId" name="change_id">
                    <input type="hidden" id="actionType" name="action_type">
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="adminNotes" name="admin_notes" rows="3" placeholder="Add any notes about this decision..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAction()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentChangeId = null;
let currentActionType = null;

function approveChange(changeId) {
    currentChangeId = changeId;
    currentActionType = 'approve';
    document.getElementById('changeId').value = changeId;
    document.getElementById('actionType').value = 'approve';
    document.getElementById('adminNotesModalLabel').textContent = 'Approve Change';
    document.getElementById('adminNotes').placeholder = 'Add any notes about approving this change...';
    
    const modal = new bootstrap.Modal(document.getElementById('adminNotesModal'));
    modal.show();
}

function rejectChange(changeId) {
    currentChangeId = changeId;
    currentActionType = 'reject';
    document.getElementById('changeId').value = changeId;
    document.getElementById('actionType').value = 'reject';
    document.getElementById('adminNotesModalLabel').textContent = 'Reject Change';
    document.getElementById('adminNotes').placeholder = 'Add any notes about rejecting this change...';
    
    const modal = new bootstrap.Modal(document.getElementById('adminNotesModal'));
    modal.show();
}

function submitAction() {
    const changeId = document.getElementById('changeId').value;
    const actionType = document.getElementById('actionType').value;
    const adminNotes = document.getElementById('adminNotes').value;
    
    const url = actionType === 'approve' 
        ? `/admin/inventory/approve/${changeId}`
        : `/admin/inventory/reject/${changeId}`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            admin_notes: adminNotes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('adminNotesModal'));
            modal.hide();
            
            // Remove the card from the page
            const card = document.querySelector(`[onclick*="${changeId}"]`).closest('.col-md-6');
            if (card) {
                card.remove();
            }
            
            // Update pending count
            updatePendingCount();
            
            // Show success message
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing request. Please try again.');
    });
}

function updatePendingCount() {
    fetch('/admin/inventory/pending-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('pendingCountBadge');
            if (data.count > 0) {
                badge.textContent = `${data.count} Pending`;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        });
}

// Update pending count on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePendingCount();
});
</script>
@endsection

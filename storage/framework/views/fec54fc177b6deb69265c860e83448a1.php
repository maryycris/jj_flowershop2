<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Event Management Dashboard</h2>
            <p class="text-muted mb-0">Manage and process all event bookings</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="refreshEvents()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <button class="btn btn-success btn-sm" onclick="exportEvents()">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Events</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($events->total()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Events</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($events->where('status', 'pending')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Confirmed Events</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($events->where('status', 'confirmed')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Events</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($events->where('status', 'completed')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Event Filters</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter" onchange="filterEvents()">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="typeFilter" class="form-label">Event Type</label>
                    <select class="form-select" id="typeFilter" onchange="filterEvents()">
                        <option value="">All Types</option>
                        <option value="wedding">Wedding</option>
                        <option value="birthday">Birthday</option>
                        <option value="anniversary">Anniversary</option>
                        <option value="corporate">Corporate</option>
                        <option value="funeral">Funeral</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFilter" class="form-label">Date Range</label>
                    <input type="date" class="form-control" id="dateFilter" onchange="filterEvents()">
                </div>
                <div class="col-md-3">
                    <label for="searchFilter" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Search events..." onkeyup="filterEvents()">
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Event Bookings</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleView()">
                    <i class="bi bi-grid-3x3-gap" id="viewToggleIcon"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="eventsTable">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>Order ID</th>
                            <th>Event Type</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Venue</th>
                            <th>Payment Status</th>
                            <th>Event Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <?php if($event->order_id): ?>
                                    <span class="badge bg-primary"><?php echo e($event->order_id); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">EVT-<?php echo e(str_pad($event->id, 6, '0', STR_PAD_LEFT)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-<?php echo e($event->event_type === 'wedding' ? 'heart-fill' : ($event->event_type === 'birthday' ? 'cake2-fill' : ($event->event_type === 'funeral' ? 'flower1' : 'gift-fill'))); ?> me-2 text-success"></i>
                                    <?php echo e(ucfirst($event->event_type)); ?>

                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong><?php echo e(\Carbon\Carbon::parse($event->event_date)->format('M d, Y')); ?></strong>
                                    <?php if($event->event_time): ?>
                                        <br><small class="text-muted"><?php echo e(\Carbon\Carbon::parse($event->event_time)->format('g:i A')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong><?php echo e($event->user->name ?? 'N/A'); ?></strong>
                                    <?php if($event->user->email): ?>
                                        <br><small class="text-muted"><?php echo e($event->user->email); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo e(Str::limit($event->venue, 25)); ?></td>
                            <td>
                                <?php if($event->payment_status === 'paid'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Paid
                                    </span>
                                <?php elseif($event->payment_status === 'partial'): ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i>Partial
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Unpaid
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger'))); ?>">
                                    <?php echo e(ucfirst($event->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e($event->created_at->format('M d, Y')); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('clerk.events.show', $event)); ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" title="Update Status">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo e($event->id); ?>, 'confirmed')">
                                                <i class="bi bi-check-circle text-info me-2"></i>Confirm
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo e($event->id); ?>, 'completed')">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>Complete
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo e($event->id); ?>, 'cancelled')">
                                                <i class="bi bi-x-circle text-danger me-2"></i>Cancel
                                            </a></li>
                                        </ul>
                                    </div>
                                    <a href="<?php echo e(route('clerk.events.invoice.view', $event->id)); ?>" class="btn btn-sm btn-outline-secondary" target="_blank" title="View Invoice">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x fa-3x text-muted mb-3"></i>
                                <br>No event bookings found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <?php echo e($events->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update event status
function updateStatus(eventId, status) {
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    
    if (confirm(`Are you sure you want to ${statusText.toLowerCase()} this event?`)) {
        // Show loading state
        const button = event.target.closest('.dropdown-item');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
        button.disabled = true;
        
        fetch(`/clerk/events/${eventId}/status`, {
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
                // Show success message
                showAlert('success', `Event status updated to ${statusText} successfully!`);
                // Reload page after short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', 'Error updating status: ' + (data.message || 'Unknown error'));
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error updating status. Please try again.');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Filter events
function filterEvents() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const table = document.getElementById('eventsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const status = row.cells[6].textContent.toLowerCase();
        const type = row.cells[1].textContent.toLowerCase();
        const date = row.cells[2].textContent;
        const searchText = row.textContent.toLowerCase();
        
        let showRow = true;
        
        if (statusFilter && !status.includes(statusFilter)) showRow = false;
        if (typeFilter && !type.includes(typeFilter)) showRow = false;
        if (dateFilter && !date.includes(dateFilter)) showRow = false;
        if (searchFilter && !searchText.includes(searchFilter)) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
    }
}

// Refresh events
function refreshEvents() {
    location.reload();
}

// Export events
function exportEvents() {
    showAlert('info', 'Export functionality will be available soon!');
}

// Toggle view (for future card view implementation)
function toggleView() {
    showAlert('info', 'Card view will be available soon!');
}

// Show alert messages
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
    console.log('Clerk Events Dashboard loaded');
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.text-xs {
    font-size: 0.7rem;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/events/index.blade.php ENDPATH**/ ?>
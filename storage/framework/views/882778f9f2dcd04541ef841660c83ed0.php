

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Invoices
                </h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print List
                    </button>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by name, invoice number, or SO number...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="ready">Ready</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Invoice List (<?php echo e($invoices->count()); ?> invoices)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="invoicesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Invoice Number</th>
                                    <th>Invoice Date</th>
                                    <th>Source Document</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="invoice-row" data-name="<?php echo e(strtolower($invoice['name'])); ?>" 
                                    data-invoice="<?php echo e(strtolower($invoice['invoice_number'])); ?>" 
                                    data-so="<?php echo e(strtolower($invoice['source_document'])); ?>"
                                    data-status="<?php echo e($invoice['status']); ?>"
                                    data-date="<?php echo e($invoice['invoice_date']); ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <?php echo e(substr($invoice['name'], 0, 1)); ?>

                                            </div>
                                            <div>
                                                <strong><?php echo e($invoice['name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-primary"><?php echo e($invoice['invoice_number']); ?></span>
                                    </td>
                                    <td><?php echo e($invoice['invoice_date']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($invoice['source_document']); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                            $statusClass = 'bg-warning text-dark';
                                            switch($invoice['status']) {
                                                case 'ready':
                                                    $statusClass = 'bg-warning text-dark';
                                                    break;
                                                case 'paid':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'overdue':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                case 'draft':
                                                    $statusClass = 'bg-secondary';
                                                    break;
                                            }
                                        ?>
                                        <span class="badge <?php echo e($statusClass); ?>"><?php echo e(ucfirst($invoice['status'])); ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-success">₱<?php echo e(number_format($invoice['total'], 2)); ?></strong>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('clerk.invoices.show', $invoice['order']->id)); ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Invoice">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if(strtolower($invoice['order']->payment_method) === 'cod' && $invoice['order']->invoice_status === 'ready'): ?>
                                            <a href="<?php echo e(route('clerk.payment.form', $invoice['order']->id)); ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Register Payment">
                                                <i class="bi bi-credit-card"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('clerk.invoices.download', $invoice['order']->id)); ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">No invoices found</p>
                                            <small>Invoices will appear here once orders are validated</small>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
}

.invoice-row {
    cursor: pointer;
    transition: background-color 0.2s;
}

.invoice-row:hover {
    background-color: #f8f9fa;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make rows clickable
    document.querySelectorAll('.invoice-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons
            if (!e.target.closest('.btn-group')) {
                const viewBtn = this.querySelector('a[title="View Invoice"]');
                if (viewBtn) {
                    window.location.href = viewBtn.href;
                }
            }
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        applyFilters();
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        applyFilters();
    });

    // Date filter
    document.getElementById('dateFilter').addEventListener('change', function() {
        applyFilters();
    });
});

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    document.querySelectorAll('.invoice-row').forEach(row => {
        const name = row.dataset.name;
        const invoice = row.dataset.invoice;
        const so = row.dataset.so;
        const status = row.dataset.status;
        const date = row.dataset.date;
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !name.includes(searchTerm) && !invoice.includes(searchTerm) && !so.includes(searchTerm)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        // Date filter
        if (dateFilter && date !== dateFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/invoices/index.blade.php ENDPATH**/ ?>
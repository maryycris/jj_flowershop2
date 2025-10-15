

<?php $__env->startSection('title', 'Invoice Management'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Invoice Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" onclick="refreshInvoices()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="invoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($invoice->invoice_number); ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('clerk.orders.show', $invoice->order_id)); ?>" class="text-primary">
                                            #<?php echo e($invoice->order_id); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($invoice->order->user->name); ?></td>
                                    <td><?php echo e($invoice->created_at->format('M d, Y')); ?></td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            ₱<?php echo e(number_format($invoice->total_amount, 2)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($invoice->status === 'paid'): ?>
                                            <span class="badge badge-success">Paid</span>
                                        <?php elseif($invoice->status === 'ready'): ?>
                                            <span class="badge badge-warning">Ready</span>
                                        <?php elseif($invoice->status === 'draft'): ?>
                                            <span class="badge badge-secondary">Draft</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($invoice->payment_type === 'online'): ?>
                                            <span class="badge badge-info">Online</span>
                                        <?php else: ?>
                                            <span class="badge badge-primary">COD</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('clerk.invoices.show', $invoice->id)); ?>" 
                                               class="btn btn-sm btn-info" title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if($invoice->status === 'ready' && $invoice->payment_type === 'cod'): ?>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="openPaymentWizard(<?php echo e($invoice->id); ?>)" 
                                                        title="Register Payment">
                                                    <i class="fas fa-credit-card"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <a href="<?php echo e(route('clerk.invoices.show', $invoice->id)); ?>?download=1" 
                                               class="btn btn-sm btn-secondary" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <br>
                                        No invoices found
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

<!-- Payment Wizard Modal -->
<div class="modal fade" id="paymentWizardModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Payment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <?php echo csrf_field(); ?>
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
                                       step="0.01" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="<?php echo e(date('Y-m-d')); ?>" required>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="validatePayment()">
                    <i class="fas fa-check"></i> Validate Payment
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
let currentInvoiceId = null;

function openPaymentWizard(invoiceId) {
    currentInvoiceId = invoiceId;
    $('#paymentWizardModal').modal('show');
}

function validatePayment() {
    if (!currentInvoiceId) {
        alert('No invoice selected');
        return;
    }

    const formData = new FormData(document.getElementById('paymentForm'));
    
    // Show loading state
    const validateBtn = document.querySelector('button[onclick="validatePayment()"]');
    const originalText = validateBtn.innerHTML;
    validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    validateBtn.disabled = true;

    fetch(`/clerk/invoices/${currentInvoiceId}/register-payment`, {
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

function refreshInvoices() {
    location.reload();
}

// Initialize DataTable if needed
$(document).ready(function() {
    $('#invoicesTable').DataTable({
        "pageLength": 25,
        "order": [[ 3, "desc" ]]
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/invoices/index.blade.php ENDPATH**/ ?>
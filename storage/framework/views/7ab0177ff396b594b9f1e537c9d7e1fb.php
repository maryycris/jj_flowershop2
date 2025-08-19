

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">My Orders</h4>
    <span class="badge bg-primary"><?php echo e($deliveries->count()); ?> assigned</span>
</div>

<?php if($deliveries->isEmpty()): ?>
<div class="text-center py-5">
    <i class="bi bi-inbox display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No orders assigned yet</h5>
    <p class="text-muted">You'll see your delivery orders here when they're assigned to you.</p>
</div>
<?php else: ?>
<div class="row">
    <?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #<?php echo e($delivery->order->id); ?></h6>
                    <span class="badge bg-<?php echo e($delivery->status === 'completed' ? 'success' : ($delivery->status === 'in_progress' ? 'warning' : 'secondary')); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $delivery->status))); ?>

                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong><?php echo e($delivery->order->user->name); ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Contact:</small><br>
                        <strong><?php echo e($delivery->order->user->contact_number); ?></strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong><?php echo e($delivery->delivery_address ?? 'Address not specified'); ?></strong>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Date:</small><br>
                        <strong><?php echo e(\Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y')); ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Time:</small><br>
                        <strong><?php echo e($delivery->delivery_time ?? 'Not specified'); ?></strong>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('driver.orders.show', $delivery->id)); ?>" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <?php if($delivery->status === 'pending'): ?>
                    <button class="btn btn-success btn-sm" onclick="updateStatus(<?php echo e($delivery->id); ?>, 'in_progress')">
                        <i class="bi bi-play me-1"></i>Start
                    </button>
                    <?php elseif($delivery->status === 'in_progress'): ?>
                    <button class="btn btn-success btn-sm" onclick="updateStatus(<?php echo e($delivery->id); ?>, 'completed')">
                        <i class="bi bi-check me-1"></i>Complete
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<script>
function updateStatus(deliveryId, status) {
    if (confirm('Are you sure you want to update this delivery status?')) {
        fetch(`/driver/deliveries/${deliveryId}/status`, {
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
                alert('Error updating status');
            }
        });
    }
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.driver_mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/driver/orders/index.blade.php ENDPATH**/ ?>
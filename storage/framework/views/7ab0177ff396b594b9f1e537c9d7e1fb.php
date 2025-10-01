

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">My Orders</h4>
    <span class="badge bg-primary"><?php echo e($orders->count()); ?> assigned</span>
</div>

<?php if($orders->isEmpty()): ?>
<div class="text-center py-5">
    <i class="bi bi-inbox display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No orders assigned yet</h5>
    <p class="text-muted">You'll see your delivery orders here when they're assigned to you.</p>
</div>
<?php else: ?>
<div class="row">
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #<?php echo e($order->id); ?></h6>
                    <span class="badge bg-<?php echo e($order->order_status === 'completed' ? 'success' : ($order->order_status === 'on_delivery' ? 'info' : 'secondary')); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $order->order_status))); ?>

                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong><?php echo e($order->user->name ?? 'N/A'); ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Contact:</small><br>
                        <strong><?php echo e($order->user->contact_number ?? 'N/A'); ?></strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong><?php echo e($order->delivery->delivery_address ?? 'Address not specified'); ?></strong>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Total Amount:</small><br>
                        <strong>₱<?php echo e(number_format($order->total_price, 2)); ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Order Date:</small><br>
                        <strong><?php echo e($order->created_at->format('M d, Y')); ?></strong>
                    </div>
                </div>
                
                <?php if($order->delivery && $order->delivery->special_instructions): ?>
                <div class="mb-3">
                    <small class="text-muted">Special Instructions:</small><br>
                    <strong class="text-info"><?php echo e($order->delivery->special_instructions); ?></strong>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('driver.orders.show', $order->id)); ?>" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <?php if($order->order_status === 'on_delivery'): ?>
                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus(<?php echo e($order->id); ?>, 'completed')">
                        <i class="bi bi-check me-1"></i>Mark Complete
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
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.driver_mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/driver/orders/index.blade.php ENDPATH**/ ?>
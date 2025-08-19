

<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h4 class="fw-bold">Welcome, <?php echo e(Auth::user()->name ?? 'Driver'); ?>!</h4>
    <p class="text-muted">Here is your dashboard overview.</p>
</div>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5 class="card-title mb-2"><i class="bi bi-truck me-2"></i>Today's Deliveries</h5>
        <p class="display-6 fw-bold"><?php echo e(isset($toDeliver) ? $toDeliver->count() : 0); ?></p>
        <small class="text-muted">Deliveries assigned to you today</small>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.driver_mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/driver/dashboard.blade.php ENDPATH**/ ?>
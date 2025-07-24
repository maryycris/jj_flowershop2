
<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/loginstyle.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container d-flex flex-column justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4" style="font-weight: 700;">Login as</h2>
        <div class="d-grid gap-3">
            <a href="<?php echo e(route('customer.login')); ?>" class="btn btn-success btn-lg">Customer</a>
            <a href="<?php echo e(route('staff.login')); ?>" class="btn btn-primary btn-lg">Staff</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mobile_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/auth/login.blade.php ENDPATH**/ ?>
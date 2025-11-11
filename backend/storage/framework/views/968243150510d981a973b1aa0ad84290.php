<?php $__env->startSection('content'); ?>
<div class="mx-auto" style="max-width: 400px;">
    <h4 class="fw-bold mb-3"><i class="bi bi-envelope"></i> Forgot Password</h4>
    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('password.email')); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label for="email" class="form-label">E-Mail Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-danger small"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="d-grid mb-2">
            <button type="submit" class="btn btn-success">Send Password Reset Link</button>
        </div>
        <div class="mb-2">
            <a href="<?php echo e(route('login')); ?>" class="small">Back to Login</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.mobile_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_FLOWERSHOP CAPSTONE\backend\../frontend/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>
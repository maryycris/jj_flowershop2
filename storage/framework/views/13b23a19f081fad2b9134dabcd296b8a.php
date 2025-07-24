
<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/loginstyle.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid vh-100 d-flex flex-column justify-content-center align-items-center bg-light">
    <div class="card shadow-lg p-2" style="max-width: 320px; width: 100%; margin-top: 12px; margin-bottom: 12px;">
        <div class="card-body p-2">
            <h2 class="text-center text-success mb-2" style="font-weight: 700;">Verification</h2>
            <p class="text-center text-muted mb-2">Enter the 6-digit code sent to your selected channel to complete registration.</p>
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <?php if(session('sms_demo_code')): ?>
                <div class="alert alert-info">[SMS Demo] Code: <b><?php echo e(session('sms_demo_code')); ?></b></div>
            <?php endif; ?>
            <?php if(isset($expired) && $expired): ?>
                <div class="alert alert-warning">Your verification code has expired. Please resend a new code.</div>
                <form method="POST" action="<?php echo e(route('verify.code.resend')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-secondary btn-sm">Resend Code</button>
                    </div>
                </form>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('verify.code.submit')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-2">
                        <label for="verification_code" class="form-label visually-hidden">Verification Code</label>
                        <input type="text" name="verification_code" id="verification_code" class="form-control form-control-sm" placeholder="Enter 6-digit code" maxlength="6" required autofocus>
                        <?php $__errorArgs = ['verification_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger" role="alert"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-success btn-sm">Verify</button>
                    </div>
                </form>
                <form method="POST" action="<?php echo e(route('verify.code.resend')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-link btn-sm">Resend Code</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.mobile_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/auth/verify_code.blade.php ENDPATH**/ ?>


<?php $__env->startSection('content'); ?>
<style>
    .change-password-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 32px 32px 24px 32px;
        max-width: 500px;
        margin: 40px auto 0 auto;
    }
    .change-password-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
        text-align: center;
    }
    .change-password-subtitle {
        font-size: 1rem;
        color: #888;
        margin-bottom: 24px;
        text-align: center;
    }
    .form-label {
        font-weight: 500;
        color: #333;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #cfe3d8;
        background: #f8faf8;
    }
    .btn-green {
        background: #7bb47b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 28px;
        font-weight: 600;
        transition: background 0.2s;
        width: 100px;
        margin: 0 auto;
        display: block;
    }
    .btn-green:hover {
        background: #5a9c5a;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-md-9">
            <div class="py-4 px-3 d-flex flex-column align-items-center justify-content-start">
                <div class="change-password-card">
                    <div class="change-password-title">Reset Password</div>
                    <div class="change-password-subtitle">Build a stronger password</div>
                    <?php if(session('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('customer.account.update_password')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Old Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-green">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/account/change_password.blade.php ENDPATH**/ ?>


<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <div class="position-relative d-inline-block">
        <img src="<?php echo e(Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-avatar.png')); ?>" 
             alt="Profile Picture" 
             class="rounded-circle" 
             style="width: 100px; height: 100px; object-fit: cover;">
        <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0" onclick="document.getElementById('profilePicture').click()">
            <i class="bi bi-camera"></i>
        </button>
    </div>
    <h4 class="fw-bold mt-2"><?php echo e(Auth::user()->name); ?></h4>
    <p class="text-muted">Driver</p>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('driver.profile.update')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <input type="file" id="profilePicture" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(this)">
            
            <div class="row mb-3">
                <div class="col-12">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo e(Auth::user()->name); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="<?php echo e(Auth::user()->email); ?>" readonly>
                    <small class="text-muted">Email cannot be changed</small>
                </div>
                <div class="col-6">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo e(Auth::user()->contact_number); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <label for="sex" class="form-label">Gender</label>
                    <select class="form-select" id="sex" name="sex" required>
                        <option value="M" <?php echo e(Auth::user()->sex === 'M' ? 'selected' : ''); ?>>Male</option>
                        <option value="F" <?php echo e(Auth::user()->sex === 'F' ? 'selected' : ''); ?>>Female</option>
                    </select>
                </div>
                <div class="col-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" value="<?php echo e(Auth::user()->username); ?>" readonly>
                    <small class="text-muted">Username cannot be changed</small>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-1"></i>Update Profile
            </button>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('driver.profile.password')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
            </div>
            
            <button type="submit" class="btn btn-warning w-100">
                <i class="bi bi-key me-1"></i>Change Password
            </button>
        </form>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = input.parentElement.querySelector('img');
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.driver_mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/driver/profile.blade.php ENDPATH**/ ?>
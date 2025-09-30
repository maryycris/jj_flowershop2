<?php $__env->startSection('content'); ?>
<style>
    .profile-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 32px 32px 24px 32px;
        max-width: 600px;
        margin: 40px auto 0 auto;
        position: relative;
    }
    .profile-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
    }
    .profile-section {
        margin-bottom: 18px;
    }
    .profile-label {
        font-weight: 500;
        color: #333;
        min-width: 140px;
        display: inline-block;
    }
    .profile-value {
        color: #222;
        font-style: normal;
    }
    .edit-details-btn {
        background: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 28px;
        font-weight: 600;
        transition: background 0.2s;
        display: block;
        margin: 0 auto;
    }
    .edit-details-btn:hover {
        background: #45a049;
    }
    .profile-image {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #e0e0e0;
        display: inline-block;
    }
    .edit-image-btn {
        background: #e0e0e0;
        color: #444;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        padding: 2px 10px;
        margin-left: 10px;
        margin-top: 8px;
    }
    .edit-image-btn:hover {
        background: #cfe3d8;
    }
</style>

<div class="clerk-dashboard-wrapper d-flex" style="background:#f4faf4;min-height:100vh;">
    <div class="clerk-sidebar p-4 d-flex flex-column align-items-center" style="min-width:220px;max-width:250px;background:#f8f9f4;height:100vh;">
        <div class="mb-4 text-center">
            <?php if($user->profile_picture): ?>
                <img src="<?php echo e(asset('storage/' . $user->profile_picture)); ?>" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #4CAF50;">
            <?php else: ?>
                <i class="bi bi-person-circle" style="font-size:3.5rem;color:#888;"></i>
            <?php endif; ?>
            <div class="fw-semibold mt-2 mb-1"><?php echo e($user->name ?? 'Clerk name'); ?></div>
        </div>
        <div class="w-100">
            <a href="<?php echo e(route('clerk.dashboard')); ?>" class="clerk-sidebar-link d-flex align-items-center mb-3 <?php if(request()->routeIs('clerk.dashboard')): ?> active <?php endif; ?>">Dashboard</a>
            <a href="<?php echo e(route('clerk.profile.edit')); ?>" class="clerk-sidebar-link d-flex align-items-center mb-3 <?php if(request()->routeIs('clerk.profile.edit')): ?> active <?php endif; ?>">Edit profile</a>
            <a href="<?php echo e(route('clerk.notifications.index')); ?>" class="clerk-sidebar-link d-flex align-items-center mb-3 <?php if(request()->routeIs('clerk.notifications.index')): ?> active <?php endif; ?>">Notification</a>
        </div>
    </div>
    
    <div class="flex-grow-1 d-flex justify-content-center align-items-start" style="min-height:100vh; padding: 40px;">
        <div class="container" style="max-width: 800px;">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="profile-card d-flex align-items-start">
                <div class="me-4 d-flex flex-column align-items-center" style="min-width: 90px;">
                    <img id="profileImagePreview" src="<?php echo e($user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://via.placeholder.com/80'); ?>" class="profile-image mb-2" alt="Profile Picture">
                    <button type="button" class="edit-image-btn" data-bs-toggle="modal" data-bs-target="#editImageModal">Edit Image</button>
                </div>
                <div class="flex-grow-1">
                    <div class="profile-title mb-2">My Personal Info</div>
                    <hr>
                    <div class="profile-section">
                        <span class="profile-label">Name:</span> 
                        <span class="profile-value"><?php echo e($user->name ?? 'N/A'); ?></span>
                    </div>
                    <div class="profile-section">
                        <span class="profile-label">Email:</span> 
                        <span class="profile-value"><?php echo e($user->email ?? 'N/A'); ?></span>
                    </div>
                    <div class="profile-section">
                        <span class="profile-label">Contact Number:</span> 
                        <span class="profile-value"><?php echo e($user->contact_number ?? 'N/A'); ?></span>
                    </div>
                    <div class="profile-section">
                        <span class="profile-label">Role:</span> 
                        <span class="profile-value"><?php echo e(ucfirst($user->role) ?? 'N/A'); ?></span>
                    </div>
                    <button class="edit-details-btn" data-bs-toggle="modal" data-bs-target="#editDetailsModal">EDIT DETAILS</button>
                </div>
            </div>

            <hr class="my-4">

            <!-- Change Password Section -->
            <div class="profile-card">
                <div class="profile-title mb-3">Change Password</div>
                <form action="<?php echo e(route('clerk.profile.password')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Details Modal -->
<div class="modal fade" id="editDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Personal Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('clerk.profile.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" value="<?php echo e(old('contact_number', $user->contact_number)); ?>" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('clerk.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                    <input type="file" name="profile_picture" accept="image/*" id="profilePicInputModal" style="display:none;">
                    <label for="profilePicInputModal" id="uploadImageLabel" style="cursor:pointer;">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 32px 48px; border: 2px dashed #4CAF50;">
                            <span style="font-size: 2rem; color: #4CAF50;">+</span>
                            <span style="font-size: 1.2rem; color: #4CAF50; font-weight: 600;">Upload image</span>
                        </div>
                    </label>
                    <img id="imagePreviewModal" src="" style="display:none; margin-top: 18px; max-width: 120px; border-radius: 50%;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="savePicBtnModal" style="display:none;">Save Picture</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.clerk-sidebar-link { color: #222; font-weight: 500; font-size: 1.08rem; text-decoration: none; transition: color 0.18s; border-radius: 6px; padding: 8px 12px; }
.clerk-sidebar-link.active, .clerk-sidebar-link:hover { background: #e6f2e6; color: #385E42 !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Modal image preview and save button logic
    document.getElementById('profilePicInputModal')?.addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('imagePreviewModal');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            document.getElementById('savePicBtnModal').style.display = 'inline-block';
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/profile/edit.blade.php ENDPATH**/ ?>
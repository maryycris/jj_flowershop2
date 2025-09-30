<?php $__env->startSection('content'); ?>
<style>
    .profile-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        padding: 36px 40px 28px 40px;
        max-width: 760px;
        margin: 20px auto 0 auto;
        position: relative;
        border: 1px solid #eef3ef;
    }
    .profile-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 4px;
        color: #2c3e50;
    }
    .profile-section {
        margin-bottom: 18px;
    }
    .profile-label {
        font-weight: 600;
        color: #546e5b;
        min-width: 160px;
        display: inline-block;
    }
    .profile-value {
        color: #1f2d27;
        font-style: normal;
    }
    .edit-details-btn {
        background: #7bb47b;
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 10px 28px;
        font-weight: 700;
        letter-spacing: .2px;
        transition: transform .15s ease, box-shadow .15s ease;
        display: block;
        margin: 6px auto 0 auto;
        box-shadow: 0 6px 14px rgba(122, 179, 122, .25);
    }
    .edit-details-btn:hover {
        background: #5a9c5a;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(122, 179, 122, .32);
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
    .profile-image-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    /* Divider styling */
    .profile-divider { border: 0; height: 1px; background: linear-gradient(to right, transparent, #e7efe7, transparent); margin: 10px 0 20px; }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-3">
            <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-7">
            <div class="py-4 px-3 d-flex flex-column align-items-center justify-content-start">
                <?php if(session('reminder')): ?>
                    <div class="alert alert-warning" style="max-width:700px;margin:20px auto 0 auto;">
                        <?php echo e(session('reminder')); ?>

                    </div>
                <?php endif; ?>
                <div class="profile-card d-flex align-items-start" style="width: 100%; margin: 0 auto;">
                    <!-- Removed profile picture from right side -->
                    <div class="flex-grow-1">
                        <div class="profile-title mb-2">My Personal Info</div>
                        <hr class="profile-divider">
                        <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">First Name:</span> <span class="profile-value"><?php echo e(Auth::user()->first_name ?? 'N/A'); ?></span></div>
                        <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">Last Name:</span> <span class="profile-value"><?php echo e(Auth::user()->last_name ?? 'N/A'); ?></span></div>
                        <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">E-mail:</span> <span class="profile-value"><?php echo e(Auth::user()->email ?? 'N/A'); ?></span></div>
                        <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">Cellphone Number:</span> <span class="profile-value"><?php echo e(Auth::user()->contact_number ?? 'N/A'); ?></span></div>
                        <div class="profile-section" style="margin-bottom: 24px;"><span class="profile-label">Address:</span> <span class="profile-value">
                            <?php
                                $user = Auth::user();
                                $defaultAddress = $user->addresses()->where('is_default', true)->first() ?? $user->addresses()->first();
                                if ($defaultAddress) {
                                    $parts = [];
                                    if (!empty($defaultAddress->street_address)) $parts[] = $defaultAddress->street_address;
                                    if (!empty($defaultAddress->barangay)) $parts[] = $defaultAddress->barangay;
                                    if (!empty($defaultAddress->municipality)) $parts[] = $defaultAddress->municipality;
                                    if (!empty($defaultAddress->city)) $parts[] = $defaultAddress->city;
                                    echo implode(', ', $parts);
                                } else {
                                    $parts = [];
                                    if (!empty($user->street_address)) $parts[] = $user->street_address;
                                    if (!empty($user->barangay)) $parts[] = $user->barangay;
                                    if (!empty($user->municipality)) $parts[] = $user->municipality;
                                    if (!empty($user->city)) $parts[] = $user->city;
                                    echo $parts ? implode(', ', $parts) : (!empty($user->address) ? $user->address : 'N/A');
                                }
                            ?>
                        </span></div>
                        <button class="edit-details-btn" data-bs-toggle="modal" data-bs-target="#editDetailsModal" style="background: #7bb47b; color: #fff; border: none; border-radius: 4px; padding: 8px 28px; font-weight: 600; display: block; margin: 0 auto;">EDIT DETAILS</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Details Modal -->
<div class="modal fade" id="editDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #f8faf8;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Personal Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('customer.account.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
            <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo e(Auth::user()->first_name ?? ''); ?>" required style="text-transform: capitalize;">
                        </div>
                        <div class="col">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo e(Auth::user()->last_name ?? ''); ?>" required style="text-transform: capitalize;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="<?php echo e(Auth::user()->email ?? ''); ?>" readonly required>
                    </div>
                <div class="mb-3">
                        <label class="form-label">Street Address *</label>
                        <input type="text" class="form-control" name="street_address" value="<?php echo e(Auth::user()->street_address ?? ''); ?>" required style="text-transform: capitalize;">
                </div>
                <div class="mb-3">
                        <label class="form-label">Barangay *</label>
                        <input type="text" class="form-control" name="barangay" value="<?php echo e(Auth::user()->barangay ?? ''); ?>" required style="text-transform: capitalize;">
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Municipality *</label>
                            <input type="text" class="form-control" name="municipality" value="<?php echo e(Auth::user()->municipality ?? ''); ?>" required style="text-transform: capitalize;">
                        </div>
                        <div class="col">
                            <label class="form-label">City *</label>
                            <input type="text" class="form-control" name="city" value="<?php echo e(Auth::user()->city ?? ''); ?>" required style="text-transform: capitalize;">
                        </div>
                </div>
                <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" name="contact_number" value="<?php echo e(Auth::user()->contact_number ?? ''); ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green" style="width: 100px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #f8faf8; border-radius: 12px;">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('customer.account.update_picture')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                    <input type="file" name="profile_picture" accept="image/*" id="profilePicInputModal" style="display:none;">
                    <label for="profilePicInputModal" id="uploadImageLabel" style="cursor:pointer;">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 32px 48px; border: 2px dashed #7bb47b;">
                            <span style="font-size: 2rem; color: #7bb47b;">+</span>
                            <span style="font-size: 1.2rem; color: #7bb47b; font-weight: 600;">Upload image</span>
                        </div>
                    </label>
                    <img id="imagePreviewModal" src="" style="display:none; margin-top: 18px; max-width: 120px; border-radius: 50%;" />
                </div>
                <div class="modal-footer" style="border-top: none; justify-content: center;">
                    <button type="submit" class="btn btn-green" id="savePicBtnModal" style="width: 120px; display:none;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body {
        background: #f4faf4 !important;
    }
    .sidebar-links .sidebar-link {
        display: block;
        padding: 8px 20px;
        border-radius: 4px;
        color: #222;
        font-weight: 400;
        text-decoration: none;
        margin-bottom: 2px;
        background: transparent;
        transition: background 0.2s, color 0.2s;
        font-size: 1.08rem;
    }
    .sidebar-links .sidebar-link.active-link {
        background: #cbe7cb;
        color: #222;
        font-weight: 600;
    }
    .sidebar-links .sidebar-link:hover {
        background: #e0f2e0;
        color: #222;
    }
    .sidebar-label {
        margin-bottom: 6px;
        letter-spacing: 0.5px;
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
    .profile-card {
        box-shadow: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent leading spaces and ensure proper case
    function preventLeadingSpaces(input) {
        input.addEventListener('input', function(e) {
            if (e.target.value.startsWith(' ')) {
                e.target.value = e.target.value.trim();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === ' ' && e.target.selectionStart === 0) {
                e.preventDefault();
            }
        });
    }
    
    // Apply to all text inputs and textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(preventLeadingSpaces);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/account/index.blade.php ENDPATH**/ ?>
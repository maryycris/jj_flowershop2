<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-md-9">
            <div class="py-4 px-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">My Address Book</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="fas fa-plus"></i> Add New Address
                    </button>
                </div>

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

                <div class="row">
                    <?php $__empty_1 = true; $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">
                                            <?php echo e($address->label ?? 'Address'); ?>

                                            <?php if($address->is_default): ?>
                                                <span class="badge bg-primary ms-2">Default</span>
                                            <?php endif; ?>
                                        </h5>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAddressModal<?php echo e($address->id); ?>">
                                                        <i class="fas fa-edit me-2"></i> Edit
                                                    </button>
                                                </li>
                                                <?php if(!$address->is_default): ?>
                                                    <li>
                                                        <form action="<?php echo e(route('customer.address_book.set-default', $address)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-star me-2"></i> Set as Default
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <form action="<?php echo e(route('customer.address_book.destroy', $address)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this address?')">
                                                            <i class="fas fa-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="card-text mb-0">
                                        <?php echo e($address->street_address); ?><br>
                                        <?php echo e($address->barangay); ?><br>
                                        <?php echo e($address->municipality); ?><br>
                                        <?php echo e($address->city); ?><br>
                                        <?php if($address->province): ?>
                                            <?php echo e($address->province); ?><br>
                                        <?php endif; ?>
                                        <?php if($address->zip_code): ?>
                                            <?php echo e($address->zip_code); ?><br>
                                        <?php endif; ?>
                                        <?php if($address->landmark): ?>
                                            <strong>Landmark:</strong> <?php echo e($address->landmark); ?><br>
                                        <?php endif; ?>
                                        <?php if($address->special_instructions): ?>
                                            <strong>Instructions:</strong> <?php echo e($address->special_instructions); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Address Modal -->
                        <div class="modal fade" id="editAddressModal<?php echo e($address->id); ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Address</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?php echo e(route('customer.address_book.update', $address)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="label<?php echo e($address->id); ?>" class="form-label">Label (Optional)</label>
                                                <input type="text" class="form-control" id="label<?php echo e($address->id); ?>" name="label" value="<?php echo e($address->label); ?>">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="first_name<?php echo e($address->id); ?>" class="form-label">First Name</label>
                                                    <input type="text" class="form-control" id="first_name<?php echo e($address->id); ?>" name="first_name" value="<?php echo e($address->first_name); ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="last_name<?php echo e($address->id); ?>" class="form-label">Last Name</label>
                                                    <input type="text" class="form-control" id="last_name<?php echo e($address->id); ?>" name="last_name" value="<?php echo e($address->last_name); ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="company<?php echo e($address->id); ?>" class="form-label">Company (Optional)</label>
                                                <input type="text" class="form-control" id="company<?php echo e($address->id); ?>" name="company" value="<?php echo e($address->company); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="street_address<?php echo e($address->id); ?>" class="form-label">Street Address</label>
                                                <input type="text" class="form-control" id="street_address<?php echo e($address->id); ?>" name="street_address" value="<?php echo e($address->street_address); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="barangay<?php echo e($address->id); ?>" class="form-label">Barangay</label>
                                                <input type="text" class="form-control" id="barangay<?php echo e($address->id); ?>" name="barangay" value="<?php echo e($address->barangay); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="municipality<?php echo e($address->id); ?>" class="form-label">Municipality</label>
                                                <input type="text" class="form-control" id="municipality<?php echo e($address->id); ?>" name="municipality" value="<?php echo e($address->municipality); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="city<?php echo e($address->id); ?>" class="form-label">City</label>
                                                <input type="text" class="form-control" id="city<?php echo e($address->id); ?>" name="city" value="<?php echo e($address->city); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="province<?php echo e($address->id); ?>" class="form-label">Province (Optional)</label>
                                                <input type="text" class="form-control" id="province<?php echo e($address->id); ?>" name="province" value="<?php echo e($address->province); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="region<?php echo e($address->id); ?>" class="form-label">Region</label>
                                                <input type="text" class="form-control" id="region<?php echo e($address->id); ?>" name="region" value="<?php echo e($address->region); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="zip_code<?php echo e($address->id); ?>" class="form-label">ZIP Code</label>
                                                <input type="text" class="form-control" id="zip_code<?php echo e($address->id); ?>" name="zip_code" value="<?php echo e($address->zip_code); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone_number<?php echo e($address->id); ?>" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phone_number<?php echo e($address->id); ?>" name="phone_number" value="<?php echo e($address->phone_number); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="landmark<?php echo e($address->id); ?>" class="form-label">Landmark (Optional)</label>
                                                <input type="text" class="form-control" id="landmark<?php echo e($address->id); ?>" name="landmark" value="<?php echo e($address->landmark); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="special_instructions<?php echo e($address->id); ?>" class="form-label">Special Instructions (Optional)</label>
                                                <textarea class="form-control" id="special_instructions<?php echo e($address->id); ?>" name="special_instructions"><?php echo e($address->special_instructions); ?></textarea>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_default<?php echo e($address->id); ?>" name="is_default" value="1" <?php echo e($address->is_default ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="is_default<?php echo e($address->id); ?>">Set as default address</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                You haven't added any addresses yet. Click the "Add New Address" button to add your first address.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('customer.address_book.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="label" class="form-label">Label (Optional)</label>
                        <input type="text" class="form-control" id="label" name="label">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo e(Auth::user()->first_name); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo e(Auth::user()->last_name); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">Company (Optional)</label>
                        <input type="text" class="form-control" id="company" name="company">
                    </div>
                    <div class="mb-3">
                        <label for="street_address" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="street_address" name="street_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                        <input type="text" class="form-control" id="barangay" name="barangay" required>
                    </div>
                    <div class="mb-3">
                        <label for="municipality" class="form-label">Municipality</label>
                        <input type="text" class="form-control" id="municipality" name="municipality">
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">Province (Optional)</label>
                        <input type="text" class="form-control" id="province" name="province">
                    </div>
                    <div class="mb-3">
                        <label for="region" class="form-label">Region</label>
                        <input type="text" class="form-control" id="region" name="region" value="Region VII" required>
                    </div>
                    <div class="mb-3">
                        <label for="zip_code" class="form-label">ZIP Code</label>
                        <input type="text" class="form-control" id="zip_code" name="zip_code" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo e(Auth::user()->contact_number); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="landmark" class="form-label">Landmark (Optional)</label>
                        <input type="text" class="form-control" id="landmark" name="landmark" value="<?php echo e(old('landmark')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                        <textarea class="form-control" id="special_instructions" name="special_instructions"><?php echo e(old('special_instructions')); ?></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1">
                        <label class="form-check-label" for="is_default">Set as default address</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Address</button>
                </div>
            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/address_book/index.blade.php ENDPATH**/ ?>
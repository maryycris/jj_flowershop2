<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.customer.alt_nav', ['active' => 'profile'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .text-capitalize {
        text-transform: capitalize;
    }
    .address-card {
        border: 1px solid #e6efe7;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
    }
    .address-card .card-header {
        background: #eafbe7;
        border-bottom: 1px solid #e6efe7;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
        padding: 14px 16px;
    }
    .address-title {
        font-weight: 700;
        color: #346c43;
        margin: 0;
    }
    .address-card .card-body {
        padding: 16px;
    }
    .set-address-btn {
        background: #7bb47b;
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
    }
    .set-address-btn:hover { background:#5aa65a; }
    /* Smaller add address modal with internal scroll */
    #addAddressModal .modal-dialog { max-width: 520px; }
    #addAddressModal .modal-body { max-height: 60vh; overflow-y: auto; }
    /* Optional thin scrollbar styling */
    #addAddressModal .modal-body::-webkit-scrollbar { width: 6px; }
    #addAddressModal .modal-body::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
    #addAddressModal .modal-body::-webkit-scrollbar-thumb { background: #7bb47b; border-radius: 3px; }
    #addAddressModal .modal-body::-webkit-scrollbar-thumb:hover { background: #5aa65a; }
    /* Mobile page label top-right */
    @media (max-width: 767.98px) {
        .page-label-mobile {
            position: absolute;
            top: 6px;
            left: 30px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #4a9448;
            background: transparent;
            padding: 0;
            border-radius: 0;
            letter-spacing: .2px;
        }
    }
    @media (max-width: 767.98px) {
        .main-content-with-sidebar { margin-left: 0 !important; max-width: 100% !important; }
        /* Add breathing room so the label doesn't collide with the first box */
        #addressBookContent { margin-top: 25px; }
        /* Ensure mobile navbars stay sticky on this page */
        .alt-topbar { position: fixed !important; }
        .mobile-bottom-nav { position: fixed !important; }
        /* Center and size address modals on mobile */
        .modal-dialog { width: 85vw !important; max-width: 85vw !important; margin: 5vh auto !important; }
        .modal { z-index: 6000 !important; }
        .modal-backdrop { z-index: 5990 !important; }
    }
</style>
<div class="container-fluid position-relative">
    <div class="row justify-content-center">
        <div class="d-md-none page-label-mobile">Address Book</div>
        <div class="col-md-3 col-lg-3 d-none d-md-block">
            <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="col-12 col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 25%; max-width: calc(75% - 30px);">
            <div id="addressBookContent" class="py-3 px-3">
                <!-- Location-Based Recommendations Box -->
                <div class="mb-4">
                    <div class="bg-white rounded-4 p-3 shadow-sm" id="locationCard" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1 fw-bold text-success">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location-Based Recommendations
                                </h6>
                                <small class="text-muted" id="locationText">Detecting your location...</small>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="updateLocation()">
                                <i class="fas fa-sync-alt me-1"></i>Update
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Toolbar Add Button removed as requested -->

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
                            <div class="card h-100 address-card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="address-title">
                                            <?php echo e($address->label ?? 'Address'); ?>

                                        </h6>
                                        <?php if($address->is_default): ?>
                                            <span class="badge bg-success">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="set-address-btn btn btn-sm" data-bs-toggle="modal" data-bs-target="#editAddressModal<?php echo e($address->id); ?>">
                                            <i class="fas fa-plus me-1"></i> Set Address
                                        </button>
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
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        <?php echo e($address->street_address); ?><br>
                                        <?php echo e($address->barangay); ?><br>
                                        <?php if($address->municipality): ?>
                                            <?php echo e($address->municipality); ?><br>
                                        <?php endif; ?>
                                        <?php echo e($address->city); ?><br>
                                        <?php if($address->region): ?>
                                            <?php echo e($address->region); ?><br>
                                        <?php endif; ?>
                                        <?php if($address->province && $address->province !== 'Cebu'): ?>
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
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 12px;">
                                    <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e0e0e0;">
                                        <h5 class="modal-title fw-bold">Edit Address</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?php echo e(route('customer.address_book.update', $address)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body" style="padding: 20px;">
                                            <!-- Hide personal info fields; they are not editable here -->
                                            <input type="hidden" name="first_name" value="<?php echo e($address->first_name); ?>">
                                            <input type="hidden" name="last_name" value="<?php echo e($address->last_name); ?>">
                                            <input type="hidden" name="email" value="<?php echo e($address->email ?? Auth::user()->email); ?>">
                                            <input type="hidden" name="company" value="<?php echo e($address->company); ?>">

                                            <div class="mb-3">
                                                <label for="street_address<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">Street Address *</label>
                                                <input type="text" class="form-control text-capitalize" id="street_address<?php echo e($address->id); ?>" name="street_address" value="<?php echo e($address->street_address); ?>" required style="border-radius: 8px; padding: 10px;">
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="zip_code<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">Postal Code *</label>
                                                    <input type="text" class="form-control" id="zip_code<?php echo e($address->id); ?>" name="zip_code" value="<?php echo e($address->zip_code); ?>" required style="border-radius: 8px; padding: 10px;">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="city<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">City *</label>
                                                    <select class="form-select text-capitalize" id="city<?php echo e($address->id); ?>" name="city" required style="border-radius: 8px; padding: 10px;">
                                                        <option value="Cebu City" <?php echo e($address->city == 'Cebu City' ? 'selected' : ''); ?>>Cebu City</option>
                                                        <option value="Mandaue City" <?php echo e($address->city == 'Mandaue City' ? 'selected' : ''); ?>>Mandaue City</option>
                                                        <option value="Lapu-Lapu City" <?php echo e($address->city == 'Lapu-Lapu City' ? 'selected' : ''); ?>>Lapu-Lapu City</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="municipality<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">Municipality</label>
                                                <input type="text" class="form-control text-capitalize" id="municipality<?php echo e($address->id); ?>" name="municipality" value="<?php echo e($address->municipality); ?>" placeholder="Enter municipality" style="border-radius: 8px; padding: 10px;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="region<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">Region / State *</label>
                                                <input type="text" class="form-control" id="region<?php echo e($address->id); ?>" name="region" value="<?php echo e($address->region ?? 'Region VII - Central Visayas'); ?>" required style="border-radius: 8px; padding: 10px;">
                                            </div>

                                            <input type="hidden" name="country" value="Philippines">
                                            <input type="hidden" name="phone_number" value="<?php echo e($address->phone_number ?? Auth::user()->contact_number); ?>">

                                            <div class="mb-3">
                                                <label for="barangay<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">Barangay *</label>
                                                <input type="text" class="form-control text-capitalize" id="barangay<?php echo e($address->id); ?>" name="barangay" value="<?php echo e($address->barangay); ?>" required style="border-radius: 8px; padding: 10px;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="landmark<?php echo e($address->id); ?>" class="form-label fw-semibold" style="font-size: 14px;">Landmark</label>
                                                <input type="text" class="form-control text-capitalize" id="landmark<?php echo e($address->id); ?>" name="landmark" value="<?php echo e($address->landmark); ?>" style="border-radius: 8px; padding: 10px;">
                                            </div>

                                            <input type="hidden" name="province" value="Cebu">
                                        </div>
                                        <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 15px 20px;">
                                            <button type="submit" class="btn w-100 fw-bold" style="background-color: #7bb47b; color: white; border-radius: 8px; padding: 12px;">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12">
                            <div class="alert d-flex align-items-center justify-content-between" style="background-color: #e8f5e8; border-color: #7bb47b; color: #2d5a2d;">
                                <div>
                                    You haven't added any addresses yet. Click the "Add New Address" button to add your first address.
                                </div>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="fas fa-plus me-1"></i> Add New Address
                                </button>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e0e0e0;">
                <h5 class="modal-title fw-bold">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('customer.address_book.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body" style="padding: 20px;">
                    <!-- Hidden personal fields auto-filled from profile -->
                    <input type="hidden" name="first_name" value="<?php echo e(Auth::user()->first_name); ?>">
                    <input type="hidden" name="last_name" value="<?php echo e(Auth::user()->last_name); ?>">
                    <input type="hidden" name="email" value="<?php echo e(Auth::user()->email); ?>">
                    <input type="hidden" name="company" value="">

                    <div class="mb-3">
                        <label for="street_address" class="form-label fw-semibold" style="font-size: 14px;">Street Address *</label>
                        <input type="text" class="form-control text-capitalize" id="street_address" name="street_address" required style="border-radius: 8px; padding: 10px;">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="zip_code" class="form-label fw-semibold" style="font-size: 14px;">Postal Code *</label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" required style="border-radius: 8px; padding: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label fw-semibold" style="font-size: 14px;">City *</label>
                            <select class="form-select text-capitalize" id="city" name="city" required style="border-radius: 8px; padding: 10px;">
                            
                                <option value="Cebu City">Cebu City</option>
                                <option value="Mandaue City">Mandaue City</option>
                                <option value="Lapu-Lapu City">Lapu-Lapu City</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="municipality" class="form-label fw-semibold" style="font-size: 14px;">Municipality</label>
                        <input type="text" class="form-control text-capitalize" id="municipality" name="municipality" placeholder="Enter municipality" style="border-radius: 8px; padding: 10px;">
                    </div>

                    <div class="mb-3">
                        <label for="region" class="form-label fw-semibold" style="font-size: 14px;">Region / State *</label>
                        <input type="text" class="form-control" id="region" name="region" value="Region VII - Central Visayas" required style="border-radius: 8px; padding: 10px;">
                    </div>

                    <input type="hidden" name="country" value="Philippines">
                    <input type="hidden" name="phone_number" value="<?php echo e(Auth::user()->contact_number); ?>">

                    <div class="mb-3">
                        <label for="barangay" class="form-label fw-semibold" style="font-size: 14px;">Barangay *</label>
                        <input type="text" class="form-control text-capitalize" id="barangay" name="barangay" required style="border-radius: 8px; padding: 10px;">
                    </div>

                    <div class="mb-3">
                        <label for="landmark" class="form-label fw-semibold" style="font-size: 14px;">Landmark</label>
                        <input type="text" class="form-control text-capitalize" id="landmark" name="landmark" style="border-radius: 8px; padding: 10px;">
                    </div>

                    <input type="hidden" name="municipality" id="municipality" value="">
                    <input type="hidden" name="province" value="Cebu">
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 15px 20px;">
                    <button type="submit" class="btn w-100 fw-bold" style="background-color: #7bb47b; color: white; border-radius: 8px; padding: 12px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize location detection
    initializeLocationDetection();

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

// Location Detection Functions
function initializeLocationDetection() {
    console.log('Initializing location detection for address book...');

    // Check if geolocation is supported
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Update location via API
                updateLocationData(latitude, longitude);
            },
            function(error) {
                console.log('Geolocation error:', error);
                // Fallback to default location (Cebu)
                updateLocationData(null, null, 'Cebu');
            }
        );
    } else {
        // Fallback to default location (Cebu)
        updateLocationData(null, null, 'Cebu');
    }
}

function updateLocationData(latitude, longitude, city) {
    console.log('Updating location data:', { latitude, longitude, city });

    fetch('<?php echo e(route("geo.location.update")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            latitude: latitude,
            longitude: longitude,
            city: city
        })
    })
    .then(response => {
        console.log('Location update response:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Location update data:', data);
        if (data.success) {
            showLocationCard(data.location);
        } else {
            console.error('Location update failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Error updating location:', error);
        alert('Error updating location. Please try again.');
    })
    .finally(() => {
        // Reset button
        const updateBtn = document.querySelector('button[onclick="updateLocation()"]');
        if (updateBtn) {
            updateBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Update';
            updateBtn.disabled = false;
        }
    });
}

function showLocationCard(location) {
    const locationCard = document.getElementById('locationCard');
    const locationText = document.getElementById('locationText');

    if (locationCard && locationText) {
        locationText.textContent = `Showing recommendations for ${location.city}`;
        locationCard.style.display = 'block';
    }
}

function updateLocation() {
    console.log('Update location clicked');

    // Show loading state
    const updateBtn = document.querySelector('button[onclick="updateLocation()"]');
    if (updateBtn) {
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
        updateBtn.disabled = true;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                console.log('Location obtained:', position.coords);
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                updateLocationData(latitude, longitude);
            },
            function(error) {
                console.error('Geolocation error:', error);
                let errorMessage = 'Unable to get your location. ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Please allow location access and try again.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'Please try again.';
                        break;
                }
                alert(errorMessage);

                // Reset button
                if (updateBtn) {
                    updateBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Update';
                    updateBtn.disabled = false;
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
        // Reset button
        if (updateBtn) {
            updateBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Update';
            updateBtn.disabled = false;
        }
    }
}

// Initialize location detection on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeLocationDetection();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_FLOWERSHOP CAPSTONE\backend\../frontend/resources/views/customer/address_book/index.blade.php ENDPATH**/ ?>
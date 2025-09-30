<?php $__env->startSection('content'); ?>
<div class="pt-2 pb-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">
    <form action="<?php echo e(route('customer.checkout.payment_method')); ?>" method="GET" id="checkoutForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="recipient_type" id="recipientType" value="someone">
        <input type="hidden" name="delivery_address" id="deliveryAddressHidden" value="">
        <input type="hidden" name="promo_code" id="promoCodeInputHidden" value="">
        <input type="hidden" name="shipping_fee" id="shippingFeeInput" value="0">
        <?php if(request('product_id')): ?>
            <input type="hidden" name="product_id" value="<?php echo e(request('product_id')); ?>">
            <input type="hidden" name="quantity" value="<?php echo e(request('quantity', 1)); ?>">
        <?php endif; ?>
        <?php if(request('catalog_product_id')): ?>
            <input type="hidden" name="catalog_product_id" value="<?php echo e(request('catalog_product_id')); ?>">
            <input type="hidden" name="quantity" value="<?php echo e(request('quantity', 1)); ?>">
        <?php endif; ?>
        <?php if(request('selected_items')): ?>
            <?php $__currentLoopData = request('selected_items'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="selected_items[]" value="<?php echo e($itemId); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <div class="row justify-content-center mt-2">
            <div class="col-12 col-lg-8 col-xl-6" style="max-width: 1200px;">
                <div class="bg-white rounded-3 p-3 mb-4 scrollable-content" style="box-shadow: none; max-height: 85vh; overflow-y: auto;">
                    <div class="mb-3">
                        <a href="<?php echo e(url('/cart')); ?>" class="btn btn-outline-success">
                            &larr; Back
                        </a>
                    </div>
                    <div class="mb-4" style="font-weight: 600; font-size: 1.15rem;">Sender Information:</div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo e(Auth::user()->email); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->contact_number); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->first_name); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->last_name); ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-3">
                        <button type="button" class="btn btn-outline-success flex-fill recipient-btn active" id="btnSomeone">Someone will receive the order</button>
                        <button type="button" class="btn btn-outline-success flex-fill recipient-btn" id="btnSelf">I will receive the order.</button>
                    </div>
                    <div id="recipientFields" class="mb-3">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-user me-2"></i>Recipient Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Recipient Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="recipient_name" id="recipientName" placeholder="Enter recipient's full name" required>
                                <small class="text-muted">Full name as it appears on ID</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Recipient Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="recipient_phone" id="recipientPhone" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" maxlength="11">
                                <small class="text-muted">Mobile number for delivery updates</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Relationship to Recipient</label>
                                <select class="form-select" name="recipient_relationship" id="recipientRelationship">
                                    <option value="">Select relationship</option>
                                    <option value="family">Family Member</option>
                                    <option value="friend">Friend</option>
                                    <option value="colleague">Colleague</option>
                                    <option value="partner">Partner/Spouse</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Special Instructions</label>
                                <textarea class="form-control" name="recipient_instructions" id="recipientInstructions" rows="2" placeholder="Any special delivery instructions..."></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Delivery Message/Card Message</label>
                                <textarea class="form-control" name="delivery_message" id="deliveryMessage" rows="3" placeholder="Write a personal message for the recipient..."></textarea>
                                <small class="text-muted">This message will be included with the delivery</small>
                            </div>
                        </div>
                    </div>
                    <div id="selfFields" class="mb-3" style="display:none">
                        <!-- No extra fields if self -- can place customer info here if needed. -->
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Addresses</label>
                        <select class="form-select" name="address_id" id="addressSelect" style="min-height: 120px;">
                            <?php $__empty_1 = true; $__currentLoopData = ($addresses ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <option value="<?php echo e($addr->id); ?>" data-address="<?php echo e($addr->street_address); ?>, <?php echo e($addr->barangay); ?>, <?php echo e($addr->municipality ?? $addr->city); ?>, <?php echo e($addr->region ?? 'Region VII'); ?>" <?php if(optional($deliveryAddress)->id === $addr->id): echo 'selected'; endif; ?>>
                                    <?php echo e($addr->recipient_name ?? (Auth::user()->first_name.' '.Auth::user()->last_name)); ?> - <?php echo e($addr->street_address); ?>, <?php echo e($addr->barangay); ?>, <?php echo e($addr->municipality ?? $addr->city); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <option disabled>No saved addresses. Add one in Address Book.</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="mb-4">
                        <?php if (isset($component)) { $__componentOriginal23e59b8adbf9ce8ebc878feefa2c4ada = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23e59b8adbf9ce8ebc878feefa2c4ada = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delivery-map','data' => ['selectedAddress' => optional($deliveryAddress)->street_address . ', ' . optional($deliveryAddress)->barangay . ', ' . optional($deliveryAddress)->municipality . ', ' . optional($deliveryAddress)->city . ', ' . optional($deliveryAddress)->region ?? '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delivery-map'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selectedAddress' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($deliveryAddress)->street_address . ', ' . optional($deliveryAddress)->barangay . ', ' . optional($deliveryAddress)->municipality . ', ' . optional($deliveryAddress)->city . ', ' . optional($deliveryAddress)->region ?? '')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23e59b8adbf9ce8ebc878feefa2c4ada)): ?>
<?php $attributes = $__attributesOriginal23e59b8adbf9ce8ebc878feefa2c4ada; ?>
<?php unset($__attributesOriginal23e59b8adbf9ce8ebc878feefa2c4ada); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23e59b8adbf9ce8ebc878feefa2c4ada)): ?>
<?php $component = $__componentOriginal23e59b8adbf9ce8ebc878feefa2c4ada; ?>
<?php unset($__componentOriginal23e59b8adbf9ce8ebc878feefa2c4ada); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 col-xl-4">
                <div class="bg-white rounded-3 p-3 mb-4" style="box-shadow: none;">
                    <div class="mb-3" style="font-weight: 600; font-size: 1.15rem;">Purchase Summary:</div>
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo e(asset('storage/' . $item->product->image)); ?>" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px;">
                        <div class="flex-grow-1 ms-2">
                            <div style="font-weight: 500;"><?php echo e($item->product->name); ?></div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success" style="font-size: 0.9rem; padding: 8px 12px;"><?php echo e($item->quantity); ?></span>
                        </div>
                        <div class="ms-3" style="font-weight: 500; font-size: 1.08rem;">₱<?php echo e(number_format($item->quantity * $item->product->price, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="input-group mb-3 mt-3">
                        <input type="text" class="form-control" placeholder="Promo code" id="promoCodeInput">
                        <button class="btn btn-success" type="button" id="applyPromoBtn">Apply</button>
                    </div>
                    <div id="promoFeedback" class="mb-2" style="font-size: 0.98rem;"></div>
                    <div class="d-flex justify-content-between mb-2 mt-4">
                        <span style="color: #888;">Subtotal (<?php echo e(count($cartItems)); ?> Item<?php echo e(count($cartItems) == 1 ? '' : 's'); ?>)</span>
                        <span style="color: #222;">₱<span id="cartSubtotal"><?php echo e(number_format($subtotal, 2)); ?></span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #888;">Shipping Fee</span>
                        <span style="color: #222;">₱<span id="shippingFeeDisplay">—</span></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight: 600;">Total</span>
                        <span style="color: #7bb47b; font-weight: 600; font-size: 1.15rem;">₱<span id="cartTotalFinal"><?php echo e(number_format($subtotal, 2)); ?></span></span>
                    </div>
                    <button type="submit" class="btn btn-success w-100" style="border-radius: 25px; font-weight: 600; font-size: 1.08rem;">Proceed to Payment Method</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->startPush('styles'); ?>
<style>
    body { background: #f4faf4; }
    .bg-white { box-shadow: none !important; }
    .btn-success {
        background: #7bb47b;
        border-color: #7bb47b;
    }
    .btn-success:hover {
        background: #5a9c5a;
        border-color: #5a9c5a;
    }
    .btn-outline-success {
        border-color: #7bb47b;
        color: #7bb47b;
    }
    .btn-outline-success:hover {
        background: #7bb47b;
        color: #fff;
    }
    .form-control[readonly] {
        background: #f4faf4;
    }
    .recipient-btn.active, .recipient-btn:focus {
        background: #cbe7cb !important;
        color: #222 !important;
        border-color: #7bb47b !important;
        font-weight: 600;
    }
    #applyPromoBtn.active {
        box-shadow: 0 0 0 2px #cbe7cb;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Update shipping fee when address selection changes
document.addEventListener('DOMContentLoaded', function() {
    const addressSelect = document.getElementById('addressSelect');
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Ensure shipping_fee is always sent
            const feeInput = document.getElementById('shippingFeeInput');
            const fee = feeInput ? feeInput.value : '';
            
            // Get delivery address from the map component
            const deliveryAddressInput = document.getElementById('deliveryAddressInput');
            const deliveryAddress = deliveryAddressInput ? deliveryAddressInput.value : '';
            
            // Update the hidden delivery address field
            const deliveryAddressHidden = document.getElementById('deliveryAddressHidden');
            if (deliveryAddressHidden) {
                deliveryAddressHidden.value = deliveryAddress;
            }
            
            const formData = new FormData(checkoutForm);
            if (fee !== '') {
                formData.set('shipping_fee', fee);
            }
            if (deliveryAddress !== '') {
                formData.set('delivery_address', deliveryAddress);
            }
            const params = new URLSearchParams(formData);
            e.preventDefault();
            window.location.href = checkoutForm.action + '?' + params.toString();
        });
    }
    
    addressSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const address = selectedOption.getAttribute('data-address');
        
        if (address && address !== 'No saved addresses. Add one in Address Book.') {
            // Update the delivery address input in the map component
            const deliveryAddressInput = document.getElementById('deliveryAddressInput');
            if (deliveryAddressInput) {
                deliveryAddressInput.value = address;
                // Trigger the map component's calculation
                deliveryAddressInput.dispatchEvent(new Event('input'));
            }
        }
    });
    
    // Initial calculation if there's a selected address
    // (Removed auto-calc on load; fee will update only after user provides/chooses an address)
});

// Function to update shipping fee display (called by map component)
function updateShippingFeeDisplay(fee) {
    console.log('updateShippingFeeDisplay called with fee:', fee);
    
    // Update the shipping fee display in the checkout summary
    const shippingDisplay = document.getElementById('shippingFeeDisplay');
    if (shippingDisplay) {
        console.log('Found shipping display element, updating to:', fee);
        shippingDisplay.textContent = fee.toFixed(2);
    } else {
        console.log('Shipping display element not found!');
    }
    
    // Update the hidden input for form submission
    const shippingInput = document.getElementById('shippingFeeInput');
    if (shippingInput) {
        console.log('Found shipping input element, updating to:', fee);
        shippingInput.value = fee;
    } else {
        console.log('Shipping input element not found!');
    }
    
    // Update the total only if a valid fee is provided
    const subtotalElement = document.getElementById('cartSubtotal');
    let total = 0;
    if (subtotalElement) {
        const subtotal = parseFloat(subtotalElement.textContent.replace(/,/g, ''));
        total = subtotal + (fee || 0);
        const totalDisplay = document.getElementById('cartTotalFinal');
        if (totalDisplay) {
            console.log('Updating total from', subtotal, 'to', total);
            totalDisplay.textContent = total.toFixed(2);
        } else {
            console.log('Total display element not found!');
        }
    } else {
        console.log('Subtotal element not found!');
    }
    
    console.log('Updated shipping fee:', fee, 'Total:', total);
}

// Recipient type toggle (visual only)
const btnSomeone = document.getElementById('btnSomeone');
const btnSelf = document.getElementById('btnSelf');
btnSomeone.onclick = function() {
    btnSomeone.classList.add('active');
    btnSelf.classList.remove('active');
    // Show recipient fields, require inputs
    document.getElementById('recipientFields').style.display = '';
    document.getElementById('recipientName').required = true;
    document.getElementById('recipientPhone').required = true;
    // Hide self fields
    document.getElementById('selfFields').style.display = 'none';
};
btnSelf.onclick = function() {
    btnSelf.classList.add('active');
    btnSomeone.classList.remove('active');
    // Hide recipient fields, remove required
    document.getElementById('recipientFields').style.display = 'none';
    document.getElementById('recipientName').required = false;
    document.getElementById('recipientPhone').required = false;
    // Show self fields (if needed)
    document.getElementById('selfFields').style.display = '';
};
// Promo code Apply (visual only)
document.getElementById('applyPromoBtn').onclick = function() {
    // No functional logic, just visual feedback (optional highlight)
    this.classList.add('active');
    setTimeout(() => this.classList.remove('active'), 300);
};
</script>
<?php $__env->stopPush(); ?>

<style>
/* Custom scrollbar styling for the left content area */
.scrollable-content::-webkit-scrollbar {
    width: 8px;
}

.scrollable-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.scrollable-content::-webkit-scrollbar-thumb {
    background: #8ACB88;
    border-radius: 4px;
}

.scrollable-content::-webkit-scrollbar-thumb:hover {
    background: #7bb47b;
}

/* For Firefox */
.scrollable-content {
    scrollbar-width: thin;
    scrollbar-color: #8ACB88 #f1f1f1;
}
</style>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/checkout/index.blade.php ENDPATH**/ ?>
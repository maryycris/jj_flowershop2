<?php $__env->startSection('content'); ?>
<div class="pt-2 pb-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">
    <form action="<?php echo e(route('customer.checkout.payment_method')); ?>" method="GET" id="checkoutForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="recipient_type" id="recipientType" value="someone">
        <input type="hidden" name="delivery_address" id="deliveryAddressHidden" value="">
        <input type="hidden" name="loyalty_discount" id="loyaltyDiscountHidden" value="<?php echo e($loyaltyDiscount ?? 0); ?>">
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
                    
                    <!-- Error Alert for Phone Number -->
                    <?php $__errorArgs = ['recipient_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Phone Number Required!</strong> <?php echo e($message); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                <input type="text" class="form-control <?php $__errorArgs = ['recipient_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="recipient_phone" id="recipientPhone" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" maxlength="11" value="<?php echo e(old('recipient_phone')); ?>">
                                <?php $__errorArgs = ['recipient_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-user me-2"></i>Your Contact Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Your Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['recipient_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="recipient_phone" id="selfPhone" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" maxlength="11" value="<?php echo e(old('recipient_phone', Auth::user()->contact_number)); ?>">
                                <?php $__errorArgs = ['recipient_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">Mobile number for delivery updates</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Addresses</label>
                        <select class="form-select" name="address_id" id="addressSelect" style="min-height: 120px;">
                            <?php $__empty_1 = true; $__currentLoopData = ($addresses ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $fullAddress = trim(collect([
                                        $addr->street_address,
                                        $addr->barangay,
                                        $addr->municipality ?? $addr->city
                                    ])->filter()->implode(', '));
                                ?>
                                <option value="<?php echo e($addr->id); ?>" data-address="<?php echo e($fullAddress); ?>" <?php if(optional($deliveryAddress)->id === $addr->id): echo 'selected'; endif; ?>>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delivery-map','data' => ['selectedAddress' => optional($deliveryAddress) ? 
                            trim(collect([
                                optional($deliveryAddress)->street_address,
                                optional($deliveryAddress)->barangay,
                                optional($deliveryAddress)->municipality ?? optional($deliveryAddress)->city
                            ])->filter()->implode(', ')) : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delivery-map'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selectedAddress' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($deliveryAddress) ? 
                            trim(collect([
                                optional($deliveryAddress)->street_address,
                                optional($deliveryAddress)->barangay,
                                optional($deliveryAddress)->municipality ?? optional($deliveryAddress)->city
                            ])->filter()->implode(', ')) : '')]); ?>
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
                    
                    <div class="mb-3 mt-3">
                        <div class="card" style="border: 2px solid #e8f5e8; background: linear-gradient(135deg, #f8f9fa, #e8f5e8);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-success fw-bold">
                                        <i class="fas fa-star me-2"></i>Loyalty Stamps
                                    </h6>
                                    <?php if($loyaltyCard): ?>
                                        <span class="badge bg-success fs-6"><?php echo e($loyaltyCard->stamps_count); ?>/5</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-6">0/5</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($loyaltyCard && $loyaltyCard->stamps_count >= 5): ?>
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fas fa-gift text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        <p class="text-success fw-bold mb-2">🎉 You have 5 stamps! You can redeem a 50% discount on your most expensive bouquet!</p>
                                        <button type="button" class="btn btn-success btn-sm" id="redeemLoyaltyBtn">
                                            <i class="fas fa-gift me-1"></i>Redeem Discount
                                        </button>
                                    </div>
                                <?php elseif($loyaltyCard && $loyaltyCard->stamps_count > 0): ?>
                                    <div class="text-center">
                                        <p class="text-muted mb-2">You need <?php echo e(5 - $loyaltyCard->stamps_count); ?> more stamp(s) to redeem a discount</p>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo e(($loyaltyCard->stamps_count / 5) * 100); ?>%"></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center">
                                        <p class="text-muted mb-2">Start earning stamps with your first order!</p>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div id="loyaltyFeedback" class="mb-2" style="font-size: 0.98rem;"></div>
                    <div class="d-flex justify-content-between mb-2 mt-4">
                        <span style="color: #888;">Subtotal (<?php echo e(count($cartItems)); ?> Item<?php echo e(count($cartItems) == 1 ? '' : 's'); ?>)</span>
                        <span style="color: #222;">₱<span id="cartSubtotal"><?php echo e(number_format($subtotal, 2)); ?></span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #888;">Shipping Fee</span>
                        <span style="color: #222;">₱<span id="shippingFeeDisplay">—</span></span>
                    </div>
                    <?php if($loyaltyDiscount > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #28a745;">Loyalty Discount</span>
                        <span style="color: #28a745; font-weight: 600;">-₱<span id="loyaltyDiscountDisplay"><?php echo e(number_format($loyaltyDiscount, 2)); ?></span></span>
                    </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight: 600;">Total</span>
                        <span style="color: #7bb47b; font-weight: 600; font-size: 1.15rem;">₱<span id="cartTotalFinal"><?php echo e(number_format($subtotal - ($loyaltyDiscount ?? 0), 2)); ?></span></span>
                    </div>
                    
                    <!-- Delivery Schedule Section -->
                    <div class="mb-4">
                        <div class="p-3" style="background: linear-gradient(135deg, #e8f5e8, #f0f8f0); border-radius: 8px; border-left: 4px solid #8ACB88;">
                            <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-calendar-check me-2 text-success"></i>Choose Your Delivery Schedule
                            </h6>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Select your preferred delivery date and time. We'll deliver your flowers when you need them most!
                            </p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="delivery_date" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Delivery Date *
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="delivery_date" 
                                           name="delivery_date" 
                                           min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                                           max="<?php echo e(date('Y-m-d', strtotime('+30 days'))); ?>"
                                           required>
                                    <small class="text-muted">Select a date at least 1 day from now</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="delivery_time" class="form-label">
                                        <i class="fas fa-clock me-2"></i>Delivery Time *
                                    </label>
                                    <select class="form-control" id="delivery_time" name="delivery_time" required>
                                        <option value="">Choose time...</option>
                                        <option value="08:00 AM">8:00 AM - 9:00 AM</option>
                                        <option value="09:00 AM">9:00 AM - 10:00 AM</option>
                                        <option value="10:00 AM">10:00 AM - 11:00 AM</option>
                                        <option value="11:00 AM">11:00 AM - 12:00 PM</option>
                                        <option value="12:00 PM">12:00 PM - 1:00 PM</option>
                                        <option value="01:00 PM">1:00 PM - 2:00 PM</option>
                                        <option value="02:00 PM">2:00 PM - 3:00 PM</option>
                                        <option value="03:00 PM">3:00 PM - 4:00 PM</option>
                                        <option value="04:00 PM">4:00 PM - 5:00 PM</option>
                                        <option value="05:00 PM">5:00 PM - 6:00 PM</option>
                                        <option value="06:00 PM">6:00 PM - 7:00 PM</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred time slot</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="proceedBtn" style="border-radius: 25px; font-weight: 600; font-size: 1.08rem;">
                        <i class="fas fa-arrow-right me-2"></i>Proceed to Payment Method
                    </button>
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
    
    /* Disabled delivery address field styling */
    #deliveryAddressInput:disabled {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }
    
    #geocodeBtn:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
    #applyPromoBtn.active {
        box-shadow: 0 0 0 2px #cbe7cb;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto-populate delivery address with default address only for "I will receive the order"
function populateDefaultAddress() {
    const recipientType = document.getElementById('recipientType').value;
    const deliveryAddressInput = document.getElementById('deliveryAddressInput');
    const geocodeBtn = document.getElementById('geocodeBtn');
    
    if (recipientType === 'self') {
        const addressSelect = document.getElementById('addressSelect');
        const defaultAddressOption = addressSelect.querySelector('option[selected]');
        if (defaultAddressOption && !defaultAddressOption.disabled) {
            const address = defaultAddressOption.getAttribute('data-address');
            if (deliveryAddressInput && address && address !== 'No saved addresses. Add one in Address Book.') {
                deliveryAddressInput.value = address;
                // Disable the delivery address field when using default address
                deliveryAddressInput.disabled = true;
                deliveryAddressInput.readOnly = true;
                deliveryAddressInput.style.backgroundColor = '#f8f9fa';
                deliveryAddressInput.style.cursor = 'not-allowed';
                
                // Keep the Find button ENABLED for shipping fee calculation
                if (geocodeBtn) {
                    geocodeBtn.disabled = false;
                    geocodeBtn.style.opacity = '';
                    geocodeBtn.style.cursor = '';
                }
                
                // Trigger the map component's calculation
                deliveryAddressInput.dispatchEvent(new Event('input'));
            } else {
                // No valid address, clear the input and enable it
                if (deliveryAddressInput) {
                    deliveryAddressInput.value = '';
                    deliveryAddressInput.disabled = false;
                    deliveryAddressInput.readOnly = false;
                    deliveryAddressInput.style.backgroundColor = '';
                    deliveryAddressInput.style.cursor = '';
                }
            }
        } else {
            // No selected address, clear the input and enable it
            if (deliveryAddressInput) {
                deliveryAddressInput.value = '';
                deliveryAddressInput.disabled = false;
                deliveryAddressInput.readOnly = false;
                deliveryAddressInput.style.backgroundColor = '';
                deliveryAddressInput.style.cursor = '';
            }
        }
    } else {
        // Enable the delivery address field when "Someone will receive" is selected
        if (deliveryAddressInput) {
            deliveryAddressInput.disabled = false;
            deliveryAddressInput.readOnly = false;
            deliveryAddressInput.style.backgroundColor = '';
            deliveryAddressInput.style.cursor = '';
        }
        
        // Keep the Find button enabled
        if (geocodeBtn) {
            geocodeBtn.disabled = false;
            geocodeBtn.style.opacity = '';
            geocodeBtn.style.cursor = '';
        }
    }
}

// Update shipping fee when address selection changes
document.addEventListener('DOMContentLoaded', function() {
    const addressSelect = document.getElementById('addressSelect');
    const checkoutForm = document.getElementById('checkoutForm');
    
    // Check on page load if "I will receive" is selected
    populateDefaultAddress();
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Ensure shipping_fee is always sent
            const feeInput = document.getElementById('shippingFeeInput');
            const fee = feeInput ? feeInput.value : '';
            
            // Get delivery address from the map component
            const deliveryAddressInput = document.getElementById('deliveryAddressInput');
            const deliveryAddress = deliveryAddressInput ? deliveryAddressInput.value : '';
            
            // Get delivery schedule fields
            const deliveryDate = document.getElementById('delivery_date').value;
            const deliveryTime = document.getElementById('delivery_time').value;
            
            // Get loyalty discount
            const loyaltyDiscount = document.getElementById('loyaltyDiscountHidden').value;
            
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
            if (deliveryDate !== '') {
                formData.set('delivery_date', deliveryDate);
            }
            if (deliveryTime !== '') {
                formData.set('delivery_time', deliveryTime);
            }
            if (loyaltyDiscount !== '') {
                formData.set('loyalty_discount', loyaltyDiscount);
            }
            const params = new URLSearchParams(formData);
            e.preventDefault();
            window.location.href = checkoutForm.action + '?' + params.toString();
        });
    }
    
    addressSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const address = selectedOption.getAttribute('data-address');
        const recipientType = document.getElementById('recipientType').value;
        const deliveryAddressInput = document.getElementById('deliveryAddressInput');
        const geocodeBtn = document.getElementById('geocodeBtn');
        
        // Only auto-populate delivery address if "I will receive" is selected
        if (address && address !== 'No saved addresses. Add one in Address Book.' && recipientType === 'self') {
            // Update the delivery address input in the map component
            if (deliveryAddressInput) {
                deliveryAddressInput.value = address;
                // Disable the delivery address field when using default address
                deliveryAddressInput.disabled = true;
                deliveryAddressInput.readOnly = true;
                deliveryAddressInput.style.backgroundColor = '#f8f9fa';
                deliveryAddressInput.style.cursor = 'not-allowed';
                
                // Keep the Find button ENABLED for shipping fee calculation
                if (geocodeBtn) {
                    geocodeBtn.disabled = false;
                    geocodeBtn.style.opacity = '';
                    geocodeBtn.style.cursor = '';
                }
                
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
    document.getElementById('recipientType').value = 'someone';
    // Show recipient fields, require inputs
    document.getElementById('recipientFields').style.display = '';
    document.getElementById('recipientName').required = true;
    document.getElementById('recipientPhone').required = true;
    // Hide self fields and remove required from self phone
    document.getElementById('selfFields').style.display = 'none';
    document.getElementById('selfPhone').required = false;
    
    // Enable delivery address field and clear it when someone else will receive
    const deliveryAddressInput = document.getElementById('deliveryAddressInput');
    const geocodeBtn = document.getElementById('geocodeBtn');
    if (deliveryAddressInput) {
        deliveryAddressInput.value = '';
        deliveryAddressInput.disabled = false;
        deliveryAddressInput.readOnly = false;
        deliveryAddressInput.style.backgroundColor = '';
        deliveryAddressInput.style.cursor = '';
    }
    // Keep Find button enabled for shipping fee calculation
    if (geocodeBtn) {
        geocodeBtn.disabled = false;
        geocodeBtn.style.opacity = '';
        geocodeBtn.style.cursor = '';
    }
};
btnSelf.onclick = function() {
    btnSelf.classList.add('active');
    btnSomeone.classList.remove('active');
    document.getElementById('recipientType').value = 'self';
    // Hide recipient fields, remove required
    document.getElementById('recipientFields').style.display = 'none';
    document.getElementById('recipientName').required = false;
    document.getElementById('recipientPhone').required = false;
    // Show self fields and make phone required
    document.getElementById('selfFields').style.display = '';
    document.getElementById('selfPhone').required = true;
    
    // Auto-populate default address when "I will receive" is selected
    populateDefaultAddress();
    
    // Ensure Find button remains enabled for shipping fee calculation
    const geocodeBtn = document.getElementById('geocodeBtn');
    if (geocodeBtn) {
        geocodeBtn.disabled = false;
        geocodeBtn.style.opacity = '';
        geocodeBtn.style.cursor = '';
    }
};

// Loyalty redemption functionality
document.addEventListener('DOMContentLoaded', function() {
    const redeemBtn = document.getElementById('redeemLoyaltyBtn');
    if (redeemBtn) {
        redeemBtn.addEventListener('click', function() {
            // Visual feedback
            this.classList.add('active');
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Redeeming...';
            this.disabled = true;
            
            // Simulate redemption process
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check me-1"></i>Redeemed!';
                this.classList.remove('active');
                this.classList.add('btn-outline-success');
                
                // Show success message
                const feedback = document.getElementById('loyaltyFeedback');
                if (feedback) {
                    feedback.innerHTML = '<div class="alert alert-success alert-sm mb-0"><i class="fas fa-gift me-1"></i>Loyalty discount applied! 50% off your most expensive bouquet.</div>';
                }
                
                // Update totals (this would be handled by server in real implementation)
                updateTotals();
            }, 1500);
        });
    }
});

function updateTotals() {
    // This function would recalculate totals with loyalty discount
    // For now, just show visual feedback
    console.log('Totals updated with loyalty discount');
}

// Auto-scroll to error field if validation fails
document.addEventListener('DOMContentLoaded', function() {
    // Reset button state if there are validation errors
    const submitBtn = document.getElementById('proceedBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Proceed to Payment Method';
        submitBtn.disabled = false;
    }
    
    <?php $__errorArgs = ['recipient_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        // Scroll to the phone number field
        setTimeout(() => {
            const phoneField = document.getElementById('recipientPhone') || document.getElementById('selfPhone');
            if (phoneField) {
                phoneField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                phoneField.focus();
                phoneField.classList.add('is-invalid');
            }
        }, 500);
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
});

// Add loading state to submit button
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('proceedBtn');
    
    // Debug: Log form data
    const formData = new FormData(this);
    console.log('Form submission data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    submitBtn.disabled = true;
    
    // Reset button state after 5 seconds in case of errors
    setTimeout(() => {
        submitBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Proceed to Payment Method';
        submitBtn.disabled = false;
    }, 5000);
});
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
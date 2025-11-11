<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.customer.alt_nav', ['active' => 'home'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    /* Custom scrollbar styling for transparent tracks */
    .scrollable-content::-webkit-scrollbar {
        width: 8px;
        background: transparent;
    }
    
    .scrollable-content::-webkit-scrollbar-track {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    .scrollable-content::-webkit-scrollbar-thumb {
        background: #7bb47b !important;
        border-radius: 4px;
        border: none !important;
    }
    
    .scrollable-content::-webkit-scrollbar-thumb:hover {
        background: #5a9c5a !important;
    }
    
    /* Force transparent track */
    .scrollable-content::-webkit-scrollbar-corner {
        background: transparent !important;
    }
    
    /* Additional overrides for complete transparency */
    .scrollable-content::-webkit-scrollbar-track-piece {
        background: transparent !important;
    }
</style>
<div class="pt-0 order-details-container" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">


        <div class="row justify-content-center">
            <!-- Left Box - Order Details -->
            <div class="col-12 col-lg-8 col-xl-6 order-1 order-lg-1 order-details-section" style="max-width: 900px;">
                <!-- Header Section for Left Box -->
                <div class="mb-2">
                    <a href="<?php echo e(route('customer.orders.index')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Go to My Purchase
                    </a>
                </div>

                <div class="bg-white rounded-3 p-3 p-md-4 mb-3 mb-lg-4 scrollable-content order-details-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; overflow-y: auto;">
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8ACB88, #7bb47b); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shopping-bag text-white" style="font-size: 1.1rem;"></i>
        </div>
        </div>
                        <div>
                            <h5 class="mb-1" style="color: #2c3e50; font-weight: 600; font-size: 1.1rem;">Order Information</h5>
                            <p class="text-muted mb-0 small">Order #<?php echo e($order->id); ?> • <?php echo e($order->created_at->format('M d, Y')); ?></p>
                </div>
                        </div>
                    
                    <!-- Order Status Cards -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px;">
                                <i class="fas fa-info-circle me-2 text-primary" style="font-size: 0.9rem;"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Order Status</small>
                                    <?php
                                        $currentStatus = $order->order_status ?? $order->status;
                                        $statusDisplay = \App\Services\OrderStatusService::getCustomerDisplayStatus($currentStatus);
                                        $statusColor = match($currentStatus) {
                                            'pending' => 'warning',
                                            'approved' => 'info', 
                                            'assigned' => 'primary',
                                            'on_delivery' => 'primary',
                                            'completed' => 'success',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            'returned' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>
                                    <span class="badge bg-<?php echo e($statusColor); ?> px-2 py-1" style="font-size: 0.7rem;"><?php echo e($statusDisplay); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Refund Information -->
                        <?php if($order->refund_amount && $order->refund_processed_at): ?>
                        <div class="col-12 mt-3">
                            <div class="alert alert-success border-0" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave me-3 text-success" style="font-size: 1.2rem;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-success fw-bold">
                                            <i class="fas fa-check-circle me-1"></i>Refund Processed
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Refund Amount</small>
                                                <strong class="text-success">₱<?php echo e(number_format($order->refund_amount, 2)); ?></strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Refund Method</small>
                                                <strong class="text-dark"><?php echo e(ucwords(str_replace('_', ' ', $order->refund_method))); ?></strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Processed Date</small>
                                                <strong class="text-dark">
                                                    <?php if($order->refund_processed_at): ?>
                                                        <?php echo e($order->refund_processed_at instanceof \Carbon\Carbon ? $order->refund_processed_at->format('M d, Y g:i A') : \Carbon\Carbon::parse($order->refund_processed_at)->format('M d, Y g:i A')); ?>

                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <?php if($order->refund_reason): ?>
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Reason</small>
                                            <span class="text-dark"><?php echo e($order->refund_reason); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px;">
                                <i class="fas fa-credit-card me-2 text-primary" style="font-size: 0.9rem;"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Payment Status</small>
                                    <?php if($order->payment_status === 'paid'): ?>
                                        <span class="badge bg-success px-2 py-1" style="font-size: 0.7rem;">Paid</span>
                                    <?php elseif($order->payment_status === 'pending'): ?>
                                        <span class="badge bg-warning px-2 py-1" style="font-size: 0.7rem;">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-2 py-1" style="font-size: 0.7rem;">Unpaid</span>
                            <?php endif; ?>
                        </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Amount & Payment Method -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2" style="background: linear-gradient(135deg, #8ACB88, #7bb47b); border-radius: 6px; color: white;">
                                <i class="fas fa-dollar-sign me-2" style="font-size: 0.9rem;"></i>
                                <div>
                                    <small class="opacity-75 d-block" style="font-size: 0.75rem;">Total Amount</small>
                                    <strong style="font-size: 1rem;">₱<?php echo e(number_format($order->total_price, 2)); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px;">
                                <i class="fas fa-wallet me-2 text-primary" style="font-size: 0.9rem;"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Payment Method</small>
                                    <span class="badge bg-info px-2 py-1" style="font-size: 0.7rem;"><?php echo e(strtoupper($order->payment_method ?? 'N/A')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products in Order -->
                    <div class="mb-3">
                        <h6 class="mb-2" style="color: #2c3e50; font-weight: 600; font-size: 1rem;">
                            <i class="fas fa-box me-2 text-primary" style="font-size: 0.9rem;"></i>Products in Order
                        </h6>
                        <div class="row g-2">
                            <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px; border-left: 3px solid #8ACB88;">
                                        <?php
                                            // Handle image path - use storage route
                                            $imagePath = null;
                                            if ($product->image) {
                                                // Clean the image path
                                                $img = ltrim($product->image, '/');
                                                $img = str_replace('storage/', '', $img);
                                                
                                                // Use storage route which will serve from backend storage
                                                $imagePath = '/storage/' . $img;
                                            }
                                        ?>
                                        <?php if($imagePath): ?>
                                            <img src="<?php echo e($imagePath); ?>" 
                                                 style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px;" 
                                                 alt="<?php echo e($product->name); ?>"
                                                 onerror="this.onerror=null; this.src='/images/logo.png'; this.style.opacity='0.5'; this.style.width='45px'; this.style.height='45px';">
                                        <?php else: ?>
                                            <div style="width: 45px; height: 45px; background: #e9ece9; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted" style="font-size: 1.2rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-1" style="color: #2c3e50; font-size: 0.9rem;"><?php echo e($product->name); ?></h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Quantity: <?php echo e($product->pivot->quantity); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong style="color: #8ACB88; font-size: 0.9rem;">₱<?php echo e(number_format($product->price * $product->pivot->quantity, 2)); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $order->customBouquets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bouquet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px; border-left: 3px solid #8ACB88;">
                                        <div class="flex-grow-1 ms-2">
                                            <button type="button" class="btn btn-link p-0 text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#customBouquetModal<?php echo e($bouquet->id); ?>" style="font-weight: 500; color: #7bb47b; font-size: 0.9rem;">
                                                Custom Bouquet
                                            </button>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Quantity: <?php echo e($bouquet->pivot->quantity); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong style="color: #8ACB88; font-size: 0.9rem;">₱<?php echo e(number_format(($bouquet->unit_price ?? $bouquet->total_price) * $bouquet->pivot->quantity, 2)); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($order->products->isEmpty() && $order->customBouquets->isEmpty()): ?>
                                <div class="col-12">
                                    <p class="text-muted text-center py-3">No items in this order.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <?php if($order->delivery): ?>
                        <div class="mb-3">
                            <h6 class="mb-2" style="color: #2c3e50; font-weight: 600; font-size: 1rem;">
                                <i class="fas fa-truck me-2 text-primary" style="font-size: 0.9rem;"></i>Delivery Information
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px;">
                                        <i class="fas fa-map-marker-alt me-2 text-primary" style="font-size: 0.9rem;"></i>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Delivery Address</small>
                                            <strong style="font-size: 0.85rem;"><?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px;">
                                        <i class="fas fa-user me-2 text-primary" style="font-size: 0.9rem;"></i>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Recipient</small>
                                            <strong style="font-size: 0.85rem;"><?php echo e($order->delivery->recipient_name ?? 'N/A'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    $canEditDelivery = in_array($order->order_status ?? $order->status, ['pending', 'approved']) && 
                                                     !in_array($order->order_status ?? $order->status, ['on_delivery', 'delivered', 'completed', 'cancelled']);
                                ?>
                                
                                <?php if($canEditDelivery): ?>
                                    <!-- Editable Delivery Schedule -->
                                    <div class="col-12">
                                        <div class="p-3" style="background: linear-gradient(135deg, #e8f5e8, #f0f8f0); border-radius: 8px; border-left: 4px solid #8ACB88;">
                                            <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                                <i class="fas fa-calendar-check me-2 text-success"></i>Choose Your Delivery Schedule
                                            </h6>
                                            <p class="text-muted small mb-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Select your preferred delivery date and time. We'll deliver your flowers when you need them most!
                                            </p>
                                            
                                            <form action="<?php echo e(route('customer.orders.update-delivery-schedule', $order->id)); ?>" method="POST" id="deliveryScheduleForm">
                                                <?php echo csrf_field(); ?>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="delivery_date" class="form-label">
                                                            <i class="fas fa-calendar me-2"></i>Delivery Date *
                                                        </label>
                                                        <input type="date" 
                                                               class="form-control" 
                                                               id="delivery_date" 
                                                               name="delivery_date" 
                                                               value="<?php echo e($order->delivery->delivery_date ?? ''); ?>"
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
                                                            <option value="08:00 AM" <?php echo e(($order->delivery->delivery_time ?? '') === '08:00 AM' ? 'selected' : ''); ?>>8:00 AM - 9:00 AM</option>
                                                            <option value="09:00 AM" <?php echo e(($order->delivery->delivery_time ?? '') === '09:00 AM' ? 'selected' : ''); ?>>9:00 AM - 10:00 AM</option>
                                                            <option value="10:00 AM" <?php echo e(($order->delivery->delivery_time ?? '') === '10:00 AM' ? 'selected' : ''); ?>>10:00 AM - 11:00 AM</option>
                                                            <option value="11:00 AM" <?php echo e(($order->delivery->delivery_time ?? '') === '11:00 AM' ? 'selected' : ''); ?>>11:00 AM - 12:00 PM</option>
                                                            <option value="12:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '12:00 PM' ? 'selected' : ''); ?>>12:00 PM - 1:00 PM</option>
                                                            <option value="01:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '01:00 PM' ? 'selected' : ''); ?>>1:00 PM - 2:00 PM</option>
                                                            <option value="02:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '02:00 PM' ? 'selected' : ''); ?>>2:00 PM - 3:00 PM</option>
                                                            <option value="03:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '03:00 PM' ? 'selected' : ''); ?>>3:00 PM - 4:00 PM</option>
                                                            <option value="04:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '04:00 PM' ? 'selected' : ''); ?>>4:00 PM - 5:00 PM</option>
                                                            <option value="05:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '05:00 PM' ? 'selected' : ''); ?>>5:00 PM - 6:00 PM</option>
                                                            <option value="06:00 PM" <?php echo e(($order->delivery->delivery_time ?? '') === '06:00 PM' ? 'selected' : ''); ?>>6:00 PM - 7:00 PM</option>
                                                        </select>
                                                        <small class="text-muted">Choose your preferred time slot</small>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-save me-2"></i>Update Delivery Schedule
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Read-only Delivery Schedule -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                            <i class="fas fa-calendar me-3 text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Delivery Date</small>
                                                <strong><?php echo e($order->delivery->delivery_date ? date('M d, Y', strtotime($order->delivery->delivery_date)) : 'N/A'); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                            <i class="fas fa-clock me-3 text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Delivery Time</small>
                                                <strong><?php echo e($order->delivery->delivery_time ?? 'N/A'); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Proof of Delivery -->
                    <?php if($order->delivery && $order->delivery->proof_of_delivery_image): ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-camera me-2 text-success"></i>Proof of Delivery
                            </h5>
                            <div class="p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #28a745;">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <p class="mb-2 text-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Delivery Confirmed!</strong>
                                        </p>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-clock me-1"></i>
                                            Delivered on: <?php echo e($order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A'); ?>

                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-user me-1"></i>
                                            Confirmed by: <?php echo e($order->delivery->driver->name ?? 'Delivery Driver'); ?>

                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#proofOfDeliveryModal">
                                            <i class="fas fa-eye me-1"></i>View Photo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Special Instructions -->
                        <?php if($order->notes): ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-sticky-note me-2 text-primary"></i>Special Instructions
                            </h5>
                            <div class="p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #8ACB88;">
                                <p class="mb-0"><?php echo e($order->notes); ?></p>
                            </div>
                            </div>
                        <?php endif; ?>
                </div>
            </div>

            <!-- Right Box - Actions & Customer Info - Always Visible -->
            <div class="col-12 col-lg-4 col-xl-4 order-2 order-lg-2 customer-info-section">
                <!-- Header Section for Right Box -->
                <div class="d-none d-lg-block" style="height: 38px; margin-bottom: 0.1rem;">
                    <!-- Spacer to align with left column button - hidden on mobile -->
                </div>
                
                <!-- Align with Order Information section -->
                <div>
                    <div class="bg-white rounded-3 p-3 p-md-4 mb-3 mb-lg-4 scrollable-content customer-info-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; overflow-y: auto;">
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6c757d, #495057); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user text-white" style="font-size: 1.1rem;"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1" style="color: #2c3e50; font-weight: 600; font-size: 1.1rem;">Customer Information</h5>
                            <p class="text-muted mb-0 small">Account Details</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center p-2 mb-2" style="background: #f8f9fa; border-radius: 6px;">
                            <i class="fas fa-user me-2 text-primary" style="font-size: 0.9rem;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Full Name</small>
                                <strong style="font-size: 0.85rem;"><?php echo e($order->user->name); ?></strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-2 mb-2" style="background: #f8f9fa; border-radius: 6px;">
                            <i class="fas fa-envelope me-2 text-primary" style="font-size: 0.9rem;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Email Address</small>
                                <strong style="font-size: 0.85rem;"><?php echo e($order->user->email); ?></strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 6px;">
                            <i class="fas fa-phone me-2 text-primary" style="font-size: 0.9rem;"></i>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Phone Number</small>
                                <strong style="font-size: 0.85rem;"><?php echo e($order->user->contact_number ?? 'N/A'); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Payment Receipt / Order Slip / Invoice Options -->
            <?php
                $isCOD = strtoupper($order->payment_method) === 'COD';
                $isOnDelivery = $order->order_status === 'on_delivery' || $order->order_status === 'completed';
                $isCompleted = $order->order_status === 'completed';
            ?>
            
            <?php if($isOnDelivery): ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-file-invoice me-2 text-primary"></i>Invoice Options
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('customer.orders.invoice.view', $order->id)); ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Invoice
                        </a>
                        <a href="<?php echo e(route('customer.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download PDF
                        </a>
                    </div>
                </div>
                
                <!-- Mark as Received Button / Received Status -->
                <div class="mb-4">
                    <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                        <i class="fas fa-check-circle me-2 text-success"></i>Order Actions
                    </h5>
                    <?php if($isCompleted): ?>
                        <!-- Already Received - Show Status -->
                        <div class="alert alert-success mb-0" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2" style="font-size: 1.2rem;"></i>
                                <div>
                                    <strong>Received</strong>
                                    <p class="mb-0 small text-muted">
                                        You have marked this order as received.
                                        <?php if($order->completed_at): ?>
                                            Received on: <?php echo e($order->completed_at->format('M d, Y g:i A')); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Not Yet Received - Show Button -->
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Click the button below when you have received your order to complete the delivery process.
                        </p>
                        <form action="<?php echo e(route('customer.orders.mark-received', $order->id)); ?>" method="POST" onsubmit="return confirm('Have you received your order? This will mark the order as completed.');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Mark as Received
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php elseif($isCOD): ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-receipt me-2 text-primary"></i>Order Slip
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('customer.orders.invoice.view', $order->id)); ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Order Slip
                        </a>
                        <a href="<?php echo e(route('customer.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download Order Slip
                        </a>
                    </div>
                </div>
            <?php else: ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-receipt me-2 text-primary"></i>Payment Receipt
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('customer.orders.invoice.view', $order->id)); ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Receipt
                        </a>
                        <a href="<?php echo e(route('customer.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download Receipt
                        </a>
                    </div>
                </div>
            <?php endif; ?>

                    <!-- Upload Payment Proof -->
            <?php if(in_array($order->payment_method, ['gcash','paymaya']) && $order->payment_status === 'unpaid'): ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-upload me-2 text-primary"></i>Upload Payment Proof
                            </h5>
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo e(route('customer.orders.uploadPaymentProof', $order->id)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <input type="hidden" name="payment_method" value="<?php echo e($order->payment_method); ?>">
                            <input type="text" class="form-control" value="<?php echo e(strtoupper($order->payment_method)); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number (optional)</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Upload Screenshot/Receipt <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Submit Payment Proof</button>
                    </form>
            </div>
            <?php endif; ?>

            <!-- Order Actions -->
            <?php if($order->status === 'pending' && $order->payment_status !== 'paid'): ?>
                        <div>
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Order Actions
                            </h5>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            You can only cancel orders that are still pending.
                        </p>
                        <form action="<?php echo e(route('customer.orders.cancel', $order->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        </form>
                </div>
            <?php endif; ?>
                </div>
                </div> <!-- Close mt-4 wrapper -->
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Desktop Styles for Order Details Page */
    @media (min-width: 992px) {
        .row.justify-content-center {
            align-items: stretch;
        }
        .order-details-section,
        .customer-info-section {
            display: flex;
            flex-direction: column;
        }
        .order-details-card {
            flex: 1;
            max-height: 85vh;
        }
        .customer-info-card {
            max-height: 85vh;
            position: sticky;
            top: 20px;
        }
    }
    
    /* Mobile Responsive Styles for Order Details Page */
    @media (max-width: 991.98px) {
        .order-details-container {
            padding-bottom: 1rem;
        }
        .order-details-section {
            margin-bottom: 1rem;
        }
        .customer-info-section {
            margin-top: 0 !important;
            margin-bottom: 2rem;
        }
        .order-details-card,
        .customer-info-card {
            max-height: none !important;
            overflow-y: visible !important;
        }
        .scrollable-content {
            max-height: none !important;
            overflow-y: visible !important;
        }
    }
    
    @media (max-width: 650px) {
        .order-details-container {
            padding: 0.5rem 0.25rem 1rem 0.25rem !important;
        }
        .order-details-section,
        .customer-info-section {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        .order-details-card,
        .customer-info-card {
            padding: 0.75rem !important;
            font-size: 0.9rem;
        }
        .customer-info-card h5,
        .order-details-card h5,
        .order-details-card h6 {
            font-size: 0.95rem !important;
        }
        .customer-info-card .d-flex,
        .order-details-card .d-flex {
            font-size: 0.85rem;
        }
        .btn {
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
        }
        .badge {
            font-size: 0.7rem !important;
        }
        .customer-info-card img {
            width: 35px !important;
            height: 35px !important;
        }
        .order-details-card img {
            width: 40px !important;
            height: 40px !important;
        }
    }
</style>

<!-- Proof of Delivery Modal -->
<?php if($order->delivery && $order->delivery->proof_of_delivery_image): ?>
<div class="modal fade" id="proofOfDeliveryModal" tabindex="-1" aria-labelledby="proofOfDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfDeliveryModalLabel">
                    <i class="fas fa-camera me-2"></i>Proof of Delivery
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <img src="/storage/<?php echo e($order->delivery->proof_of_delivery_image); ?>" 
                         alt="Proof of Delivery" 
                         class="img-fluid rounded shadow" 
                         style="max-height: 500px; border: 2px solid #dee2e6;">
                </div>
                <div class="row text-start">
                    <div class="col-md-6">
                        <p><strong>Order #:</strong> <?php echo e($order->id); ?></p>
                        <p><strong>Delivered on:</strong> <?php echo e($order->delivery->proof_of_delivery_taken_at ? $order->delivery->proof_of_delivery_taken_at->format('M d, Y g:i A') : 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Confirmed by:</strong> <?php echo e($order->delivery->driver->name ?? 'Delivery Driver'); ?></p>
                        <p><strong>Delivery Address:</strong> <?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="/storage/<?php echo e($order->delivery->proof_of_delivery_image); ?>" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Download Photo
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Custom Bouquet Detail Modal -->
<?php $__currentLoopData = $order->customBouquets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bouquet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $modalId = $bouquet->id;
        $items = \App\Models\CustomizeItem::where('status', true)->get();
        $assemblyFee = 150;
        $components = [];
        $totalPrice = $assemblyFee;
        
        // Get wrapper
        if ($bouquet->wrapper) {
            $item_data = $items->firstWhere('name', $bouquet->wrapper);
            if ($item_data) {
                $components[] = [
                    'category' => 'Wrapper',
                    'name' => $bouquet->wrapper,
                    'quantity' => 1,
                    'price' => $item_data->price ?? 0
                ];
                $totalPrice += $item_data->price ?? 0;
            }
        }
        
        // Get flowers
        foreach (['focal_flower_1', 'focal_flower_2', 'focal_flower_3'] as $flowerField) {
            if ($bouquet->$flowerField) {
                $item_data = $items->firstWhere('name', $bouquet->$flowerField);
                if ($item_data) {
                    $components[] = [
                        'category' => 'Fresh Flowers',
                        'name' => $bouquet->$flowerField,
                        'quantity' => 1,
                        'price' => $item_data->price ?? 0
                    ];
                    $totalPrice += $item_data->price ?? 0;
                }
            }
        }
        
        // Get greenery
        if ($bouquet->greenery) {
            $item_data = $items->firstWhere('name', $bouquet->greenery);
            if ($item_data) {
                $components[] = [
                    'category' => 'Greenery',
                    'name' => $bouquet->greenery,
                    'quantity' => 1,
                    'price' => $item_data->price ?? 0
                ];
                $totalPrice += $item_data->price ?? 0;
            }
        }
        
        // Get filler
        if ($bouquet->filler) {
            $item_data = $items->firstWhere('name', $bouquet->filler);
            if ($item_data) {
                $components[] = [
                    'category' => 'Artificial Flowers',
                    'name' => $bouquet->filler,
                    'quantity' => 1,
                    'price' => $item_data->price ?? 0
                ];
                $totalPrice += $item_data->price ?? 0;
            }
        }
        
        // Get ribbon
        if ($bouquet->ribbon) {
            $item_data = $items->firstWhere('name', $bouquet->ribbon);
            if ($item_data) {
                $components[] = [
                    'category' => 'Ribbon',
                    'name' => $bouquet->ribbon,
                    'quantity' => 1,
                    'price' => $item_data->price ?? 0
                ];
                $totalPrice += $item_data->price ?? 0;
            }
        }
        
        // Add assembly fee
        $components[] = [
            'category' => 'Service',
            'name' => 'Assembly Fee',
            'quantity' => 1,
            'price' => $assemblyFee
        ];
    ?>
    
    <div class="modal fade custom-bouquet-modal" id="customBouquetModal<?php echo e($modalId); ?>" tabindex="-1" aria-labelledby="customBouquetModalLabel<?php echo e($modalId); ?>" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header" style="border-bottom: 1px solid #e9ecef;">
                    <h6 class="modal-title" id="customBouquetModalLabel<?php echo e($modalId); ?>" style="font-weight: 600; color: #222; font-size: 1rem;">Custom Bouquet Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                    <!-- Bouquet Preview Image -->
                    <div class="text-center mb-3">
                        <?php
                            // Get raw preview_image value from database (not accessor)
                            $rawPreviewPath = $bouquet->getAttributes()['preview_image'] ?? null;
                            $isComposite = $rawPreviewPath && (strpos($rawPreviewPath, 'custom_bouquets/') === 0 || strpos($rawPreviewPath, 'custom_bouquet_') !== false);
                            
                            // Build component images array
                            $componentImages = [];
                            $flowerIndex = 0;
                            
                            // Wrapper (background) - matches customize page: fills container
                            if ($bouquet->wrapper) {
                                $wrapperItem = $items->firstWhere('name', $bouquet->wrapper);
                                if ($wrapperItem && $wrapperItem->image && file_exists(storage_path('app/public/' . $wrapperItem->image))) {
                                    $componentImages[] = [
                                        'image' => '/storage/' . $wrapperItem->image, 
                                        'type' => 'wrapper',
                                        'z' => 10,
                                        'style' => 'inset: 0; width: 100%; height: 100%; object-fit: cover;'
                                    ];
                                }
                            }
                            
                            // Flowers (overlay) - matches customize page positioning
                            foreach (['focal_flower_1', 'focal_flower_2', 'focal_flower_3'] as $flowerField) {
                                if ($bouquet->$flowerField) {
                                    $flowerItem = $items->firstWhere('name', $bouquet->$flowerField);
                                    if ($flowerItem && $flowerItem->image && file_exists(storage_path('app/public/' . $flowerItem->image))) {
                                        // Match customize page: first flower centered, others positioned relative
                                        if ($flowerIndex == 0) {
                                            // First flower: matches customize page style (centered, 45% from bottom)
                                            $style = 'width: 20%; height: auto; object-fit: contain; bottom: 45%; left: 44%; transform: translateX(-50%);';
                                        } elseif ($flowerIndex == 1) {
                                            // Second flower: positioned to the right
                                            $style = 'width: 18%; height: auto; object-fit: contain; bottom: 45%; left: 56%; transform: translateX(-50%);';
                                        } else {
                                            // Third flower: positioned to the left
                                            $style = 'width: 18%; height: auto; object-fit: contain; bottom: 45%; left: 32%; transform: translateX(-50%);';
                                        }
                                        $componentImages[] = [
                                            'image' => '/storage/' . $flowerItem->image, 
                                            'type' => 'flower',
                                            'z' => 60,
                                            'style' => $style
                                        ];
                                        $flowerIndex++;
                                    }
                                }
                            }
                            
                            // Greenery - matches customize page: 42% width, centered, 44% from bottom
                            if ($bouquet->greenery) {
                                $greeneryItem = $items->firstWhere('name', $bouquet->greenery);
                                if ($greeneryItem && $greeneryItem->image && file_exists(storage_path('app/public/' . $greeneryItem->image))) {
                                    $componentImages[] = [
                                        'image' => '/storage/' . $greeneryItem->image, 
                                        'type' => 'greenery',
                                        'z' => 20,
                                        'style' => 'width: 42%; height: auto; object-fit: contain; bottom: 44%; left: 50%; transform: translateX(-50%); opacity: 0.95;'
                                    ];
                                }
                            }
                            
                            // Filler - matches customize page: 20% width, right side, 45% from bottom
                            if ($bouquet->filler) {
                                $fillerItem = $items->firstWhere('name', $bouquet->filler);
                                if ($fillerItem && $fillerItem->image && file_exists(storage_path('app/public/' . $fillerItem->image))) {
                                    $componentImages[] = [
                                        'image' => '/storage/' . $fillerItem->image, 
                                        'type' => 'filler',
                                        'z' => 25,
                                        'style' => 'width: 20%; height: auto; object-fit: contain; bottom: 45%; left: 56%; transform: translateX(-50%);'
                                    ];
                                }
                            }
                            
                            // Ribbon (top layer) - matches customize page: 20% width, centered, 29% from bottom
                            if ($bouquet->ribbon) {
                                $ribbonItem = $items->firstWhere('name', $bouquet->ribbon);
                                if ($ribbonItem && $ribbonItem->image && file_exists(storage_path('app/public/' . $ribbonItem->image))) {
                                    $componentImages[] = [
                                        'image' => '/storage/' . $ribbonItem->image, 
                                        'type' => 'ribbon',
                                        'z' => 80,
                                        'style' => 'width: 20%; height: auto; object-fit: contain; bottom: 29%; left: 50%; transform: translateX(-50%);'
                                    ];
                                }
                            }
                        ?>
                        
                        <?php if($isComposite && $rawPreviewPath && file_exists(storage_path('app/public/' . $rawPreviewPath))): ?>
                            <!-- Composite image (GD generated) -->
                            <img src="<?php echo e($bouquet->preview_image); ?>" alt="Custom Bouquet Preview" style="max-width: 100%; max-height: 250px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <?php elseif(count($componentImages) > 0): ?>
                            <!-- CSS-based component layering (fallback when GD not available) -->
                            <div class="component-preview-container" style="position: relative; width: 250px; height: 250px; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; background: #fff;">
                                <?php $__currentLoopData = $componentImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <img src="<?php echo e($comp['image']); ?>" 
                                         alt="Component <?php echo e($comp['type']); ?>" 
                                         style="position: absolute; z-index: <?php echo e($comp['z']); ?>; <?php echo e($comp['style']); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <!-- Fallback: generic image -->
                            <img src="/images/landingpage_bouquet/bokk.png" alt="Custom Bouquet Preview" style="max-width: 100%; max-height: 250px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="mb-3">
                        <h6 style="font-weight: 600; color: #222; margin-bottom: 12px; border-bottom: 2px solid #7bb47b; padding-bottom: 6px; font-size: 0.95rem;">Price Summary</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="font-size: 0.75rem; font-weight: 600; color: #555;">Category</th>
                                        <th style="font-size: 0.75rem; font-weight: 600; color: #555;">Component</th>
                                        <th style="font-size: 0.75rem; font-weight: 600; color: #555; text-align: center;">Quantity</th>
                                        <th style="font-size: 0.75rem; font-weight: 600; color: #555; text-align: right;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="font-size: 0.8rem; color: #666;"><?php echo e($component['category']); ?></td>
                                        <td style="font-size: 0.8rem; color: #222; font-weight: 500;"><?php echo e($component['name']); ?></td>
                                        <td style="font-size: 0.8rem; color: #666; text-align: center;"><?php echo e($component['quantity']); ?></td>
                                        <td style="font-size: 0.8rem; color: #222; text-align: right; font-weight: 500;">₱<?php echo e(number_format($component['price'], 2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot style="border-top: 2px solid #dee2e6;">
                                    <tr>
                                        <td colspan="3" style="font-size: 0.85rem; font-weight: 600; color: #222; padding-top: 10px;">Total Price:</td>
                                        <td style="font-size: 0.9rem; font-weight: 600; color: #7bb47b; text-align: right; padding-top: 10px;">₱<?php echo e(number_format($totalPrice, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-size: 0.8rem; color: #666; padding-top: 6px;">Quantity:</td>
                                        <td style="font-size: 0.8rem; color: #222; text-align: right; padding-top: 6px; font-weight: 500;"><?php echo e($bouquet->pivot->quantity); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-size: 0.85rem; font-weight: 600; color: #222; padding-top: 6px;">Subtotal:</td>
                                        <td style="font-size: 0.9rem; font-weight: 600; color: #7bb47b; text-align: right; padding-top: 6px;">₱<?php echo e(number_format($totalPrice * $bouquet->pivot->quantity, 2)); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_FLOWERSHOP CAPSTONE\backend\../frontend/resources/views/customer/orders/show.blade.php ENDPATH**/ ?>
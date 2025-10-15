<?php $__env->startSection('content'); ?>
<div class="pt-0 pb-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">


        <div class="row justify-content-center">
            <!-- Left Box - Order Details -->
            <div class="col-12 col-lg-8 col-xl-6" style="max-width: 1200px;">
                <!-- Header Section for Left Box -->
                <div class="mb-2">
                    <div class="rounded-2 p-2" style="background: linear-gradient(135deg, #8ACB88, #7bb47b); box-shadow: 0 2px 10px rgba(0,0,0,0.08); border: none; display: inline-block;">
                        <h4 class="mb-0" style="color: white; font-weight: 600;">
                            Order Details #<?php echo e($order->id); ?>

                        </h4>
                    </div>
    </div>

                <div class="bg-white rounded-3 p-4 mb-4 scrollable-content" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; max-height: 85vh; overflow-y: auto;">
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #8ACB88, #7bb47b); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shopping-bag text-white" style="font-size: 1.5rem;"></i>
        </div>
        </div>
                        <div>
                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700;">Order Information</h4>
                            <p class="text-muted mb-0">Order #<?php echo e($order->id); ?> • <?php echo e($order->created_at->format('M d, Y')); ?></p>
                </div>
                        </div>
                    
                    <!-- Order Status Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-info-circle me-3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Order Status</small>
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
                                    <span class="badge bg-<?php echo e($statusColor); ?> px-3 py-2"><?php echo e($statusDisplay); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-credit-card me-3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Payment Status</small>
                                    <?php if($order->payment_status === 'paid'): ?>
                                        <span class="badge bg-success px-3 py-2">Paid</span>
                                    <?php elseif($order->payment_status === 'pending'): ?>
                                        <span class="badge bg-warning px-3 py-2">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-3 py-2">Unpaid</span>
                            <?php endif; ?>
                        </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Amount & Payment Method -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #8ACB88, #7bb47b); border-radius: 8px; color: white;">
                                <i class="fas fa-dollar-sign me-3"></i>
                                <div>
                                    <small class="opacity-75 d-block">Total Amount</small>
                                    <strong style="font-size: 1.2rem;">₱<?php echo e(number_format($order->total_price, 2)); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-wallet me-3 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Payment Method</small>
                                    <span class="badge bg-info px-3 py-2"><?php echo e(strtoupper($order->payment_method ?? 'N/A')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products in Order -->
                    <div class="mb-4">
                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-box me-2 text-primary"></i>Products in Order
                        </h5>
                        <div class="row g-3">
                            <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #8ACB88;">
                                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" alt="<?php echo e($product->name); ?>">
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1" style="color: #2c3e50;"><?php echo e($product->name); ?></h6>
                                            <small class="text-muted">Quantity: <?php echo e($product->pivot->quantity); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong style="color: #8ACB88; font-size: 1.1rem;">₱<?php echo e(number_format($product->price * $product->pivot->quantity, 2)); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <?php if($order->delivery): ?>
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-truck me-2 text-primary"></i>Delivery Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <i class="fas fa-map-marker-alt me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Delivery Address</small>
                                            <strong><?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <i class="fas fa-user me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Recipient</small>
                                            <strong><?php echo e($order->delivery->recipient_name ?? 'N/A'); ?></strong>
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

            <!-- Right Box - Actions & Customer Info -->
            <div class="col-12 col-lg-4 col-xl-4">
                <!-- Header Section for Right Box -->
                <div class="d-flex justify-content-end mb-2">
                    <a href="<?php echo e(route('customer.orders.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to My Orders
                    </a>
                </div>
                
                <div class="bg-white rounded-3 p-4 mb-4 scrollable-content" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; max-height: 85vh; overflow-y: auto;">
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6c757d, #495057); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700;">Customer Information</h4>
                            <p class="text-muted mb-0">Account Details</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center p-3 mb-3" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-user me-3 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Full Name</small>
                                <strong><?php echo e($order->user->name); ?></strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 mb-3" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-envelope me-3 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <strong><?php echo e($order->user->email); ?></strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px;">
                            <i class="fas fa-phone me-3 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Phone Number</small>
                                <strong><?php echo e($order->user->contact_number ?? 'N/A'); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Payment Receipt / Order Slip / Invoice Options -->
            <?php
                $isCOD = strtoupper($order->payment_method) === 'COD';
                $isOnDelivery = $order->order_status === 'on_delivery' || $order->order_status === 'completed';
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
                
                <!-- Mark as Received Button -->
                <div class="mb-4">
                    <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                        <i class="fas fa-check-circle me-2 text-success"></i>Order Actions
                    </h5>
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
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Custom scrollbar styling for the content areas */
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
                    <img src="<?php echo e(asset('storage/' . $order->delivery->proof_of_delivery_image)); ?>" 
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
                <a href="<?php echo e(asset('storage/' . $order->delivery->proof_of_delivery_image)); ?>" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Download Photo
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/orders/show.blade.php ENDPATH**/ ?>
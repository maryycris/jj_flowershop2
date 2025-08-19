

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Order Details #<?php echo e($order->id); ?></h2>
        <a href="<?php echo e(route('customer.orders.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to My Orders
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Order ID:</strong> #<?php echo e($order->id); ?>

                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong> 
                            <?php if($order->status === 'pending'): ?>
                                <span class="badge bg-warning">Pending Approval</span>
                            <?php else: ?>
                                <span class="badge bg-<?php echo e($order->status === 'approved' ? 'info' : 
                                    ($order->status === 'processing' ? 'primary' : 
                                    ($order->status === 'completed' ? 'success' : 
                                    ($order->status === 'cancelled' ? 'danger' : 'secondary')))); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Order Date:</strong> <?php echo e($order->created_at->format('M d, Y h:i A')); ?>

                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Total Amount:</strong> ₱<?php echo e(number_format($order->total_price, 2)); ?>

                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Payment Status:</strong> 
                            <span class="badge bg-<?php echo e($order->payment_status === 'paid' ? 'success' : 'warning'); ?>"><?php echo e(ucfirst($order->payment_status)); ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Payment Method:</strong> 
                            <span class="badge bg-info"><?php echo e(strtoupper($order->payment_method ?? 'N/A')); ?></span>
                        </div>
                        <?php if($order->notes): ?>
                            <div class="col-md-12 mb-3">
                                <strong>Special Instructions:</strong> <?php echo e($order->notes); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Invoice Buttons -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Invoice Options</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('customer.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                        <i class="fas fa-eye me-2"></i> View Invoice
                    </a>
                    <a href="<?php echo e(route('customer.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                        <i class="fas fa-download me-2"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Products in Order</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="img-thumbnail me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="<?php echo e($product->name); ?>">
                                    <div>
                                        <strong><?php echo e($product->name); ?></strong>
                                        <small class="text-muted d-block">Quantity: <?php echo e($product->pivot->quantity); ?></small>
                                    </div>
                                </div>
                                <span>₱<?php echo e(number_format($product->price * $product->pivot->quantity, 2)); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>

            <?php if($order->delivery): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Delivery Status:</strong> 
                                <span class="badge bg-<?php echo e($order->delivery->status === 'pending' ? 'warning' : 
                                    ($order->delivery->status === 'in_transit' ? 'info' : 
                                    ($order->delivery->status === 'delivered' ? 'success' : 
                                    ($order->delivery->status === 'cancelled' ? 'danger' : 'secondary')))); ?>"><?php echo e(ucfirst($order->delivery->status)); ?></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Delivery Date:</strong> <?php echo e($order->delivery->delivery_date ? date('M d, Y', strtotime($order->delivery->delivery_date)) : 'N/A'); ?>

                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Delivery Time:</strong> <?php echo e($order->delivery->delivery_time ?? 'N/A'); ?>

                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Recipient Name:</strong> <?php echo e($order->delivery->recipient_name ?? 'N/A'); ?>

                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Recipient Phone:</strong> <?php echo e($order->delivery->recipient_phone ?? 'N/A'); ?>

                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Delivery Address:</strong> <?php echo e($order->delivery->delivery_address ?? 'N/A'); ?>

                            </div>
                            <?php if($order->delivery->notes): ?>
                                <div class="col-md-12 mb-3">
                                    <strong>Delivery Notes:</strong> <?php echo e($order->delivery->notes); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Name:</strong> <?php echo e($order->user->name); ?></p>
                    <p class="mb-2"><strong>Email:</strong> <?php echo e($order->user->email); ?></p>
                    <p class="mb-0"><strong>Phone:</strong> <?php echo e($order->user->contact_number ?? 'N/A'); ?></p>
                </div>
            </div>

            <?php if(in_array($order->payment_method, ['gcash','paymaya']) && $order->payment_status === 'unpaid'): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Upload Payment Proof</h5>
                </div>
                <div class="card-body">
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
            </div>
            <?php endif; ?>

            <!-- Order Actions -->
            <?php if($order->status === 'pending' && $order->payment_status !== 'paid'): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Order Actions</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            You can only cancel orders that are still pending and within 24 hours of placement.
                        </p>
                        <form action="<?php echo e(route('customer.orders.cancel', $order->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/orders/show.blade.php ENDPATH**/ ?>
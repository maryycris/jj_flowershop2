

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Order Details #<?php echo e($order->id); ?></h1>
        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Status: <strong><?php echo e(ucfirst($order->status)); ?></strong></span>
            <span>Date: <strong><?php echo e($order->created_at->format('M d, Y')); ?></strong></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Customer Details:</h5>
                    <p><strong>Name:</strong> <?php echo e($order->user->name ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo e($order->user->email ?? 'N/A'); ?></p>
                    <p><strong>Contact:</strong> <?php echo e($order->user->contact_number ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Delivery Information:</h5>
                    <p><strong>Address:</strong> <?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></p>
                    <p><strong>Date:</strong> <?php echo e($order->delivery ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') : 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recipient Details Validation Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-user-check me-2"></i>Recipient Details Validation
            </h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('clerk.orders.validate-recipient', $order->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Recipient Name</label>
                            <input type="text" class="form-control" name="recipient_name" value="<?php echo e($order->delivery->recipient_name ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Recipient Phone</label>
                            <input type="text" class="form-control" name="recipient_phone" value="<?php echo e($order->delivery->recipient_phone ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Relationship to Recipient</label>
                            <input type="text" class="form-control" value="<?php echo e(ucfirst($order->delivery->recipient_relationship ?? 'Not specified')); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Delivery Address</label>
                            <textarea class="form-control" name="delivery_address" rows="3" required><?php echo e($order->delivery->delivery_address ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Special Instructions</label>
                            <textarea class="form-control" name="special_instructions" rows="2" placeholder="Any special delivery instructions..."><?php echo e($order->delivery->special_instructions ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced Recipient Information Display -->
                <?php if($order->delivery->delivery_message || $order->delivery->recipient_relationship): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Additional Recipient Information
                            </h6>
                            <?php if($order->delivery->delivery_message): ?>
                                <div class="mb-2">
                                    <strong>Delivery Message:</strong>
                                    <p class="mb-0 mt-1"><?php echo e($order->delivery->delivery_message); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if($order->delivery->recipient_relationship): ?>
                                <div>
                                    <strong>Relationship:</strong> <?php echo e(ucfirst($order->delivery->recipient_relationship)); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="recipient_verified" name="recipient_verified" required>
                            <label class="form-check-label fw-bold" for="recipient_verified">
                                I have verified all recipient details are correct
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="address_verified" name="address_verified" required>
                            <label class="form-check-label fw-bold" for="address_verified">
                                I have confirmed the delivery address is valid and accessible
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="contact_verified" name="contact_verified" required>
                            <label class="form-check-label fw-bold" for="contact_verified">
                                I have verified the contact number is reachable
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle me-2"></i>Validate Recipient Details
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Payment Receipt / Order Slip / Invoice Options -->
    <?php
        $isCOD = strtoupper($order->payment_method) === 'COD';
        $isOnDelivery = $order->order_status === 'on_delivery' || $order->order_status === 'completed';
    ?>
    
    <?php if($isOnDelivery): ?>
        <!-- Invoice Options - Only show when order is on delivery or completed -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Invoice Options</h5>
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('clerk.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Invoice
                </a>
                <a href="<?php echo e(route('clerk.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    <?php elseif($isCOD): ?>
        <!-- Order Slip - Show when COD payment method -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Order Slip</h5>
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('clerk.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Order Slip
                </a>
                <a href="<?php echo e(route('clerk.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                    <i class="fas fa-download"></i> Download Order Slip
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Payment Receipt - Show for other payment methods -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Payment Receipt</h5>
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('clerk.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Receipt
                </a>
                <a href="<?php echo e(route('clerk.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
                    <i class="fas fa-download"></i> Download Receipt
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Payment Proof Section -->
    <?php
        $latestProof = $order->paymentProofs()->latest()->first();
    ?>
    <?php if($latestProof): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Payment Proof</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Reference Number:</strong> <?php echo e($latestProof->reference_number ?? 'N/A'); ?>

            </div>
            <div class="mb-3">
                <strong>Payment Method:</strong> <?php echo e(strtoupper($latestProof->payment_method)); ?>

            </div>
            <div class="mb-3">
                <strong>Status:</strong> <span class="badge bg-<?php echo e($latestProof->status === 'approved' ? 'success' : ($latestProof->status === 'pending' ? 'warning' : 'danger')); ?>"><?php echo e(ucfirst($latestProof->status)); ?></span>
            </div>
            <div class="mb-3">
                <strong>Screenshot/Receipt:</strong><br>
                <img src="<?php echo e(asset('storage/' . $latestProof->image_path)); ?>" alt="Payment Proof" class="img-fluid rounded" style="max-width: 300px; max-height: 300px;">
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Register Payment Button for COD Orders -->
    <?php if(strtolower($order->payment_method) === 'cod' && $order->invoice_status === 'ready'): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-credit-card me-2"></i>
                Payment Registration Required
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-3">This COD order requires payment registration. Click the button below to register the payment received.</p>
            <a href="<?php echo e(route('clerk.payment.form', $order->id)); ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Register Payment
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Payment Tracking Section -->
    <?php if($order->paymentTracking->count() > 0): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Payment History
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Recorded By</th>
                            <th>Memo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->paymentTracking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($payment->payment_date->format('M d, Y')); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo e(strtoupper($payment->payment_method)); ?></span>
                            </td>
                            <td>₱<?php echo e(number_format($payment->amount, 2)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($payment->status === 'completed' ? 'success' : 'warning'); ?>">
                                    <?php echo e(ucfirst($payment->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e($payment->recordedBy->name ?? 'System'); ?></td>
                            <td><?php echo e($payment->memo ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Products</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Payment Method:</strong>
                <?php if($order->payment_method === 'gcash'): ?>
                    GCASH (Processed via PAY MONGGO)
                <?php elseif($order->payment_method === 'paymaya'): ?>
                    PAYMAYA (Processed via PAY MONGGO)
                <?php elseif($order->payment_method === 'cod'): ?>
                    Cash on Delivery (COD)
                <?php else: ?>
                    <?php echo e(strtoupper($order->payment_method ?? 'N/A')); ?>

                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo e($product->name); ?></strong>
                            <small class="d-block text-muted">SKU: <?php echo e($product->code ?? 'N/A'); ?></small>
                        </div>
                        <div>
                            <span><?php echo e($product->pivot->quantity); ?> x ₱<?php echo e(number_format($product->price, 2)); ?></span>
                            <strong class="ms-3">₱<?php echo e(number_format($product->price * $product->pivot->quantity, 2)); ?></strong>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="card-footer">
            <?php
                $subtotal = $order->products->sum(function($product) {
                    return $product->pivot->quantity * $product->price;
                });
                $shippingFee = $order->delivery->shipping_fee ?? 0;
                if ($shippingFee == 0 && $order->total_price > $subtotal) {
                    $shippingFee = $order->total_price - $subtotal;
                }
                $total = $subtotal + $shippingFee;
            ?>
            <div class="row">
                <div class="col-6 text-start">
                    <div class="mb-2">
                        <strong>Subtotal:</strong> ₱<?php echo e(number_format($subtotal, 2)); ?>

                    </div>
                    <div class="mb-2">
                        <strong>Shipping Fee:</strong> ₱<?php echo e(number_format($shippingFee, 2)); ?>

                    </div>
                </div>
                <div class="col-6 text-end">
                    <h4 class="mb-0">Total: ₱<?php echo e(number_format($total, 2)); ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/orders/show.blade.php ENDPATH**/ ?>

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
                    <?php if($order->delivery && $order->delivery->driver_decision): ?>
                        <p><strong>Driver Decision:</strong> 
                            <span class="badge bg-<?php echo e($order->delivery->driver_decision === 'accepted' ? 'success' : 'danger'); ?>">
                                <?php echo e(ucfirst($order->delivery->driver_decision)); ?>

                            </span>
                            <?php if($order->delivery->driver_decision === 'declined' && $order->delivery->decline_reason): ?>
                                <br><small class="text-muted">Reason: <?php echo e($order->delivery->decline_reason); ?></small>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <?php if($order->delivery && $order->delivery->proof_of_delivery_image): ?>
                        <p><strong>Proof of Delivery:</strong> 
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#proofOfDeliveryModal">
                                <i class="fas fa-camera me-1"></i>View Photo
                            </button>
                            <br><small class="text-muted">Delivered on: <?php echo e($order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A'); ?></small>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
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
                <a href="<?php echo e(route('admin.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Invoice
                </a>
                <a href="<?php echo e(route('admin.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
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
                <a href="<?php echo e(route('admin.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Order Slip
                </a>
                <a href="<?php echo e(route('admin.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
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
                <a href="<?php echo e(route('admin.orders.invoice.view', $order->id)); ?>" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-eye"></i> View Receipt
                </a>
                <a href="<?php echo e(route('admin.orders.invoice.download', $order->id)); ?>" class="btn btn-success">
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
        <div class="card-footer text-end">
            <h4>Total: ₱<?php echo e(number_format($order->total_price, 2)); ?></h4>
        </div>
    </div>
</div>

<!-- Proof of Delivery Modal -->
<?php if($order->delivery && $order->delivery->proof_of_delivery_image): ?>
<div class="modal fade" id="proofOfDeliveryModal" tabindex="-1" aria-labelledby="proofOfDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfDeliveryModalLabel">
                    <i class="fas fa-camera me-2"></i>Proof of Delivery - Order #<?php echo e($order->id); ?>

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
                        <p><strong>Customer:</strong> <?php echo e($order->user->name ?? 'N/A'); ?></p>
                        <p><strong>Delivered on:</strong> <?php echo e($order->delivery->proof_of_delivery_taken_at ? \Carbon\Carbon::parse($order->delivery->proof_of_delivery_taken_at)->format('M d, Y g:i A') : 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Confirmed by:</strong> <?php echo e($order->delivery->driver->name ?? 'Delivery Driver'); ?></p>
                        <p><strong>Delivery Address:</strong> <?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Delivered</span></p>
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
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>
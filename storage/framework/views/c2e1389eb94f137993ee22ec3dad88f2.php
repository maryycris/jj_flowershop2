

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #e6f0e6;">
                        <a href="<?php echo e(route('admin.orders.index', ['type' => 'walkin'])); ?>" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Back" aria-label="Back" style="width:34px;height:34px;padding:0;">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <span class="badge bg-warning text-dark">Quotation</span>
                        <div class="ms-2 small text-muted">Order Number</div>
                        <div class="fw-semibold">00<?php echo e($order->id); ?></div>
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-truck text-dark" style="font-size: 24px;"></i>
                                <small class="text-muted">Deliver</small>
                                <span class="badge bg-primary">1</span>
                            </div>
                            <a href="<?php echo e(route('admin.orders.walkin.validate', $order->id)); ?>" class="btn btn-success">Create Invoice</a>
                            <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                        </div>
                    </div>

                    <div class="px-3 pt-2 pb-3">
                        <div class="row g-0 border" style="border-color:#d9ecd9 !important;">
                            <div class="col-md-4" style="background:#e6f5e6;">
                                <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Customer</div>
                                <div class="p-3 small">
                                    <div class="fw-semibold mb-1"><?php echo e($order->user->name ?? 'Walk-in Customer'); ?></div>
                                    <?php if($order->delivery): ?>
                                        <div><?php echo e($order->delivery->delivery_address); ?></div>
                                        <div><?php echo e($order->delivery->recipient_phone); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="p-3 fw-semibold" style="background:#e6f5e6;border-bottom:1px solid #d9ecd9;">Delivery Address</div>
                                <div class="p-3 small">
                                    <?php if($order->delivery): ?>
                                        <div class="mb-1"><?php echo e($order->delivery->delivery_address); ?></div>
                                    <?php else: ?>
                                        <div class="text-muted">No delivery address</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 fw-semibold" style="background:#e6f5e6;border-bottom:1px solid #d9ecd9;">Order Date</div>
                                <div class="p-3 small"><?php echo e($order->created_at->format('m/d/Y')); ?></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Order Line</div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th style="width:35%">Product</th>
                                            <th style="width:35%">Description</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:15%">Unit Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($product->name); ?></td>
                                                <td><?php echo e($product->description); ?></td>
                                                <td><?php echo e($product->pivot->quantity); ?></td>
                                                <td>₱<?php echo e(number_format($product->price, 2)); ?></td>
                                            </tr>
                                            
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-2 fw-semibold">Total: ₱<?php echo e(number_format($order->total_price, 2)); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/orders/walkin/quotation.blade.php ENDPATH**/ ?>
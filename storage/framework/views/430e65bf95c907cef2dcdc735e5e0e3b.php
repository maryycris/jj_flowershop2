

<?php $__env->startSection('admin_content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #e6f0e6;">
                        <?php
                            $status = $order->order_status ?? $order->status ?? 'pending';
                            $statusMap = [
                                'approved' => ['class' => 'bg-success', 'text' => 'Approved'],
                                'on_delivery' => ['class' => 'bg-info', 'text' => 'On Delivery'],
                                'completed' => ['class' => 'bg-primary', 'text' => 'Completed'],
                                'cancelled' => ['class' => 'bg-danger', 'text' => 'Cancelled'],
                                'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Pending'],
                            ];
                            $badge = $statusMap[$status] ?? $statusMap['pending'];
                        ?>
                        <div class="ms-2 small text-muted"><?php echo e(sprintf('%05d', $order->id)); ?></div>
                        <div class="ms-2 small text-muted"><?php echo e($inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001'); ?></div>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-light d-flex align-items-center" style="border:1px solid #e6e6e6;" onclick="showMoves()">
                                <i class="bi bi-list me-1"></i>
                                Moves
                            </button>
                        </div>
                    </div>

                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="border-bottom:1px solid #e6f0e6;">
                        <?php if(($order->order_status ?? $order->status) === 'pending'): ?>
                            <button type="button" class="btn btn-success" onclick="showConfirmAlert()">Validate</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                            <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" id="readyBtn" onclick="markAsReady()">Ready</button>
                            <button type="button" class="btn btn-sm btn-light" id="doneBtn" onclick="markAsDone()" style="display: none;">Done</button>
                        </div>
                    </div>

                    <div class="px-3 pt-3 pb-4">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="p-3 fw-semibold">INVENTORY / <?php echo e($inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001'); ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 fw-semibold">Delivery Address</div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 small"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 small">
                                    <?php if($order->delivery): ?>
                                        <div class="mb-1"><?php echo e($order->delivery->delivery_address); ?></div>
                                    <?php endif; ?>
                                    <div class="mt-2"><span class="fw-semibold">Schedule Date:</span> <span class="text-muted"><?php echo e(optional($order->created_at)->format('m/d/Y')); ?></span></div>
                                    <div class="mt-1"><span class="fw-semibold">Order Number:</span> <span class="text-muted"><?php echo e(sprintf('%05d', $order->id)); ?></span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Operations</div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th>Product</th>
                                            <th>Demand</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $demand = (int) ($product->pivot->quantity ?? 0);
                                                $stockAvailable = (int) ($product->stock ?? 0);
                                                
                                                // Check if we can fulfill based on composition analysis
                                                $canFulfillFromComposition = false;
                                                $compositionMessage = '';
                                                
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $composition = $productCompositions[$product->id];
                                                    $canFulfillFromComposition = $composition['can_fulfill'];
                                                    $compositionMessage = $canFulfillFromComposition ? 
                                                        'Can be made from materials' : 
                                                        'Insufficient materials';
                                                }
                                                
                                                // Use composition analysis if available, otherwise fall back to stock
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $quantityToProvide = $canFulfillFromComposition ? $demand : 0;
                                                    $isInsufficientStock = !$canFulfillFromComposition;
                                                    $stockMessage = $compositionMessage;
                                                } else {
                                                    $quantityToProvide = max(0, min($demand, $stockAvailable));
                                                    $isInsufficientStock = $stockAvailable < $demand;
                                                    $stockMessage = $isInsufficientStock ? 
                                                        "(Insufficient stock: {$stockAvailable} available)" : 
                                                        "({$stockAvailable} available)";
                                                }
                                            ?>
                                            <tr class="<?php echo e($isInsufficientStock ? 'table-warning' : ''); ?>">
                                                <td><?php echo e($product->name); ?></td>
                                                <td><?php echo e($demand); ?></td>
                                                <td>
                                                    <span class="<?php echo e($isInsufficientStock ? 'text-warning' : ''); ?>">
                                                        <?php echo e($quantityToProvide); ?>

                                                        <?php if($isInsufficientStock): ?>
                                                            <small class="text-muted">(<?php echo e($stockMessage); ?>)</small>
                                                        <?php else: ?>
                                                            <small class="text-success">(<?php echo e($stockMessage); ?>)</small>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Product Composition Breakdown -->
                        <?php if(isset($productCompositions) && !empty($productCompositions)): ?>
                            <div class="mt-4">
                                <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#f8f9fa;border:1px solid #dee2e6;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Product Composition Breakdown</div>
                                <div class="table-responsive" style="border:1px solid #dee2e6;">
                                    <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(isset($productCompositions[$product->id]) && $productCompositions[$product->id]): ?>
                                            <?php
                                                $composition = $productCompositions[$product->id];
                                                $quantity = $product->pivot->quantity;
                                            ?>
                                            <div class="p-3 border-bottom">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0"><?php echo e($product->name); ?> (Qty: <?php echo e($quantity); ?>)</h6>
                                                    <span class="badge bg-<?php echo e($composition['can_fulfill'] ? 'success' : 'danger'); ?>">
                                                        <?php echo e($composition['can_fulfill'] ? 'Can Fulfill' : 'Cannot Fulfill'); ?>

                                                    </span>
                                                </div>
                                                
                                                <?php if($composition['total_components'] > 0): ?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Material</th>
                                                                            <th>Required</th>
                                                                            <th>Available</th>
                                                                            <th>Status</th>
                                                                            <th>Shortage</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php $__currentLoopData = $composition['components']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <tr class="<?php echo e($component['sufficient'] ? '' : 'table-warning'); ?>">
                                                                                <td>
                                                                                    <strong><?php echo e($component['composition']->component_name); ?></strong>
                                                                                    <?php if($component['component']): ?>
                                                                                        <br><small class="text-muted">ID: <?php echo e($component['component']->id); ?></small>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php echo e($component['required_quantity']); ?> <?php echo e($component['composition']->unit); ?>

                                                                                    <br><small class="text-muted"><?php echo e($component['composition']->quantity); ?> per unit</small>
                                                                                </td>
                                                                                <td>
                                                                                    <?php echo e($component['available_stock']); ?> <?php echo e($component['composition']->unit); ?>

                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge bg-<?php echo e($component['status_class']); ?>">
                                                                                        <?php echo e($component['status']); ?>

                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if($component['shortage'] > 0): ?>
                                                                                        <span class="text-danger">
                                                                                            <?php echo e($component['shortage']); ?> <?php echo e($component['composition']->unit); ?> short
                                                                                        </span>
                                                                                    <?php else: ?>
                                                                                        <span class="text-success">✓</span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            Summary: <?php echo e($composition['sufficient_components']); ?>/<?php echo e($composition['total_components']); ?> materials sufficient
                                                        </small>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-muted">
                                                        <i class="fas fa-info-circle"></i> No composition data available for this product.
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for validation -->
<form method="POST" action="<?php echo e(route('admin.orders.online.validate.confirm', $order)); ?>" class="d-none" id="validateForm">
    <?php echo csrf_field(); ?>
</form>

<!-- Moves Modal -->
<div class="modal fade" id="movesModal" tabindex="-1" aria-labelledby="movesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movesModalLabel">Order History & Moves</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="timeline">
                    <?php if($order->statusHistories): ?>
                        <?php $__currentLoopData = $order->statusHistories->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?php echo e(ucfirst($history->status)); ?></div>
                                        <div class="text-muted small"><?php echo e($history->created_at->format('M d, Y g:i A')); ?></div>
                                        <?php if($history->notes): ?>
                                            <div class="text-muted small"><?php echo e($history->notes); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-muted">No history available for this order.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Ready Confirmation Modal -->
<div class="modal fade" id="readyModal" tabindex="-1" aria-labelledby="readyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="readyModalLabel">Mark Order as Ready?</div>
                <div class="mb-3 text-muted">
                    <small>This will mark the order as ready for delivery and assign a driver.</small>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <form method="POST" action="<?php echo e(route('admin.orders.mark-ready', $order)); ?>" class="m-0" id="readyForm">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success" onclick="showReadyAlert()">Confirm Ready</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Done Confirmation Modal -->
<div class="modal fade" id="doneModal" tabindex="-1" aria-labelledby="doneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="doneModalLabel">Complete Order Processing?</div>
                <div class="mb-3 text-muted">
                    <small>This will complete the order processing and mark it as done.</small>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <form method="POST" action="<?php echo e(route('admin.orders.mark-done', $order)); ?>" class="m-0" id="doneForm">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-primary" onclick="showDoneAlert()">Complete</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showMoves() {
    const movesModal = new bootstrap.Modal(document.getElementById('movesModal'));
    movesModal.show();
}

function showConfirmAlert() {
    Swal.fire({
        title: 'Are you sure you want to proceed?',
        text: 'This will validate the order and assign a driver for delivery.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showValidateAlert();
        }
    });
}

function markAsReady() {
    const readyModal = new bootstrap.Modal(document.getElementById('readyModal'));
    readyModal.show();
}

function markAsDone() {
    const doneModal = new bootstrap.Modal(document.getElementById('doneModal'));
    doneModal.show();
}

function showValidateAlert() {
    event.preventDefault(); // Prevent form submission
    
    Swal.fire({
        title: 'Validating Orders',
        text: 'Order is being validated and moved to Ready status',
        icon: 'info',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Submit the form after the alert
        document.getElementById('validateForm').submit();
    });
}

function showReadyAlert() {
    event.preventDefault(); // Prevent form submission
    
    Swal.fire({
        title: 'Marking as Ready...',
        text: 'Order is being marked as ready for delivery',
        icon: 'success',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Submit the form after the alert
        document.getElementById('readyForm').submit();
    });
}

function showDoneAlert() {
    event.preventDefault(); // Prevent form submission
    
    Swal.fire({
        title: 'Completing Order...',
        text: 'Order processing is being completed',
        icon: 'success',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Submit the form after the alert
        document.getElementById('doneForm').submit();
    });
}

// Update button states based on order status
document.addEventListener('DOMContentLoaded', function() {
    const orderStatus = '<?php echo e($order->order_status ?? $order->status); ?>';
    const readyBtn = document.getElementById('readyBtn');
    const doneBtn = document.getElementById('doneBtn');
    
    if (orderStatus === 'approved') {
        readyBtn.style.display = 'inline-block';
        readyBtn.disabled = false;
    } else if (orderStatus === 'on_delivery') {
        readyBtn.style.display = 'none';
        doneBtn.style.display = 'inline-block';
        doneBtn.disabled = false;
    } else if (orderStatus === 'completed') {
        readyBtn.style.display = 'none';
        doneBtn.style.display = 'inline-block';
        doneBtn.disabled = true;
        doneBtn.textContent = 'Completed';
    }
});
</script>

<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 18px;
    bottom: -12px;
    width: 2px;
    background: #e9ecef;
}

.timeline-marker {
    flex-shrink: 0;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/orders/online/validate.blade.php ENDPATH**/ ?>
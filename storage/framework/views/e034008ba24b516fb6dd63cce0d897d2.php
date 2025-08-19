<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php $type = request('type', 'online'); ?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="mb-0"><?php echo e($type == 'online' ? 'Online Orders' : 'Walk-in Orders'); ?></h2>
                <?php if($type == 'walkin'): ?>
                    <a href="<?php echo e(route('admin.orders.create')); ?>" class="btn btn-success">Add New</a>
                <?php endif; ?>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="type" value="<?php echo e($type); ?>">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by name or order number..." value="<?php echo e(request('search')); ?>">
                        <button type="submit" class="btn btn-success">Search</button>
                    </form>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Region</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($type == 'online'): ?>
                            <?php $__empty_1 = true; $__currentLoopData = $onlineOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($order->user->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($order->id); ?></td>
                                    <td><?php echo e($order->created_at->format('m/d/Y')); ?></td>
                                    <td>₱<?php echo e(number_format($order->total_price, 2)); ?></td>
                                    <td><span class="badge bg-warning text-dark"><?php echo e(ucfirst($order->status)); ?></span></td>
                                    <td>
                                        <?php if($order->status === 'pending'): ?>
                                            <form action="<?php echo e(route('admin.orders.approve', $order->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                        <?php elseif($order->status === 'approved'): ?>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDeliveryModal<?php echo e($order->id); ?>">Assign for Delivery</button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center">No online orders found.</td></tr>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php $__empty_1 = true; $__currentLoopData = $walkInOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($order->user->name ?? 'Walk-in Customer'); ?></td>
                                    <td><?php echo e($order->id); ?></td>
                                    <td><?php echo e($order->created_at->format('m/d/Y')); ?></td>
                                    <td>₱<?php echo e(number_format($order->total_price, 2)); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo e(ucfirst($order->status)); ?></span></td>
                                    <td>
                                        <?php if($order->status === 'approved'): ?>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDeliveryModal<?php echo e($order->id); ?>">Assign for Delivery</button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-info">View</a>
                                        <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" class="btn btn-sm btn-outline-secondary">Invoice</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center">No walk-in orders found.</td></tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Success Message -->
<?php if(session('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: '<?php echo e(session('success')); ?>',
                icon: 'success',
                html: `
                    <div class="text-start">
                        <p><?php echo e(session('success')); ?></p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="dontShowAgain">
                            <label class="form-check-label" for="dontShowAgain">
                                Don't show this message again
                            </label>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                allowOutsideClick: true,
                didOpen: () => {
                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        Swal.close();
                    }, 5000);
                }
            });
        });
    </script>
<?php endif; ?>

<!-- Assign for Delivery Modals for Online Orders -->
<?php $__currentLoopData = $onlineOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($order->status === 'approved'): ?>
        <div class="modal fade" id="assignDeliveryModal<?php echo e($order->id); ?>" tabindex="-1" aria-labelledby="assignDeliveryModalLabel<?php echo e($order->id); ?>" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="assignDeliveryModalLabel<?php echo e($order->id); ?>">Assign for Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="<?php echo e(route('admin.orders.assignDelivery', $order->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="driver_id<?php echo e($order->id); ?>" class="form-label">Select Driver</label>
                    <select class="form-select" id="driver_id<?php echo e($order->id); ?>" name="driver_id" required>
                      <option value="">Select Driver</option>
                      <?php $__currentLoopData = \App\Models\User::where('role', 'driver')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($driver->id); ?>"><?php echo e($driver->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="delivery_date<?php echo e($order->id); ?>" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery_date<?php echo e($order->id); ?>" name="delivery_date" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Assign</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Assign for Delivery Modals for Walk-in Orders -->
<?php $__currentLoopData = $walkInOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($order->status === 'approved'): ?>
        <div class="modal fade" id="assignDeliveryModal<?php echo e($order->id); ?>" tabindex="-1" aria-labelledby="assignDeliveryModalLabel<?php echo e($order->id); ?>" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="assignDeliveryModalLabel<?php echo e($order->id); ?>">Assign for Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="<?php echo e(route('admin.orders.assignDelivery', $order->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="driver_id<?php echo e($order->id); ?>" class="form-label">Select Driver</label>
                    <select class="form-select" id="driver_id<?php echo e($order->id); ?>" name="driver_id" required>
                      <option value="">Select Driver</option>
                      <?php $__currentLoopData = \App\Models\User::where('role', 'driver')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($driver->id); ?>"><?php echo e($driver->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="delivery_date<?php echo e($order->id); ?>" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery_date<?php echo e($order->id); ?>" name="delivery_date" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Assign</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        color: #000;
        background-color: #f8f9fc;
        border-color: #dee2e6 #dee2e6 #f8f9fc;
    }
    .tab-content {
        border-top: none;
        padding-top: 1rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Custom SweetAlert function with checkbox and auto-dismiss
    function showSweetAlertWithCheckbox(title, message, icon = 'success', timer = 5000) {
        return Swal.fire({
            title: title,
            html: `
                <div class="text-start">
                    <p>${message}</p>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="dontShowAgain">
                        <label class="form-check-label" for="dontShowAgain">
                            Don't show this message again
                        </label>
                    </div>
                </div>
            `,
            icon: icon,
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            allowOutsideClick: true,
            didOpen: () => {
                // Auto-dismiss after specified time
                setTimeout(() => {
                    Swal.close();
                }, timer);
            }
        });
    }

    // Show success message on page load if exists
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('success')): ?>
            showSweetAlertWithCheckbox('Success!', '<?php echo e(session('success')); ?>', 'success', 5000);
        <?php endif; ?>
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>
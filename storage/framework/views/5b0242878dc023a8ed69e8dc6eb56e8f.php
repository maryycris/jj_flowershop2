<?php $__env->startSection('content'); ?>
<div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Notifications</h3>
            <?php if($notifications->count() > 0): ?>
                <form action="<?php echo e(route('clerk.notifications.deleteAll')); ?>" method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete all notifications?')">
                        <i class="bi bi-trash"></i> Delete All
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('clerk.notifications.index')); ?>" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Notifications</option>
                            <option value="unread" <?php echo e(request('status') == 'unread' ? 'selected' : ''); ?>>Unread Only</option>
                            <option value="read" <?php echo e(request('status') == 'read' ? 'selected' : ''); ?>>Read Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="order" <?php echo e(request('type') == 'order' ? 'selected' : ''); ?>>Orders</option>
                            <option value="inventory" <?php echo e(request('type') == 'inventory' ? 'selected' : ''); ?>>Inventory</option>
                            <option value="event" <?php echo e(request('type') == 'event' ? 'selected' : ''); ?>>Events</option>
                            <option value="payment" <?php echo e(request('type') == 'payment' ? 'selected' : ''); ?>>Payments</option>
                            <option value="system" <?php echo e(request('type') == 'system' ? 'selected' : ''); ?>>System</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        <a href="<?php echo e(route('clerk.notifications.index')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php if($notifications->count() > 0): ?>
            <div class="row">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-primary">
                                            <i class="bi bi-bell-fill me-2"></i>
                                            <?php echo e($notification->data['title'] ?? 'Notification'); ?>

                                        </h6>
                                        <p class="card-text text-muted mb-2">
                                            <?php echo e($notification->data['message'] ?? $notification->data['body'] ?? 'No message available'); ?>

                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo e($notification->created_at->diffForHumans()); ?>

                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        <?php if($notification->read_at): ?>
                                            <span class="badge bg-success">Read</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Unread</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="text-muted mt-3">No Notifications</h4>
                <p class="text-muted">You don't have any notifications at the moment.</p>
            </div>
        <?php endif; ?>
</div>
<?php $__env->startPush('styles'); ?>
<style>
.clerk-sidebar-link { color: #222; font-weight: 500; font-size: 1.08rem; text-decoration: none; transition: color 0.18s; border-radius: 6px; padding: 8px 12px; }
.clerk-sidebar-link.active, .clerk-sidebar-link:hover { background: #e6f2e6; color: #4CAF50 !important;}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/notifications/index.blade.php ENDPATH**/ ?>
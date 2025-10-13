

<?php $__env->startSection('title', 'Inventory Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history me-2"></i>Inventory Logs</h2>
        <div>
            <a href="<?php echo e(route('admin.inventory-logs.export', request()->query())); ?>" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.inventory-logs.index')); ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo e(request('search')); ?>" placeholder="Search logs...">
                </div>
                <div class="col-md-2">
                    <label for="action" class="form-label">Action</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">All Actions</option>
                        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($action); ?>" <?php echo e(request('action') == $action ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($action)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id">
                        <option value="">All Products</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id') == $product->id ? 'selected' : ''); ?>>
                                <?php echo e($product->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="<?php echo e(route('admin.inventory-logs.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Inventory Changes Log</h5>
        </div>
        <div class="card-body">
            <?php if($logs->count()): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Product</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Changes</th>
                                <th>IP Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($log->id); ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo e($log->created_at->format('M d, Y')); ?><br>
                                            <?php echo e($log->created_at->format('h:i A')); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <?php echo e(substr($log->user->name ?? 'U', 0, 1)); ?>

                                            </div>
                                            <div>
                                                <div class="fw-bold"><?php echo e($log->user->name ?? 'Unknown'); ?></div>
                                                <small class="text-muted"><?php echo e($log->user->role ?? 'Unknown'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold"><?php echo e($log->product->name ?? 'Unknown Product'); ?></div>
                                            <small class="text-muted"><?php echo e($log->product->product_code ?? 'N/A'); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php switch($log->action):
                                            case ('edit'): ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </span>
                                                <?php break; ?>
                                            <?php case ('delete'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </span>
                                                <?php break; ?>
                                            <?php case ('create'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-plus me-1"></i>Create
                                                </span>
                                                <?php break; ?>
                                            <?php case ('restore'): ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-undo me-1"></i>Restore
                                                </span>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst($log->action)); ?></span>
                                        <?php endswitch; ?>
                                    </td>
                                    <td>
                                        <small><?php echo e($log->description); ?></small>
                                    </td>
                                    <td>
                                        <?php if($log->changes_summary): ?>
                                            <small class="text-muted"><?php echo e($log->changes_summary); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($log->ip_address ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.inventory-logs.show', $log)); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($logs->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No inventory logs found</h5>
                    <p class="text-muted">No changes have been made to the inventory yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/inventory-logs/index.blade.php ENDPATH**/ ?>
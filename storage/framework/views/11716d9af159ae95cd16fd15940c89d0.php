<?php $__env->startSection('admin_content'); ?>
<div class="container-fluid" style="background: #F6FBF4; min-height: 100vh;">
    <div class="row">
        <!-- Main Dashboard Content (no duplicate nav) -->
        <div class="col pt-4">
            <div class="row g-4 mb-3">
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-pink text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-hourglass-split me-1"></i> Pending Orders</div>
                        <div class="dashboard-count"><?php echo e($pendingOrdersCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'approved'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-blue text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-check2-circle me-1"></i> Approved Orders</div>
                        <div class="dashboard-count"><?php echo e($approvedOrdersCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'on_delivery'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-red text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-truck me-1"></i> On Delivery</div>
                        <div class="dashboard-count"><?php echo e($onDeliveryCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'completed', 'today' => 1])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-yellow text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-star-fill me-1"></i> Complete Order Today</div>
                        <div class="dashboard-count"><?php echo e($completedTodayCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
            </div>
            <!-- Restock Card -->
            <div class="row mt-2">
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.inventory.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-orange text-start p-3" style="min-width:260px;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold me-2" style="color: #6c757d; letter-spacing: 2px;">RESTOCK</span>
                                <i class="bi bi-exclamation-triangle" style="color: #FFD600;"></i>
                            </div>
                            <div class="restock-list">
                                <?php if(isset($restockProducts) && count($restockProducts)): ?>
                                    <?php $__currentLoopData = $restockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex justify-content-between"><span><?php echo e($product->name); ?></span> <span><?php echo e($product->stock); ?></span></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="text-muted">No products need restocking.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.dashboard-card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.18s cubic-bezier(.4,2,.6,1), box-shadow 0.18s; }
.dashboard-card:hover { transform: translateY(-6px) scale(1.04); box-shadow: 0 8px 24px rgba(0,0,0,0.10); cursor: pointer; }
.dashboard-pink { background: #F8D6F8; }
.dashboard-blue { background: #D6E6F8; }
.dashboard-red { background: #F8D6D6; }
.dashboard-yellow { background: #FFF8D6; }
.dashboard-orange { background: #F8D6C1; }
.dashboard-count { font-size: 2.2rem; font-weight: bold; margin-top: 0.5rem; }
.restock-list div { font-size: 1.1rem; margin-bottom: 0.2rem; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>
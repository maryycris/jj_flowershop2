<?php $__env->startSection('content'); ?>
<div class="clerk-dashboard-wrapper d-flex">
    <div class="clerk-sidebar p-4 d-flex flex-column align-items-center" style="min-width:220px;max-width:250px;background:#f8f9f4;height:100vh;">
        <div class="mb-4 text-center">
            <i class="bi bi-person-circle" style="font-size:3.5rem;color:#888;"></i>
            <div class="fw-semibold mt-2 mb-1">Clerk name</div>
        </div>
        <div class="w-100">
            <a href="<?php echo e(route('clerk.dashboard')); ?>" class="clerk-sidebar-link d-flex align-items-center mb-3 <?php if(request()->routeIs('clerk.dashboard')): ?> active <?php endif; ?>">Dashboard</a>
            <a href="<?php echo e(route('clerk.profile.edit')); ?>" class="clerk-sidebar-link d-flex align-items-center mb-3 <?php if(request()->routeIs('clerk.profile.edit')): ?> active <?php endif; ?>">Edit profile</a>
            <a href="<?php echo e(route('clerk.notifications.index')); ?>" class="clerk-sidebar-link d-flex align-items-center mb-3 <?php if(request()->routeIs('clerk.notifications.index')): ?> active <?php endif; ?>">Notification</a>
        </div>
    </div>
    <div class="flex-grow-1 p-4">
        <!-- Main dashboard cards (admin style) -->
        <!-- Example clickable dashboard cards -->
        <div class="dashboard-cards d-flex gap-3">
            <a href="<?php echo e(route('clerk.orders.index', ['status' => 'pending'])); ?>" style="text-decoration: none; color: inherit;">
                <div class="dashboard-card dashboard-pink text-center p-3" style="min-width:180px;">
                    <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-hourglass-split me-1"></i> Pending Orders</div>
                        <div class="dashboard-count"><?php echo e($pendingOrdersCount ?? 0); ?></div>
                </div>
            </a>
            <a href="<?php echo e(route('clerk.orders.index', ['status' => 'approved'])); ?>" style="text-decoration: none; color: inherit;">
                <div class="dashboard-card dashboard-blue text-center p-3" style="min-width:180px;">
                    <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-check2-circle me-1"></i> Approved Orders</div>
                        <div class="dashboard-count"><?php echo e($approvedOrdersCount ?? 0); ?></div>
                </div>
            </a>
            <a href="<?php echo e(route('clerk.orders.index', ['status' => 'on_delivery'])); ?>" style="text-decoration: none; color: inherit;">
                <div class="dashboard-card dashboard-red text-center p-3" style="min-width:180px;">
                    <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-truck me-1"></i> On Delivery</div>
                        <div class="dashboard-count"><?php echo e($onDeliveryCount ?? 0); ?></div>
                </div>
            </a>
            <a href="<?php echo e(route('clerk.orders.index', ['status' => 'completed', 'today' => 1])); ?>" style="text-decoration: none; color: inherit;">
                <div class="dashboard-card dashboard-yellow text-center p-3" style="min-width:180px;">
                    <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-star-fill me-1"></i> Complete Order Today</div>
                        <div class="dashboard-count"><?php echo e($completedTodayCount ?? 0); ?></div>
                </div>
            </a>
        </div>
        <div class="row mt-2">
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo e(route('clerk.inventory.index')); ?>" style="text-decoration: none; color: inherit;">
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
.clerk-sidebar-link { color: #222; font-weight: 500; font-size: 1.08rem; text-decoration: none; transition: color 0.18s; border-radius: 6px; padding: 8px 12px; }
.clerk-sidebar-link.active, .clerk-sidebar-link:hover { background: #e6f2e6; color: #385E42 !important; }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/dashboard.blade.php ENDPATH**/ ?>
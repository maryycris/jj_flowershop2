
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h4 class="mb-3">Promoted Banners</h4>
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBannerModal">
            Add banner
        </button>
    </div>

    <!-- Add Banner Modal -->
    <div class="modal fade" id="addBannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Promoted Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(url('/admin/promoted-banners')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Image *</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link URL</label>
                            <input type="text" name="link_url" class="form-control" placeholder="/customer/products?category=bouquets">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
                <img src="<?php echo e(asset('storage/'.$banner->image)); ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <div class="mb-1"><strong><?php echo e($banner->title); ?></strong></div>
                    <div class="mb-2 small text-muted"><?php echo e($banner->link_url); ?></div>
                    <form action="<?php echo e(url('/admin/promoted-banners/'.$banner->id)); ?>" method="POST" enctype="multipart/form-data" class="row g-2">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="col-12">
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-6">
                            <input type="text" name="title" class="form-control form-control-sm" value="<?php echo e($banner->title); ?>" placeholder="Title">
                        </div>
                        <div class="col-6">
                            <input type="text" name="link_url" class="form-control form-control-sm" value="<?php echo e($banner->link_url); ?>" placeholder="Link URL">
                        </div>
                        <div class="col-4">
                            <input type="number" name="sort_order" class="form-control form-control-sm" value="<?php echo e($banner->sort_order); ?>" placeholder="Order">
                        </div>
                        <div class="col-8 d-grid">
                            <button class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </form>
                    <form action="<?php echo e(url('/admin/promoted-banners/'.$banner->id)); ?>" method="POST" class="mt-2">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-outline-danger btn-sm w-100">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/promoted_banners/index.blade.php ENDPATH**/ ?>
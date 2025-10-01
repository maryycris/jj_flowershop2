
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="bg-white rounded-3 p-3" style="box-shadow:none;">
                <ul class="nav nav-tabs" id="customizeTabs" role="tablist">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php if($i==0): ?> active <?php endif; ?>" data-bs-toggle="tab" data-bs-target="#tab-<?php echo e(Str::slug($cat)); ?>" type="button" role="tab"><?php echo e($cat); ?></button>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <div class="tab-content border-start border-end border-bottom rounded-bottom p-3" id="customizeTabContent">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tab-pane fade <?php if($i==0): ?> show active <?php endif; ?>" id="tab-<?php echo e(Str::slug($cat)); ?>" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><?php echo e($cat); ?> Items</h5>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal" data-category="<?php echo e($cat); ?>"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                        <div class="row g-3">
                            <?php $__currentLoopData = ($items[$cat] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <?php if($item->image): ?>
                                        <img src="<?php echo e(asset('storage/'.$item->image)); ?>" class="card-img-top" style="height:140px;object-fit:cover;">
                                    <?php endif; ?>
                                    <div class="card-body p-2">
                                        <div class="fw-semibold"><?php echo e($item->name); ?></div>
                                        <div class="text-muted small">₱<?php echo e(number_format($item->price ?? 0,2)); ?></div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between p-2">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($item->id); ?>">Edit</button>
                                        <form method="POST" action="<?php echo e(route('clerk.customize.destroy',$item->id)); ?>" onsubmit="return confirm('Delete this item?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo e($item->id); ?>" tabindex="-1">
                              <div class="modal-dialog">
                                <form class="modal-content" method="POST" action="<?php echo e(route('clerk.customize.update',$item->id)); ?>" enctype="multipart/form-data">
                                  <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                  <div class="modal-header"><h5 class="modal-title">Edit <?php echo e($cat); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                  <div class="modal-body">
                                    <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" value="<?php echo e($item->name); ?>" required></div>
                                    <div class="mb-2"><label class="form-label">Category</label>
                                        <select name="category" class="form-select">
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c); ?>" <?php if($item->category==$c): ?> selected <?php endif; ?>><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="mb-2"><label class="form-label">Price (optional)</label><input type="number" step="0.01" name="price" class="form-control" value="<?php echo e($item->price); ?>"></div>
                                    <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control"></div>
                                    <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?php echo e($item->description); ?></textarea></div>
                                  </div>
                                  <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
                                </form>
                              </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal (shared) -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="<?php echo e(route('clerk.customize.store')); ?>" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <div class="modal-header"><h5 class="modal-title">Add Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Category</label>
            <select name="category" id="addCategorySelect" class="form-select">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c); ?>"><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="mb-2"><label class="form-label">Price (optional)</label><input type="number" step="0.01" name="price" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Add</button></div>
    </form>
  </div>
  </div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('addModal')?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (btn && btn.dataset.category) {
        document.getElementById('addCategorySelect').value = btn.dataset.category;
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/customize/index.blade.php ENDPATH**/ ?>

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
                            <h5 class="mb-0">
                                <?php if($cat == 'Greenery'): ?> Greenery Items
                                <?php elseif($cat == 'Ribbons'): ?> Ribbons Items
                                <?php elseif($cat == 'Wrappers'): ?> Wrappers Items
                                <?php else: ?> <?php echo e($cat); ?> Items
                                <?php endif; ?>
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="selectAllBtn btn btn-outline-primary btn-sm" onclick="toggleSelectAll()">
                                    <i class="bi bi-check-square"></i> Select All
                                </button>
                                <button class="removeSelectedBtn btn btn-danger btn-sm" style="display: none;" onclick="removeSelectedItems()">
                                    <i class="bi bi-trash"></i> Remove Selected
                                </button>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal" data-category="<?php echo e($cat); ?>"><i class="bi bi-plus-lg"></i> Add</button>
                            </div>
                        </div>
                        <div class="row g-3">
                            <?php $__currentLoopData = ($items[$cat] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <div class="position-relative">
                                        <?php if($item->image): ?>
                                            <img src="<?php echo e(asset('storage/'.$item->image)); ?>" class="card-img-top" style="height:140px;object-fit:cover;">
                                        <?php endif; ?>
                                        <div class="position-absolute top-0 start-0 p-2" style="z-index: 10;">
                                            <input type="checkbox" class="form-check-input item-checkbox" value="<?php echo e($item->id); ?>" onchange="console.log('Checkbox changed:', this.checked, this.value); toggleRemoveButton();" style="width: 18px; height: 18px; background-color: white; border: 2px solid #007bff;">
                                        </div>
                                    </div>
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

<?php $__env->startPush('styles'); ?>
<style>
.item-checkbox {
    width: 18px !important;
    height: 18px !important;
    background-color: white !important;
    border: 2px solid #007bff !important;
    border-radius: 3px !important;
    cursor: pointer !important;
    z-index: 10 !important;
}

.item-checkbox:checked {
    background-color: #007bff !important;
    border-color: #007bff !important;
}

.removeSelectedBtn {
    transition: all 0.3s ease;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('addModal')?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (btn && btn.dataset.category) {
        document.getElementById('addCategorySelect').value = btn.dataset.category;
    }
});

// Initialize the remove button state on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing remove button');
    // Initially hide all remove buttons
    const removeBtns = document.querySelectorAll('.removeSelectedBtn');
    removeBtns.forEach(btn => {
        btn.style.display = 'none';
    });
    console.log('Initialized', removeBtns.length, 'remove buttons');
    
    // Add event listeners for tab switching
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            console.log('Tab switched to:', e.target.textContent);
            // Re-initialize button state when tab changes
            setTimeout(toggleRemoveButton, 100);
        });
    });
});

function toggleSelectAll() {
    // Get the current active tab
    const activeTab = document.querySelector('.tab-pane.active');
    if (!activeTab) return;
    
    // Get all checkboxes in the current active tab
    const checkboxes = activeTab.querySelectorAll('.item-checkbox');
    const selectAllBtn = activeTab.querySelector('.selectAllBtn');
    
    if (checkboxes.length === 0) return;
    
    // Check if all checkboxes are currently checked
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    if (allChecked) {
        // If all are checked, uncheck all
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        selectAllBtn.innerHTML = '<i class="bi bi-check-square"></i> Select All';
        selectAllBtn.className = 'selectAllBtn btn btn-outline-primary btn-sm';
    } else {
        // If not all are checked, check all
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
        selectAllBtn.innerHTML = '<i class="bi bi-x-square"></i> Deselect All';
        selectAllBtn.className = 'selectAllBtn btn btn-outline-secondary btn-sm';
    }
    
    // Update the remove button state
    toggleRemoveButton();
}

function toggleRemoveButton() {
    // Get all checked checkboxes
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
    // Get all remove buttons
    const removeBtns = document.querySelectorAll('.removeSelectedBtn');
    
    console.log('Checkboxes found:', checkboxes.length);
    console.log('Remove buttons found:', removeBtns.length);
    
    // Show/hide all remove buttons based on whether any checkboxes are checked
    if (checkboxes.length > 0) {
        removeBtns.forEach(btn => {
            btn.style.display = 'inline-block';
        });
        console.log('Showing remove buttons');
    } else {
        removeBtns.forEach(btn => {
            btn.style.display = 'none';
        });
        console.log('Hiding remove buttons');
    }
    
    // Also update the button text to show count
    removeBtns.forEach(btn => {
        if (checkboxes.length > 0) {
            btn.innerHTML = '<i class="bi bi-trash"></i> Remove Selected (' + checkboxes.length + ')';
        } else {
            btn.innerHTML = '<i class="bi bi-trash"></i> Remove Selected';
        }
    });
    
    // Update select all button state
    updateSelectAllButtonState();
}

function updateSelectAllButtonState() {
    // Get the current active tab
    const activeTab = document.querySelector('.tab-pane.active');
    if (!activeTab) return;
    
    const checkboxes = activeTab.querySelectorAll('.item-checkbox');
    const selectAllBtn = activeTab.querySelector('.selectAllBtn');
    
    if (checkboxes.length === 0) return;
    
    const checkedCount = activeTab.querySelectorAll('.item-checkbox:checked').length;
    
    if (checkedCount === 0) {
        selectAllBtn.innerHTML = '<i class="bi bi-check-square"></i> Select All';
        selectAllBtn.className = 'selectAllBtn btn btn-outline-primary btn-sm';
    } else if (checkedCount === checkboxes.length) {
        selectAllBtn.innerHTML = '<i class="bi bi-x-square"></i> Deselect All';
        selectAllBtn.className = 'selectAllBtn btn btn-outline-secondary btn-sm';
    } else {
        selectAllBtn.innerHTML = '<i class="bi bi-check-square"></i> Select All';
        selectAllBtn.className = 'selectAllBtn btn btn-outline-primary btn-sm';
    }
}

function removeSelectedItems() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const selectedIds = Array.from(checkboxes).map(cb => cb.value);
    
    console.log('Selected IDs:', selectedIds);
    
    if (selectedIds.length === 0) {
        alert('Please select items to remove.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} selected item(s)?`)) {
        // Create a form to submit the bulk delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("clerk.customize.bulk-delete")); ?>';
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);
        
        // Add method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Add selected IDs
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        console.log('Submitting form with action:', form.action);
        form.submit();
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/customize/index.blade.php ENDPATH**/ ?>
<?php $__env->startPush('styles'); ?>
<style>
.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-icon i {
    font-size: 14px;
}

/* Action Buttons Styling */
.action-btn {
    width: 50px;
    height: 40px;
    border: none;
    background: transparent;
    color: #4CAF50;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    padding: 0;
    font-size: 16px;
    flex: 1;
    min-width: 50px;
    max-width: 50px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.action-btn i {
    transition: color 0.3s ease;
}

/* Edit Button */
.edit-btn:hover {
    background-color: #007bff;
    color: white;
}

.edit-btn:hover i {
    color: white;
}

/* Delete Button */
.delete-btn:hover {
    background-color: #dc3545;
    color: white;
}

.delete-btn:hover i {
    color: white;
}


/* Ensure buttons are evenly spaced and fill the column */
.d-flex.justify-content-center.gap-2 {
    width: 100%;
    max-width: 120px;
    margin: 0 auto;
    gap: 8px !important;
}

/* Make sure both buttons have exactly the same width */
.edit-btn, .delete-btn {
    width: 50px !important;
    flex: 1 1 50px;
}

/* Marked for deletion styling */
.marked-for-deletion {
    border: 2px solid #dc3545 !important;
    background-color: transparent !important;
}

.marked-for-deletion td {
    border-color: #dc3545 !important;
    background-color: transparent !important;
}

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('admin_content'); ?>
<div class="mx-auto" style="max-width: 1100px; padding-top: 24px;">
<!-- Main Tab Navigation -->
<ul class="nav nav-tabs mb-4" id="mainInventoryTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="true">
            <i class="bi bi-box-seam me-2"></i>Inventory
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inventory-logs-tab" data-bs-toggle="tab" data-bs-target="#inventory-logs" type="button" role="tab" aria-controls="inventory-logs" aria-selected="false">
            <i class="bi bi-clock-history me-2"></i>Inventory Logs
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="mainInventoryTabsContent">
    <!-- Inventory Tab -->
    <div class="tab-pane fade show active" id="inventory" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Inventory Management</h4>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" id="saveChangesBtn" title="Save inventory changes and create history record">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add New Product</button>
            </div>
        </div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
      <form action="<?php echo e(route('admin.inventory.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
          <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Select Category</option>
              <option value="Fresh Flowers">Fresh Flowers</option>
              <option value="Dried Flowers">Dried Flowers</option>
              <option value="Artificial Flowers">Artificial Flowers</option>
              <option value="Floral Supplies">Floral Supplies</option>
              <option value="Packaging Materials">Packaging Materials</option>
              <option value="Wrappers">Wrappers</option>
              <option value="Ribbon">Ribbon</option>
              <option value="Other Offers">Other Offers</option>
              <option value="Greenery">Greenery</option>
            </select>
                        </div>
          <div class="mb-3">
            <label for="price" class="form-label">Selling Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
          <div class="mb-3">
            <label for="cost_price" class="form-label">Acquisition Cost</label>
            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price">
                </div>
          <div class="mb-3">
            <label for="reorder_min" class="form-label">Reordering Min</label>
            <input type="number" class="form-control" id="reorder_min" name="reorder_min">
        </div>
          <div class="mb-3">
            <label for="reorder_max" class="form-label">Reordering Max</label>
            <input type="number" class="form-control" id="reorder_max" name="reorder_max">
                        </div>
          <div class="mb-3">
            <label for="stock" class="form-label">Qty On Hand</label>
            <input type="number" class="form-control" id="stock" name="stock">
                    </div>
          <div class="mb-3">
            <label for="qty_consumed" class="form-label">Qty Consumed</label>
            <input type="number" class="form-control" id="qty_consumed" name="qty_consumed">
                </div>
          <div class="mb-3">
            <label for="qty_damaged" class="form-label">Qty Damaged</label>
            <input type="number" class="form-control" id="qty_damaged" name="qty_damaged">
        </div>
          <div class="mb-3">
            <label for="qty_sold" class="form-label">Qty Sold</label>
            <input type="number" class="form-control" id="qty_sold" name="qty_sold">
                    </div>
                </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Toolbar: Search + Update -->
<div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
    <div class="input-group" style="max-width: 360px;">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" id="inventorySearch" placeholder="Search code, name, or category..." autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" id="clearInventorySearch">Clear</button>
    </div>
    </div>

<!-- Inventory Submitted Modal -->
<div class="modal fade" id="inventorySubmittedModal" tabindex="-1" aria-labelledby="inventorySubmittedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
          <circle cx="12" cy="12" r="12" fill="#e6f4ea"/>
          <path d="M7 13l3 3 7-7" stroke="#4caf50" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h5 class="mb-3">YOUR INVENTORY CHANGES HAVE BEEN SUBMITTED FOR REVIEW</h5>
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
</div>

<?php if($products->count()): ?>
    <!-- Bootstrap Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
        <?php $__currentLoopData = ['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Greenery', 'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Other Offers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php if($loop->first): ?> active <?php endif; ?>" id="tab-<?php echo e(Str::slug($category)); ?>" data-bs-toggle="tab" data-bs-target="#<?php echo e(Str::slug($category)); ?>" type="button" role="tab" aria-controls="<?php echo e(Str::slug($category)); ?>" aria-selected="<?php echo e($loop->first ? 'true' : 'false'); ?>">
                    <?php echo e($category); ?>

                </button>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <div class="tab-content" id="inventoryTabsContent">
        <?php $__currentLoopData = ['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Greenery', 'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Other Offers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tab-pane fade <?php if($loop->first): ?> show active <?php endif; ?>" id="<?php echo e(Str::slug($category)); ?>" role="tabpanel">
            <div class="table-responsive inventory-scroll">
                    <table class="table table-bordered align-middle">
                        <thead>
                    <tr>
                                <th>Product Code</th>
                        <th>Name</th>
                        <th>Category</th>
                                <th>Selling Price</th>
                                <th>Acquisition Cost</th>
                                <th colspan="2">Reordering Rules<br><small>(Min / Max)</small></th>
                                <th>Qty On Hand</th>
                                <th>Qty Consumed</th>
                                <th>Qty Damaged</th>
                                <th>Qty Sold</th>
                                <th>Qty to Purchase<br><small>(Max - On Hand)</small></th>
                                <th>Date</th>
                                <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($product->category === $category): ?>
                                <?php
                                    $min = $product->reorder_min ?? 0;
                                    $max = $product->reorder_max ?? 0;
                                    $stock = $product->stock ?? 0;
                                    $qtyToPurchase = ($stock < $max) ? ($max - $stock) : 0;
                                ?>
                                    <tr id="product-row-<?php echo e($product->id); ?>" data-product-id="<?php echo e($product->id); ?>"
                                        <?php if($product->is_marked_for_deletion): ?> 
                                            class="marked-for-deletion" 
                                            style="border: 2px solid #dc3545 !important;"
                                        <?php endif; ?>>
                                    <td><?php echo e($product->code ?? $product->id); ?></td>
                                    <td><?php echo e($product->name); ?></td>
                                    <td><?php echo e($product->category); ?></td>
                                    <td><?php echo e($product->price); ?></td>
                                    <td><?php echo e($product->cost_price ?? '-'); ?></td>
                                    <td><?php echo e($min); ?></td>
                                    <td><?php echo e($max); ?></td>
                                    <td><?php echo e($stock); ?></td>
                                    <td><?php echo e($product->qty_consumed ?? '-'); ?></td>
                                    <td><?php echo e($product->qty_damaged ?? '-'); ?></td>
                                    <td><?php echo e($product->qty_sold ?? '-'); ?></td>
                                    <td><?php echo e($qtyToPurchase); ?></td>
                                    <td><?php echo e($product->created_at ? $product->created_at->format('Y-m-d') : '-'); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Edit Button -->
                                            <button class="btn btn-sm action-btn edit-btn edit-product-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo e($product->id); ?>" data-product-id="<?php echo e($product->id); ?>"><i class="bi bi-pencil-square"></i></button>
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm action-btn delete-btn delete-product-btn" title="Delete" data-product-id="<?php echo e($product->id); ?>"><i class="bi bi-trash3"></i></button>
                                        </div>
                                            <!-- Edit Modal -->
                                <div class="modal fade" id="editProductModal<?php echo e($product->id); ?>" tabindex="-1" aria-labelledby="editProductModalLabel<?php echo e($product->id); ?>" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel<?php echo e($product->id); ?>">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form action="<?php echo e(route('admin.inventory.update', $product->id)); ?>" method="POST" data-product-id="<?php echo e($product->id); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                                          <div class="mb-3">
                                                        <label for="name<?php echo e($product->id); ?>" class="form-label">Product Name</label>
                                                        <input type="text" class="form-control" id="name<?php echo e($product->id); ?>" name="name" value="<?php echo e($product->name); ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="category<?php echo e($product->id); ?>" class="form-label">Category</label>
                                                        <select class="form-select" id="category<?php echo e($product->id); ?>" name="category" required>
                                                          <option value="Fresh Flowers" <?php if($product->category == 'Fresh Flowers'): ?> selected <?php endif; ?>>Fresh Flowers</option>
                                                          <option value="Dried Flowers" <?php if($product->category == 'Dried Flowers'): ?> selected <?php endif; ?>>Dried Flowers</option>
                                                          <option value="Artificial Flowers" <?php if($product->category == 'Artificial Flowers'): ?> selected <?php endif; ?>>Artificial Flowers</option>
                                                          <option value="Floral Supplies" <?php if($product->category == 'Floral Supplies'): ?> selected <?php endif; ?>>Floral Supplies</option>
                                                          <option value="Packaging Materials" <?php if($product->category == 'Packaging Materials'): ?> selected <?php endif; ?>>Packaging Materials</option>
                                                          <option value="Wrappers" <?php if($product->category == 'Wrappers'): ?> selected <?php endif; ?>>Wrappers</option>
                                                          <option value="Ribbon" <?php if($product->category == 'Ribbon'): ?> selected <?php endif; ?>>Ribbon</option>
                                                          <option value="Other Offers" <?php if($product->category == 'Other Offers'): ?> selected <?php endif; ?>>Other Offers</option>
                                                          <option value="Greenery" <?php if($product->category == 'Greenery'): ?> selected <?php endif; ?>>Greenery</option>
                                                        </select>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="price<?php echo e($product->id); ?>" class="form-label">Selling Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="price<?php echo e($product->id); ?>" name="price" value="<?php echo e($product->price); ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="cost_price<?php echo e($product->id); ?>" class="form-label">Acquisition Cost</label>
                                                        <input type="number" step="0.01" class="form-control" id="cost_price<?php echo e($product->id); ?>" name="cost_price" value="<?php echo e($product->cost_price); ?>">
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="reorder_min<?php echo e($product->id); ?>" class="form-label">Reordering Min</label>
                                                        <input type="number" class="form-control" id="reorder_min<?php echo e($product->id); ?>" name="reorder_min" value="<?php echo e($product->reorder_min); ?>">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="reorder_max<?php echo e($product->id); ?>" class="form-label">Reordering Max</label>
                                                        <input type="number" class="form-control" id="reorder_max<?php echo e($product->id); ?>" name="reorder_max" value="<?php echo e($product->reorder_max); ?>">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="stock<?php echo e($product->id); ?>" class="form-label">Qty On Hand</label>
                                                        <input type="number" class="form-control" id="stock<?php echo e($product->id); ?>" name="stock" value="<?php echo e($product->stock); ?>">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_consumed<?php echo e($product->id); ?>" class="form-label">Qty Consumed</label>
                                                        <input type="number" class="form-control" id="qty_consumed<?php echo e($product->id); ?>" name="qty_consumed" value="<?php echo e($product->qty_consumed); ?>">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_damaged<?php echo e($product->id); ?>" class="form-label">Qty Damaged</label>
                                                        <input type="number" class="form-control" id="qty_damaged<?php echo e($product->id); ?>" name="qty_damaged" value="<?php echo e($product->qty_damaged); ?>">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_sold<?php echo e($product->id); ?>" class="form-label">Qty Sold</label>
                                                        <input type="number" class="form-control" id="qty_sold<?php echo e($product->id); ?>" name="qty_sold" value="<?php echo e($product->qty_sold); ?>">
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                      <button type="submit" class="btn btn-primary" id="updateBtn<?php echo e($product->id); ?>">Update</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                </td>
                                    </tr>
                                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>
    <p>No products found.</p>
<?php endif; ?>
    </div> <!-- End Inventory Tab -->
    
    <!-- Inventory Logs Tab -->
    <div class="tab-pane fade" id="inventory-logs" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Inventory Update Requests</h4>
            <small class="text-muted">Pending Logs: <?php echo e($pendingLogs->count()); ?></small>
        </div>
        
        
        
        

        <!-- Inventory Update Request Section (dynamic) -->
        <div class="update-request-section">
            <h4>Inventory Update Request</h4>
            
            <?php if($pendingLogs->count() > 0): ?>
                <div class="request-header">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Clerk's name:</strong> <span id="clerkName"><?php echo e($pendingLogs->first()->user->name ?? 'Unknown'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Date:</strong> <span id="requestDate"><?php echo e($pendingLogs->first()->created_at->format('Y-m-d')); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="request-actions">
                    <button class="btn btn-accept" id="acceptChangesBtn">
                        <i class="bi bi-check-circle me-1"></i> Accept Changes
                    </button>
                    <button class="btn btn-decline" id="declineChangesBtn">
                        <i class="bi bi-x-circle me-1"></i> Decline
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Category Tabs (always show) -->
            <ul class="nav nav-tabs" id="updateRequestTabs" role="tablist">
                <?php
                    $firstTabWithLogs = null;
                    foreach($categories as $cat) {
                        if(isset($logsByCategory[$cat]) && $logsByCategory[$cat]->count() > 0) {
                            $firstTabWithLogs = $cat;
                            break;
                        }
                    }
                ?>
                <?php $__currentLoopData = ($categories ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $tabId = Str::slug($cat); ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo e(($firstTabWithLogs && $cat === $firstTabWithLogs) ? 'active' : ($index === 0 && !$firstTabWithLogs ? 'active' : '')); ?>" 
                                id="<?php echo e($tabId); ?>-tab" 
                                data-bs-target="#<?php echo e($tabId); ?>" 
                                type="button" 
                                role="tab" 
                                aria-controls="<?php echo e($tabId); ?>">
                            <?php echo e($cat); ?> 
                            <?php if(isset($logsByCategory[$cat]) && $logsByCategory[$cat]->count() > 0): ?>
                                <span class="badge bg-primary ms-1"><?php echo e($logsByCategory[$cat]->count()); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            
            <div class="tab-content" id="updateRequestTabContent">
                <?php $__currentLoopData = ($categories ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $tabId = Str::slug($cat); ?>
                    <div class="category-tab-content <?php echo e(($firstTabWithLogs && $cat === $firstTabWithLogs) ? 'active' : ($index === 0 && !$firstTabWithLogs ? 'active' : '')); ?>" 
                         id="<?php echo e($tabId); ?>" 
                         data-category="<?php echo e($cat); ?>">
                        <div class="update-request-table">
                            <?php $catLogs = ($logsByCategory[$cat] ?? collect()); ?>
                            <div class="alert alert-info mb-3">
                                <strong><?php echo e($cat); ?>:</strong> <?php echo e($catLogs->count()); ?> pending logs
                            </div>
                            <?php if($catLogs->count() > 0): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product Code</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Selling Price</th>
                                            <th>Acquisition Cost</th>
                                            <th>Reordering Rules (Min / Max)</th>
                                            <th>Qty On Hand</th>
                                            <th>Qty Consumed</th>
                                            <th>Qty Damaged</th>
                                            <th>Qty Sold</th>
                                            <th>Qty to Purchase (Max - On Hand)</th>
                                            <th>Date</th>
                                            <th style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $catLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $p = $log->product; 
                                                $nv = (array)($log->new_values ?? []);
                                                $rowClass = $log->action === 'create' ? 'row-added' : ($log->action === 'edit' ? 'row-edited' : 'row-deleted');
                                            ?>
                                            <tr class="<?php echo e($rowClass); ?>">
                                                <td><?php echo e($log->action === 'create' ? 'NEW' : ($p->code ?? $p->id ?? 'N/A')); ?></td>
                                                <td><?php echo e($nv['name'] ?? ($p->name ?? 'Product Deleted')); ?></td>
                                                <td><?php echo e($nv['category'] ?? ($p->category ?? 'N/A')); ?></td>
                                                <td><?php echo e($nv['price'] ?? ($p->price ?? '0')); ?></td>
                                                <td><?php echo e($nv['cost_price'] ?? ($p->cost_price ?? 'N/A')); ?></td>
                                                <td><?php echo e(($nv['reorder_min'] ?? ($p->reorder_min ?? 0)) . ' / ' . ($nv['reorder_max'] ?? ($p->reorder_max ?? 0))); ?></td>
                                                <td><?php echo e($nv['stock'] ?? ($p->stock ?? 0)); ?></td>
                                                <td><?php echo e($nv['qty_consumed'] ?? ($p->qty_consumed ?? 0)); ?></td>
                                                <td><?php echo e($nv['qty_damaged'] ?? ($p->qty_damaged ?? 0)); ?></td>
                                                <td><?php echo e($nv['qty_sold'] ?? ($p->qty_sold ?? 0)); ?></td>
                                                <td><?php echo e(max(0, (int)($nv['reorder_max'] ?? ($p->reorder_max ?? 0)) - (int)($nv['stock'] ?? ($p->stock ?? 0)))); ?></td>
                                                <td><?php echo e(optional(optional($log)->created_at)->format('Y-m-d')); ?></td>
                                                <td>
                                                    <?php if(($log->status ?? 'pending') === 'pending'): ?>
                                                        <form method="post" action="<?php echo e(route('admin.admin.inventory.approve-pending')); ?>" onsubmit="return approveSingle(event, <?php echo e($log->id); ?>);" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button class="btn btn-success btn-sm">Approve</button>
                                                        </form>
                                                        <form method="post" action="<?php echo e(route('admin.admin.inventory.reject-single', $log->id)); ?>" onsubmit="return rejectSingle(event, <?php echo e($log->id); ?>);" class="d-inline ms-1">
                                                            <?php echo csrf_field(); ?>
                                                            <button class="btn btn-outline-danger btn-sm">Decline</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3">No Pending Changes</h4>
                                    <p class="text-muted">All inventory changes have been reviewed.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div> <!-- End Inventory Logs Tab -->
</div> <!-- End Tab Content -->


<style>
/* Inventory Add and Edit Modal scrollbar styling */
#addProductModal .modal-body::-webkit-scrollbar,
[id^="editProductModal"] .modal-body::-webkit-scrollbar {
    width: 6px;
}
#addProductModal .modal-body::-webkit-scrollbar-track,
[id^="editProductModal"] .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
#addProductModal .modal-body::-webkit-scrollbar-thumb,
[id^="editProductModal"] .modal-body::-webkit-scrollbar-thumb {
    background: #7bb47b;
    border-radius: 3px;
}
#addProductModal .modal-body::-webkit-scrollbar-thumb:hover,
[id^="editProductModal"] .modal-body::-webkit-scrollbar-thumb:hover {
    background: #5aa65a;
}

.product-row-edited {
    background-color: rgba(135, 206, 235, 0.5) !important; /* 50% transparent sky-blue */
    transition: background-color 0.3s ease;
}

.product-row-edited td {
    background-color: rgba(135, 206, 235, 0.5) !important; /* Blue background for all cells */
    border-color: #87CEEB !important;
}

.product-row-deleted {
    background-color: rgba(255, 99, 99, 0.5) !important; /* 50% transparent red */
    transition: background-color 0.3s ease;
}

.product-row-deleted td {
    background-color: rgba(255, 99, 99, 0.5) !important; /* Red background for all cells */
    border-color: #FF6363 !important;
}
</style>
<style>
/* Override Bootstrap table-striped styling */
.table tbody tr:nth-of-type(odd) {
    background-color: transparent !important;
}

.table tbody tr:nth-of-type(even) {
    background-color: transparent !important;
}

/* Ensure full row background coverage */
.table tbody tr {
    background-color: transparent !important;
}

.table tbody tr td {
    background-color: transparent !important;
    border-color: #dee2e6 !important;
}

.inventory-scroll { max-height: 60vh; overflow-y: scroll; overflow-x: auto; scrollbar-color: #7bb47b #f1f1f1; scrollbar-width: thin; }
.inventory-scroll table { margin-bottom: 0; }
.inventory-scroll table thead th { position: sticky; top: 0; background: #e6f4ea; color: #1e874b; z-index: 2; }
/* Green scrollbar styling (WebKit) */
.inventory-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
.inventory-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
.inventory-scroll::-webkit-scrollbar-thumb { background: #7bb47b; border-radius: 4px; }
.inventory-scroll::-webkit-scrollbar-thumb:hover { background: #5aa65a; }
/* Hide scrollbar arrows */
.inventory-scroll::-webkit-scrollbar-button { display: none; width: 0; height: 0; }

/* Make category tab names green */
#inventoryTabs .nav-link { color: #27ae60 !important; }
#inventoryTabs .nav-link.active { color: #1e874b !important; border-color: transparent !important; }


/* Inventory Update Request Styling */
.update-request-section {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.request-header {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.request-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.btn-accept {
    background: #28a745;
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
}

.btn-accept:hover {
    background: #218838;
    color: white;
}

.btn-decline {
    background: transparent;
    border: 2px solid #dc3545;
    color: #dc3545;
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 500;
}

.btn-decline:hover {
    background: #dc3545;
    color: white;
}

/* Table styling for update request */
.update-request-table {
    margin-top: 20px;
}

.update-request-table .table {
    margin-bottom: 0;
}

.update-request-table .table th {
    background: #e6f4ea;
    border-bottom: 2px solid #28a745;
    font-weight: 600;
    color: #2d5a2d;
}

/* Row border colors */
.row-added {
    border: 2px solid #28a745 !important;
}

.row-edited {
    border: 2px solid #007bff !important;
}

.row-deleted {
    border: 2px solid #dc3545 !important;
}

/* Soft background colors similar to clerk side */
.row-added td { background-color: #e8fbe8 !important; }
.row-edited td { background-color: #e8f0ff !important; }
.row-deleted td { background-color: #fdeaea !important; }

/* Custom tab content visibility */
.category-tab-content {
    display: none !important;
}

.category-tab-content.active {
    display: block !important;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin inventory highlighting script loaded');
    
    // Admin inventory changes are applied immediately - no tracking needed
    
    // Function to handle Edit button clicks
    function handleEditClick(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Edit clicked for product:', productId);
        
        if (row) {
            console.log('Edit modal opened for product:', productId);
        }
    }
    
    // Function to handle modal form submission (Update button in modal)
    function handleModalUpdate(event) {
        event.preventDefault();
        // Resolve the form element reliably whether called via button or form
        const form = (this && this.tagName === 'FORM')
            ? this
            : (event.target.closest ? event.target.closest('form[data-product-id]') : null);
        if (!form) {
            console.error('handleModalUpdate: Could not resolve form element');
            alert('Unable to update: form not found.');
            return;
        }
        const productId = form.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Modal Update clicked for product:', productId);
        console.log('Form action:', form.action);
        try { console.log('Form data ready'); } catch (_) {}
        
        // Submit the form data
        const formData = new FormData(form);
        
        // Ensure CSRF token is included in form data
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.set('_token', csrfToken);
        
        console.log('CSRF Token:', csrfToken);
        console.log('Form action:', form.action);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            const ct = response.headers.get('content-type') || '';
            if (ct.includes('application/json')) { return response.json(); }
            return response.text().then(t => { throw new Error('Unexpected response: ' + t.slice(0, 120)); });
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                console.log('Product updated successfully:', productId);
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal' + productId));
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                alert('Product updated successfully!');
                
                // Reload the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error updating product:', error);
            alert('Error updating product. Please try again. Error: ' + error.message);
        });
    }
    
    // Function to handle Delete button clicks (Admin - immediate deletion)
    function handleDeleteClick(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Delete clicked for product:', productId);
        
        if (row) {
            // Show confirmation message
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Send delete request
                fetch(`/admin/inventory/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Delete response status:', response.status);
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Delete failed');
                })
                .then(data => {
                    console.log('Delete response data:', data);
                    if (data.success) {
                        // Remove row from table
                        row.remove();
                        alert('Product deleted successfully!');
                    } else {
                        alert('Failed to delete product: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Error deleting product. Please try again.');
                });
            }
        }
    }
    
    // Use event delegation for dynamically loaded content
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-product-btn')) {
            handleEditClick.call(event.target, event);
        } else if (event.target.classList.contains('delete-product-btn')) {
            handleDeleteClick.call(event.target, event);
        } else if (event.target.type === 'submit' && event.target.closest('form[data-product-id]')) {
            // Handle form submission (Update button in modal)
            const form = event.target.closest('form[data-product-id]');
            handleModalUpdate.call(form, event);
        } else if (event.target.id && event.target.id.startsWith('updateBtn')) {
            // Handle Update button click directly
            event.preventDefault();
            const form = event.target.closest('form[data-product-id]');
            if (form) {
                handleModalUpdate.call(form, event);
            }
        }
    });

    // Simple client-side filter for inventory table rows
    const searchInput = document.getElementById('inventorySearch');
    const clearBtn = document.getElementById('clearInventorySearch');
    function filterRows(query) {
        const normalized = (query || '').toLowerCase().trim();
        const activePane = document.querySelector('#inventoryTabsContent .tab-pane.show.active');
        if (!activePane) return;
        const rows = activePane.querySelectorAll('tbody tr[id^="product-row-"]');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const code = (cells[0]?.textContent || '').toLowerCase();
            const name = (cells[1]?.textContent || '').toLowerCase();
            const category = (cells[2]?.textContent || '').toLowerCase();
            const match = !normalized || code.includes(normalized) || name.includes(normalized) || category.includes(normalized);
            row.style.display = match ? '' : 'none';
        });
    }
    if (searchInput) {
        searchInput.addEventListener('input', function(e){ filterRows(e.target.value); });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function(){ searchInput.value = ''; filterRows(''); searchInput.focus(); });
    }
    // When switching tabs, re-apply current filter
    document.getElementById('inventoryTabs')?.addEventListener('shown.bs.tab', function(){ filterRows(searchInput?.value || ''); });
    
    // Admin inventory changes are applied immediately - no Update button needed

    // Handle Save Changes button
    document.getElementById('saveChangesBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to save all inventory changes? This will create a history record in Inventory Reports.')) {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...';
            this.disabled = true;
            
            // Simulate save process (replace with actual API call later)
            setTimeout(() => {
                // Reset button state
                this.innerHTML = originalText;
                this.disabled = false;
                
                // Show success message
                alert('Inventory changes saved successfully! History record created in Inventory Reports.');
                
                // Here you would typically:
                // 1. Collect all modified inventory data
                // 2. Send to backend to save changes
                // 3. Create history record for Inventory Reports
                // 4. Reset any tracking variables
                
            }, 1500); // Simulate 1.5 second save process
        }
    });

    // Inventory Logs Tab Functionality
    // Remove old bulk-accept button wiring if element not present
    const bulkAccept = document.getElementById('acceptChangesBtn');
    if (bulkAccept) {
        bulkAccept.addEventListener('click', function() {
            if (!confirm('Accept and apply all pending inventory changes?')) return;
            const btn = this;
            btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';
            fetch('<?php echo e(route('admin.admin.inventory.approve-pending')); ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                .then(r => r.json())
                .then(data => { alert(data.message || 'Inventory changes accepted.'); location.reload(); })
                .catch(() => { alert('Failed to apply changes.'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Accept Changes'; });
        });
    }
    
    // Handle Decline button
    const declineBtn = document.getElementById('declineChangesBtn');
    if (declineBtn) {
        declineBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to decline these inventory changes?')) {
                alert('Inventory changes have been declined.');
                
                // Here you would typically:
                // 1. Mark the request as declined
                // 2. Notify the clerk
                // 3. Remove or hide this request
            }
        });
    }
    
    // Simple tab switching for Inventory Logs category tabs
    document.addEventListener('click', function(e) {
        if (e.target.matches('#updateRequestTabs button[data-bs-target]')) {
            e.preventDefault();
            
            const targetId = e.target.getAttribute('data-bs-target').substring(1);
            console.log('Tab clicked:', e.target.textContent.trim(), 'Target ID:', targetId);
            
            // Hide all tab content
            document.querySelectorAll('.category-tab-content').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Remove active from all tabs
            document.querySelectorAll('#updateRequestTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show target content
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.add('active');
                console.log('Showing content for:', targetId);
            } else {
                console.error('Target content not found:', targetId);
            }
            
            // Add active to clicked tab
            e.target.classList.add('active');
        }
    });
    
});

function submitAdminAction(e, form) {
    e.preventDefault();
    fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
        .then(r => r.json())
        .then(data => { alert(data.message || 'Done'); location.reload(); })
        .catch(() => alert('Request failed'));
    return false;
}

function approveSingle(e, logId) {
    e.preventDefault();
    fetch(`/admin/inventory/approve-log/${logId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
        .then(r => r.json())
        .then(data => { if (data.success) { location.reload(); } else { alert(data.message || 'Failed'); } })
        .catch(() => alert('Request failed'));
    return false;
}

function rejectSingle(e, logId) {
    e.preventDefault();
    if (!confirm('Decline this change?')) return false;
    fetch(`/admin/inventory/reject-log/${logId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
        .then(r => r.json())
        .then(data => { if (data.success) { location.reload(); } else { alert(data.message || 'Failed'); } })
        .catch(() => alert('Request failed'));
    return false;
}

</script>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/inventory.blade.php ENDPATH**/ ?>
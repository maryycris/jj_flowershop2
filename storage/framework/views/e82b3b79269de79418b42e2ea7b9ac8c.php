<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Inventory (Clerk) <span id="pendingIcon" class="badge bg-warning ms-2" style="display: none; background-color: #ff8c00 !important;"><i class="fas fa-clock"></i> Pending</span></h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add New Product</button>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('clerk.inventory.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="mb-3">
            <label for="code" class="form-label">Product Code</label>
            <input type="text" class="form-control" id="code" name="code" required>
          </div>
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
              <option value="Materials, Tools, and Equipment">Materials, Tools, and Equipment</option>
              <option value="Office Supplies">Office Supplies</option>
              <option value="Other Offers">Other Offers</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Selling Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
          </div>
          <div class="mb-3">
            <label for="cost_price" class="form-label">Cost Price</label>
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

<!-- Update Button -->
<div class="d-flex justify-content-end mt-3">
    <button class="btn btn-success" id="submitInventoryBtn">Update</button>
</div>

<!-- Inventory Submitted Modal -->
<div class="modal fade" id="inventorySubmittedModal" tabindex="-1" aria-labelledby="inventorySubmittedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
          <circle cx="12" cy="12" r="12" fill="#fff3cd"/>
          <path d="M7 13l3 3 7-7" stroke="#ff8c00" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h5 class="mb-3">Your request for updating the inventory has been send to the Admin. Please wait for the Admin's approval.</h5>
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="background-color: #ff8c00; border-color: #ff8c00; color: white;">OK</button>
      </div>
    </div>
  </div>
</div>

<?php if($products->count()): ?>
    <!-- Bootstrap Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
        <?php $__currentLoopData = ['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Floral Supplies', 'Packaging Materials', 'Materials, Tools, and Equipment', 'Office Supplies', 'Other Offers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php if($loop->first): ?> active <?php endif; ?>" id="tab-<?php echo e(Str::slug($category)); ?>" data-bs-toggle="tab" data-bs-target="#<?php echo e(Str::slug($category)); ?>" type="button" role="tab" aria-controls="<?php echo e(Str::slug($category)); ?>" aria-selected="<?php echo e($loop->first ? 'true' : 'false'); ?>">
                    <?php echo e($category); ?>

                </button>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <div class="tab-content" id="inventoryTabsContent">
        <?php $__currentLoopData = ['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Floral Supplies', 'Packaging Materials', 'Materials, Tools, and Equipment', 'Office Supplies', 'Other Offers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tab-pane fade <?php if($loop->first): ?> show active <?php endif; ?>" id="<?php echo e(Str::slug($category)); ?>" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
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
                                    <tr id="product-row-<?php echo e($product->id); ?>" data-product-id="<?php echo e($product->id); ?>">
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
                                    <td>
                                            <!-- Edit Button -->
                                        <button class="btn btn-sm btn-primary edit-product-btn" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo e($product->id); ?>" data-product-id="<?php echo e($product->id); ?>">Edit</button>
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm btn-danger delete-product-btn" data-product-id="<?php echo e($product->id); ?>">Delete</button>
                                            <!-- Edit Modal -->
                                <div class="modal fade" id="editProductModal<?php echo e($product->id); ?>" tabindex="-1" aria-labelledby="editProductModalLabel<?php echo e($product->id); ?>" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel<?php echo e($product->id); ?>">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form action="<?php echo e(route('clerk.inventory.update', $product->id)); ?>" method="POST" data-product-id="<?php echo e($product->id); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body">
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
                                                          <option value="Materials, Tools, and Equipment" <?php if($product->category == 'Materials, Tools, and Equipment'): ?> selected <?php endif; ?>>Materials, Tools, and Equipment</option>
                                                          <option value="Office Supplies" <?php if($product->category == 'Office Supplies'): ?> selected <?php endif; ?>>Office Supplies</option>
                                                          <option value="Other Offers" <?php if($product->category == 'Other Offers'): ?> selected <?php endif; ?>>Other Offers</option>
                                                        </select>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="price<?php echo e($product->id); ?>" class="form-label">Selling Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="price<?php echo e($product->id); ?>" name="price" value="<?php echo e($product->price); ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="cost_price<?php echo e($product->id); ?>" class="form-label">Cost Price</label>
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
                                                      <button type="submit" class="btn btn-primary">Edit</button>
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

<style>
.product-row-edited {
    background-color: rgba(135, 206, 235, 0.7) !important; /* 70% transparent sky-blue - more visible */
    transition: background-color 0.3s ease;
    border: 2px solid #87CEEB !important; /* Add border to make it more visible */
}

.product-row-deleted {
    background-color: rgba(255, 99, 99, 0.7) !important; /* 70% transparent red - more visible */
    transition: background-color 0.3s ease;
    border: 2px solid #FF6363 !important; /* Add border to make it more visible */
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inventory highlighting script loaded');
    
    // Track edited and deleted products
    let editedProducts = new Set();
    let deletedProducts = new Set();
    
    // Load highlighted products from session storage
    const savedEdited = sessionStorage.getItem('editedProducts');
    const savedDeleted = sessionStorage.getItem('deletedProducts');
    
    if (savedEdited) {
        editedProducts = new Set(JSON.parse(savedEdited));
        console.log('Loaded edited products from session:', Array.from(editedProducts));
    }
    
    if (savedDeleted) {
        deletedProducts = new Set(JSON.parse(savedDeleted));
        console.log('Loaded deleted products from session:', Array.from(deletedProducts));
    }
    
    // Apply highlighting to loaded products
    editedProducts.forEach(productId => {
        const row = document.getElementById('product-row-' + productId);
        if (row) {
            row.classList.add('product-row-edited');
            row.style.backgroundColor = 'rgba(135, 206, 235, 0.7)';
            row.style.border = '2px solid #87CEEB';
            console.log('Applied blue highlight to product:', productId);
        }
    });
    
    deletedProducts.forEach(productId => {
        const row = document.getElementById('product-row-' + productId);
        if (row) {
            row.classList.add('product-row-deleted');
            row.style.backgroundColor = 'rgba(255, 99, 99, 0.7)';
            row.style.border = '2px solid #FF6363';
            console.log('Applied red highlight to product:', productId);
        }
    });

    // Clear any existing staged data to ensure fresh start
    sessionStorage.removeItem('stagedEdits');
    sessionStorage.removeItem('editedProducts');
    sessionStorage.removeItem('deletedProducts');
    
    // Function to hide pending icon (for future admin approval)
    window.hidePendingIcon = function() {
        const pendingIcon = document.getElementById('pendingIcon');
        if (pendingIcon) {
            pendingIcon.style.display = 'none';
            console.log('Pending icon hidden');
        }
    };
    
    // Apply staged edits to table on load so they persist across refreshes
    try {
        const stagedEdits = JSON.parse(sessionStorage.getItem('stagedEdits') || '{}');
        Object.keys(stagedEdits).forEach(pid => {
            const row = document.getElementById('product-row-' + pid);
            const v = stagedEdits[pid];
            if (row && v) {
                if (row.cells[1]) row.cells[1].textContent = v.name || row.cells[1].textContent;
                if (row.cells[2]) row.cells[2].textContent = v.category || row.cells[2].textContent;
                if (row.cells[3]) row.cells[3].textContent = parseFloat(v.price || row.cells[3].textContent || 0).toFixed(2);
                if (row.cells[4]) row.cells[4].textContent = parseFloat(v.cost_price || row.cells[4].textContent || 0).toFixed(2);
                if (row.cells[5]) row.cells[5].textContent = v.reorder_min || row.cells[5].textContent;
                if (row.cells[6]) row.cells[6].textContent = v.reorder_max || row.cells[6].textContent;
                if (row.cells[7]) row.cells[7].textContent = v.stock || row.cells[7].textContent;
                if (row.cells[8]) row.cells[8].textContent = v.qty_consumed || row.cells[8].textContent;
                if (row.cells[9]) row.cells[9].textContent = v.qty_damaged || row.cells[9].textContent;
                if (row.cells[10]) row.cells[10].textContent = v.qty_sold || row.cells[10].textContent;
                const qtyToPurchase = (parseInt(v.reorder_max || 0) - parseInt(v.stock || 0));
                if (row.cells[11]) row.cells[11].textContent = Math.max(0, qtyToPurchase);
                row.classList.add('product-row-edited');
                row.style.backgroundColor = 'rgba(135, 206, 235, 0.7)';
                row.style.border = '2px solid #87CEEB';
                console.log('Applied staged values for product:', pid);
            }
        });
    } catch (e) {
        console.warn('Failed to apply staged edits from session', e);
    }
    
    // Function to handle Edit button clicks (table row Edit button)
    function handleEditClick(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Edit clicked for product:', productId);
        console.log('Table Edit button clicked - no highlighting yet');
        
        // Don't highlight the row yet - only when Edit button in modal is clicked
        // Just open the modal
    }
    
    // Function to handle modal form submission (Edit button in modal)
    function handleModalUpdate(event) {
        // This function is no longer used - forms submit normally now
        console.log('handleModalUpdate called but not used');
    }
    
    // Function to handle Delete button clicks
    function handleDeleteClick(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Delete clicked for product:', productId);
        
        if (row) {
            // Remove from edited list if it was there
            editedProducts.delete(productId);
            row.classList.remove('product-row-edited');
            
            // Add to deleted list and highlight
            deletedProducts.add(productId);
            row.classList.add('product-row-deleted');
            
            // Save to session storage
            sessionStorage.setItem('deletedProducts', JSON.stringify(Array.from(deletedProducts)));
            
            console.log('Row highlighted for delete:', productId);
            console.log('Deleted products set:', Array.from(deletedProducts));
            console.log('Row classes:', row.classList);
            console.log('Row element:', row);
            console.log('Row has deleted class:', row.classList.contains('product-row-deleted'));
            
            // Show confirmation message
            if (confirm('Are you sure you want to mark this product for deletion? The admin will review this change.')) {
                console.log('Product marked for deletion:', productId);
            } else {
                // If user cancels, remove the highlighting
                deletedProducts.delete(productId);
                row.classList.remove('product-row-deleted');
                console.log('Delete cancelled for product:', productId);
            }
        } else {
            console.error('Row not found for product ID:', productId);
            console.log('Looking for element with ID: product-row-' + productId);
        }
    }
    
    // Use event delegation for dynamically loaded content
    document.addEventListener('click', function(event) {
        console.log('Click detected on:', event.target);
        console.log('Button classes:', event.target.classList);
        
        if (event.target.classList.contains('edit-product-btn')) {
            console.log('Edit button clicked');
            handleEditClick.call(event.target, event);
        } else if (event.target.classList.contains('delete-product-btn')) {
            console.log('Delete button clicked');
            handleDeleteClick.call(event.target, event);
        } else if (event.target.textContent === 'OK' && event.target.closest('#inventorySubmittedModal')) {
            console.log('OK button clicked via event delegation');
            
            // Show pending icon
            const pendingIcon = document.getElementById('pendingIcon');
            if (pendingIcon) {
                pendingIcon.style.display = 'inline-block';
                console.log('Pending icon shown');
            }
            
            // Maintain highlights when OK is clicked
            editedProducts.forEach(productId => {
                const row = document.getElementById('product-row-' + productId);
                if (row) {
                    row.classList.add('product-row-edited');
                    row.style.backgroundColor = 'rgba(135, 206, 235, 0.7)';
                    row.style.border = '2px solid #87CEEB';
                    console.log('Maintained blue highlight for product:', productId);
                }
            });
            
            deletedProducts.forEach(productId => {
                const row = document.getElementById('product-row-' + productId);
                if (row) {
                    row.classList.add('product-row-deleted');
                    row.style.backgroundColor = 'rgba(255, 99, 99, 0.7)';
                    row.style.border = '2px solid #FF6363';
                    console.log('Maintained red highlight for product:', productId);
                }
            });
        }
    });
    
    // Add direct click handlers for Edit buttons in modals
    document.addEventListener('click', function(event) {
        if (event.target.type === 'submit' && event.target.textContent === 'Edit') {
            event.preventDefault(); // Prevent normal form submission
            console.log('Edit button clicked in modal');
            console.log('Button element:', event.target);
            
            // Find the form that contains this button
            const form = event.target.closest('form[data-product-id]');
            console.log('Closest form:', form);
            
            if (form) {
                console.log('Form found, submitting via AJAX');
                
                // Get the product ID and highlight the row immediately
                const productId = form.getAttribute('data-product-id');
                const row = document.getElementById('product-row-' + productId);
                
                if (row && productId) {
                    // Remove from deleted list if it was there
                    deletedProducts.delete(productId);
                    row.classList.remove('product-row-deleted');
                    
                    // Add to edited list and highlight
                    editedProducts.add(productId);
                    row.classList.add('product-row-edited');
                    
                    // Save to session storage
                    sessionStorage.setItem('editedProducts', JSON.stringify(Array.from(editedProducts)));
                    
                    console.log('Row highlighted for edit:', productId);
                    
                    // Force a visual update
                    row.style.backgroundColor = 'rgba(135, 206, 235, 0.7)';
                    row.style.border = '2px solid #87CEEB';
                    
                    // Build staged values from form and update UI only (no backend save yet)
                    const formData = new FormData(form);
                    const newValues = {
                        name: formData.get('name'),
                        category: formData.get('category'),
                        price: formData.get('price'),
                        cost_price: formData.get('cost_price'),
                        reorder_min: formData.get('reorder_min'),
                        reorder_max: formData.get('reorder_max'),
                        stock: formData.get('stock'),
                        qty_consumed: formData.get('qty_consumed'),
                        qty_damaged: formData.get('qty_damaged'),
                        qty_sold: formData.get('qty_sold')
                    };

                    // Persist staged values in sessionStorage so they survive refresh
                    let stagedEdits = {};
                    try { stagedEdits = JSON.parse(sessionStorage.getItem('stagedEdits') || '{}'); } catch(e) { stagedEdits = {}; }
                    stagedEdits[productId] = newValues;
                    sessionStorage.setItem('stagedEdits', JSON.stringify(stagedEdits));

                    // Update the table visually
                    if (row) {
                        if (row.cells[1]) row.cells[1].textContent = newValues.name;
                        if (row.cells[2]) row.cells[2].textContent = newValues.category;
                        if (row.cells[3]) row.cells[3].textContent = parseFloat(newValues.price || 0).toFixed(2);
                        if (row.cells[4]) row.cells[4].textContent = parseFloat(newValues.cost_price || 0).toFixed(2);
                        if (row.cells[5]) row.cells[5].textContent = newValues.reorder_min || 0;
                        if (row.cells[6]) row.cells[6].textContent = newValues.reorder_max || 0;
                        if (row.cells[7]) row.cells[7].textContent = newValues.stock || 0;
                        if (row.cells[8]) row.cells[8].textContent = newValues.qty_consumed || 0;
                        if (row.cells[9]) row.cells[9].textContent = newValues.qty_damaged || 0;
                        if (row.cells[10]) row.cells[10].textContent = newValues.qty_sold || 0;
                        const qtyToPurchase = (parseInt(newValues.reorder_max || 0) - parseInt(newValues.stock || 0));
                        if (row.cells[11]) row.cells[11].textContent = Math.max(0, qtyToPurchase);
                        console.log('Row visually updated with staged values');
                    }

                    // Reset button and close modal
                    const submitBtn = event.target;
                    submitBtn.textContent = 'Edit';
                    submitBtn.disabled = false;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal' + productId));
                    if (modal) { modal.hide(); }
                    alert('Staged for review. Click the green Update button to submit to admin.');
                } else {
                    console.error('Row or productId not found:', { row, productId });
                }
            } else {
                console.log('No form found');
            }
        }
    });
    
    // Handle Update button click
    const updateBtn = document.getElementById('submitInventoryBtn');
    if (updateBtn) {
        updateBtn.addEventListener('click', function() {
            console.log('Main Update button clicked');
            console.log('Edited products:', Array.from(editedProducts));
            console.log('Deleted products:', Array.from(deletedProducts));
            
            // Create summary message
            let summaryMessage = 'Your request for updating the inventory has been send to the Admin. Please wait for the Admin\'s approval.';
            
            // Submit changes to backend first
            const submitData = {
                edited_products: JSON.stringify(Array.from(editedProducts)),
                deleted_products: JSON.stringify(Array.from(deletedProducts)),
                staged_edits: JSON.stringify(stagedEdits),
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            fetch('<?php echo e(route("clerk.inventory.submit-changes")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(submitData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Changes submitted successfully:', data);
                    
                    // Show confirmation modal with summary
                    const modal = document.getElementById('inventorySubmittedModal');
                    if (modal) {
                        // Update modal content
                        const modalBody = modal.querySelector('.modal-body h5');
                        if (modalBody) {
                            modalBody.textContent = summaryMessage;
                        }
                        
                        // Use Bootstrap modal instance to show the modal
                        const bootstrapModal = new bootstrap.Modal(modal);
                        bootstrapModal.show();
                
                        // Add event listener for OK button to maintain highlighting
                        const okButton = modal.querySelector('button[data-bs-dismiss="modal"]');
                        if (okButton) {
                            console.log('OK button found, adding click handler');
                            
                            // Use a simple click handler
                            okButton.onclick = function() {
                                console.log('OK button clicked - maintaining highlights');
                                
                                // Show pending icon
                                const pendingIcon = document.getElementById('pendingIcon');
                                if (pendingIcon) {
                                    pendingIcon.style.display = 'inline-block';
                                    console.log('Pending icon shown');
                                }
                                
                                // Keep all highlighted rows visible
                                editedProducts.forEach(productId => {
                                    const row = document.getElementById('product-row-' + productId);
                                    if (row) {
                                        row.classList.add('product-row-edited');
                                        row.style.backgroundColor = 'rgba(135, 206, 235, 0.7)';
                                        row.style.border = '2px solid #87CEEB';
                                        console.log('Maintained blue highlight for product:', productId);
                                    }
                                });
                                
                                deletedProducts.forEach(productId => {
                                    const row = document.getElementById('product-row-' + productId);
                                    if (row) {
                                        row.classList.add('product-row-deleted');
                                        row.style.backgroundColor = 'rgba(255, 99, 99, 0.7)';
                                        row.style.border = '2px solid #FF6363';
                                        console.log('Maintained red highlight for product:', productId);
                                    }
                                });
                            };
                        } else {
                            console.error('OK button not found in modal');
                        }
                    }
                } else {
                    console.error('Failed to submit changes:', data.message);
                    alert('Error submitting changes: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error submitting changes:', error);
                alert('Error submitting changes. Please try again.');
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/inventory/index.blade.php ENDPATH**/ ?>
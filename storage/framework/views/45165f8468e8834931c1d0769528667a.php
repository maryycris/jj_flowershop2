<?php $__env->startSection('admin_content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Inventory (Admin)</h2>
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
      <form action="<?php echo e(route('admin.inventory.store')); ?>" method="POST">
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
                                      <form action="<?php echo e(route('admin.inventory.update', $product->id)); ?>" method="POST" data-product-id="<?php echo e($product->id); ?>">
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
                                                      <button type="submit" class="btn btn-primary">Update</button>
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
    background-color: rgba(135, 206, 235, 0.5) !important; /* 50% transparent sky-blue */
    transition: background-color 0.3s ease;
}

.product-row-deleted {
    background-color: rgba(255, 99, 99, 0.5) !important; /* 50% transparent red */
    transition: background-color 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin inventory highlighting script loaded');
    
    // Track edited and deleted products
    let editedProducts = new Set();
    let deletedProducts = new Set();
    
    // Function to handle Edit button clicks
    function handleEditClick(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Edit clicked for product:', productId);
        
        if (row) {
            // Remove from deleted list if it was there
            deletedProducts.delete(productId);
            row.classList.remove('product-row-deleted');
            
            // Add to edited list and highlight
            editedProducts.add(productId);
            row.classList.add('product-row-edited');
            
            console.log('Row highlighted for edit:', productId);
        }
    }
    
    // Function to handle modal form submission (Update button in modal)
    function handleModalUpdate(event) {
        event.preventDefault();
        const form = event.target;
        const productId = form.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Modal Update clicked for product:', productId);
        
        // Submit the form data
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Keep the row highlighted as edited
                if (row) {
                    editedProducts.add(productId);
                    row.classList.add('product-row-edited');
                    console.log('Product updated and row highlighted:', productId);
                }
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal' + productId));
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                alert('Product updated successfully!');
            }
        })
        .catch(error => {
            console.error('Error updating product:', error);
            alert('Error updating product. Please try again.');
        });
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
            
            console.log('Row highlighted for delete:', productId);
            
            // Show confirmation message
            if (confirm('Are you sure you want to mark this product for deletion? The admin will review this change.')) {
                console.log('Product marked for deletion:', productId);
            } else {
                // If user cancels, remove the highlighting
                deletedProducts.delete(productId);
                row.classList.remove('product-row-deleted');
                console.log('Delete cancelled for product:', productId);
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
        }
    });
    
    // Handle Update button click
    const updateBtn = document.getElementById('submitInventoryBtn');
    if (updateBtn) {
        updateBtn.addEventListener('click', function() {
            console.log('Update button clicked');
            console.log('Edited products:', Array.from(editedProducts));
            console.log('Deleted products:', Array.from(deletedProducts));
            
            // Create summary message
            let summaryMessage = 'YOUR INVENTORY CHANGES HAVE BEEN SUBMITTED FOR REVIEW\n\n';
            
            if (editedProducts.size > 0) {
                summaryMessage += `📝 Products to EDIT (${editedProducts.size}):\n`;
                editedProducts.forEach(productId => {
                    const row = document.getElementById('product-row-' + productId);
                    const productName = row ? row.cells[1].textContent : 'Unknown';
                    summaryMessage += `• ${productName} (ID: ${productId})\n`;
                });
                summaryMessage += '\n';
            }
            
            if (deletedProducts.size > 0) {
                summaryMessage += `🗑️ Products to DELETE (${deletedProducts.size}):\n`;
                deletedProducts.forEach(productId => {
                    const row = document.getElementById('product-row-' + productId);
                    const productName = row ? row.cells[1].textContent : 'Unknown';
                    summaryMessage += `• ${productName} (ID: ${productId})\n`;
                });
                summaryMessage += '\n';
            }
            
            if (editedProducts.size === 0 && deletedProducts.size === 0) {
                summaryMessage += 'No changes made.';
            } else {
                summaryMessage += 'The admin will review and approve these changes.';
            }
            
            // Show confirmation modal with summary
            const modal = document.getElementById('inventorySubmittedModal');
            if (modal) {
                // Update modal content
                const modalBody = modal.querySelector('.modal-body h5');
                if (modalBody) {
                    modalBody.textContent = summaryMessage;
                }
                
                modal.style.display = 'block';
                modal.classList.add('show');
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/inventory.blade.php ENDPATH**/ ?>
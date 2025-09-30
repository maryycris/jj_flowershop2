@extends('layouts.admin_app')
@section('admin_content')
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
      <form action="{{ route('admin.inventory.store') }}" method="POST">
        @csrf
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

@if($products->count())
    <!-- Bootstrap Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
        @foreach(['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Floral Supplies', 'Packaging Materials', 'Other Offers'] as $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($loop->first) active @endif" id="tab-{{ Str::slug($category) }}" data-bs-toggle="tab" data-bs-target="#{{ Str::slug($category) }}" type="button" role="tab" aria-controls="{{ Str::slug($category) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $category }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" id="inventoryTabsContent">
        @foreach(['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Floral Supplies', 'Packaging Materials', 'Other Offers'] as $category)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ Str::slug($category) }}" role="tabpanel">
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
                    @foreach($products as $product)
                                @if($product->category === $category)
                                @php
                                    $min = $product->reorder_min ?? 0;
                                    $max = $product->reorder_max ?? 0;
                                    $stock = $product->stock ?? 0;
                                    $qtyToPurchase = ($stock < $max) ? ($max - $stock) : 0;
                                @endphp
                                    <tr id="product-row-{{ $product->id }}" data-product-id="{{ $product->id }}">
                                    <td>{{ $product->code ?? $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->cost_price ?? '-' }}</td>
                                    <td>{{ $min }}</td>
                                    <td>{{ $max }}</td>
                                    <td>{{ $stock }}</td>
                                    <td>{{ $product->qty_consumed ?? '-' }}</td>
                                    <td>{{ $product->qty_damaged ?? '-' }}</td>
                                    <td>{{ $product->qty_sold ?? '-' }}</td>
                                    <td>{{ $qtyToPurchase }}</td>
                                    <td>{{ $product->created_at ? $product->created_at->format('Y-m-d') : '-' }}</td>
                                    <td>
                                            <!-- Edit Button -->
                                        <button class="btn btn-sm btn-primary edit-product-btn" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" data-product-id="{{ $product->id }}">Edit</button>
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm btn-danger delete-product-btn" data-product-id="{{ $product->id }}">Delete</button>
                                            <!-- Edit Modal -->
                                <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel{{ $product->id }}">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form action="{{ route('admin.inventory.update', $product->id) }}" method="POST" data-product-id="{{ $product->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                          <div class="mb-3">
                                                        <label for="name{{ $product->id }}" class="form-label">Product Name</label>
                                                        <input type="text" class="form-control" id="name{{ $product->id }}" name="name" value="{{ $product->name }}" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="category{{ $product->id }}" class="form-label">Category</label>
                                                        <select class="form-select" id="category{{ $product->id }}" name="category" required>
                                                          <option value="Fresh Flowers" @if($product->category == 'Fresh Flowers') selected @endif>Fresh Flowers</option>
                                                          <option value="Dried Flowers" @if($product->category == 'Dried Flowers') selected @endif>Dried Flowers</option>
                                                          <option value="Artificial Flowers" @if($product->category == 'Artificial Flowers') selected @endif>Artificial Flowers</option>
                                                          <option value="Floral Supplies" @if($product->category == 'Floral Supplies') selected @endif>Floral Supplies</option>
                                                          <option value="Packaging Materials" @if($product->category == 'Packaging Materials') selected @endif>Packaging Materials</option>
                                                          <option value="Other Offers" @if($product->category == 'Other Offers') selected @endif>Other Offers</option>
                                                        </select>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="price{{ $product->id }}" class="form-label">Selling Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="price{{ $product->id }}" name="price" value="{{ $product->price }}" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="cost_price{{ $product->id }}" class="form-label">Cost Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="cost_price{{ $product->id }}" name="cost_price" value="{{ $product->cost_price }}">
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="reorder_min{{ $product->id }}" class="form-label">Reordering Min</label>
                                                        <input type="number" class="form-control" id="reorder_min{{ $product->id }}" name="reorder_min" value="{{ $product->reorder_min }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="reorder_max{{ $product->id }}" class="form-label">Reordering Max</label>
                                                        <input type="number" class="form-control" id="reorder_max{{ $product->id }}" name="reorder_max" value="{{ $product->reorder_max }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="stock{{ $product->id }}" class="form-label">Qty On Hand</label>
                                                        <input type="number" class="form-control" id="stock{{ $product->id }}" name="stock" value="{{ $product->stock }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_consumed{{ $product->id }}" class="form-label">Qty Consumed</label>
                                                        <input type="number" class="form-control" id="qty_consumed{{ $product->id }}" name="qty_consumed" value="{{ $product->qty_consumed }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_damaged{{ $product->id }}" class="form-label">Qty Damaged</label>
                                                        <input type="number" class="form-control" id="qty_damaged{{ $product->id }}" name="qty_damaged" value="{{ $product->qty_damaged }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_sold{{ $product->id }}" class="form-label">Qty Sold</label>
                                                        <input type="number" class="form-control" id="qty_sold{{ $product->id }}" name="qty_sold" value="{{ $product->qty_sold }}">
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
                                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        @endforeach
</div>
@else
    <p>No products found.</p>
@endif

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
@endsection 
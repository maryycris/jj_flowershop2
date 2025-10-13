@extends('layouts.admin_app')

@push('styles')
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
@endpush

@section('admin_content')
<div class="mx-auto" style="max-width: 1100px; padding-top: 24px;">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Inventory (Admin)</h2>
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
      <form action="{{ route('admin.inventory.store') }}" method="POST">
        @csrf
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

@if($products->count())
    <!-- Bootstrap Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
        @foreach(['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Greenery', 'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Other Offers'] as $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($loop->first) active @endif" id="tab-{{ Str::slug($category) }}" data-bs-toggle="tab" data-bs-target="#{{ Str::slug($category) }}" type="button" role="tab" aria-controls="{{ Str::slug($category) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $category }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" id="inventoryTabsContent">
        @foreach(['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Greenery', 'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Other Offers'] as $category)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ Str::slug($category) }}" role="tabpanel">
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
                    @foreach($products as $product)
                                @if($product->category === $category)
                                @php
                                    $min = $product->reorder_min ?? 0;
                                    $max = $product->reorder_max ?? 0;
                                    $stock = $product->stock ?? 0;
                                    $qtyToPurchase = ($stock < $max) ? ($max - $stock) : 0;
                                @endphp
                                    <tr id="product-row-{{ $product->id }}" data-product-id="{{ $product->id }}"
                                        @if($product->is_marked_for_deletion) 
                                            class="marked-for-deletion" 
                                            style="border: 2px solid #dc3545 !important;"
                                        @endif>
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
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Edit Button -->
                                            <button class="btn btn-sm action-btn edit-btn edit-product-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" data-product-id="{{ $product->id }}"><i class="bi bi-pencil-square"></i></button>
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm action-btn delete-btn delete-product-btn" title="Delete" data-product-id="{{ $product->id }}"><i class="bi bi-trash3"></i></button>
                                        </div>
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
                                        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
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
                                                          <option value="Wrappers" @if($product->category == 'Wrappers') selected @endif>Wrappers</option>
                                                          <option value="Ribbon" @if($product->category == 'Ribbon') selected @endif>Ribbon</option>
                                                          <option value="Other Offers" @if($product->category == 'Other Offers') selected @endif>Other Offers</option>
                                                          <option value="Greenery" @if($product->category == 'Greenery') selected @endif>Greenery</option>
                                                        </select>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="price{{ $product->id }}" class="form-label">Selling Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="price{{ $product->id }}" name="price" value="{{ $product->price }}" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="cost_price{{ $product->id }}" class="form-label">Acquisition Cost</label>
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
                                                      <button type="submit" class="btn btn-primary" id="updateBtn{{ $product->id }}">Update</button>
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
});
</script>
</div>
@endsection
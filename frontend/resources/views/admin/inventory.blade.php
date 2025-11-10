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
    font-size: 12px;
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

/* Main inventory tabs styling - green color */
#mainInventoryTabs .nav-link {
    color: #28a745 !important;
    border-color: #dee2e6;
    font-size: 0.9rem;
}

#mainInventoryTabs .nav-link.active {
    color: #28a745 !important;
    background-color: #f8f9fa;
    border-color: #dee2e6 #dee2e6 #f8f9fa;
    font-weight: 500;
}


/* Search Bar Styling */
#inventorySearch {
    font-size: 0.85rem;
}

#inventorySearch::placeholder {
    font-size: 0.8rem;
}

#clearInventorySearch {
    font-size: 0.8rem;
}

/* Add New Material Button */
.btn-success {
    font-size: 0.85rem;
}

/* Category Tabs */
#inventoryTabs .nav-link {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

/* Inventory Table Styling */
.table {
    font-size: 0.75rem;
    background-color: white;
}

.table thead th {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
    background-color: white;
}

.table tbody {
    background-color: white;
}

.table tbody td {
    font-size: 0.7rem;
    padding: 0.4rem 0.3rem;
    vertical-align: middle;
    background-color: white;
}

.table tbody tr {
    background-color: white;
}

/* Action Buttons */
.action-btn {
    width: 35px !important;
    height: 30px !important;
    font-size: 12px !important;
}

/* Modal Styling */
.modal-title {
    font-size: 1.1rem;
}

.modal-body .form-label {
    font-size: 0.85rem;
    font-weight: 500;
}

.modal-body .form-control,
.modal-body .form-select {
    font-size: 0.8rem;
}

/* Button Styling */
.btn-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Inventory Logs Tab Styling */
#inventory-logs-tab .nav-link {
    font-size: 0.85rem;
}

/* Table Responsive Improvements */
.table-responsive {
    font-size: 0.75rem;
}

/* Badge Styling */
.badge {
    font-size: 0.65rem;
}

/* Form Controls in Table */
.table .form-control,
.table .form-select {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

/* Input Group Text */
.input-group-text {
    font-size: 0.8rem;
}

/* Inventory Logs Content */
#inventory-logs .card-title {
    font-size: 1rem;
}

#inventory-logs .card-text {
    font-size: 0.8rem;
}

/* Content Positioning and Spacing Adjustments */
.mx-auto {
    padding-top: 12px !important; /* Reduced from 24px */
}

/* Search and Action Bar Spacing */
.d-flex.flex-wrap.justify-content-between.align-items-center.mt-3 {
    margin-top: 1rem !important; /* Reduced from mt-3 */
    margin-bottom: 0.5rem !important; /* Added small bottom margin */
}

/* Category Tabs Spacing */
#inventoryTabs {
    margin-bottom: 1rem !important; /* Reduced from mb-3 */
}

/* Table Container Spacing */
.tab-content {
    margin-top: 0.5rem;
}

/* Inventory Scroll Container */
.inventory-scroll {
    margin-top: 0.25rem;
}

/* Overall Page Spacing */
.container-fluid {
    padding-top: 0.5rem;
}

/* Reduce gap between search bar and category tabs */
.gap-2 {
    gap: 0.5rem !important;
}

/* Update Request Tabs */
#updateRequestTabs .nav-link {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}

#mainInventoryTabs .nav-link:hover {
    color: #28a745 !important;
    border-color: #dee2e6;
}

/* Category tabs in inventory update request - green color with soft background */
#updateRequestTabs .nav-link {
    color: #28a745 !important;
    border: none !important;
    background-color: transparent !important;
    border-radius: 8px !important;
    margin-right: 4px !important;
    border-bottom: none !important;
}

#updateRequestTabs .nav-link.active {
    color: #28a745 !important;
    background-color: rgba(40, 167, 69, 0.15) !important;
    border: none !important;
    border-bottom: none !important;
    border-radius: 8px !important;
}

#updateRequestTabs .nav-link:hover {
    color: #28a745 !important;
    background-color: rgba(40, 167, 69, 0.08) !important;
    border: none !important;
    border-bottom: none !important;
    border-radius: 8px !important;
}

/* Override Bootstrap's default tab styling */
#updateRequestTabs .nav-tabs {
    border-bottom: none !important;
}

#updateRequestTabs .nav-tabs .nav-link {
    border: none !important;
    border-bottom: none !important;
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
<div class="mx-auto" style="max-width: 1400px; padding-top: 18px; max-height: 77vh; ">
<!-- Main Tab Navigation -->
<ul class="nav nav-tabs mb-3" id="mainInventoryTabs" role="tablist">
    <li class="nav-item" role="presentation" style="flex: 1;">
        <button class="nav-link active w-100" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="true">
            <i class="bi bi-box-seam me-2"></i>Inventory
        </button>
    </li>
    <li class="nav-item" role="presentation" style="flex: 1;">
        <button class="nav-link w-100" id="inventory-logs-tab" data-bs-toggle="tab" data-bs-target="#inventory-logs" type="button" role="tab" aria-controls="inventory-logs" aria-selected="false">
            <i class="bi bi-clock-history me-2"></i>Inventory Changes Request
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="mainInventoryTabsContent">
    <!-- Inventory Tab -->
    <div class="tab-pane fade show active" id="inventory" role="tabpanel">

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
      <form action="{{ route('admin.inventory.store') }}" method="POST" onsubmit="handleAddProductForm(event)">
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

<!-- Toolbar: Search + Add New Product -->
<div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
    <div class="input-group" style="max-width: 360px;">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" id="inventorySearch" placeholder="Search code, name, or category..." autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" id="clearInventorySearch">Clear</button>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add New Material</button>
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
                                    // Calculate quantity needed to purchase: Max - On Hand
                                    // Shows how many units needed to reach maximum reorder level
                                    // If stock is already at or above max, shows 0
                                    $qtyToPurchase = ($stock < $max) ? max(0, $max - $stock) : 0;
                                @endphp
                                    <tr id="product-row-{{ $product->id }}" data-product-id="{{ $product->id }}"
                                        @if($product->is_marked_for_deletion) 
                                            class="marked-for-deletion row-deleted"
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
    </div> <!-- End Inventory Tab -->
    
    <!-- Inventory Logs Tab -->
    <div class="tab-pane fade" id="inventory-logs" role="tabpanel">
        
        
        
        

        <!-- Inventory Update Request Section (dynamic) -->
        <div class="update-request-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Inventory Changes Request</h4>
                <small class="text-muted">Pending Logs: {{ $pendingLogs->count() }}</small>
            </div>
            
            
            <!-- Category Tabs (always show) -->
            <ul class="nav nav-tabs" id="updateRequestTabs" role="tablist">
                @php
                    $firstTabWithLogs = null;
                    foreach($categories as $cat) {
                        if(isset($logsByCategory[$cat]) && $logsByCategory[$cat]->count() > 0) {
                            $firstTabWithLogs = $cat;
                            break;
                        }
                    }
                @endphp
                @foreach(($categories ?? []) as $index => $cat)
                    @php $tabId = Str::slug($cat); @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ ($firstTabWithLogs && $cat === $firstTabWithLogs) ? 'active' : ($index === 0 && !$firstTabWithLogs ? 'active' : '') }}" 
                                id="{{ $tabId }}-tab" 
                                data-bs-target="#{{ $tabId }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="{{ $tabId }}">
                            {{ $cat }} 
                            @if(isset($logsByCategory[$cat]) && $logsByCategory[$cat]->count() > 0)
                                <span class="badge bg-primary ms-1">{{ $logsByCategory[$cat]->count() }}</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>
            
            <div class="tab-content" id="updateRequestTabContent">
                @foreach(($categories ?? []) as $index => $cat)
                    @php $tabId = Str::slug($cat); @endphp
                    <div class="category-tab-content {{ ($firstTabWithLogs && $cat === $firstTabWithLogs) ? 'active' : ($index === 0 && !$firstTabWithLogs ? 'active' : '') }}" 
                         id="{{ $tabId }}" 
                         data-category="{{ $cat }}">
                        <div class="update-request-table">
                            @php $catLogs = ($logsByCategory[$cat] ?? collect()); @endphp
                            <div class="alert alert-info mb-3">
                                <strong>{{ $cat }}:</strong> {{ $catLogs->count() }} pending logs
                            </div>
                            @if($catLogs->count() > 0)
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
                                        @foreach($catLogs as $log)
                                            @php
                                                $p = $log->product; 
                                                $nv = (array)($log->new_values ?? []);
                                                $rowClass = $log->action === 'create' ? 'row-added' : ($log->action === 'edit' ? 'row-edited' : 'row-deleted');
                                            @endphp
                                            <tr class="{{ $rowClass }}">
                                                <td>{{ $log->action === 'create' ? 'NEW' : ($p->code ?? $p->id ?? 'N/A') }}</td>
                                                <td>{{ $nv['name'] ?? ($p->name ?? 'Product Deleted') }}</td>
                                                <td>{{ $nv['category'] ?? ($p->category ?? 'N/A') }}</td>
                                                <td>{{ $nv['price'] ?? ($p->price ?? '0') }}</td>
                                                <td>{{ $nv['cost_price'] ?? ($p->cost_price ?? 'N/A') }}</td>
                                                <td>{{ ($nv['reorder_min'] ?? ($p->reorder_min ?? 0)) . ' / ' . ($nv['reorder_max'] ?? ($p->reorder_max ?? 0)) }}</td>
                                                <td>{{ $nv['stock'] ?? ($p->stock ?? 0) }}</td>
                                                <td>{{ $nv['qty_consumed'] ?? ($p->qty_consumed ?? 0) }}</td>
                                                <td>{{ $nv['qty_damaged'] ?? ($p->qty_damaged ?? 0) }}</td>
                                                <td>{{ $nv['qty_sold'] ?? ($p->qty_sold ?? 0) }}</td>
                                                @php
                                                    $logMax = (int)($nv['reorder_max'] ?? ($p->reorder_max ?? 0));
                                                    $logStock = (int)($nv['stock'] ?? ($p->stock ?? 0));
                                                    $logQtyToPurchase = ($logStock < $logMax) ? max(0, $logMax - $logStock) : 0;
                                                @endphp
                                                <td>{{ $logQtyToPurchase }}</td>
                                                <td>{{ optional(optional($log)->created_at)->format('Y-m-d') }}</td>
                                                <td>
                                                    @if(($log->status ?? 'pending') === 'pending')
                                                        <form method="post" action="{{ route('admin.admin.inventory.approve-pending') }}" onsubmit="return approveSingle(event, {{ $log->id }});" class="d-inline">
                                                            @csrf
                                                            <button class="btn btn-success btn-sm">Approve</button>
                                                        </form>
                                                        <form method="post" action="{{ route('admin.admin.inventory.reject-single', $log->id) }}" onsubmit="return rejectSingle(event, {{ $log->id }});" class="d-inline ms-1">
                                                            @csrf
                                                            <button class="btn btn-outline-danger btn-sm">Decline</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">No Pending Changes</h6>
                                    <p class="text-muted small">All inventory changes have been reviewed.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
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

/* Removed conflicting styles - using row-edited, row-added, row-deleted instead */
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
/* Soft background colors similar to clerk side - no borders */
.update-request-table .row-added {
    background-color: rgba(40, 167, 69, 0.15) !important;
    border: none !important;
}

.update-request-table .row-edited {
    background-color: rgba(0, 123, 255, 0.15) !important;
    border: none !important;
}

.update-request-table .row-deleted {
    background-color: rgba(220, 53, 69, 0.15) !important;
    border: none !important;
}

/* Ensure table cells inherit the soft background */
.update-request-table .row-added td { 
    background-color: rgba(40, 167, 69, 0.15) !important; 
    border: none !important;
}
.update-request-table .row-edited td { 
    background-color: rgba(0, 123, 255, 0.15) !important; 
    border: none !important;
}
.update-request-table .row-deleted td { 
    background-color: rgba(220, 53, 69, 0.15) !important; 
    border: none !important;
}

/* Custom tab content visibility */
.category-tab-content {
    display: none !important;
}

.category-tab-content.active {
    display: block !important;
}

</style>

<script>
// Store current active tabs
let currentActiveMainTab = null;
let currentActiveCategoryTab = null;

// Initialize tab state preservation
document.addEventListener('DOMContentLoaded', function() {
    // Check if URL has hash fragment to activate a specific tab (e.g., from notification)
    if (window.location.hash === '#inventory-logs') {
        // Activate the inventory logs tab
        const inventoryLogsTab = document.querySelector('#inventory-logs-tab');
        if (inventoryLogsTab) {
            const tab = new bootstrap.Tab(inventoryLogsTab);
            tab.show();
            currentActiveMainTab = 'inventory-logs-tab';
        }
        // Remove hash from URL without scrolling
        history.replaceState(null, null, window.location.pathname + window.location.search);
    }
    
    // Check if there's a saved main tab state
    const savedMainTab = sessionStorage.getItem('activeInventoryMainTab');
    const savedCategoryTab = sessionStorage.getItem('activeInventoryCategoryTab');
    
    if (savedMainTab && !window.location.hash) {
        // Activate the saved main tab (only if no hash fragment)
        const mainTabButton = document.querySelector(`#${savedMainTab}`);
        if (mainTabButton) {
            const tab = new bootstrap.Tab(mainTabButton);
            tab.show();
            currentActiveMainTab = savedMainTab;
        }
        // Clear the saved main tab state
        sessionStorage.removeItem('activeInventoryMainTab');
    } else if (!window.location.hash) {
        // Store the initially active main tab
        const activeMainTab = document.querySelector('#mainInventoryTabs .nav-link.active');
        if (activeMainTab) {
            currentActiveMainTab = activeMainTab.id;
        }
    }
    
    if (savedCategoryTab) {
        // Activate the saved category tab
        const categoryTabButton = document.querySelector(`#${savedCategoryTab}`);
        if (categoryTabButton) {
            const tab = new bootstrap.Tab(categoryTabButton);
            tab.show();
            currentActiveCategoryTab = savedCategoryTab;
        }
        // Clear the saved category tab state
        sessionStorage.removeItem('activeInventoryCategoryTab');
    } else {
        // Store the initially active category tab
        const activeCategoryTab = document.querySelector('#inventoryTabs .nav-link.active');
        if (activeCategoryTab) {
            currentActiveCategoryTab = activeCategoryTab.id;
        }
    }
    
    // Listen for main tab changes
    const mainTabButtons = document.querySelectorAll('#mainInventoryTabs .nav-link');
    mainTabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            currentActiveMainTab = e.target.id;
        });
    });
    
    // Listen for category tab changes
    const categoryTabButtons = document.querySelectorAll('#inventoryTabs .nav-link');
    categoryTabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            currentActiveCategoryTab = e.target.id;
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin inventory highlighting script loaded');
    
    // Manual test function to highlight Rose (product ID 635)
    window.testHighlightRose = function() {
        const roseRow = document.getElementById('product-row-635');
        if (roseRow) {
            roseRow.classList.add('product-row-edited');
            roseRow.style.backgroundColor = 'rgba(135, 206, 235, 0.5)';
            roseRow.style.backgroundColor = 'rgba(0, 123, 255, 0.1)';
            console.log('Manually highlighted Rose (product 635)');
        } else {
            console.log('Rose row not found with ID: product-row-635');
        }
    };
    
    // Admin inventory - no staging needed, direct actions only
    console.log('Admin inventory loaded - direct actions enabled');
    
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
            showAlert('Unable to update: form not found.', 'error');
            return;
        }
        const productId = form.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Modal Update clicked for product:', productId);
        console.log('Form action:', form.action);
        try { console.log('Form data ready'); } catch (_) {}
        
        // Submit the form data directly (IMMEDIATE SAVE)
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
                showAlert(data.message || 'Product updated successfully!', 'success');
                
                // Update the row data in place instead of reloading
                if (row) {
                    const cells = row.querySelectorAll('td');
                    const formData = new FormData(form);
                    
                    // Update each cell with the new data
                    if (cells[1]) cells[1].textContent = formData.get('name') || cells[1].textContent;
                    if (cells[2]) cells[2].textContent = formData.get('category') || cells[2].textContent;
                    if (cells[3]) cells[3].textContent = parseFloat(formData.get('price') || 0).toFixed(2);
                    if (cells[4]) cells[4].textContent = parseFloat(formData.get('cost_price') || 0).toFixed(2);
                    if (cells[5]) cells[5].textContent = formData.get('reorder_min') || cells[5].textContent;
                    if (cells[6]) cells[6].textContent = formData.get('reorder_max') || cells[6].textContent;
                    if (cells[7]) cells[7].textContent = formData.get('stock') || cells[7].textContent;
                    if (cells[8]) cells[8].textContent = formData.get('qty_consumed') || cells[8].textContent;
                    if (cells[9]) cells[9].textContent = formData.get('qty_damaged') || cells[9].textContent;
                    if (cells[10]) cells[10].textContent = formData.get('qty_sold') || cells[10].textContent;
                    
                    // Recalculate qty_to_purchase: Max - On Hand
                    const reorderMax = parseInt(formData.get('reorder_max') || 0);
                    const stock = parseInt(formData.get('stock') || 0);
                    const qtyToPurchase = (stock < reorderMax) ? Math.max(0, reorderMax - stock) : 0;
                    if (cells[11]) cells[11].textContent = qtyToPurchase;
                }
                
                // Show success message
                showAlert('Product updated successfully!', 'success');
            } else {
                showAlert('Error: ' + (data.message || 'Unknown error occurred'), 'error');
            }
        })
        .catch(error => {
            console.error('Error updating product:', error);
            showAlert('Error updating product. Please try again. Error: ' + error.message, 'error');
        });
    }
    
    // Function to handle Delete button clicks (Admin - immediate deletion)
    function handleDeleteClick(event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        const row = document.getElementById('product-row-' + productId);
        
        console.log('Delete clicked for product:', productId);
        
        if (row) {
            // Show smart delete confirmation modal
            showDeleteConfirmationModal(productId, row);
        }
    }

    // AJAX function to handle add product form submission
    async function handleAddProductForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                modal.hide();
                
                // Show success message
                showAlert(result.message, 'success');
                
                // Reload the page to show the new product and preserve tab state
                setTimeout(() => {
                    if (currentActiveMainTab) {
                        sessionStorage.setItem('activeInventoryMainTab', currentActiveMainTab);
                    }
                    if (currentActiveCategoryTab) {
                        sessionStorage.setItem('activeInventoryCategoryTab', currentActiveCategoryTab);
                    }
                    location.reload();
                }, 1000);
            } else {
                showAlert(result.message || 'An error occurred', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while adding the product', 'error');
        }
    }

    // AJAX function to handle delete product
    async function deleteProduct(productId) {
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }
        
        try {
            const response = await fetch(`/admin/inventory/product/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                showAlert(result.message, 'success');
                
                // Reload the page to remove the deleted product and preserve tab state
                setTimeout(() => {
                    if (currentActiveMainTab) {
                        sessionStorage.setItem('activeInventoryMainTab', currentActiveMainTab);
                    }
                    if (currentActiveCategoryTab) {
                        sessionStorage.setItem('activeInventoryCategoryTab', currentActiveCategoryTab);
                    }
                    location.reload();
                }, 1000);
            } else {
                showAlert(result.message || 'An error occurred', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred while deleting the product', 'error');
        }
    }

    // Function to show cleaner alerts (matching Customize section style)
    function showAlert(message, type = 'success') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.clean-alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Determine alert styling
        const isSuccess = type === 'success' || type === 'Success';
        const alertType = isSuccess ? 'success' : 'danger';
        const icon = isSuccess ? 'check-circle' : 'exclamation-triangle';
        const bgColor = isSuccess ? '#d4edda' : '#f8d7da';
        const borderColor = isSuccess ? '#c3e6cb' : '#f5c6cb';
        const textColor = isSuccess ? '#155724' : '#721c24';
        
        // Create new alert with cleaner styling
        const alertDiv = document.createElement('div');
        alertDiv.className = 'clean-alert';
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            max-width: 450px;
            background: ${bgColor};
            border: 1px solid ${borderColor};
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInRight 0.3s ease-out;
        `;
        
        alertDiv.innerHTML = `
            <i class="fas fa-${icon}" style="color: ${textColor}; font-size: 18px; flex-shrink: 0;"></i>
            <span style="color: ${textColor}; font-weight: 500; flex: 1; font-size: 14px;">${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="flex-shrink: 0; opacity: 0.7;"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 4 seconds with fade out
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 300);
            }
        }, 4000);
    }
    
    // Add CSS animations if not already present
    if (!document.getElementById('clean-alert-styles')) {
        const style = document.createElement('style');
        style.id = 'clean-alert-styles';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
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
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterRows(e.target.value);
            }
        });
        // Remove live typing search - only search on Enter key press
        // searchInput.addEventListener('input', function(e){ filterRows(e.target.value); });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function(){ searchInput.value = ''; filterRows(''); searchInput.focus(); });
    }
    // When switching tabs, re-apply current filter
    document.getElementById('inventoryTabs')?.addEventListener('shown.bs.tab', function(){ filterRows(searchInput?.value || ''); });
    
    // Admin inventory changes are applied immediately - no Update button needed

    // Admin inventory - no Save Changes button needed (direct actions only)

    // Inventory Logs Tab Functionality
    // Remove old bulk-accept button wiring if element not present
    const bulkAccept = document.getElementById('acceptChangesBtn');
    if (bulkAccept) {
        bulkAccept.addEventListener('click', function() {
            if (!confirm('Accept and apply all pending inventory changes?')) return;
            const btn = this;
            btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';
            fetch('{{ route('admin.admin.inventory.approve-pending') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                .then(r => r.json())
                .then(data => { showAlert(data.message || 'Inventory changes accepted.', 'success'); setTimeout(() => location.reload(), 1000); })
                .catch(() => { showAlert('Failed to apply changes.', 'error'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Accept Changes'; });
        });
    }
    
    // Handle Decline button
    const declineBtn = document.getElementById('declineChangesBtn');
    if (declineBtn) {
        declineBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to decline these inventory changes?')) {
                showAlert('Inventory changes have been declined.', 'success');
                
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
        .then(data => { showAlert(data.message || 'Done', 'success'); setTimeout(() => location.reload(), 1000); })
        .catch(() => showAlert('Request failed', 'error'));
    return false;
}

function approveSingle(e, logId) {
    e.preventDefault();
    fetch(`/admin/inventory/approve-log/${logId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
        .then(r => r.json())
        .then(data => { 
            if (data.success) { 
                // Remove the approved row from the table
                const row = e.target.closest('tr');
                if (row) {
                    row.remove();
                }
                // Show success message
                showAlert('Change approved successfully!', 'success');
            } else { 
                showAlert(data.message || 'Failed', 'error'); 
            } 
        })
        .catch(() => showAlert('Request failed', 'error'));
    return false;
}

function rejectSingle(e, logId) {
    e.preventDefault();
    if (!confirm('Decline this change?')) return false;
    fetch(`/admin/inventory/reject-log/${logId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
        .then(r => r.json())
        .then(data => { 
            if (data.success) { 
                // Remove the rejected row from the table
                const row = e.target.closest('tr');
                if (row) {
                    row.remove();
                }
                // Show success message
                showAlert('Change declined successfully!', 'success');
            } else { 
                showAlert(data.message || 'Failed', 'error'); 
            } 
        })
        .catch(() => showAlert('Request failed', 'error'));
    return false;
}

// Smart Delete Confirmation Modal
function showDeleteConfirmationModal(productId, row) {
    // Get product name from the row
    const productName = row.querySelector('td:nth-child(2)').textContent.trim();
    
    // Check if product is used in compositions (this would be an AJAX call in real implementation)
    // For now, we'll show a smart modal that reminds about compositions
    const modalHtml = `
        <div class="modal fade" id="smartDeleteModal" tabindex="-1" aria-labelledby="smartDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="smartDeleteModalLabel">
                            <i class="fas fa-trash me-2"></i>
                            Delete Product
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-3">Delete "${productName}"?</h6>
                        <p class="text-muted mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete(${productId}, '${productName}')">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('smartDeleteModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('smartDeleteModal'));
    modal.show();
    
    // Store the row reference for later use
    window.currentDeleteRow = row;
}


// Function to confirm delete
function confirmDelete(productId, productName) {
    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('smartDeleteModal'));
    modal.hide();
    
    // Proceed with the original delete logic
    if (window.currentDeleteRow) {
        const row = window.currentDeleteRow;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Send delete request immediately (IMMEDIATE DELETE)
        fetch(`/admin/inventory/product/${productId}`, {
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
                console.log('Product deleted successfully:', productId);
                
                // Remove row from table immediately
                if (row) {
                    row.remove();
                }
                
                showAlert(`"${productName}" has been deleted successfully!`, 'success');
            } else {
                showAlert('Failed to delete product: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showAlert('Error deleting product. Please try again.', 'error');
        });
    }
}

</script>
</div>
@endsection
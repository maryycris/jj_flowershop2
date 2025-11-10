@extends('layouts.clerk_app')

@push('styles')
<style>
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
    cursor: pointer !important;
    z-index: 10 !important;
    pointer-events: auto !important;
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

/* Edit Product Modal scrollbar styling */
.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #4CAF50;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #45a049;
}

/* Marked for deletion (use row background, not borders) */
table.table tr.product-row-deleted,
table.table tr.product-row-deleted td {
    background-color: rgba(255, 99, 99, 0.5) !important; /* soft red */
}


/* Ensure Actions header doesn't have action buttons */
thead th:last-child {
    text-align: center;
}

thead th:last-child::before,
thead th:last-child::after {
    display: none !important;
    content: none !important;
}

/* Remove any action buttons from table header */
thead .action-btn {
    display: none !important;
}

thead .d-flex {
    display: none !important;
}

/* More specific rules to hide action buttons in header */
thead th:last-child .action-btn,
thead th:last-child .edit-btn,
thead th:last-child .delete-btn,
thead th:last-child .btn,
thead th:last-child i {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
}

/* Ensure Actions header only shows text */
thead th:last-child {
    text-align: center !important;
    font-weight: bold !important;
    position: relative !important;
    overflow: hidden !important;
}

thead th:last-child * {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

thead th:last-child::before {
    content: "Actions" !important;
    display: block !important;
    font-weight: bold !important;
    position: relative !important;
    z-index: 10 !important;
}

/* Ensure thead stays fixed and has higher z-index than any action buttons */
thead {
    position: sticky !important;
    top: 0 !important;
    z-index: 100 !important;
    background: #e6f4ea !important;
}

thead th {
    position: sticky !important;
    top: 0 !important;
    z-index: 101 !important;
    background: #e6f4ea !important;
}

/* Clerk Inventory Font Size Reductions */
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

/* Action Buttons */
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
    font-size: 0.7rem !important;
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
</style>
@endpush


@section('content')
<div class="mx-auto" style="max-width: 1400px; padding-top: 12px;">

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addProductForm" action="{{ route('clerk.inventory.store') }}" method="POST">
        @csrf
        <div class="modal-body">
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
              <option value="Materials, Tools, and Equipment">Materials, Tools, and Equipment</option>
              <option value="Office Supplies">Office Supplies</option>
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
<div class="d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2">
    <div class="input-group" style="max-width: 360px;">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" id="inventorySearch" placeholder="Search code, name, or category..." autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" id="clearInventorySearch">Clear</button>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add New Material</button>
    <button class="btn btn-success" id="submitInventoryBtn">Save changes</button>
    </div>
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

<!-- Changes Reminder Modal -->
<div class="modal fade" id="changesReminderModal" tabindex="-1" aria-labelledby="changesReminderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="changesReminderModalLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Changes Pending Update
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <i class="fas fa-edit fa-3x text-warning mb-3"></i>
          <h6 class="text-dark">You have unsaved changes!</h6>
          <p class="text-muted">Don't forget to click the <strong>"Save changes"</strong> button to save your changes and notify the admin.</p>
        </div>
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          <strong>Reminder:</strong> Your changes are currently staged and will be lost if you navigate away without clicking "Save changes".
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">I'll Update Later</button>
        <button type="button" class="btn btn-success" id="updateNowBtn">
          <i class="fas fa-save me-2"></i>Update Now
        </button>
      </div>
    </div>
  </div>
</div>

@if($products->count())
    <!-- Bootstrap Nav Tabs -->
    <ul class="nav nav-tabs mb-2" id="inventoryTabs" role="tablist">
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
                    <table class="table table-bordered align-middle" style="font-size: 0.75rem;">
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
                                            class="product-row-deleted" 
                                        @endif>
                                    <td style="font-size: 0.8rem;">{{ $product->code ?? $product->id }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->name }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->category }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->price }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->cost_price ?? '-' }}</td>
                                    <td style="font-size: 0.8rem;">{{ $min }}</td>
                                    <td style="font-size: 0.8rem;">{{ $max }}</td>
                                    <td style="font-size: 0.8rem;">{{ $stock }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->qty_consumed ?? '-' }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->qty_damaged ?? '-' }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->qty_sold ?? '-' }}</td>
                                    <td style="font-size: 0.8rem;">{{ $qtyToPurchase }}</td>
                                    <td style="font-size: 0.8rem;">{{ $product->created_at ? $product->created_at->format('Y-m-d') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Edit Button -->
                                            <button class="btn btn-sm action-btn edit-btn edit-product-btn" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" data-product-id="{{ $product->id }}" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm action-btn delete-btn" 
                                                    data-product-id="{{ $product->id }}" 
                                                    data-is-marked="{{ $product->is_marked_for_deletion ? 'true' : 'false' }}"
                                                    title="{{ $product->is_marked_for_deletion ? 'Unmark for deletion' : 'Mark for deletion' }}">
                                                <i class="bi {{ $product->is_marked_for_deletion ? 'bi-arrow-counterclockwise' : 'bi-trash3' }}"></i>
                                            </button>
                                        </div>
                                            <!-- Edit Modal -->
                                <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel{{ $product->id }}">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form action="{{ route('clerk.inventory.update', $product->id) }}" method="POST" data-product-id="{{ $product->id }}">
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
                                                          <option value="Wrappers" @if($product->category == 'Wrappers') selected @endif>Wrappers</option>
                                                          <option value="Ribbon" @if($product->category == 'Ribbon') selected @endif>Ribbon</option>
                                                          <option value="Materials, Tools, and Equipment" @if($product->category == 'Materials, Tools, and Equipment') selected @endif>Materials, Tools, and Equipment</option>
                                                          <option value="Office Supplies" @if($product->category == 'Office Supplies') selected @endif>Office Supplies</option>
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
                                                      <button type="submit" class="btn btn-primary">Edit</button>
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
</div>

<style>
/* Override Bootstrap table-striped styling */
.table tbody tr:nth-of-type(odd) {
    background-color: white !important;
}

.table tbody tr:nth-of-type(even) {
    background-color: white !important;
}

/* Ensure full row background coverage */
.table tbody tr {
    background-color: white !important;
}

.table tbody tr td {
    background-color: white !important;
    border-color: #dee2e6 !important;
}

table.table tr.product-row-edited,
table.table tr.product-row-edited td {
    background-color: rgba(135, 206, 235, 0.5) !important; /* edited: blue */
}

.product-row-deleted,
.product-row-deleted td {
    background-color: rgba(255, 99, 99, 0.5) !important; /* deleted: red */
}

/* Newly added (staged) rows should be green */
table.table tr.product-row-added,
table.table tr.product-row-added td {
    background-color: rgba(46, 204, 113, 0.35) !important; /* soft green */
}

/* Discard button for pending new rows */
.discard-new-btn { padding: 2px 8px; font-size: 0.8rem; }
.discard-new-btn[disabled] { opacity: .6; pointer-events: none; }
</style>
<style>
.inventory-scroll {
    max-height: 60vh;
    overflow-y: scroll; /* always show vertical scrollbar for consistency */
    overflow-x: auto;
    scrollbar-color: #7bb47b #f1f1f1; /* Firefox */
    scrollbar-width: thin;            /* Firefox */
}
.inventory-scroll table { margin-bottom: 0; }
/* Keep table header fixed while body scrolls */
.inventory-scroll table thead th {
    position: sticky;
    top: 0;
    background: #e6f4ea; /* light green */
    color: #1e874b;      /* green text */
    z-index: 2;
}
/* Green scrollbar styling (WebKit) */
.inventory-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
.inventory-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
.inventory-scroll::-webkit-scrollbar-thumb { background: #7bb47b; border-radius: 4px; }
.inventory-scroll::-webkit-scrollbar-thumb:hover { background: #5aa65a; }
/* Hide scrollbar arrows for smoother look */
.inventory-scroll::-webkit-scrollbar-button { display: none; width: 0; height: 0; }

/* Make category tab names green */
#inventoryTabs .nav-link { color: #27ae60 !important; }
#inventoryTabs .nav-link.active { color: #1e874b !important; border-color: transparent !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inventory highlighting script loaded');
    
    // Track edited and deleted products
    let editedProducts = new Set();
    let deletedProducts = new Set();
    let stagedEdits = {};
    let newProducts = []; // staged new products (no IDs yet)
    let submittedForApproval = false; // Track if changes have been submitted
    
    // Load highlighted products from session storage
    const savedEdited = sessionStorage.getItem('editedProducts');
    const savedDeleted = sessionStorage.getItem('deletedProducts');
    const savedStagedEdits = sessionStorage.getItem('stagedEdits');
    const savedNewProducts = sessionStorage.getItem('newProducts');
    const savedSubmitted = sessionStorage.getItem('submittedForApproval');
    
    if (savedEdited) {
        editedProducts = new Set(JSON.parse(savedEdited));
        console.log('Loaded edited products from session:', Array.from(editedProducts));
    }
    
    if (savedDeleted) {
        deletedProducts = new Set(JSON.parse(savedDeleted));
        console.log('Loaded deleted products from session:', Array.from(deletedProducts));
    }
    
    if (savedStagedEdits) {
        try {
            stagedEdits = JSON.parse(savedStagedEdits);
            console.log('Loaded staged edits from session:', stagedEdits);
        } catch(e) {
            console.error('Error parsing staged edits:', e);
            stagedEdits = {};
        }
    }
    if (savedNewProducts) {
        try {
            newProducts = JSON.parse(savedNewProducts) || [];
        } catch(e) { newProducts = []; }
    }
    
    if (savedSubmitted) {
        submittedForApproval = JSON.parse(savedSubmitted);
        console.log('Loaded submitted status from session:', submittedForApproval);
    }
    
    // Debug: Log all session storage values
    console.log('=== SESSION STORAGE DEBUG ===');
    console.log('submittedForApproval:', submittedForApproval);
    console.log('editedProducts:', Array.from(editedProducts));
    console.log('deletedProducts:', Array.from(deletedProducts));
    console.log('stagedEdits:', stagedEdits);
    console.log('newProducts:', newProducts);
    console.log('=============================');
    
    // Function to disable actions for marked items
    function disableActionsForMarkedItems() {
        // Disable actions for edited products
        editedProducts.forEach(productId => {
            const row = document.getElementById('product-row-' + productId);
            if (row) {
                const editBtn = row.querySelector('.edit-product-btn');
                const deleteBtn = row.querySelector('.delete-btn');
                if (editBtn) {
                    editBtn.disabled = true;
                    editBtn.style.opacity = '0.5';
                    editBtn.style.cursor = 'not-allowed';
                }
                if (deleteBtn) {
                    deleteBtn.disabled = true;
                    deleteBtn.style.opacity = '0.5';
                    deleteBtn.style.cursor = 'not-allowed';
                }
            }
        });
        
        // Disable actions for deleted products
        deletedProducts.forEach(productId => {
            const row = document.getElementById('product-row-' + productId);
            if (row) {
                const editBtn = row.querySelector('.edit-product-btn');
                const deleteBtn = row.querySelector('.delete-btn');
                if (editBtn) {
                    editBtn.disabled = true;
                    editBtn.style.opacity = '0.5';
                    editBtn.style.cursor = 'not-allowed';
                }
                if (deleteBtn) {
                    deleteBtn.disabled = true;
                    deleteBtn.style.opacity = '0.5';
                    deleteBtn.style.cursor = 'not-allowed';
                }
            }
        });
        
        // Disable actions for new products
        document.querySelectorAll('.product-row-added').forEach(row => {
            const editBtn = row.querySelector('.edit-product-btn');
            const deleteBtn = row.querySelector('.delete-btn');
            if (editBtn) {
                editBtn.disabled = true;
                editBtn.style.opacity = '0.5';
                editBtn.style.cursor = 'not-allowed';
            }
            if (deleteBtn) {
                deleteBtn.disabled = true;
                deleteBtn.style.opacity = '0.5';
                deleteBtn.style.cursor = 'not-allowed';
            }
        });
    }
    
    // Apply markings if there are any pending changes
    editedProducts.forEach(productId => {
        const row = document.getElementById('product-row-' + productId);
        if (row) {
            row.classList.add('product-row-edited');
            row.style.backgroundColor = '';
            row.style.border = '';
        }
    });
    
    deletedProducts.forEach(productId => {
        const row = document.getElementById('product-row-' + productId);
        if (row) {
            row.classList.add('product-row-deleted');
            row.style.backgroundColor = '';
            row.style.border = '';
        }
    });
    
    // Render any staged new products into the active tab
    if (submittedForApproval && newProducts.length > 0) {
        console.log('Rendering staged new products:', newProducts);
        newProducts.forEach(renderNewProductRow);
    }
    
    // If submitted for approval, disable actions for marked items and check for admin decision
    if (submittedForApproval) {
        setTimeout(() => {
            disableActionsForMarkedItems();
        }, 100);
        
        // Check if admin has made a decision
        checkAdminDecision();
        
        // Check every 5 seconds for admin decision
        setInterval(checkAdminDecision, 5000);
        
        // Fallback: Clear markings after 5 seconds regardless of API status
        setTimeout(() => {
            console.log('Fallback: Clearing markings after 5 seconds');
            clearSubmittedStatus();
        }, 5000);
    }
    
    // Function to check if admin has made a decision
    async function checkAdminDecision() {
        if (!submittedForApproval) return; // Stop if already cleared
        
        console.log('Checking admin decision...');
        
        try {
            const response = await fetch('/clerk/inventory/check-approval-status', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            console.log('API Response status:', response.status);
            
            if (response.ok) {
                const data = await response.json();
                console.log('Admin decision check response:', data);
                
                // If no pending changes, admin has decided (approved or rejected)
                if (!data.pending_changes && !data.pending_additions && !data.pending_product_changes) {
                    console.log('No pending changes - admin has decided - clearing markings');
                    clearSubmittedStatus();
                } else {
                    console.log('Still pending changes:', {
                        pending_changes: data.pending_changes,
                        pending_additions: data.pending_additions,
                        pending_product_changes: data.pending_product_changes
                    });
                }
            } else {
                console.error('API Error:', response.status, response.statusText);
                const errorText = await response.text();
                console.error('Error details:', errorText);
            }
        } catch (error) {
            console.error('Error checking admin decision:', error);
        }
    }

    // Only clear staged data if not submitted for approval
    if (!submittedForApproval) {
    sessionStorage.removeItem('stagedEdits');
    sessionStorage.removeItem('editedProducts');
    sessionStorage.removeItem('deletedProducts');
        sessionStorage.removeItem('submittedForApproval');
        sessionStorage.removeItem('newProducts');
    }
    
    
    // Function to clear submitted status (called when admin approves changes)
    window.clearSubmittedStatus = function() {
        submittedForApproval = false;
        sessionStorage.removeItem('submittedForApproval');
        sessionStorage.removeItem('editedProducts');
        sessionStorage.removeItem('deletedProducts');
        sessionStorage.removeItem('stagedEdits');
        sessionStorage.removeItem('newProducts');
        
        // Clear highlighting
        editedProducts.clear();
        deletedProducts.clear();
        stagedEdits = {};
        newProducts = [];
        
        // Remove highlighting from rows
        document.querySelectorAll('.product-row-edited, .product-row-deleted, .product-row-added').forEach(row => {
            row.classList.remove('product-row-edited', 'product-row-deleted', 'product-row-added');
            row.style.backgroundColor = '';
            row.style.border = '';
        });
        
        // Re-enable all buttons
        document.querySelectorAll('.edit-product-btn, .delete-btn, .discard-new-btn').forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = '';
            btn.style.cursor = '';
        });
        
        // Re-enable save and add buttons
        const saveBtn = document.getElementById('submitInventoryBtn');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.style.opacity = '';
            saveBtn.style.cursor = '';
        }
        
        const addBtn = document.querySelector('[data-bs-target="#addProductModal"]');
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.style.opacity = '';
            addBtn.style.cursor = '';
        }
        
        // Remove staged new product rows
        document.querySelectorAll('.product-row-added').forEach(row => {
            row.remove();
        });
        
        console.log('Submitted status cleared - all actions re-enabled');
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
                // Recalculate qty_to_purchase: Max - On Hand
                const reorderMax = parseInt(v.reorder_max || 0);
                const stock = parseInt(v.stock || 0);
                const qtyToPurchase = (stock < reorderMax) ? Math.max(0, reorderMax - stock) : 0;
                if (row.cells[11]) row.cells[11].textContent = qtyToPurchase;
                row.classList.add('product-row-edited');
                row.style.backgroundColor = '';
                row.style.border = '';
                console.log('Applied staged values for product:', pid);
            }
        });
    } catch (e) {
        console.warn('Failed to apply staged edits from session', e);
    }

    // Render any staged new products into the active tab
    function renderNewProductRow(prod) {
        const pane = document.querySelector('#inventoryTabsContent .tab-pane.show.active');
        if (!pane) return;
        const tbody = pane.querySelector('tbody');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.classList.add('product-row-added');
        tr.innerHTML = `
            <td>NEW</td>
            <td>${prod.name || '-'}</td>
            <td>${prod.category || '-'}</td>
            <td>${Number(prod.price||0).toFixed(2)}</td>
            <td>${Number(prod.cost_price||0).toFixed(2)}</td>
            <td>${prod.reorder_min||0}</td>
            <td>${prod.reorder_max||0}</td>
            <td>${prod.stock||0}</td>
            <td>${prod.qty_consumed||0}</td>
            <td>${prod.qty_damaged||0}</td>
            <td>${prod.qty_sold||0}</td>
            <td>${Math.max(0, (parseInt(prod.reorder_max||0) - parseInt(prod.stock||0)))}</td>
            <td>-</td>
            <td class="text-center">(pending) <button type="button" class="btn btn-outline-danger btn-sm discard-new-btn">Discard</button></td>
        `;
        tbody.prepend(tr);
    }

    newProducts.forEach(renderNewProductRow);
    
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
    
    
    // Use event delegation for dynamically loaded content
    document.addEventListener('click', function(event) {
        console.log('Click detected on:', event.target);
        console.log('Button classes:', event.target.classList);
        
        // Check if button is disabled
        if (event.target.disabled || event.target.style.opacity === '0.5') {
            console.log('Button is disabled, ignoring click');
            event.preventDefault();
            event.stopPropagation();
            return;
        }
        
        if (event.target.classList.contains('edit-product-btn')) {
            console.log('Edit button clicked');
            handleEditClick.call(event.target, event);
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

    // Simple client-side filter for inventory table rows (per active tab)
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
    // Re-apply filter and markings when switching tabs
    document.getElementById('inventoryTabs')?.addEventListener('shown.bs.tab', function(){ 
        filterRows(searchInput?.value || ''); 
        
        // Re-apply markings after tab switch
        if (submittedForApproval) {
            setTimeout(() => {
                // Re-apply highlighting
                editedProducts.forEach(productId => {
                    const row = document.getElementById('product-row-' + productId);
                    if (row) {
                        row.classList.add('product-row-edited');
                        row.style.backgroundColor = '';
                        row.style.border = '';
                    }
                });
                
                deletedProducts.forEach(productId => {
                    const row = document.getElementById('product-row-' + productId);
                    if (row) {
                        row.classList.add('product-row-deleted');
                        row.style.backgroundColor = '';
                        row.style.border = '';
                    }
                });
                
                // Re-disable actions
                disableActionsForMarkedItems();
            }, 50);
        }
    });
    
    // Add direct click handlers for Delete buttons
    document.addEventListener('click', function(event) {
        console.log('Click detected on:', event.target);
        console.log('Button classes:', event.target.classList);
        
        // Check if the clicked element is a delete button or inside a delete button
        const deleteBtn = event.target.closest('.delete-btn');
        console.log('Delete button found:', deleteBtn);
        
        if (deleteBtn) {
            event.preventDefault();
            event.stopPropagation();
            
            console.log('Delete button clicked!');
            
            const productId = deleteBtn.getAttribute('data-product-id');
            const isMarked = deleteBtn.getAttribute('data-is-marked') === 'true';
            const productRow = document.getElementById('product-row-' + productId);
            const icon = deleteBtn.querySelector('i');
            
            console.log('Product ID:', productId, 'Is Marked:', isMarked);
            
            const confirmMessage = isMarked ? 
                'Are you sure you want to unmark this product for deletion?' : 
                'Are you sure you want to mark this product for deletion?';
            
            // Show confirmation
            if (confirm(confirmMessage)) {
                console.log('User confirmed, updating deletion status locally...');
                
                if (isMarked) {
                    // Unmark for deletion
                    deletedProducts.delete(productId);
                    productRow.classList.remove('product-row-deleted');
                    productRow.style.backgroundColor = '';
                    productRow.style.border = '';
                    
                    // Update button
                    icon.className = 'bi bi-trash3';
                    deleteBtn.setAttribute('title', 'Mark for deletion');
                    deleteBtn.setAttribute('data-is-marked', 'false');
                    
                    console.log('Product unmarked for deletion:', productId);
                } else {
                    // Mark for deletion
                    deletedProducts.add(productId);
                    productRow.classList.add('product-row-deleted');
                    productRow.style.backgroundColor = 'rgba(255, 99, 99, 0.7)';
                    productRow.style.border = '2px solid #FF6363';
                    
                    // Update button
                    icon.className = 'bi bi-arrow-counterclockwise';
                    deleteBtn.setAttribute('title', 'Unmark for deletion');
                    deleteBtn.setAttribute('data-is-marked', 'true');
                    
                    console.log('Product marked for deletion:', productId);
                }
                
                // Save to session storage
                sessionStorage.setItem('deletedProducts', JSON.stringify(Array.from(deletedProducts)));
                
                // Show success message
                console.log('Product deletion status updated successfully');
            }
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
                    
                    // let CSS handle the visuals
                    row.style.backgroundColor = '';
                    row.style.border = '';
                    
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
                        // Recalculate qty_to_purchase: Max - On Hand
                        const reorderMax = parseInt(newValues.reorder_max || 0);
                        const stock = parseInt(newValues.stock || 0);
                        const qtyToPurchase = (stock < reorderMax) ? Math.max(0, reorderMax - stock) : 0;
                        if (row.cells[11]) row.cells[11].textContent = qtyToPurchase;
                        console.log('Row visually updated with staged values');
                    }

                    // Reset button and close modal
                    const submitBtn = event.target;
                    submitBtn.textContent = 'Edit';
                    submitBtn.disabled = false;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal' + productId));
                    if (modal) { modal.hide(); }
                    
                    // Product marked for deletion successfully
                } else {
                    console.error('Row or productId not found:', { row, productId });
                }
            } else {
                console.log('No form found');
            }
        }
    });
    
    // Intercept Add New Product submit to stage visually and save for approval
    const addForm = document.getElementById('addProductForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e){
            e.preventDefault();
            const fd = new FormData(addForm);
            const prod = Object.fromEntries(fd.entries());
            newProducts.push(prod);
            sessionStorage.setItem('newProducts', JSON.stringify(newProducts));
            // render with green background
            renderNewProductRow(prod);
            // close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            if (modal) modal.hide();
        });
    }

    // Discard a staged new product (by row)
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('discard-new-btn')) {
            const row = e.target.closest('tr');
            if (row) {
                // Remove from session list by matching name+category+price (best-effort)
                const cells = row.querySelectorAll('td');
                const name = cells[1]?.textContent?.trim();
                const category = cells[2]?.textContent?.trim();
                newProducts = newProducts.filter(p => !(p.name===name && p.category===category));
                sessionStorage.setItem('newProducts', JSON.stringify(newProducts));
                row.remove();
            }
        }
    });


    // Handle Update button click
    const updateBtn = document.getElementById('submitInventoryBtn');
    if (updateBtn) {
        console.log('Update button found, adding event listener');
        updateBtn.addEventListener('click', function() {
            console.log('Main Update button clicked');
            console.log('Edited products:', Array.from(editedProducts));
            console.log('Deleted products:', Array.from(deletedProducts));
            console.log('Staged edits:', stagedEdits);
            console.log('New products:', newProducts);
            console.log('Edited products size:', editedProducts.size);
            console.log('Deleted products size:', deletedProducts.size);
            console.log('New products length:', newProducts.length);
            
            // Check if there are any changes to submit
            if (editedProducts.size === 0 && deletedProducts.size === 0 && newProducts.length === 0) {
                console.log('No changes detected - editedProducts.size:', editedProducts.size, 'deletedProducts.size:', deletedProducts.size, 'newProducts.length:', newProducts.length);
                alert('No changes to submit. Please make some changes first.');
                return;
            }
            
            // Create summary message
            let summaryMessage = 'Your request for updating the inventory has been send to the Admin. Please wait for the Admin\'s approval.';
            
            // Submit changes to backend first
            const submitData = {
                edited_products: JSON.stringify(Array.from(editedProducts)),
                deleted_products: JSON.stringify(Array.from(deletedProducts)),
                staged_edits: JSON.stringify(stagedEdits),
                new_products: JSON.stringify(newProducts),
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            fetch('{{ route("clerk.inventory.submit-changes") }}', {
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
                    
                    // Mark as submitted for approval
                    submittedForApproval = true;
                    sessionStorage.setItem('submittedForApproval', JSON.stringify(true));
                    
                    // Keep the marked products in session storage for persistence
                    sessionStorage.setItem('editedProducts', JSON.stringify(Array.from(editedProducts)));
                    sessionStorage.setItem('deletedProducts', JSON.stringify(Array.from(deletedProducts)));
                    sessionStorage.setItem('stagedEdits', JSON.stringify(stagedEdits));
                    sessionStorage.setItem('newProducts', JSON.stringify(newProducts));
                    
                    // Disable actions for marked items only
                    disableActionsForMarkedItems();
                    
                    // Keep the save button and add product button enabled
                    // (Buttons remain clickable after save changes)
                    
                    
                    // Disable discard buttons for new products
                    document.querySelectorAll('.discard-new-btn').forEach(btn => { 
                        btn.disabled = true; 
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                    });
                    
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
                
                        // Add event listener for OK button
                        const okButton = modal.querySelector('button[data-bs-dismiss="modal"]');
                        if (okButton) {
                            console.log('OK button found, adding click handler');
                            
                            // Use a simple click handler
                            okButton.onclick = function() {
                                console.log('OK button clicked - changes submitted successfully');
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
    
    // Function to show changes reminder modal
    function showChangesReminder() {
        const reminderModal = new bootstrap.Modal(document.getElementById('changesReminderModal'));
        reminderModal.show();
        
        // Handle "Update Now" button click
        const updateNowBtn = document.getElementById('updateNowBtn');
        if (updateNowBtn) {
            updateNowBtn.addEventListener('click', function() {
                reminderModal.hide();
                // Trigger the main update button click
                const mainUpdateBtn = document.getElementById('submitInventoryBtn');
                if (mainUpdateBtn) {
                    mainUpdateBtn.click();
                }
            });
        }
    }
    
    // Show reminder when user tries to navigate away with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        const editedProducts = new Set(JSON.parse(sessionStorage.getItem('editedProducts') || '[]'));
        const deletedProducts = new Set(JSON.parse(sessionStorage.getItem('deletedProducts') || '[]'));
        
        if (editedProducts.size > 0 || deletedProducts.size > 0) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // Show reminder when user clicks on navigation links with unsaved changes
    document.addEventListener('click', function(e) {
        if (e.target.matches('a[href]') && !e.target.href.includes('#')) {
            const editedProducts = new Set(JSON.parse(sessionStorage.getItem('editedProducts') || '[]'));
            const deletedProducts = new Set(JSON.parse(sessionStorage.getItem('deletedProducts') || '[]'));
            
            if (editedProducts.size > 0 || deletedProducts.size > 0) {
                e.preventDefault();
                showChangesReminder();
            }
        }
    });

});
</script>
@endsection
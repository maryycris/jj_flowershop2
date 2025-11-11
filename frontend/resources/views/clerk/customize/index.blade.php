@extends('layouts.clerk_app')
@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="bg-white rounded-3 p-3" style="box-shadow:none;">
                <ul class="nav nav-tabs" id="customizeTabs" role="tablist">
                    @foreach($categories as $i => $cat)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($i==0) active @endif" data-bs-toggle="tab" data-bs-target="#tab-{{ Str::slug($cat) }}" type="button" role="tab">{{ $cat }}</button>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content border-start border-end border-bottom rounded-bottom p-3" id="customizeTabContent">
                    @foreach($categories as $i => $cat)
                    <div class="tab-pane fade @if($i==0) show active @endif" id="tab-{{ Str::slug($cat) }}" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                @if($cat == 'Greenery') Greenery Items
                                @elseif($cat == 'Ribbons') Ribbons Items
                                @elseif($cat == 'Wrappers') Wrappers Items
                                @else {{ $cat }} Items
                                @endif
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="selectAllBtn btn btn-outline-primary btn-sm" onclick="toggleSelectAll()">
                                    <i class="bi bi-check-square"></i> Select All
                                </button>
                                <button class="removeSelectedBtn btn btn-danger btn-sm" style="display: none;" onclick="removeSelectedItems()">
                                    <i class="bi bi-trash"></i> Remove Selected
                                </button>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal" data-category="{{ $cat }}"><i class="bi bi-plus-lg"></i> Add</button>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach(($items[$cat] ?? []) as $item)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <div class="position-relative">
                                        @if($item->image)
                                            <img src="{{ asset('storage/'.$item->image) }}" class="card-img-top" style="height:140px;object-fit:cover;">
                                        @else
                                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:140px;">
                                                <div class="text-center text-muted">
                                                    <i class="bi bi-image fs-1"></i>
                                                    <div class="small mt-2">No Image</div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="position-absolute top-0 start-0 p-2" style="z-index: 10;">
                                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" onchange="console.log('Checkbox changed:', this.checked, this.value); toggleRemoveButton();" style="width: 18px; height: 18px; background-color: white; border: 2px solid #007bff;">
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <div class="text-muted small">₱{{ number_format($item->computed_price ?? ($item->inventoryItem->price ?? ($item->price ?? 0)), 2) }}</div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-center gap-2 p-2">
                                        <button class="btn btn-sm action-btn edit-btn" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                        <form method="POST" action="{{ route('clerk.customize.destroy',$item->id) }}" onsubmit="return confirm('Delete this item?')" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm action-btn delete-btn" title="Delete"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                              <div class="modal-dialog">
                                <form class="modal-content" method="POST" action="{{ route('clerk.customize.update',$item->id) }}" enctype="multipart/form-data">
                                  @csrf @method('PUT')
                                  <div class="modal-header"><h5 class="modal-title">Edit {{ $cat }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                  <div class="modal-body">
                                    <div class="mb-2">
                                        <label class="form-label">Category</label>
                                        <select name="category" class="form-select" required onchange="loadInventoryItemsEdit({{ $item->id }})">
                                            <option value="">Select Category</option>
                                            <option value="Fresh Flowers" @if($item->category=='Fresh Flowers') selected @endif>Fresh Flowers</option>
                                            <option value="Artificial Flowers" @if($item->category=='Artificial Flowers') selected @endif>Artificial Flowers</option>
                                            <option value="Wrappers" @if($item->category=='Wrappers') selected @endif>Wrappers</option>
                                            <option value="Greenery" @if($item->category=='Greenery') selected @endif>Greenery</option>
                                            <option value="Ribbon" @if($item->category=='Ribbon') selected @endif>Ribbon</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Item Name</label>
                                        <div class="searchable-dropdown">
                                            <input type="text" id="itemSearchInputEdit{{ $item->id }}" class="form-control" placeholder="Search items..." autocomplete="off" required value="{{ $item->name }}">
                                            <input type="hidden" name="name" id="selectedItemNameEdit{{ $item->id }}" value="{{ $item->name }}">
                                            <input type="hidden" name="inventory_item_id" id="selectedItemIdEdit{{ $item->id }}" value="{{ $item->inventory_item_id ?? '' }}">
                                            <div class="dropdown-options" id="itemDropdownOptionsEdit{{ $item->id }}" style="display: none;">
                                                <!-- Options will be populated dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Price</label>
                                        <input type="number" step="0.01" name="price" id="itemPriceEdit{{ $item->id }}" class="form-control" readonly value="{{ $item->inventoryItem ? $item->inventoryItem->price : ($item->price ?? 0) }}">
                                        <small class="text-muted">Price will be auto-filled from inventory</small>
                                    </div>
                                    <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control"></div>
                                  </div>
                                  <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
                                </form>
                              </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal (shared) -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('clerk.customize.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Add Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
        <div class="mb-2">
            <label class="form-label">Category</label>
            <select name="category" id="addCategorySelect" class="form-select" required onchange="loadInventoryItems()">
                <option value="">Select Category</option>
                <option value="Fresh Flowers">Fresh Flowers</option>
                <option value="Artificial Flowers">Artificial Flowers</option>
                <option value="Wrappers">Wrappers</option>
                <option value="Greenery">Greenery</option>
                <option value="Ribbon">Ribbon</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">Item Name</label>
            <div class="searchable-dropdown">
                <input type="text" id="itemSearchInput" class="form-control" placeholder="Search items..." autocomplete="off" required>
                <input type="hidden" name="name" id="selectedItemName">
                <input type="hidden" name="inventory_item_id" id="selectedItemId">
                <div class="dropdown-options" id="itemDropdownOptions" style="display: none;">
                    <!-- Options will be populated dynamically -->
                </div>
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" id="itemPrice" class="form-control" readonly>
            <small class="text-muted">Price will be auto-filled from inventory</small>
        </div>
        <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Add</button></div>
    </form>
  </div>
  </div>

@push('styles')
<style>
/* Add Item Modal scrollbar styling */
#addModal .modal-body::-webkit-scrollbar {
    width: 6px;
}
#addModal .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
#addModal .modal-body::-webkit-scrollbar-thumb {
    background: #7bb47b;
    border-radius: 3px;
}
#addModal .modal-body::-webkit-scrollbar-thumb:hover {
    background: #7bb47b;
}

/* Searchable Dropdown Styles */
.searchable-dropdown {
    position: relative;
}

.dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.dropdown-option {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
}

.dropdown-option:hover {
    background-color: #f8f9fa;
}

.dropdown-option:last-child {
    border-bottom: none;
}

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

/* Action Buttons Styling */
.action-btn,
.action-btn.btn,
.action-btn.btn-sm {
    width: 50px;
    height: 40px;
    border: none !important;
    background-color: transparent !important;
    background-image: none !important;
    color: #4CAF50 !important;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    padding: 0 !important;
    margin: 0 !important;
    font-size: 16px;
    flex: 1;
    min-width: 50px;
    max-width: 50px;
    box-shadow: none !important;
    text-decoration: none !important;
    vertical-align: baseline !important;
    cursor: pointer;
    position: relative;
    z-index: 1;
}

.action-btn:focus,
.action-btn:active,
.action-btn:focus-visible,
.action-btn:not(:hover),
.action-btn.btn:focus,
.action-btn.btn:active,
.action-btn.btn:focus-visible,
.action-btn.btn:not(:hover) {
    background-color: transparent !important;
    background-image: none !important;
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.action-btn i {
    transition: color 0.3s ease;
}

/* Edit Button */
.action-btn.edit-btn:hover,
.btn.action-btn.edit-btn:hover {
    background-color: #007bff !important;
    color: white !important;
}

.action-btn.edit-btn:hover i,
.btn.action-btn.edit-btn:hover i {
    color: white !important;
}

/* Delete Button */
.action-btn.delete-btn:hover,
.btn.action-btn.delete-btn:hover {
    background-color: #dc3545 !important;
    color: white !important;
}

.action-btn.delete-btn:hover i,
.btn.action-btn.delete-btn:hover i {
    color: white !important;
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

/* Card footer styling for better integration */
.card-footer {
    background: transparent !important;
    border-top: none !important;
    padding: 8px 12px !important;
}

/* Ensure buttons blend with card background */
.card .action-btn {
    background: transparent !important;
    background-color: transparent !important;
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('addModal')?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (btn && btn.dataset.category) {
        document.getElementById('addCategorySelect').value = btn.dataset.category;
        loadInventoryItems(); // Load items when modal opens with pre-selected category
    }
});

// Load inventory items based on selected category
async function loadInventoryItems() {
    const categorySelect = document.getElementById('addCategorySelect');
    const searchInput = document.getElementById('itemSearchInput');
    const dropdownOptions = document.getElementById('itemDropdownOptions');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemId = document.getElementById('selectedItemId');
    const itemPrice = document.getElementById('itemPrice');
    
    const category = categorySelect.value;
    
    // Clear previous selections
    searchInput.value = '';
    selectedItemName.value = '';
    selectedItemId.value = '';
    itemPrice.value = '';
    dropdownOptions.innerHTML = '';
    dropdownOptions.style.display = 'none';
    
    if (!category) {
        return;
    }
    
    try {
        const response = await fetch(`/clerk/api/inventory/${category}`);
        const items = await response.json();
        
        // Set up search functionality - only search on Enter key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.toLowerCase();
                const filteredItems = items.filter(item => 
                    item.name.toLowerCase().includes(query)
                );
                
                displayItems(filteredItems);
            }
        });
        // Remove live typing search - only search on Enter key press
        // searchInput.addEventListener('input', function() {
        //     const query = this.value.toLowerCase();
        //     const filteredItems = items.filter(item => 
        //         item.name.toLowerCase().includes(query)
        //     );
        //     
        //     displayItems(filteredItems);
        // });
        
        // Show all items initially
        displayItems(items);
        
    } catch (error) {
        console.error('Error loading inventory items:', error);
    }
}

// Display items in dropdown
function displayItems(items) {
    const dropdownOptions = document.getElementById('itemDropdownOptions');
    
    if (items.length === 0) {
        dropdownOptions.innerHTML = '<div class="dropdown-option text-muted">No items found</div>';
        dropdownOptions.style.display = 'block';
        return;
    }
    
    dropdownOptions.innerHTML = '';
    items.forEach(item => {
        const option = document.createElement('div');
        option.className = 'dropdown-option';
        option.innerHTML = `
            <div class="fw-bold">${item.name}</div>
            <small class="text-muted">Price: ₱${item.price} | Stock: ${item.stock}</small>
        `;
        
        option.addEventListener('click', function() {
            selectItem(item);
        });
        
        dropdownOptions.appendChild(option);
    });
    
    dropdownOptions.style.display = 'block';
}

// Select an item
function selectItem(item) {
    const searchInput = document.getElementById('itemSearchInput');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemId = document.getElementById('selectedItemId');
    const itemPrice = document.getElementById('itemPrice');
    const dropdownOptions = document.getElementById('itemDropdownOptions');
    
    searchInput.value = item.name;
    selectedItemName.value = item.name;
    selectedItemId.value = item.id;
    itemPrice.value = item.price;
    
    dropdownOptions.style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const searchableDropdown = document.querySelector('.searchable-dropdown');
    if (searchableDropdown && !searchableDropdown.contains(e.target)) {
        document.getElementById('itemDropdownOptions').style.display = 'none';
    }
});

// Load inventory items for edit modal
async function loadInventoryItemsEdit(itemId) {
    const categorySelect = document.querySelector(`#editModal${itemId} select[name="category"]`);
    const searchInput = document.getElementById(`itemSearchInputEdit${itemId}`);
    const dropdownOptions = document.getElementById(`itemDropdownOptionsEdit${itemId}`);
    const selectedItemName = document.getElementById(`selectedItemNameEdit${itemId}`);
    const selectedItemId = document.getElementById(`selectedItemIdEdit${itemId}`);
    const itemPrice = document.getElementById(`itemPriceEdit${itemId}`);
    
    const category = categorySelect.value;
    
    // Clear previous selections
    searchInput.value = '';
    selectedItemName.value = '';
    selectedItemId.value = '';
    itemPrice.value = '';
    dropdownOptions.innerHTML = '';
    dropdownOptions.style.display = 'none';
    
    if (!category) {
        return;
    }
    
    try {
        const response = await fetch(`/clerk/api/inventory/${category}`);
        const items = await response.json();
        
        // Set up search functionality - only search on Enter key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.toLowerCase();
                const filteredItems = items.filter(item => 
                    item.name.toLowerCase().includes(query)
                );
                
                displayItemsEdit(filteredItems, itemId);
            }
        });
        // Remove live typing search - only search on Enter key press
        // searchInput.addEventListener('input', function() {
        //     const query = this.value.toLowerCase();
        //     const filteredItems = items.filter(item => 
        //         item.name.toLowerCase().includes(query)
        //     );
        //     
        //     displayItemsEdit(filteredItems, itemId);
        // });
        
        // Show all items initially
        displayItemsEdit(items, itemId);
        
    } catch (error) {
        console.error('Error loading inventory items:', error);
    }
}

// Display items in edit dropdown
function displayItemsEdit(items, itemId) {
    const dropdownOptions = document.getElementById(`itemDropdownOptionsEdit${itemId}`);
    
    if (items.length === 0) {
        dropdownOptions.innerHTML = '<div class="dropdown-option text-muted">No items found</div>';
        dropdownOptions.style.display = 'block';
        return;
    }
    
    dropdownOptions.innerHTML = '';
    items.forEach(item => {
        const option = document.createElement('div');
        option.className = 'dropdown-option';
        option.innerHTML = `
            <div class="fw-bold">${item.name}</div>
            <small class="text-muted">Price: ₱${item.price} | Stock: ${item.stock}</small>
        `;
        
        option.addEventListener('click', function() {
            selectItemEdit(item, itemId);
        });
        
        dropdownOptions.appendChild(option);
    });
    
    dropdownOptions.style.display = 'block';
}

// Select an item in edit modal
function selectItemEdit(item, itemId) {
    const searchInput = document.getElementById(`itemSearchInputEdit${itemId}`);
    const selectedItemName = document.getElementById(`selectedItemNameEdit${itemId}`);
    const selectedItemId = document.getElementById(`selectedItemIdEdit${itemId}`);
    const itemPrice = document.getElementById(`itemPriceEdit${itemId}`);
    const dropdownOptions = document.getElementById(`itemDropdownOptionsEdit${itemId}`);
    
    searchInput.value = item.name;
    selectedItemName.value = item.name;
    selectedItemId.value = item.id;
    itemPrice.value = item.price;
    
    dropdownOptions.style.display = 'none';
}

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
        form.action = '{{ route("clerk.customize.bulk-delete") }}';
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
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
@endpush
@endsection



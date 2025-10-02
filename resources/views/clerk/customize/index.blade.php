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
                                        @endif
                                        <div class="position-absolute top-0 start-0 p-2" style="z-index: 10;">
                                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" onchange="console.log('Checkbox changed:', this.checked, this.value); toggleRemoveButton();" style="width: 18px; height: 18px; background-color: white; border: 2px solid #007bff;">
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <div class="text-muted small">₱{{ number_format($item->price ?? 0,2) }}</div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between p-2">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">Edit</button>
                                        <form method="POST" action="{{ route('clerk.customize.destroy',$item->id) }}" onsubmit="return confirm('Delete this item?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">Delete</button>
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
                                    <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ $item->name }}" required></div>
                                    <div class="mb-2"><label class="form-label">Category</label>
                                        <select name="category" class="form-select">
                                            @foreach($categories as $c)<option value="{{ $c }}" @if($item->category==$c) selected @endif>{{ $c }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2"><label class="form-label">Price (optional)</label><input type="number" step="0.01" name="price" class="form-control" value="{{ $item->price }}"></div>
                                    <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control"></div>
                                    <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea></div>
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
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Category</label>
            <select name="category" id="addCategorySelect" class="form-select">
                @foreach($categories as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
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

@push('styles')
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
@endpush

@push('scripts')
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



@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Header with Navigation and Workflow -->
                    <div class="p-3" style="border-bottom:1px solid #e6f0e6;">
                        <!-- Navigation and Order Info -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <a href="{{ route('admin.orders.walkin.sales_order', $order->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Back" aria-label="Back" style="width:34px;height:34px;padding:0;">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <button type="button" class="btn btn-info btn-sm px-3 py-1" style="border-radius: 15px; font-size: 0.75rem;">New</button>
                <div class="ms-2 small text-muted">
                    <div>Quotations / {{ sprintf('%05d', $order->id) }}</div>
                    <div class="fw-bold text-primary">
                        @if($order->salesOrder)
                            <a href="{{ route('admin.sales-orders.show', $order->id) }}" class="text-decoration-none text-primary" style="cursor: pointer;">
                                {{ str_replace('-', '', $order->salesOrder->so_number) }}
                            </a>
                        @else
                            SO{{ sprintf('%05d', $order->id) }}
                        @endif
                    </div>
                </div>
                        </div>
                        
                        <!-- Action Buttons and Workflow -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                    <i class="bi bi-check-circle me-1"></i>Validate
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="printOrder()">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                                <a href="{{ route('admin.sales-orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <!-- Workflow Indicator -->
                                <div class="d-flex align-items-center gap-1 ms-3">
                                    <div class="badge bg-secondary text-white px-2 py-1">Draft</div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                    <div class="badge bg-secondary text-white px-2 py-1">Waiting</div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                    <div class="badge {{ request('view') === 'moves' ? 'bg-success' : 'bg-secondary' }} text-white px-2 py-1" id="readyBadge">Ready</div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                    <div class="badge {{ $order->order_status === 'approved' || $order->order_status === 'completed' || $order->order_status === 'on_delivery' ? 'bg-success' : 'bg-secondary' }} text-white px-2 py-1" id="doneBadge">Done</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details Section -->
                    <div class="px-3 pt-3 pb-4">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="p-3">
                                    <h4 class="fw-bold mb-2">{{ $inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001' }}</h4>
                                    <div class="text-muted">Delivery Address</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3">
                                    <div class="fw-semibold mb-2">Customer Information</div>
                                    <div class="small">
                                        @php
                                            $notes = $order->notes ?? '';
                                            $customerName = $order->user->name ?? 'Walk-in Customer';
                                            if (!empty($notes) && preg_match('/Customer:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                                                $customerName = trim($m[1]);
                                            }
                                            $emailFromNotes = null;
                                            if (!empty($notes) && preg_match('/Email:\s*([^;,\s]+@[^;,\s]+)/', $notes, $m)) {
                                                $emailFromNotes = trim($m[1]);
                                            }
                                            
                                            // Get loyalty card info
                                            $loyaltyCard = null;
                                            $loyaltyStamps = 0;
                                            $canEarnStamp = false;
                                            
                                            // Check if this order can earn a stamp (any product ≥ ₱500)
                                            foreach ($order->products as $product) {
                                                if ($product->price >= 500) {
                                                    $canEarnStamp = true;
                                                    break;
                                                }
                                            }
                                            
                                            // Check if customer can redeem loyalty discount (5 stamps)
                                            $canRedeemDiscount = false;
                                            $loyaltyDiscountAmount = 0;
                                            $discountedProduct = null;
                                            if ($loyaltyCard && $loyaltyCard->stamps_count >= 5) {
                                                $canRedeemDiscount = true;
                                                // Find most expensive bouquet for 50% discount
                                                $bouquets = $order->products->filter(function($product) {
                                                    return strtolower($product->category) === 'bouquet' && !str_contains(strtolower($product->category), 'mini');
                                                });
                                                if ($bouquets->isNotEmpty()) {
                                                    $discountedProduct = $bouquets->sortByDesc('price')->first();
                                                    $loyaltyDiscountAmount = $discountedProduct->price * 0.5;
                                                }
                                            }
                                            
                                            // For walk-in customers, try to find/create loyalty card by email
                                            if ($order->user_id) {
                                                $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $order->user_id)->where('status', 'active')->first();
                                            } elseif ($emailFromNotes) {
                                                // For walk-in customers, find user by email or create loyalty card
                                                $user = \App\Models\User::where('email', $emailFromNotes)->first();
                                                if ($user) {
                                                    $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $user->id)->where('status', 'active')->first();
                                                }
                                            }
                                            
                                            if ($loyaltyCard) {
                                                $loyaltyStamps = $loyaltyCard->stamps_count;
                                            }
                                        @endphp
                                        <div class="mb-1">{{ $customerName }}</div>
                                        @if($emailFromNotes)
                                            <div class="mb-1">{{ $emailFromNotes }}</div>
                                        @endif
                                        @if($order->delivery)
                                            <div class="mb-1">{{ $order->delivery->delivery_address }}</div>
                                        @endif
                                        
                                        <!-- Loyalty Card Info -->
                                        @if($canRedeemDiscount)
                                            <div class="mt-2 p-2" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <small class="fw-semibold text-success">🎉 Loyalty Reward!</small>
                                                    <small class="text-success fw-bold">{{ $loyaltyStamps }}/5 stamps</small>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-success fw-bold">
                                                        <i class="bi bi-gift-fill me-1"></i>50% OFF on {{ $discountedProduct->name }}!
                                                    </small>
                                                    <br>
                                                    <small class="text-success">
                                                        Discount: ₱{{ number_format($loyaltyDiscountAmount, 2) }}
                                                    </small>
                                                </div>
                                            </div>
                                        @elseif($canEarnStamp)
                                            <div class="mt-2 p-2" style="background: #f8f9fa; border-radius: 4px;">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <small class="fw-semibold text-primary">Loyalty Card</small>
                                                    @if($loyaltyCard)
                                                        <small class="text-muted">{{ $loyaltyStamps }}/5 stamps</small>
                                                    @else
                                                        <small class="text-muted">New customer</small>
                                                    @endif
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-success">
                                                        <i class="bi bi-star-fill me-1"></i>Earns 1 stamp (₱500+ order)
                                                    </small>
                                                </div>
                                            </div>
                                        @elseif($loyaltyCard)
                                            <div class="mt-2 p-2" style="background: #f8f9fa; border-radius: 4px;">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <small class="fw-semibold text-primary">Loyalty Card</small>
                                                    <small class="text-muted">{{ $loyaltyStamps }}/5 stamps</small>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="bi bi-star me-1"></i>No stamp (order < ₱500)
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <div class="mb-1"><span class="fw-semibold">Schedule Date:</span> <span class="text-muted">{{ optional($order->created_at)->format('m/d/Y') }}</span></div>
                                            <div><span class="fw-semibold">Order Number:</span> <span class="text-muted">{{ sprintf('%05d', $order->id) }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Operations Section -->
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Operations</div>
                            </div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0" id="operationsTable">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th style="width:30%">Product</th>
                                            <th style="width:20%">Demand</th>
                                            <th style="width:20%">Quantity</th>
                                            <th style="width:15%">UoM</th>
                                            <th style="width:15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $product)
                                            @php
                                                $demand = (int) ($product->pivot->quantity ?? 0);
                                                $stockAvailable = (int) ($product->stock ?? 0);
                                                
                                                // Check if we can fulfill based on composition analysis
                                                $canFulfillFromComposition = false;
                                                $compositionMessage = '';
                                                
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $composition = $productCompositions[$product->id];
                                                    $canFulfillFromComposition = $composition['can_fulfill'];
                                                    $compositionMessage = $canFulfillFromComposition ? 
                                                        'Can be made from materials' : 
                                                        'Insufficient materials';
                                                }
                                                
                                                // Use composition analysis if available, otherwise fall back to stock
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $quantityToProvide = $canFulfillFromComposition ? $demand : 0;
                                                    $isInsufficientStock = !$canFulfillFromComposition;
                                                    $stockMessage = $compositionMessage;
                                                } else {
                                                    $quantityToProvide = max(0, min($demand, $stockAvailable));
                                                    $isInsufficientStock = $stockAvailable < $demand;
                                                    $stockMessage = $isInsufficientStock ? 
                                                        "(Insufficient stock: {$stockAvailable} available)" : 
                                                        "({$stockAvailable} available)";
                                                }
                                            @endphp
                                            <tr class="{{ $isInsufficientStock ? 'table-warning' : '' }}">
                                                <td>
                                                    <div class="fw-semibold">{{ $product->name }}</div>
                                                    @if($isInsufficientStock)
                                                        <small class="text-warning">{{ $stockMessage }}</small>
                                                    @else
                                                        <small class="text-success">{{ $stockMessage }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm demand-input" value="{{ $demand }}" min="0" data-product-id="{{ $product->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm quantity-input" value="{{ $quantityToProvide }}" min="0" data-product-id="{{ $product->id }}">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm uom-select" data-product-id="{{ $product->id }}">
                                                        <option value="pcs">PCS</option>
                                                        <option value="dozen">Dozen</option>
                                                        <option value="box">Box</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-success" onclick="validateProduct({{ $product->id }})" title="Validate">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning" onclick="editProduct({{ $product->id }})" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" onclick="removeProduct({{ $product->id }})" title="Remove">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Product Composition Breakdown -->
                        @if($order->products->isNotEmpty())
                            <div class="mt-4">
                                <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#f8f9fa;border:1px solid #dee2e6;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Product Composition Breakdown</div>
                                <div class="table-responsive" style="border:1px solid #dee2e6;">
                                    @foreach($order->products as $product)
                                        @php
                                            $quantity = $product->pivot->quantity;
                                            $hasComposition = isset($productCompositions[$product->id]) && $productCompositions[$product->id];
                                            $composition = $hasComposition ? $productCompositions[$product->id] : null;
                                        @endphp
                                        <div class="p-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">{{ $product->name }} (Qty: {{ $quantity }})</h6>
                                                @if($hasComposition && $composition['total_components'] > 0)
                                                    <span class="badge bg-{{ $composition['can_fulfill'] ? 'success' : 'danger' }}">
                                                        {{ $composition['can_fulfill'] ? 'Can Fulfill' : 'Cannot Fulfill' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        No Composition Data
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($hasComposition && $composition['total_components'] > 0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Material</th>
                                                                            <th>Required</th>
                                                                            <th>Available</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($composition['components'] as $component)
                                                                            <tr class="{{ $component['sufficient'] ? '' : 'table-warning' }}">
                                                                                <td>
                                                                                    <strong>{{ $component['composition']->component_name }}</strong>
                                                                                    @if($component['component'])
                                                                                        <br><small class="text-muted">ID: {{ $component['component']->id }}</small>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    {{ $component['required_quantity'] }} {{ $component['composition']->unit }}
                                                                                    <br><small class="text-muted">{{ $component['composition']->quantity }} per unit</small>
                                                                                </td>
                                                                                <td>
                                                                                    {{ $component['available_stock'] }} {{ $component['composition']->unit }}
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge bg-{{ $component['status_class'] }}">
                                                                                        {{ $component['status'] }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            Summary: {{ $composition['sufficient_components'] }}/{{ $composition['total_components'] }} materials sufficient
                                                        </small>
                                                    </div>
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-info-circle"></i> <strong>No composition data available for this product.</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        To set up product compositions, go to 
                                                        <a href="{{ route('admin.products.index') }}" target="_blank" class="text-decoration-none">
                                                            Product Catalog
                                                        </a> and add composition data for this product. Without composition data, inventory will not be automatically deducted when this order is validated.
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="confirmModalLabel">Are you sure you want to proceed ?</div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-success" id="confirmValidateBtn">Confirm</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize event listeners
    initializeEventListeners();
    
    // Initialize table interactions
    initializeTableInteractions();
    
    // Handle Validate button click via AJAX
    const confirmValidateBtn = document.getElementById('confirmValidateBtn');
    if (confirmValidateBtn) {
        confirmValidateBtn.addEventListener('click', function() {
            // Close the modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();
            
            // Send AJAX request to validate
            fetch(`{{ route('admin.orders.walkin.validate.confirm', $order->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update Done badge to green
                const doneBadge = document.getElementById('doneBadge');
                if (doneBadge) {
                    doneBadge.classList.remove('bg-secondary');
                    doneBadge.classList.add('bg-success');
                }
                
                // Make Ready badge grey again (return to process)
                const readyBadge = document.getElementById('readyBadge');
                if (readyBadge) {
                    readyBadge.classList.remove('bg-success');
                    readyBadge.classList.add('bg-secondary');
                }
                
                // Hide Validate button since order is now validated
                const validateBtn = document.querySelector('.btn-success[data-bs-target="#confirmModal"]');
                if (validateBtn) {
                    validateBtn.style.display = 'none';
                }
                
                // Update Available stock numbers immediately without refresh
                try {
                    if (data && data.updated_components) {
                        data.updated_components.forEach(c => {
                            // Find all composition tables
                            const tables = document.querySelectorAll('.table.table-sm.mb-0');
                            tables.forEach(table => {
                                const rows = table.querySelectorAll('tbody tr');
                                rows.forEach(row => {
                                    const idCell = row.querySelector('small.text-muted');
                                    if (idCell && idCell.textContent.includes(`ID: ${c.product_id}`)) {
                                        // Available is the 3rd column (index 2)
                                        const availableCell = row.children[2];
                                        if (availableCell) {
                                            availableCell.textContent = `${c.stock_after} ${c.unit || ''}`.trim();
                                        }
                                    }
                                });
                            });
                        });
                    }
                } catch (e) { 
                    console.log('UI update error:', e);
                }

                // Show success message then force a refresh to reflect latest stock levels
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Order validated successfully!',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while validating the order',
                    confirmButtonColor: '#d33'
                });
            });
        });
    }
});

function initializeEventListeners() {
    // Moves button functionality
    // Moves button removed
    
    
    // Input change listeners
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('demand-input') || e.target.classList.contains('quantity-input')) {
            updateProductValidation(e.target);
        }
    });
}

function initializeTableInteractions() {
    // Add row highlighting on hover
    const tableRows = document.querySelectorAll('#operationsTable tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
}

function showMovesModal() {
    Swal.fire({
        title: 'Inventory Moves',
        html: `
            <div class="text-start">
                <h6>Movement Details</h6>
                <p><strong>Movement Number:</strong> {{ $inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001' }}</p>
                <p><strong>Type:</strong> Outbound</p>
                <p><strong>Status:</strong> Ready for validation</p>
                <p><strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'View Details',
        cancelButtonText: 'Close'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to detailed moves view
            window.location.href = '{{ route("admin.orders.walkin.validate", $order->id) }}?view=moves';
        }
    });
}

function showOperationsMenu() {
    Swal.fire({
        title: 'Operations Menu',
        html: `
            <div class="d-grid gap-2">
                <button class="btn btn-outline-primary" onclick="bulkValidate()">
                    <i class="bi bi-check-all me-2"></i>Validate All Products
                </button>
                <button class="btn btn-outline-secondary" onclick="exportData()">
                    <i class="bi bi-download me-2"></i>Export Data
                </button>
                <button class="btn btn-outline-info" onclick="refreshStock()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh Stock
                </button>
                <button class="btn btn-outline-warning" onclick="adjustQuantities()">
                    <i class="bi bi-sliders me-2"></i>Adjust Quantities
                </button>
            </div>
        `,
        showCancelButton: true,
        cancelButtonText: 'Close',
        showConfirmButton: false
    });
}

function validateProduct(productId) {
    const row = document.querySelector(`tr:has([data-product-id="${productId}"])`);
    const demandInput = row.querySelector('.demand-input');
    const quantityInput = row.querySelector('.quantity-input');
    
    const demand = parseInt(demandInput.value);
    const quantity = parseInt(quantityInput.value);
    
    if (quantity > demand) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Warning',
            text: 'Quantity cannot exceed demand. Please adjust the quantity.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show validation success
    Swal.fire({
        icon: 'success',
        title: 'Product Validated!',
        text: `Product validated successfully. Quantity: ${quantity}`,
        timer: 1500
    });
    
    // Update row styling
    row.classList.remove('table-warning');
    row.classList.add('table-success');
}

function editProduct(productId) {
    const row = document.querySelector(`tr:has([data-product-id="${productId}"])`);
    const productName = row.querySelector('.fw-semibold').textContent;
    
    Swal.fire({
        title: 'Edit Product',
        html: `
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" id="editProductName" value="${productName}">
            </div>
            <div class="mb-3">
                <label class="form-label">Demand</label>
                <input type="number" class="form-control" id="editDemand" min="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" id="editQuantity" min="0">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const name = document.getElementById('editProductName').value;
            const demand = document.getElementById('editDemand').value;
            const quantity = document.getElementById('editQuantity').value;
            
            if (!name || !demand || !quantity) {
                Swal.showValidationMessage('Please fill in all fields');
            }
            
            return { name, demand, quantity };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Update the row with new values
            row.querySelector('.fw-semibold').textContent = result.value.name;
            row.querySelector('.demand-input').value = result.value.demand;
            row.querySelector('.quantity-input').value = result.value.quantity;
            
            Swal.fire({
                icon: 'success',
                title: 'Product Updated!',
                text: 'Product information has been updated successfully.',
                timer: 1500
            });
        }
    });
}

function removeProduct(productId) {
    Swal.fire({
        title: 'Remove Product',
        text: 'Are you sure you want to remove this product from the order?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            const row = document.querySelector(`tr:has([data-product-id="${productId}"])`);
            row.remove();
            
            Swal.fire({
                icon: 'success',
                title: 'Product Removed!',
                text: 'Product has been removed from the order.',
                timer: 1500
            });
        }
    });
}

function updateProductValidation(input) {
    const productId = input.getAttribute('data-product-id');
    const row = input.closest('tr');
    const demandInput = row.querySelector('.demand-input');
    const quantityInput = row.querySelector('.quantity-input');
    
    const demand = parseInt(demandInput.value) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    
    // Update row styling based on validation
    if (quantity > demand) {
        row.classList.add('table-warning');
        row.classList.remove('table-success');
    } else if (quantity === demand && quantity > 0) {
        row.classList.add('table-success');
        row.classList.remove('table-warning');
    } else {
        row.classList.remove('table-success', 'table-warning');
    }
}

function bulkValidate() {
    const rows = document.querySelectorAll('#operationsTable tbody tr');
    let validatedCount = 0;
    
    rows.forEach(row => {
        const demandInput = row.querySelector('.demand-input');
        const quantityInput = row.querySelector('.quantity-input');
        
        const demand = parseInt(demandInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (quantity <= demand && quantity > 0) {
            row.classList.add('table-success');
            validatedCount++;
        }
    });
    
    Swal.fire({
        icon: 'success',
        title: 'Bulk Validation Complete!',
        text: `${validatedCount} products have been validated successfully.`,
        timer: 2000
    });
}

function exportData() {
    Swal.fire({
        icon: 'info',
        title: 'Export Data',
        text: 'Exporting order data...',
        timer: 1500
    });
    
    // Here you would implement actual export functionality
    console.log('Exporting order data...');
}

function refreshStock() {
    Swal.fire({
        icon: 'info',
        title: 'Refreshing Stock',
        text: 'Updating stock information...',
        timer: 1500
    });
    
    // Here you would implement actual stock refresh functionality
    console.log('Refreshing stock...');
}

function adjustQuantities() {
    Swal.fire({
        title: 'Adjust Quantities',
        html: `
            <div class="mb-3">
                <label class="form-label">Adjustment Type</label>
                <select class="form-select" id="adjustmentType">
                    <option value="increase">Increase All</option>
                    <option value="decrease">Decrease All</option>
                    <option value="set">Set to Demand</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" class="form-control" id="adjustmentAmount" min="0" value="1">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Apply',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const type = document.getElementById('adjustmentType').value;
            const amount = parseInt(document.getElementById('adjustmentAmount').value);
            
            const rows = document.querySelectorAll('#operationsTable tbody tr');
            rows.forEach(row => {
                const quantityInput = row.querySelector('.quantity-input');
                const demandInput = row.querySelector('.demand-input');
                
                let currentQuantity = parseInt(quantityInput.value) || 0;
                let newQuantity = currentQuantity;
                
                switch(type) {
                    case 'increase':
                        newQuantity = currentQuantity + amount;
                        break;
                    case 'decrease':
                        newQuantity = Math.max(0, currentQuantity - amount);
                        break;
                    case 'set':
                        newQuantity = parseInt(demandInput.value) || 0;
                        break;
                }
                
                quantityInput.value = newQuantity;
                updateProductValidation(quantityInput);
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Quantities Adjusted!',
                text: 'All quantities have been adjusted successfully.',
                timer: 1500
            });
        }
    });
}

function printOrder() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const orderData = {
        orderNumber: '{{ sprintf("%05d", $order->id) }}',
        movementNumber: '{{ $inventoryMovement ? $inventoryMovement->movement_number : "OUT / 0001" }}',
        customerName: '{{ $order->user->name ?? "Walk-in Customer" }}',
        deliveryAddress: '{{ $order->delivery->delivery_address ?? "" }}',
        orderDate: '{{ $order->created_at->format("m/d/Y") }}'
    };
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Order Validation - ${orderData.orderNumber}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .order-info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>J'J FLOWER SHOP</h1>
                <h2>Order Validation Report</h2>
            </div>
            <div class="order-info">
                <p><strong>Order Number:</strong> ${orderData.orderNumber}</p>
                <p><strong>Movement Number:</strong> ${orderData.movementNumber}</p>
                <p><strong>Customer:</strong> ${orderData.customerName}</p>
                <p><strong>Delivery Address:</strong> ${orderData.deliveryAddress}</p>
                <p><strong>Order Date:</strong> ${orderData.orderDate}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Demand</th>
                        <th>Quantity</th>
                        <th>UoM</th>
                    </tr>
                </thead>
                <tbody>
                    ${Array.from(document.querySelectorAll('#operationsTable tbody tr')).map(row => {
                        const cells = row.querySelectorAll('td');
                        return `
                            <tr>
                                <td>${cells[0].querySelector('.fw-semibold').textContent}</td>
                                <td>${cells[1].querySelector('input').value}</td>
                                <td>${cells[2].querySelector('input').value}</td>
                                <td>${cells[3].querySelector('select').value}</td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection

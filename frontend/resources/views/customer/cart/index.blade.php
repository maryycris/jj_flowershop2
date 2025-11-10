@extends('layouts.customer_app')

@include('components.customer.alt_nav', ['active' => 'home'])

@section('content')
<div class="container py-4 cart-container" style="background: #f4faf4; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-12">
            <div class="bg-white rounded-3 p-3 p-md-4 scrollable-content cart-items-section" style="box-shadow: none;">
                <div class="mb-3">
                    <button type="button" id="cartBackBtn" class="btn btn-outline-success btn-sm">&larr; Back</button>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="selectAllItems">
                        <label class="form-check-label" for="selectAllItems" style="font-weight: 500; font-size: 0.9rem;">SELECT ALL ITEMS</label>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" id="deleteAllItemsBtn"><i class="fas fa-trash"></i> DELETE</button>
                </div>
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table align-middle mb-0" style="background: #fff;">
                        <thead style="background: #f8faf8;">
                            <tr>
                                <th style="width: 36px;"></th>
                                <th>Product Name</th>
                                <th>Model</th>
                                <th style="width: 120px;">Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th style="width: 36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItemsContainer">
                        @forelse($cartItems as $item)
                            <tr class="cart-item" data-item-id="{{ $item->id }}">
                                <td><input type="checkbox" class="form-check-input item-checkbox"></td>
                                <td class="d-flex align-items-center gap-2">
                                    @if($item->item_type === 'custom_bouquet')
                                        <!-- Custom Bouquet Display -->
                                        <div style="width: 54px; height: 54px; background: linear-gradient(45deg, #ff6b6b, #4ecdc4); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                                            CUSTOM
                                        </div>
                                        <span style="font-weight: 500;">Custom Bouquet</span>
                                    @else
                                        <!-- Regular Product Display -->
                                        <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px;">
                                        <span style="font-weight: 500;">{{ $item->product->name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->item_type === 'custom_bouquet')
                                        Custom Bouquet
                                    @else
                                        {{ $item->product->category ?? '—' }}
                                    @endif
                                </td>
                                <td class="quantity-column">
                                    <div class="input-group quantity-control-small">
                                        <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="decrement">-</button>
                                        <input type="text" class="form-control form-control-sm text-center quantity-input" value="{{ $item->quantity }}" readonly>
                                        <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="increment">+</button>
                                    </div>
                                </td>
                                <td>
                                    @if($item->item_type === 'custom_bouquet')
                                        ₱<span class="item-unit-price">{{ number_format($item->customBouquet ? ($item->customBouquet->unit_price ?? $item->customBouquet->total_price) : 0, 2) }}</span>
                                    @else
                                        ₱<span class="item-unit-price">{{ number_format($item->product ? $item->product->price : 0, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->item_type === 'custom_bouquet')
                                        ₱<span class="item-total-price">{{ number_format($item->quantity * ($item->customBouquet ? ($item->customBouquet->unit_price ?? $item->customBouquet->total_price) : 0), 2) }}</span>
                                    @else
                                        ₱<span class="item-total-price">{{ number_format($item->quantity * ($item->product ? $item->product->price : 0), 2) }}</span>
                                    @endif
                                </td>
                                <td><button class="btn btn-outline-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">THE CART IS EMPTY</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Card View -->
                <div class="d-lg-none" id="mobileCartItemsContainer">
                    @forelse($cartItems as $item)
                        <div class="cart-item-mobile mb-3 p-3 bg-light rounded-3" data-item-id="{{ $item->id }}" style="border: 1px solid #e9ecef;">
                            <div class="d-flex align-items-center mb-2">
                                <input type="checkbox" class="form-check-input me-2 item-checkbox" style="flex-shrink: 0;">
                                @if($item->item_type === 'custom_bouquet')
                                    <div style="width: 50px; height: 50px; background: linear-gradient(45deg, #ff6b6b, #4ecdc4); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 11px; margin-right: 12px;">
                                        CUSTOM
                                    </div>
                                    <span style="font-weight: 500; flex: 1;">Custom Bouquet</span>
                                @else
                                    <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                                    <span style="font-weight: 500; flex: 1;">{{ $item->product->name }}</span>
                                @endif
                                <button class="btn btn-outline-danger btn-sm remove-item-btn" style="padding: 4px 8px;"><i class="fas fa-trash"></i></button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #666; font-size: 0.85rem;">Model:</span>
                                <span style="font-size: 0.9rem;">
                                    @if($item->item_type === 'custom_bouquet')
                                        Custom Bouquet
                                    @else
                                        {{ $item->product->category ?? '—' }}
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #666; font-size: 0.85rem;">Unit Price:</span>
                                <span style="font-size: 0.9rem;">
                                    @if($item->item_type === 'custom_bouquet')
                                        ₱<span class="item-unit-price">{{ number_format($item->customBouquet ? ($item->customBouquet->unit_price ?? $item->customBouquet->total_price) : 0, 2) }}</span>
                                    @else
                                        ₱<span class="item-unit-price">{{ number_format($item->product ? $item->product->price : 0, 2) }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #666; font-size: 0.85rem;">Quantity:</span>
                                <div class="input-group quantity-control-small" style="width: auto;">
                                    <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="decrement">-</button>
                                    <input type="text" class="form-control form-control-sm text-center quantity-input" value="{{ $item->quantity }}" readonly style="width: 45px;">
                                    <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="increment">+</button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid #dee2e6;">
                                <span style="font-weight: 600; color: #333;">Total:</span>
                                <span style="font-weight: 600; color: #7bb47b; font-size: 1rem;">
                                    @if($item->item_type === 'custom_bouquet')
                                        ₱<span class="item-total-price">{{ number_format($item->quantity * ($item->customBouquet ? ($item->customBouquet->unit_price ?? $item->customBouquet->total_price) : 0), 2) }}</span>
                                    @else
                                        ₱<span class="item-total-price">{{ number_format($item->quantity * ($item->product ? $item->product->price : 0), 2) }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">THE CART IS EMPTY</div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Location & Purchase Summary - Always visible -->
        <div class="col-lg-4 col-12 mt-3 mt-lg-0">
            <div class="bg-white rounded-3 p-3 p-md-4 location-summary-card" style="box-shadow: none;">
                <div class="mb-3">
                    <div style="font-weight: 500; color: #444; font-size: 0.95rem;">Location</div>
                    <div style="margin-top: 4px; color: #222; font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2"></i>Bang-bang Cordova Cebu</div>
                </div>
                <hr style="margin: 1rem 0;">
                <div class="mb-3" style="font-weight: 600; font-size: 1.05rem;">Purchase Summary</div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: #888; font-size: 0.9rem;">Subtotal (0 Items)</span>
                    <span style="color: #222; font-size: 0.9rem;">₱<span id="cartSubtotal">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: #888; font-size: 0.9rem;">Shipping Fee</span>
                    <span style="color: #222; font-size: 0.9rem;">—</span>
                </div>
                <hr style="margin: 1rem 0;">
                <div class="d-flex justify-content-between mb-3">
                    <span style="font-weight: 600; font-size: 1rem;">Subtotal</span>
                    <span style="color: #7bb47b; font-weight: 600; font-size: 1rem;">₱<span id="cartSubtotalFinal">0.00</span></span>
                </div>
                <button type="button" id="proceedToCheckoutBtn" class="btn btn-success w-100" style="border-radius: 25px; font-weight: 600; font-size: 1rem; padding: 10px;" disabled>Proceed to Checkout</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Back button: go to previous page if exists; fallback to dashboard
        const backBtn = document.getElementById('cartBackBtn');
        if (backBtn) {
            backBtn.addEventListener('click', function() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '{{ route('customer.dashboard') }}';
                }
            });
        }

        function updateCartTotal() {
            let newSubtotal = 0;
            let selectedItemsCount = 0;
            
            // Handle both desktop (.cart-item) and mobile (.cart-item-mobile) views
            document.querySelectorAll('.cart-item, .cart-item-mobile').forEach(itemElement => {
                const quantityInput = itemElement.querySelector('.quantity-input');
                const unitPriceElement = itemElement.querySelector('.item-unit-price');
                const totalPriceElement = itemElement.querySelector('.item-total-price');
                
                if (quantityInput && unitPriceElement && totalPriceElement) {
                    const quantity = parseInt(quantityInput.value);
                    const unitPrice = parseFloat(unitPriceElement.textContent.replace(/[^0-9.-]+/g,""));
                    const itemTotalPrice = quantity * unitPrice;
                    totalPriceElement.textContent = itemTotalPrice.toFixed(2);
                    
                    // Only add to subtotal if item is selected
                    const checkbox = itemElement.querySelector('.item-checkbox');
                    if (checkbox && checkbox.checked) {
                        newSubtotal += itemTotalPrice;
                        selectedItemsCount++;
                    }
                }
            });
            
            // Update subtotal display
            document.getElementById('cartSubtotal').textContent = newSubtotal.toFixed(2);
            document.getElementById('cartSubtotalFinal').textContent = newSubtotal.toFixed(2);
            
            // Update item count in subtotal text
            const subtotalText = document.querySelector('#cartSubtotal').closest('div').querySelector('span:first-child');
            if (subtotalText) {
                subtotalText.textContent = `Subtotal (${selectedItemsCount} Item${selectedItemsCount === 1 ? '' : 's'})`;
            }

            // Enable/disable checkout button depending on selection
            const proceedBtn = document.getElementById('proceedToCheckoutBtn');
            if (proceedBtn) {
                proceedBtn.disabled = selectedItemsCount === 0;
            }
        }

        // Quantity controls - works with both desktop and mobile
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const cartItemElement = this.closest('.cart-item') || this.closest('.cart-item-mobile');
                if (!cartItemElement) return;
                
                const cartItemId = cartItemElement.dataset.itemId;
                const quantityInput = cartItemElement.querySelector('.quantity-input');
                let currentQuantity = parseInt(quantityInput.value);
                const action = this.dataset.action;

                if (action === 'decrement' && currentQuantity > 1) {
                    currentQuantity--;
                } else if (action === 'increment') {
                    currentQuantity++;
                }

                fetch(`/customer/cart-items/${cartItemId}/update-quantity`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ quantity: currentQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        quantityInput.value = data.new_quantity;
                        updateCartTotal();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating quantity:', error);
                    alert('Failed to update quantity.');
                });
            });
        });

        // Remove individual item - works with both desktop and mobile
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', function () {
                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    const cartItemElement = this.closest('.cart-item') || this.closest('.cart-item-mobile');
                    if (!cartItemElement) return;
                    
                    const cartItemId = cartItemElement.dataset.itemId;
                    const isMobile = cartItemElement.classList.contains('cart-item-mobile');
                    const container = isMobile ? document.getElementById('mobileCartItemsContainer') : document.getElementById('cartItemsContainer');

                    fetch(`/customer/cart-items/${cartItemId}/remove`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cartItemElement.remove();
                            updateCartTotal();
                            
                            // Check if cart is empty
                            const remainingItems = document.querySelectorAll('.cart-item, .cart-item-mobile').length;
                            if (remainingItems === 0) {
                                if (isMobile) {
                                    container.innerHTML = '<div class="text-center py-4">THE CART IS EMPTY</div>';
                                } else {
                                    container.innerHTML = '<tr><td colspan="7" class="text-center py-4">THE CART IS EMPTY</td></tr>';
                                }
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error removing item:', error);
                        alert('Failed to remove item.');
                    });
                }
            });
        });

        // Individual item checkboxes
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCartTotal();
                
                // Update select all checkbox state
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                const selectAllCheckbox = document.getElementById('selectAllItems');
                
                if (selectAllCheckbox) {
                    if (checkedCheckboxes.length === allCheckboxes.length) {
                        selectAllCheckbox.checked = true;
                        selectAllCheckbox.indeterminate = false;
                    } else if (checkedCheckboxes.length === 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    } else {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = true;
                    }
                }
            });
        });

        // Select all items checkbox
        const selectAllItemsCheckbox = document.getElementById('selectAllItems');
        if (selectAllItemsCheckbox) {
            selectAllItemsCheckbox.addEventListener('change', function() {
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateCartTotal();
            });
        }

        // Proceed to checkout button - works with both desktop and mobile
        const proceedToCheckoutBtn = document.getElementById('proceedToCheckoutBtn');
        if (proceedToCheckoutBtn) {
            proceedToCheckoutBtn.addEventListener('click', function() {
                const selectedItems = document.querySelectorAll('.item-checkbox:checked');
                if (selectedItems.length === 0) {
                    alert('Please select at least one item to proceed to checkout.');
                    return;
                }
                
                // Get selected item IDs from both desktop and mobile views
                const selectedItemIds = Array.from(selectedItems).map(checkbox => {
                    const itemElement = checkbox.closest('.cart-item') || checkbox.closest('.cart-item-mobile');
                    return itemElement ? itemElement.dataset.itemId : null;
                }).filter(id => id !== null);
                
                // Redirect to checkout with selected items
                const checkoutUrl = new URL('{{ route("customer.checkout.index") }}', window.location.origin);
                selectedItemIds.forEach(id => {
                    checkoutUrl.searchParams.append('selected_items[]', id);
                });
                
                window.location.href = checkoutUrl.toString();
            });
        }

        // Delete all items button - works with both desktop and mobile
        const deleteAllItemsBtn = document.getElementById('deleteAllItemsBtn');
        if (deleteAllItemsBtn) {
            deleteAllItemsBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete all items from your cart?')) {
                    fetch('/customer/cart-items/delete-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear both desktop and mobile views
                            const desktopContainer = document.getElementById('cartItemsContainer');
                            const mobileContainer = document.getElementById('mobileCartItemsContainer');
                            
                            if (desktopContainer) {
                                desktopContainer.innerHTML = '<tr><td colspan="7" class="text-center py-4">THE CART IS EMPTY</td></tr>';
                            }
                            if (mobileContainer) {
                                mobileContainer.innerHTML = '<div class="text-center py-4">THE CART IS EMPTY</div>';
                            }
                            updateCartTotal();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting all items:', error);
                        alert('Failed to delete all items.');
                    });
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    body { background: #f4faf4; }
    .bg-white { box-shadow: none !important; }
    .table th, .table td { vertical-align: middle; }
    .table thead th {
        color: #6c757d;
        font-weight: 600;
        border-bottom: 2px solid #e0e0e0;
        font-size: 0.88rem;
        letter-spacing: .2px;
        text-transform: none;
    }
    .table { border-radius: 12px; overflow: hidden; }
    /* Product name hierarchy */
    #cartItemsContainer td:nth-child(2) span {
        font-size: 0.98rem;
        font-weight: 600;
        color: #222;
    }
    /* Prices hierarchy */
    #cartItemsContainer td:nth-child(5) { /* unit price */
        font-size: 0.95rem;
        color: #333;
        font-weight: 500;
    }
    #cartItemsContainer td:nth-child(6) { /* line total */
        font-size: 1rem;
        font-weight: 700;
        color: #2e7d32;
    }
    /* Location / summary hierarchy */
    .location-summary-card .summary-heading {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f3b1f;
    }
    .location-summary-card .summary-label { color: #888; font-size: 0.92rem; }
    .location-summary-card .summary-value { color: #222; font-size: 0.95rem; font-weight: 600; }
    .quantity-control-small {
        width: 100%;
        max-width: 110px;
        margin: 0 auto;
    }
    .quantity-control-small .btn {
        width: 28px;
        height: 28px;
        font-size: 0.9rem;
        padding: 0;
        border-radius: 4px;
        border-color: #7bb47b;
        color: #7bb47b;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .quantity-control-small .btn:hover {
        background: #7bb47b;
        color: #fff;
    }
    .quantity-control-small .form-control-sm {
        width: 35px;
        height: 28px;
        font-size: 0.85rem;
        background: #f4faf4;
        border: 1px solid #dee2e6;
        padding: 0.2rem;
        flex-shrink: 0;
    }
    .quantity-control-small .input-group {
        justify-content: center;
    }
    
    /* Responsive adjustments for quantity controls */
    @media (max-width: 1200px) {
        .quantity-column {
            width: auto !important;
            min-width: 110px;
        }
        .quantity-control-small {
            max-width: 100%;
        }
    }
    
    @media (max-width: 991.98px) {
        .quantity-column {
            width: auto !important;
            min-width: 120px;
        }
        .quantity-control-small {
            max-width: 100%;
        }
        .quantity-control-small .btn {
            width: 30px;
            height: 30px;
            font-size: 0.95rem;
            min-width: 30px;
        }
        .quantity-control-small .form-control-sm {
            width: 40px;
            height: 30px;
            font-size: 0.9rem;
            min-width: 40px;
        }
    }
    
    @media (max-width: 768px) {
        .quantity-column {
            width: auto !important;
            min-width: 130px;
        }
        .quantity-control-small .btn {
            width: 32px;
            height: 32px;
            font-size: 1rem;
            min-width: 32px;
        }
        .quantity-control-small .form-control-sm {
            width: 45px;
            height: 32px;
            font-size: 0.95rem;
            min-width: 45px;
        }
    }
    .btn-success {
        background: #7bb47b;
        border-color: #7bb47b;
    }
    .btn-success:hover {
        background: #5a9c5a;
        border-color: #5a9c5a;
    }
    .btn-outline-success {
        border-color: #7bb47b;
        color: #7bb47b;
    }
    .btn-outline-success:hover {
        background: #7bb47b;
        color: #fff;
    }
    .btn-outline-danger {
        border-color: #e57373;
        color: #e57373;
    }
    .btn-outline-danger:hover {
        background: #e57373;
        color: #fff;
    }
    
    /* Custom scrollbar styling for the cart content area */
    .scrollable-content::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .scrollable-content::-webkit-scrollbar-thumb {
        background: #8ACB88;
        border-radius: 4px;
    }

    .scrollable-content::-webkit-scrollbar-thumb:hover {
        background: #7bb47b;
    }

    /* For Firefox */
    .scrollable-content {
        scrollbar-width: thin;
        scrollbar-color: #8ACB88 #f1f1f1;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 991.98px) {
        .cart-container {
            padding: 0.75rem 0.5rem !important;
        }
        .cart-items-section {
            margin-bottom: 1rem;
            max-height: none !important;
            overflow-y: visible !important;
        }
        .scrollable-content {
            max-height: none !important;
            overflow-y: visible !important;
        }
        .location-summary-card {
            position: relative !important;
            margin-top: 1rem;
        }
    }

    @media (max-width: 650px) {
        .cart-container {
            padding: 0.5rem 0.25rem !important;
        }
        .cart-items-section {
            padding: 0.75rem !important;
        }
        .cart-item-mobile {
            font-size: 0.92rem;
        }
        .quantity-control-small {
            max-width: 100%;
            width: 100%;
        }
        .quantity-control-small .btn {
            width: 34px;
            height: 34px;
            font-size: 1.05rem;
            padding: 0;
            min-width: 34px;
        }
        .quantity-control-small .form-control-sm {
            width: 50px;
            height: 34px;
            font-size: 1rem;
            min-width: 50px;
        }
        .location-summary-card {
            padding: 0.75rem !important;
            font-size: 0.9rem;
        }
        .location-summary-card .summary-heading { font-size: 1rem; }
        .location-summary-card .summary-label { font-size: 0.88rem; }
        .location-summary-card .summary-value { font-size: 0.98rem; }
        #proceedToCheckoutBtn {
            font-size: 0.95rem !important;
            padding: 8px !important;
        }
        .btn-outline-danger.btn-sm {
            padding: 4px 6px;
            font-size: 0.85rem;
        }
    }
</style>
@endpush 
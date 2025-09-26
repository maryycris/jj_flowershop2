@extends('layouts.customer_app')

@section('content')
<div class="mb-3">
    <a href="{{ url('/customer/dashboard') }}" class="btn btn-outline-success">
        &larr; Back
    </a>
</div>
<div class="container py-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-white rounded-3 p-4" style="box-shadow: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input class="form-check-input me-2" type="checkbox" id="selectAllItems">
                        <label class="form-check-label" for="selectAllItems" style="font-weight: 500;">SELECT ALL ITEMS</label>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" id="deleteAllItemsBtn"><i class="fas fa-trash"></i> DELETE</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="background: #fff;">
                        <thead style="background: #f8faf8;">
                            <tr>
                                <th style="width: 36px;"></th>
                                <th>Product Name</th>
                                <th>Model</th>
                                <th>Quantity</th>
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
                                    <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px;">
                                    <span style="font-weight: 500;">{{ $item->product->name }}</span>
                                </td>
                                <td>{{ $item->product->category ?? '—' }}</td>
                                <td>
                                    <div class="input-group quantity-control-small">
                                        <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="decrement">-</button>
                                        <input type="text" class="form-control form-control-sm text-center quantity-input" value="{{ $item->quantity }}" readonly>
                                        <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="increment">+</button>
                                    </div>
                                </td>
                                <td>₱<span class="item-unit-price">{{ number_format($item->product->price, 2) }}</span></td>
                                <td>₱<span class="item-total-price">{{ number_format($item->quantity * $item->product->price, 2) }}</span></td>
                                <td><button class="btn btn-outline-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">THE CART IS EMPTY</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="bg-white rounded-3 p-4" style="box-shadow: none;">
                <div class="mb-3">
                    <div style="font-weight: 500; color: #444;">Location</div>
                    <div style="margin-top: 4px; color: #222;"><i class="fas fa-map-marker-alt me-2"></i>Bang-bang Cordova Cebu</div>
                </div>
                <hr>
                <div class="mb-3" style="font-weight: 600; font-size: 1.1rem;">Purchase Summary</div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: #888;">Subtotal (0 Items)</span>
                    <span style="color: #222;">₱<span id="cartSubtotal">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: #888;">Shipping Fee</span>
                    <span style="color: #222;">—</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span style="font-weight: 600;">Subtotal</span>
                    <span style="color: #7bb47b; font-weight: 600;">₱<span id="cartSubtotalFinal">0.00</span></span>
                </div>
                <button type="button" id="proceedToCheckoutBtn" class="btn btn-success w-100" style="border-radius: 25px; font-weight: 600; font-size: 1.08rem;" disabled>Proceed to Checkout</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateCartTotal() {
            let newSubtotal = 0;
            let selectedItemsCount = 0;
            
            document.querySelectorAll('.cart-item').forEach(itemElement => {
                const quantity = parseInt(itemElement.querySelector('.quantity-input').value);
                const unitPrice = parseFloat(itemElement.querySelector('.item-unit-price').textContent.replace(/[^0-9.-]+/g,""));
                const itemTotalPrice = quantity * unitPrice;
                itemElement.querySelector('.item-total-price').textContent = itemTotalPrice.toFixed(2);
                
                // Only add to subtotal if item is selected
                const checkbox = itemElement.querySelector('.item-checkbox');
                if (checkbox && checkbox.checked) {
                    newSubtotal += itemTotalPrice;
                    selectedItemsCount++;
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

        // Quantity controls
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const cartItemElement = this.closest('.cart-item');
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

        // Remove individual item
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', function () {
                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    const cartItemElement = this.closest('.cart-item');
                    const cartItemId = cartItemElement.dataset.itemId;

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
                            if (document.querySelectorAll('.cart-item').length === 0) {
                                document.getElementById('cartItemsContainer').innerHTML = '<tr><td colspan="7" class="text-center">THE CART IS EMPTY</td></tr>';
                                document.getElementById('cartSubtotal').closest('div').remove(); // Remove subtotal section
                                document.getElementById('cartSubtotalFinal').closest('div').remove(); // Remove final subtotal section
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

        // Proceed to checkout button
        const proceedToCheckoutBtn = document.getElementById('proceedToCheckoutBtn');
        if (proceedToCheckoutBtn) {
            proceedToCheckoutBtn.addEventListener('click', function() {
                const selectedItems = document.querySelectorAll('.item-checkbox:checked');
                if (selectedItems.length === 0) {
                    alert('Please select at least one item to proceed to checkout.');
                    return;
                }
                
                // Get selected item IDs
                const selectedItemIds = Array.from(selectedItems).map(checkbox => {
                    return checkbox.closest('.cart-item').dataset.itemId;
                });
                
                // Redirect to checkout with selected items
                const checkoutUrl = new URL('{{ route("customer.checkout.index") }}', window.location.origin);
                selectedItemIds.forEach(id => {
                    checkoutUrl.searchParams.append('selected_items[]', id);
                });
                
                window.location.href = checkoutUrl.toString();
            });
        }

        // Delete all items button
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
                            document.getElementById('cartItemsContainer').innerHTML = '<tr><td colspan="7" class="text-center">THE CART IS EMPTY</td></tr>';
                            if (document.getElementById('cartSubtotal')) {
                                document.getElementById('cartSubtotal').closest('div').remove();
                            }
                            if (document.getElementById('cartSubtotalFinal')) {
                                document.getElementById('cartSubtotalFinal').closest('div').remove();
                            }
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
    .table thead th { color: #888; font-weight: 500; border-bottom: 2px solid #e0e0e0; }
    .table { border-radius: 12px; overflow: hidden; }
    .quantity-control-small .btn {
        width: 30px;
        height: 30px;
        font-size: 1.1rem;
        padding: 0;
        border-radius: 50%;
        border-color: #7bb47b;
        color: #7bb47b;
    }
    .quantity-control-small .btn:hover {
        background: #7bb47b;
        color: #fff;
    }
    .quantity-control-small .form-control-sm {
        width: 40px;
        height: 30px;
        font-size: 1rem;
        background: #f4faf4;
        border: none;
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
</style>
@endpush 
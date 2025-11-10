@extends('layouts.admin_app')
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 text-gray-800 mb-0">New Walk-in Order</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-success">&larr; Back</a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_type" value="walk-in">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">Customer Information</h5>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Order Method</label>
                        <select class="form-select" id="order_method_top" style="width: 160px;" onchange="if(this.value==='delivery'){window.location='{{ route('admin.orders.walkin.delivery') }}';}">
                            <option value="picked_up" selected>Pick-up</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter customer's name" required style="text-transform: capitalize;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="order_date" class="form-label">Order Date</label>
                        <input type="date" class="form-control" id="order_date" name="order_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <!-- Shipping fee hidden for Pick-up orders -->
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Mode of Payment</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <!-- Delivery payment options -->
                        <option value="gcash" data-method="delivery">GCASH</option>
                        <option value="paymaya" data-method="delivery">PAYMAYA</option>
                        <option value="cod" data-method="delivery">Cash on Delivery (COD)</option>
                        <!-- Pick-up payment options (initially hidden) -->
                        <option value="gcash" data-method="picked_up" style="display: none;">GCASH</option>
                        <option value="paymaya" data-method="picked_up" style="display: none;">PAYMAYA</option>
                        <option value="cash" data-method="picked_up" style="display: none;">CASH</option>
                    </select>
                </div>
                <h5 class="mb-3">Address Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="invoice_address" class="form-label">Invoice Address</label>
                        <textarea class="form-control" id="invoice_address" name="invoice_address" rows="2" placeholder="Enter invoice address" required style="text-transform: capitalize;"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="delivery_address" class="form-label">Delivery Address</label>
                        <textarea class="form-control" id="delivery_address" name="delivery_address" rows="2" placeholder="Enter delivery address" required style="text-transform: capitalize;"></textarea>
                    </div>
                </div>
                <hr class="my-4">
                <h5 class="mb-3">Products</h5>
                <div id="order_lines_container">
                    <div class="row order-line mb-2">
                        <div class="col-md-5">
                            <select class="form-select product-select" name="products[0][product_id]" required>
                                <option value="" selected disabled>Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control quantity-input" name="products[0][quantity]" placeholder="Qty" min="1" value="1" required>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="text" class="form-control unit-price-input" placeholder="Unit Price" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-order-line w-100">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success" id="add_order_line">Add Product</button>
                <div class="text-end mt-4">
                    <h3>Total: ₱<span id="total_amount">0.00</span></h3>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let productIndex = 1;
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.order-line').forEach(function(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            total += (quantity * unitPrice);
        });
        // Add shipping fee if visible
        const shippingFeeGroup = document.getElementById('shipping_fee_group');
        if (shippingFeeGroup && !shippingFeeGroup.classList.contains('d-none')) {
            const shippingFee = parseFloat(document.getElementById('shipping_fee').value) || 0;
            total += shippingFee;
        }
        document.getElementById('total_amount').textContent = total.toFixed(2);
    }
    function updateUnitPrice(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const unitPriceInput = selectElement.closest('.order-line').querySelector('.unit-price-input');
        const price = selectedOption.getAttribute('data-price');
        unitPriceInput.value = price ? parseFloat(price).toFixed(2) : '0.00';
        calculateTotal();
    }
    function addEventListenersToRow(row) {
        row.querySelector('.product-select').addEventListener('change', function() { updateUnitPrice(this); });
        row.querySelector('.quantity-input').addEventListener('input', calculateTotal);
        row.querySelector('.remove-order-line').addEventListener('click', function() {
            this.closest('.order-line').remove();
            calculateTotal();
        });
    }
    document.querySelectorAll('.order-line').forEach(addEventListenersToRow);
    document.getElementById('add_order_line').addEventListener('click', function() {
        const container = document.getElementById('order_lines_container');
        const newRow = document.createElement('div');
        newRow.className = 'row order-line mb-2';
        const newProductRow = `
            <div class="col-md-5">
                <select class="form-select product-select" name="products[${productIndex}][product_id]" required>
                    <option value="" selected disabled>Select a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control quantity-input" name="products[${productIndex}][quantity]" placeholder="Qty" min="1" value="1" required>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="text" class="form-control unit-price-input" placeholder="Unit Price" readonly>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-order-line w-100">Remove</button>
            </div>
        `;
        newRow.innerHTML = newProductRow;
        container.appendChild(newRow);
        addEventListenersToRow(newRow);
        productIndex++;
    });
    // Shipping fee logic
    const orderMethod = document.getElementById('order_method');
    const shippingFeeGroup = document.getElementById('shipping_fee_group');
    const shippingFeeInput = document.getElementById('shipping_fee');
    const deliveryAddressInput = document.getElementById('delivery_address');
    const originAddress = 'Cordova, Cebu'; // Change to your shop's address if needed

    // Payment method logic
    const paymentMethodSelect = document.getElementById('payment_method');
    
    function updatePaymentMethods() {
        const selectedMethod = orderMethod.value;
        const currentValue = paymentMethodSelect.value;
        
        // Hide all options first
        Array.from(paymentMethodSelect.options).forEach(option => {
            option.style.display = 'none';
        });
        
        // Show only options for selected method
        Array.from(paymentMethodSelect.options).forEach(option => {
            if (option.getAttribute('data-method') === selectedMethod) {
                option.style.display = '';
            }
        });
        
        // Set default value based on method
        if (selectedMethod === 'delivery') {
            paymentMethodSelect.value = 'gcash';
        } else if (selectedMethod === 'picked_up') {
            paymentMethodSelect.value = 'cash';
        }
        
        // If current value is not available in new options, set to first available
        const availableOptions = Array.from(paymentMethodSelect.options).filter(option => 
            option.style.display !== 'none'
        );
        if (availableOptions.length > 0 && !availableOptions.some(option => option.value === currentValue)) {
            paymentMethodSelect.value = availableOptions[0].value;
        }
    }

    async function fetchShippingFee() {
        if (orderMethod.value === 'delivery') {
            const destination = deliveryAddressInput.value;
            if (destination.trim().length > 0) {
                shippingFeeInput.value = '...';
                try {
                    const response = await fetch('/api/calculate-shipping-fee', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ origin: originAddress, destination: destination })
                    });
                    const data = await response.json();
                    shippingFeeInput.value = data.fee ?? 0;
                } catch (e) {
                    shippingFeeInput.value = 0;
                }
            } else {
                shippingFeeInput.value = 0;
            }
        } else {
            shippingFeeInput.value = 0;
        }
        calculateTotal();
    }
    
    function toggleShippingFee() {
        if (orderMethod.value === 'delivery') {
            shippingFeeGroup.classList.remove('d-none');
            deliveryAddressInput.required = true;
        } else {
            shippingFeeGroup.classList.add('d-none');
            deliveryAddressInput.required = false;
        }
    }
    
    deliveryAddressInput.addEventListener('input', fetchShippingFee);
    orderMethod.addEventListener('change', function() {
        updatePaymentMethods();
        toggleShippingFee();
        fetchShippingFee();
    });
    
    // Initialize on page load
    updatePaymentMethods();
    toggleShippingFee();
    calculateTotal();
    
    // Prevent leading spaces and ensure proper case
    function preventLeadingSpaces(input) {
        input.addEventListener('input', function(e) {
            if (e.target.value.startsWith(' ')) {
                e.target.value = e.target.value.trim();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === ' ' && e.target.selectionStart === 0) {
                e.preventDefault();
            }
        });
    }
    
    // Apply to all text inputs and textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(preventLeadingSpaces);
});
</script>
@endpush 
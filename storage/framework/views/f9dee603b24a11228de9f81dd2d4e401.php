

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(url('/customer/dashboard')); ?>" class="btn btn-outline-success">
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
                        <?php $__empty_1 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="cart-item" data-item-id="<?php echo e($item->id); ?>">
                                <td><input type="checkbox" class="form-check-input item-checkbox"></td>
                                <td class="d-flex align-items-center gap-2">
                                    <img src="<?php echo e(asset('storage/' . $item->product->image)); ?>" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px;">
                                    <span style="font-weight: 500;"><?php echo e($item->product->name); ?></span>
                                </td>
                                <td><?php echo e($item->product->category ?? '—'); ?></td>
                                <td>
                                    <div class="input-group quantity-control-small">
                                        <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="decrement">-</button>
                                        <input type="text" class="form-control form-control-sm text-center quantity-input" value="<?php echo e($item->quantity); ?>" readonly>
                                        <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="increment">+</button>
                                    </div>
                                </td>
                                <td>₱<span class="item-unit-price"><?php echo e(number_format($item->product->price, 2)); ?></span></td>
                                <td>₱<span class="item-total-price"><?php echo e(number_format($item->quantity * $item->product->price, 2)); ?></span></td>
                                <td><button class="btn btn-outline-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="text-center">THE CART IS EMPTY</td></tr>
                        <?php endif; ?>
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
                    <span style="color: #888;">Subtotal (<?php echo e(count($cartItems)); ?> Item<?php echo e(count($cartItems) == 1 ? '' : 's'); ?>)</span>
                    <span style="color: #222;">₱<span id="cartSubtotal"><?php echo e(number_format($subtotal, 2)); ?></span></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: #888;">Shipping Fee</span>
                    <span style="color: #222;">—</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span style="font-weight: 600;">Subtotal</span>
                    <span style="color: #7bb47b; font-weight: 600;">₱<span id="cartSubtotalFinal"><?php echo e(number_format($subtotal, 2)); ?></span></span>
                </div>
                <a href="<?php echo e(route('customer.checkout.index')); ?>" class="btn btn-success w-100" style="border-radius: 25px; font-weight: 600; font-size: 1.08rem;">Proceed to Checkout</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateCartTotal() {
            let newSubtotal = 0;
            document.querySelectorAll('.cart-item').forEach(itemElement => {
                const quantity = parseInt(itemElement.querySelector('.quantity-input').value);
                const unitPrice = parseFloat(itemElement.querySelector('.item-unit-price').textContent.replace(/[^0-9.-]+/g,""));
                const itemTotalPrice = quantity * unitPrice;
                itemElement.querySelector('.item-total-price').textContent = itemTotalPrice.toFixed(2);
                newSubtotal += itemTotalPrice;
            });
            document.getElementById('cartSubtotal').textContent = newSubtotal.toFixed(2);
            document.getElementById('cartSubtotalFinal').textContent = newSubtotal.toFixed(2);
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

        // Select all items checkbox
        const selectAllItemsCheckbox = document.getElementById('selectAllItems');
        if (selectAllItemsCheckbox) {
            selectAllItemsCheckbox.addEventListener('change', function() {
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/cart/index.blade.php ENDPATH**/ ?>
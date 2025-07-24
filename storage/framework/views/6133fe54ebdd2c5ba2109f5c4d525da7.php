<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 18px;">
      <div class="modal-body p-4 d-flex" style="gap: 32px; align-items: flex-start;">
        <div style="flex: 1; text-align: center;">
          <img id="modalProductImage" src="" alt="Product" style="max-width: 260px; max-height: 260px; border-radius: 10px; object-fit: cover;">
          <div class="mt-3" id="modalProductDescription" style="font-size: 1.02rem; color: #444; text-align: left;">Description of the product...</div>
        </div>
        <div style="flex: 1; min-width: 260px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h3 id="modalProductName" style="font-weight: 600; margin-bottom: 0;">Product Name</h3>
            <span id="modalProductPrice" style="font-size: 1.3rem; font-weight: 600; color: #222;">₱0.00</span>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label mb-1" style="font-weight: 500;">Deliver to</label>
            <div class="input-group" style="border-radius: 25px; overflow: hidden;">
              <span class="input-group-text" style="background: #cbe7cb; border: none;"><i class="fas fa-map-marker-alt"></i></span>
              <input type="text" class="form-control" id="modalDeliveryLocation" placeholder="Enter delivery address" style="border: none; background: #eafbe6;">
              <button class="btn btn-link px-2" type="button" id="clearDeliveryLocation"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label mb-1" style="font-weight: 500;">Quantity</label>
            <div class="d-flex align-items-center" style="gap: 12px;">
              <button class="btn btn-outline-success rounded-circle px-0" type="button" id="modalQtyMinus" style="width: 36px; height: 36px; font-size: 1.3rem;">-</button>
              <input type="text" id="modalProductQty" value="1" readonly style="width: 48px; text-align: center; border: none; background: #f4faf4; font-size: 1.1rem;">
              <button class="btn btn-outline-success rounded-circle px-0" type="button" id="modalQtyPlus" style="width: 36px; height: 36px; font-size: 1.3rem;">+</button>
            </div>
          </div>
          <div class="mb-3 d-flex align-items-center justify-content-between">
            <span style="font-size: 1.1rem; font-weight: 500;">Total</span>
            <span id="modalProductTotal" style="font-size: 1.2rem; font-weight: 600; color: #222;">₱0.00</span>
          </div>
          <div class="d-flex gap-3 mt-4">
            <button class="btn btn-outline-success flex-fill" id="modalAddToCartBtn" type="button" style="border-radius: 25px; font-weight: 500;">Add to cart</button>
            <button class="btn btn-success flex-fill" id="modalBuyNowBtn" type="button" style="border-radius: 25px; font-weight: 500;">Buy now</button>
          </div>
        </div>
      </div>
      <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
  </div>
</div>
<style>
  #productModal .modal-content { box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
  #productModal .btn-outline-success { border-color: #7bb47b; color: #7bb47b; }
  #productModal .btn-outline-success:hover { background: #7bb47b; color: #fff; }
  #productModal .btn-success { background: #7bb47b; border-color: #7bb47b; }
  #productModal .btn-success:hover { background: #5a9c5a; border-color: #5a9c5a; }
</style>
<script>
  // This script expects you to set product data and show the modal via JS
  let modalProduct = null;
  function openProductModal(product) {
    modalProduct = product;
    document.getElementById('modalProductImage').src = product.image;
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalProductPrice').textContent = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalProductDescription').textContent = product.description || 'Description of the product...';
    document.getElementById('modalProductQty').value = 1;
    document.getElementById('modalProductTotal').textContent = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalDeliveryLocation').value = '';
    var modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
  }
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalQtyMinus').onclick = function() {
      let qty = parseInt(document.getElementById('modalProductQty').value);
      if (qty > 1) qty--;
      document.getElementById('modalProductQty').value = qty;
      updateModalTotal();
    };
    document.getElementById('modalQtyPlus').onclick = function() {
      let qty = parseInt(document.getElementById('modalProductQty').value);
      qty++;
      document.getElementById('modalProductQty').value = qty;
      updateModalTotal();
    };
    document.getElementById('clearDeliveryLocation').onclick = function() {
      document.getElementById('modalDeliveryLocation').value = '';
    };
    document.getElementById('modalAddToCartBtn').onclick = function() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      fetch("<?php echo e(route('customer.cart.add')); ?>", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
        },
        body: JSON.stringify({ product_id: modalProduct.id, quantity: qty })
      })
      .then(response => {
        if (response.redirected) {
          window.location.href = response.url;
        } else if (!response.ok) {
          return response.json().then(errorData => {
            alert('Failed to add product to cart: ' + (errorData.message || 'Unknown error'));
            throw new Error('Server error');
          });
        } else {
          alert('Product added to cart!');
        }
      })
      .catch(error => {
        alert('An error occurred while adding product to cart.');
      });
    };
    document.getElementById('modalBuyNowBtn').onclick = function() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      // Redirect to checkout with product and quantity as query params
      const url = `<?php echo e(url('/customer/checkout')); ?>?product_id=${modalProduct.id}&quantity=${qty}`;
      window.location.href = url;
    };
    function updateModalTotal() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      const total = qty * parseFloat(modalProduct.price);
      document.getElementById('modalProductTotal').textContent = '₱' + total.toFixed(2);
    }
  });
</script> <?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/products/modal.blade.php ENDPATH**/ ?>
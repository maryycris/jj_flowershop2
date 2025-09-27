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
            <button class="btn btn-outline-danger" id="modalAddToFavoritesBtn" type="button" title="Add to Favorites" style="border-radius: 25px; font-weight: 500; padding: 0 14px;"><i class="bi bi-heart"></i></button>
            <button class="btn btn-success flex-fill" id="modalBuyNowBtn" type="button" style="border-radius: 25px; font-weight: 500;">Buy now</button>
          </div>
          <?php if(request()->has('event_id')): ?>
          <div class="d-flex gap-3 mt-3">
            <button class="btn btn-warning flex-fill" id="modalAddToEventBtn" type="button" style="border-radius: 25px; font-weight: 500;">
              <i class="fas fa-calendar-plus me-2"></i>Add to Event
            </button>
          </div>
          <?php endif; ?>
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
    console.log('Product data:', product); // Debug: see what data is being passed
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
    // Add to Favorites (placeholder: navigates to favorites page)
    const favBtn = document.getElementById('modalAddToFavoritesBtn');
    if (favBtn) {
      favBtn.onclick = function() {
        if (!modalProduct) return;
        try {
          const item = {
            id: modalProduct.id,
            name: modalProduct.name,
            price: modalProduct.price,
            image: modalProduct.image
          };
          const key = 'jj_favorites';
          const existing = JSON.parse(sessionStorage.getItem(key) || '[]');
          // prevent duplicates by id
          const next = existing.filter(p => String(p.id) !== String(item.id));
          next.push(item);
          sessionStorage.setItem(key, JSON.stringify(next));
        } catch (e) { /* ignore storage issues */ }
        // Visual feedback: fill heart and disable briefly
        favBtn.innerHTML = '<i class="bi bi-heart-fill"></i>';
        favBtn.classList.add('btn-danger');
        favBtn.title = 'Added to Favorites';
      };
    }
    
    // Add to Event functionality
    const addToEventBtn = document.getElementById('modalAddToEventBtn');
    if (addToEventBtn) {
      addToEventBtn.onclick = function() {
        if (!modalProduct) return;
        const qty = parseInt(document.getElementById('modalProductQty').value);
        const eventId = new URLSearchParams(window.location.search).get('event_id');
        
        if (!eventId) {
          alert('No event selected. Please go back to your event order summary.');
          return;
        }
        
        fetch(`<?php echo e(url('/customer/events')); ?>/${eventId}/add-product`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
          },
          body: JSON.stringify({ 
            product_id: modalProduct.id, 
            quantity: qty 
          })
        })
        .then(response => {
          if (response.ok) {
            alert('Product added to event successfully!');
            // Close modal and redirect back to order summary
            const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
            modal.hide();
            window.location.href = `<?php echo e(url('/customer/events')); ?>/${eventId}/order-summary`;
          } else {
            return response.json().then(errorData => {
              alert('Failed to add product to event: ' + (errorData.message || 'Unknown error'));
            });
          }
        })
        .catch(error => {
          alert('An error occurred while adding product to event.');
        });
      };
    }
    function updateModalTotal() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      const total = qty * parseFloat(modalProduct.price);
      document.getElementById('modalProductTotal').textContent = '₱' + total.toFixed(2);
    }
  });
</script> <?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/products/modal.blade.php ENDPATH**/ ?>
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 18px; overflow: hidden;">
      <div class="modal-body p-4 d-flex flex-column" style="gap: 20px;">
        <div class="d-flex flex-column flex-lg-row" style="gap: 24px; align-items: flex-start;">
          <!-- Left: Image + description -->
          <div class="flex-grow-1" style="min-width: 280px; max-width: 320px;">
            <div class="d-flex flex-column align-items-center">
              <img id="modalProductImage" src="" alt="Product" style="width: 100%; max-width: 300px; aspect-ratio: 1/1; border-radius: 12px; object-fit: cover; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
              <div class="mt-3 w-100">
                <label class="form-label fw-semibold mb-2" style="color: #1f2d27; font-size: 0.95rem;">Description</label>
                <div id="modalProductDescription" class="form-control" style="min-height: 80px; border: 1px solid #d7ead6; background: #f7fff6; border-radius: 8px; font-size: 0.95rem; color: #475057; resize: none; padding: 12px; font-family: inherit;">
                  Description of the product...
                </div>
              </div>
        </div>
          </div>
          
          <!-- Right: Details + Actions -->
          <div class="flex-grow-1" style="min-width: 280px;">
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div>
                <h3 id="modalProductName" class="mb-1" style="font-weight: 700; color: #1f2d27; font-size: 1.4rem;">Product Name</h3>
                <div id="modalRatingSummary" class="d-flex align-items-center" style="gap:6px;">
                  <div class="rating-stars" aria-label="average rating" style="font-size: 0.9rem; color:#f3c04b;"></div>
                  <small id="modalRatingText" class="text-muted" style="font-size: 0.85rem;">No reviews yet</small>
                </div>
              </div>
              <span id="modalProductPrice" class="badge text-bg-light" style="font-size: 1.1rem; font-weight: 700; color:#1f2d27; background:#eafbe6; border:1px solid #cbe7cb;">₱0.00</span>
            </div>
            
            <hr class="my-2">
            
          <div class="mb-3">
              <label class="form-label mb-1 fw-semibold" style="font-size: 0.9rem;">Deliver to</label>
              <div class="input-group rounded-pill overflow-hidden" style="border:1px solid #d7ead6;">
              <span class="input-group-text" style="background: #cbe7cb; border: none;"><i class="fas fa-map-marker-alt"></i></span>
                <input type="text" class="form-control" id="modalDeliveryLocation" placeholder="Enter delivery address" style="border: none; background: #f7fff6; font-size: 0.9rem;">
              <button class="btn btn-link px-2" type="button" id="clearDeliveryLocation"><i class="fas fa-times"></i></button>
            </div>
          </div>
            
          <div class="mb-3">
              <label class="form-label mb-1 fw-semibold" style="font-size: 0.9rem;">Quantity</label>
              <div class="d-flex align-items-center" style="gap: 10px;">
                <button class="btn btn-outline-success rounded-circle px-0" type="button" id="modalQtyMinus" style="width: 32px; height: 32px; font-size: 1.1rem;">-</button>
                <input type="text" id="modalProductQty" value="1" readonly style="width: 50px; text-align: center; border: none; background: #f4faf4; font-size: 1rem; border-radius:6px; padding:4px 0;">
                <button class="btn btn-outline-success rounded-circle px-0" type="button" id="modalQtyPlus" style="width: 32px; height: 32px; font-size: 1.1rem;">+</button>
              </div>
            </div>
            
            <div class="mb-3 d-flex align-items-center justify-content-between">
              <span class="fw-semibold" style="font-size: 1rem;">Total</span>
              <span id="modalProductTotal" style="font-size: 1.1rem; font-weight: 700; color: #1f2d27;">₱0.00</span>
            </div>
            
            <div class="d-flex gap-2 mt-3 flex-wrap">
              <button class="btn btn-outline-success flex-fill" id="modalAddToCartBtn" type="button" style="border-radius: 8px; font-weight: 600; font-size: 0.9rem; padding: 8px 12px;">Add to cart</button>
              <button class="btn btn-outline-danger" id="modalAddToFavoritesBtn" type="button" title="Add to Favorites" style="border-radius: 8px; font-weight: 600; padding: 8px 12px;"><i class="bi bi-heart"></i></button>
              <button class="btn btn-success flex-fill" id="modalBuyNowBtn" type="button" style="border-radius: 8px; font-weight: 600; font-size: 0.9rem; padding: 8px 12px;">Buy now</button>
            </div>
            
          </div>
          </div>
        
        <!-- Reviews Section -->
        <div id="modalReviewsSection" class="mt-2" style="background:#ffffff; border:1px solid #eef3ef; border-radius:10px; padding:12px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 fw-bold" style="color:#1f2d27; font-size: 0.95rem;">Customer Reviews</h6>
            <small id="modalReviewsCount" class="text-muted" style="font-size: 0.8rem;"></small>
          </div>
          <div id="modalReviewsList" style="max-height: 160px; overflow:auto;">
            <div class="text-muted small">Loading reviews…</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
  #productModal .modal-content { box-shadow: 0 16px 44px rgba(0,0,0,0.12); }
  #productModal .btn-outline-success { border-color: #7bb47b; color: #7bb47b; }
  #productModal .btn-outline-success:hover { background: #7bb47b; color: #fff; }
  #productModal .btn-success { background: #7bb47b; border-color: #7bb47b; }
  #productModal .btn-success:hover { background: #5a9c5a; border-color: #5a9c5a; }
  #productModal .review-item { border-bottom: 1px dashed #e7eee8; padding: 10px 0; }
  #productModal .review-item:last-child { border-bottom: 0; }
  #productModal .star { color:#d3d7d4; }
  #productModal .star.filled { color:#f3c04b; }
</style>
<script>
  let modalProduct = null;
  function renderStars(rating) {
    const full = Math.floor(rating || 0);
    const hasHalf = (rating - full) >= 0.5;
    let html = '';
    for (let i = 1; i <= 5; i++) {
      if (i <= full) { html += '<i class="bi bi-star-fill star filled"></i>'; }
      else if (i === full + 1 && hasHalf) { html += '<i class="bi bi-star-half star filled"></i>'; }
      else { html += '<i class="bi bi-star star"></i>'; }
    }
    return html;
  }
  function setRatingSummary(avg, count) {
    const stars = document.querySelector('#modalRatingSummary .rating-stars');
    const text = document.getElementById('modalRatingText');
    const countEl = document.getElementById('modalReviewsCount');
    if (stars) stars.innerHTML = renderStars(avg);
    if (text) text.textContent = (count && count > 0) ? `${(avg||0).toFixed(1)} • ${count} review${count>1?'s':''}` : 'No reviews yet';
    if (countEl) countEl.textContent = (count && count>0) ? `${count} review${count>1?'s':''}` : '';
  }
  function loadProductReviews(productId) {
    const list = document.getElementById('modalReviewsList');
    if (list) list.innerHTML = '<div class="text-muted small">Loading reviews…</div>';
    fetch(`{{ url('/customer/products') }}/${productId}/reviews`)
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(data => {
        const reviews = Array.isArray(data.reviews) ? data.reviews : [];
        const avg = Number(data.average_rating || 0);
        setRatingSummary(avg, reviews.length);
        if (!reviews.length) {
          list.innerHTML = '<div class="text-muted small">No reviews yet.</div>';
          return;
        }
        list.innerHTML = reviews.map(rv => {
          const name = rv.user_name || 'Anonymous';
          const when = rv.created_at ? new Date(rv.created_at).toLocaleDateString() : '';
          const stars = renderStars(Number(rv.rating || 0));
          const text = (rv.comment || '').replace(/</g,'&lt;');
          return `<div class="review-item">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center" style="gap:8px;">
                        <strong style="color:#1f2d27; font-size:0.95rem;">${name}</strong>
                        <span class="rating-stars" style="font-size:0.9rem;">${stars}</span>
                      </div>
                      <small class="text-muted">${when}</small>
                    </div>
                    <div class="mt-1" style="color:#475057; font-size:0.95rem;">${text}</div>
                  </div>`;
        }).join('');
      })
      .catch(() => {
        setRatingSummary(0,0);
        if (list) list.innerHTML = '<div class="text-muted small">No reviews yet.</div>';
      });
  }
  function openProductModal(product) {
    modalProduct = product;
    document.getElementById('modalProductImage').src = product.image;
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalProductPrice').textContent = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalProductDescription').textContent = product.description || 'Description of the product...';
    document.getElementById('modalProductQty').value = 1;
    document.getElementById('modalProductTotal').textContent = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalDeliveryLocation').value = '';
    setRatingSummary(0,0);
    if (product.id) { loadProductReviews(product.id); }
    setFavStateOnOpen();
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
      fetch("{{ route('customer.cart.add') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content') },
        body: JSON.stringify({ catalog_product_id: modalProduct.id, quantity: qty })
      })
      .then(response => {
        if (response.redirected) { window.location.href = response.url; }
        else if (!response.ok) { return response.json().then(errorData => { alert('Failed to add product to cart: ' + (errorData.message || 'Unknown error')); throw new Error('Server error'); }); }
        else { alert('Product added to cart!'); }
      })
      .catch(() => { alert('An error occurred while adding product to cart.'); });
    };
    document.getElementById('modalBuyNowBtn').onclick = function() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      const url = `{{ url('/customer/checkout') }}?catalog_product_id=${modalProduct.id}&quantity=${qty}`;
      window.location.href = url;
    };
    const favBtn = document.getElementById('modalAddToFavoritesBtn');
    // Build API routes from Laravel route() to avoid path mistakes (GLOBAL)
    window.favRoutes = {
      index: "{{ route('customer.favorites.index') }}",
      store: "{{ route('customer.favorites.store') }}",
      checkTpl: "{{ route('customer.favorites.check', ['product' => '__ID__']) }}",
      destroyTpl: "{{ route('customer.favorites.destroy', ['product' => '__ID__']) }}"
    };
    function getFavs(){
      try { return JSON.parse(sessionStorage.getItem('jj_favorites') || '[]'); } catch(e) { return []; }
    }
    function setFavs(arr){ sessionStorage.setItem('jj_favorites', JSON.stringify(arr)); }
    function isFav(id){ return getFavs().some(p => String(p.id) === String(id)); }
    window.updateFavButtonUI = function(favored){
      if (!favBtn) return;
      if (favored){
        favBtn.innerHTML = '<i class="bi bi-heart-fill"></i>';
        favBtn.classList.remove('btn-outline-danger');
        favBtn.classList.add('btn-danger');
        favBtn.title = 'Remove from Favorites';
      } else {
        favBtn.innerHTML = '<i class="bi bi-heart"></i>';
        favBtn.classList.remove('btn-danger');
        favBtn.classList.add('btn-outline-danger');
        favBtn.title = 'Add to Favorites';
      }
    }
    if (favBtn) {
      // Toggle on click (optimistic + fast)
      favBtn.onclick = function() {
        if (!modalProduct) return;
        const csrf = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
        const icon = favBtn.querySelector('i');
        const wasFilled = icon && icon.classList.contains('bi-heart-fill');
        const nextFilled = !wasFilled;
        // instant UI feedback
        updateFavButtonUI(nextFilled);
        // prevent double-click spam
        favBtn.disabled = true;

        const req = wasFilled
          ? fetch(window.favRoutes.destroyTpl.replace('__ID__', encodeURIComponent(modalProduct.id)), {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
              credentials: 'same-origin'
            })
          : fetch(window.favRoutes.store, {
          method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
              body: JSON.stringify({ product_id: modalProduct.id }),
              credentials: 'same-origin'
            });

        req.then(res => {
            if (!res.ok) throw new Error('request_failed');
          })
          .catch(() => {
            // revert UI on failure and resync from server
            updateFavButtonUI(wasFilled);
            setFavStateOnOpen();
          })
          .finally(() => { favBtn.disabled = false; });
      };
      favBtn.setAttribute('role', 'button');
      favBtn.style.cursor = 'pointer';
    }
    function updateModalTotal() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      const total = qty * parseFloat(modalProduct.price);
      document.getElementById('modalProductTotal').textContent = '₱' + total.toFixed(2);
    }
  });
  // When opening, also set heart state based on current favorites
  function setFavStateOnOpen(){
    const favBtn = document.getElementById('modalAddToFavoritesBtn');
    if (!modalProduct || !favBtn) return;
    const checkUrl = (window.favRoutes ? window.favRoutes.checkTpl : '').replace('__ID__', encodeURIComponent(modalProduct.id));
    fetch(checkUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(state => window.updateFavButtonUI && window.updateFavButtonUI(!!state.favored))
      .catch(() => window.updateFavButtonUI && window.updateFavButtonUI(false));
  }
</script> 

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
              </div>
              <span id="modalProductPrice" class="badge text-bg-light" style="font-size: 1.1rem; font-weight: 700; color:#1f2d27; background:#eafbe6; border:1px solid #cbe7cb;">₱0.00</span>
            </div>
            
            <hr class="my-2">
            
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
        <div id="modalReviewsSection" class="mt-3" style="background:#ffffff; border:1px solid #eef3ef; border-radius:12px; padding:16px;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center" style="gap: 8px;">
              <h6 class="mb-0 fw-bold" style="color:#1f2d27; font-size: 1.1rem;">Customer Reviews</h6>
              <div id="modalRatingSummary" class="d-flex align-items-center" style="gap:4px;">
                <div class="rating-stars" aria-label="average rating" style="font-size: 0.9rem; color:#f3c04b;"></div>
                <small id="modalRatingText" class="text-muted" style="font-size: 0.85rem;">No reviews yet</small>
              </div>
            </div>
            <small id="modalReviewsCount" class="text-muted fw-semibold" style="font-size: 0.9rem; background:#f8f9fa; padding:4px 8px; border-radius:12px;"></small>
          </div>
          <div id="modalReviewsList" style="max-height: 200px; overflow-y:auto; padding-right: 8px;">
            <div class="text-center text-muted py-3">
              <i class="bi bi-chat-square-text" style="font-size: 2rem; opacity: 0.3;"></i>
              <div class="mt-2">No reviews yet</div>
            </div>
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
          list.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-chat-square-text" style="font-size: 2rem; opacity: 0.3;"></i><div class="mt-2">No reviews yet</div></div>';
          return;
        }
        list.innerHTML = reviews.map(rv => {
          const name = rv.user_name || 'Anonymous';
          const when = rv.created_at ? new Date(rv.created_at).toLocaleDateString() : '';
          const stars = renderStars(Number(rv.rating || 0));
          const text = (rv.comment || '').replace(/</g,'&lt;');
          return `<div class="review-item" style="border-bottom: 1px solid #f0f0f0; padding: 12px 0;">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                      <div class="d-flex align-items-center" style="gap:10px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px; height:32px; background:#e9ecef; color:#6c757d; font-weight:600; font-size:0.8rem;">
                          ${name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                          <div class="fw-semibold" style="color:#1f2d27; font-size:0.95rem;">${name}</div>
                          <div class="rating-stars" style="font-size:0.85rem;">${stars}</div>
                        </div>
                      </div>
                      <small class="text-muted" style="font-size:0.8rem;">${when}</small>
                    </div>
                    <div class="ms-11" style="color:#495057; font-size:0.9rem; line-height:1.4;">${text}</div>
                  </div>`;
        }).join('');
      })
      .catch(() => {
        setRatingSummary(0,0);
        if (list) list.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-chat-square-text" style="font-size: 2rem; opacity: 0.3;"></i><div class="mt-2">No reviews yet</div></div>';
      });
  }
    function openProductModal(product) {
      console.log('Opening product modal with:', product);
      modalProduct = product;
      document.getElementById('modalProductImage').src = product.image;
      document.getElementById('modalProductName').textContent = product.name;
      document.getElementById('modalProductPrice').textContent = '₱' + parseFloat(product.price).toFixed(2);
      document.getElementById('modalProductDescription').textContent = product.description || 'Description of the product...';
      document.getElementById('modalProductQty').value = 1;
      document.getElementById('modalProductTotal').textContent = '₱' + parseFloat(product.price).toFixed(2);
      setRatingSummary(0,0);
      if (product.id) { loadProductReviews(product.id); }
      setFavStateOnOpen();
      var modal = new bootstrap.Modal(document.getElementById('productModal'));
      modal.show();
      console.log('Modal opened for product ID:', product.id);
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
    document.getElementById('modalAddToCartBtn').onclick = function() {
      if (!modalProduct) {
        console.error('No modalProduct found');
        alert('No product selected');
        return;
      }
      
      console.log('Adding to cart:', modalProduct);
      const qty = parseInt(document.getElementById('modalProductQty').value);
      console.log('Quantity:', qty);
      
      const requestData = { catalog_product_id: modalProduct.id, quantity: qty };
      console.log('Request data:', requestData);
      
      fetch("{{ route('customer.cart.add') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content') },
        body: JSON.stringify(requestData)
      })
      .then(response => {
        console.log('Response status:', response.status);
        if (response.redirected) { 
          console.log('Redirected to:', response.url);
          window.location.href = response.url; 
        }
        else if (!response.ok) { 
          return response.json().then(errorData => { 
            console.error('Server error:', errorData);
            
            // Handle inventory validation errors with SweetAlert
            if (errorData.type === 'insufficient_inventory' || errorData.type === 'missing_components') {
              showInventoryAlert(errorData);
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorData.message || 'Failed to add product to cart'
              });
            }
            throw new Error('Server error'); 
          }); 
        }
        else { 
          console.log('Successfully added to cart');
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Product added to cart!',
            timer: 2000,
            showConfirmButton: false
          });
        }
      })
      .catch(error => { 
        console.error('Fetch error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while adding product to cart.'
        });
      });
    };
    document.getElementById('modalBuyNowBtn').onclick = function() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      const url = `{{ url('/customer/checkout') }}?catalog_product_id=${modalProduct.id}&quantity=${qty}`;
      window.location.href = url;
    };
    // Function to show inventory alert with alternatives
    function showInventoryAlert(errorData) {
      let issuesText = '';
      if (errorData.issues && errorData.issues.length > 0) {
        issuesText = '\n\nIssues:\n';
        errorData.issues.forEach(issue => {
          issuesText += `• ${issue.message}\n`;
        });
      }

      let alternativesHtml = '';
      if (errorData.alternatives && errorData.alternatives.length > 0) {
        alternativesHtml = '<div class="mt-3"><h6>Suggested Alternatives:</h6><div class="row">';
        errorData.alternatives.forEach(alt => {
          alternativesHtml += `
            <div class="col-md-4 mb-2">
              <div class="card border-primary">
                <img src="${alt.image || '/images/placeholder.jpg'}" class="card-img-top" style="height: 100px; object-fit: cover;">
                <div class="card-body p-2">
                  <h6 class="card-title">${alt.name}</h6>
                  <p class="card-text">₱${alt.price}</p>
                  <button class="btn btn-sm btn-primary" onclick="selectAlternative(${alt.id}, '${alt.name}')">Select</button>
                </div>
              </div>
            </div>
          `;
        });
        alternativesHtml += '</div></div>';
      }

      // Determine icon and title based on error type
      let icon = 'warning';
      let title = 'Low Stock Alert!';
      let showContinueButton = true;
      
      if (errorData.type === 'missing_components') {
        icon = 'error';
        title = 'Product Unavailable!';
        showContinueButton = false;
      }

      const swalConfig = {
        icon: icon,
        title: title,
        html: `<div class="text-start">${errorData.message}${issuesText}</div>${alternativesHtml}`,
        confirmButtonText: showContinueButton ? 'Continue Anyway' : 'OK',
        confirmButtonColor: showContinueButton ? '#ffc107' : '#dc3545',
        width: alternativesHtml ? '600px' : '400px'
      };

      if (showContinueButton) {
        swalConfig.showCancelButton = true;
        swalConfig.cancelButtonText = 'Cancel';
        swalConfig.cancelButtonColor = '#6c757d';
      }

      Swal.fire(swalConfig).then((result) => {
        if (result.isConfirmed && showContinueButton) {
          // User chose to continue anyway - add to cart without inventory check
          addToCartAnyway();
        }
      });
    }

    // Function to add to cart without inventory validation
    function addToCartAnyway() {
      if (!modalProduct) return;
      const qty = parseInt(document.getElementById('modalProductQty').value);
      const formData = new FormData();
      formData.append('quantity', qty);
      formData.append('catalog_product_id', modalProduct.id);
      formData.append('_token', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
      formData.append('force_add', 'true'); // Flag to bypass inventory check

      fetch('{{ route("customer.cart.add") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (response.ok) {
          Swal.fire({
            icon: 'success',
            title: 'Added to Cart!',
            text: 'Product added to cart (backorder)',
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          throw new Error('Failed to add to cart');
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to add product to cart'
        });
      });
    }

    // Function to select alternative product
    function selectAlternative(altId, altName) {
      Swal.close();
      // Open the modal for the alternative product
      // This would need to be implemented based on your product structure
      Swal.fire({
        icon: 'info',
        title: 'Alternative Selected',
        text: `You selected: ${altName}. Please add this product to your cart.`,
        confirmButtonText: 'OK'
      });
    }

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

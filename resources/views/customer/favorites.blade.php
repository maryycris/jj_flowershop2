@extends('layouts.customer_app')

@section('content')
<div class="py-4" style="background: #f4faf4; min-height: calc(100vh - 200px);">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="fw-bold m-0" style="color:#385E42;">My Favorites</h3>
            <div class="d-flex align-items-center gap-2">
                <button id="removeSelectedBtn" class="btn btn-outline-danger btn-sm" disabled>
                    <i class="bi bi-trash me-1"></i>Remove selected
                </button>
                <button id="clearAllBtn" class="btn btn-outline-secondary btn-sm" title="Clear all favorites">
                    <i class="bi bi-x-circle me-1"></i>Clear all
                </button>
            </div>
        </div>
        <div id="favContainer" class="row g-3 mt-3"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('favContainer');
  const removeSelectedBtn = document.getElementById('removeSelectedBtn');
  const clearAllBtn = document.getElementById('clearAllBtn');
  const routes = {
    index: "{{ route('customer.favorites.index') }}",
    destroyTpl: "{{ route('customer.favorites.destroy', ['product' => '__ID__']) }}"
  };
  function fetchItems() {
    return fetch(routes.index)
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(data => Array.isArray(data.favorites) ? data.favorites : []);
  }
  function render() {
    container.innerHTML = '';
    fetchItems().then(items => {
      if (!items.length) {
        container.innerHTML = '<p class="text-muted">No favorites yet. Add one from the product modal.</p>';
        removeSelectedBtn.disabled = true;
        return;
      }
      items.forEach(p => {
      const col = document.createElement('div');
      col.className = 'col-6 col-md-4 col-lg-3';
      col.innerHTML = `
        <div class="card h-100 position-relative" data-id="${p.id}" data-name="${p.name.replace(/"/g,'&quot;')}" data-price="${p.price}" data-image="${p.image || ''}" style="border:1px solid #e0e0e0; border-radius:10px;">
          <input type="checkbox" class="form-check-input position-absolute" style="top:10px; right:10px; transform:scale(1.2);" />
          <div class="fav-open" style="cursor:pointer;">
            <img src="${p.image}" class="card-img-top" style="height:150px; object-fit:cover; border-radius:10px 10px 0 0;" alt="${p.name}">
            <div class="card-body text-center p-2">
              <div class="fw-semibold">${p.name}</div>
              <div class="text-success">₱${parseFloat(p.price).toFixed(2)}</div>
            </div>
          </div>
            <button class="btn btn-sm btn-outline-danger mt-2 remove-single"><i class="bi bi-trash"></i> Remove</button>
        </div>
        </div>`;
      container.appendChild(col);
      });

      // Wire up events for checkboxes and single removes
      container.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', updateRemoveButton);
      });
      container.querySelectorAll('.remove-single').forEach(btn => {
        btn.addEventListener('click', function() {
          const card = this.closest('.card');
          const id = card.getAttribute('data-id');
          const url = routes.destroyTpl.replace('__ID__', encodeURIComponent(id));
          fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
          }).then(() => render());
        });
      });
      // Open modal from favorites
      container.querySelectorAll('.fav-open').forEach(box => {
        box.addEventListener('click', function(e){
          // ignore if checkbox clicked accidentally
          if (e.target.closest('input[type="checkbox"]')) return;
          const card = this.closest('.card');
          const product = {
            id: card.getAttribute('data-id'),
            name: card.getAttribute('data-name'),
            price: card.getAttribute('data-price'),
            image: card.getAttribute('data-image') || '',
            description: ''
          };
          if (typeof openProductModal === 'function') {
            openProductModal(product);
          }
        });
      });
      updateRemoveButton();
    });
  }
  function updateRemoveButton() {
    const anyChecked = !!container.querySelector('input[type="checkbox"]:checked');
    removeSelectedBtn.disabled = !anyChecked;
  }

  removeSelectedBtn.addEventListener('click', function() {
    const checkedIds = Array.from(container.querySelectorAll('input[type="checkbox"]:checked'))
      .map(cb => cb.closest('.card').getAttribute('data-id'));
    if (!checkedIds.length) return;
    Promise.all(checkedIds.map(id => fetch(routes.destroyTpl.replace('__ID__', encodeURIComponent(id)), {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
    }))).then(() => render());
  });

  clearAllBtn.addEventListener('click', function() {
    // Fetch all then remove via API
    fetchItems().then(items => {
      return Promise.all(items.map(p => fetch(routes.destroyTpl.replace('__ID__', encodeURIComponent(p.id)), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
      })));
    }).then(() => render());
  });


  render();
});
</script>
<style>
/* Hover effect for favorites cards */
.fav-open { transition: transform .15s ease, box-shadow .15s ease; border-radius: 10px 10px 0 0; }
.fav-open:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.08); }
.card:hover { border-color: #cfe9cf; }
/* Ensure checkboxes stay clickable above hover layer */
.card .form-check-input { position: absolute; z-index: 5; }
.fav-open { position: relative; z-index: 1; }
</style>
@endpush
@endsection

@include('customer.products.modal')



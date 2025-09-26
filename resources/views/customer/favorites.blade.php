@extends('layouts.customer_app')

@section('content')
<div class="container py-4">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('favContainer');
  const removeSelectedBtn = document.getElementById('removeSelectedBtn');
  const clearAllBtn = document.getElementById('clearAllBtn');
  const key = 'jj_favorites';

  function getItems() {
    try { return JSON.parse(sessionStorage.getItem(key) || '[]'); } catch(e) { return []; }
  }
  function setItems(items) {
    sessionStorage.setItem(key, JSON.stringify(items));
  }
  function render() {
    const items = getItems();
    container.innerHTML = '';
    if (!items.length) {
      container.innerHTML = '<p class="text-muted">No favorites yet. Add one from the product modal.</p>';
      removeSelectedBtn.disabled = true;
      return;
    }
    items.forEach(p => {
      const col = document.createElement('div');
      col.className = 'col-6 col-md-4 col-lg-3';
      col.innerHTML = `
        <div class="card h-100 position-relative" data-id="${p.id}" style="border:1px solid #e0e0e0; border-radius:10px;">
          <input type="checkbox" class="form-check-input position-absolute" style="top:10px; right:10px; transform:scale(1.2);" />
          <img src="${p.image}" class="card-img-top" style="height:150px; object-fit:cover; border-radius:10px 10px 0 0;" alt="${p.name}">
          <div class="card-body text-center p-2">
            <div class="fw-semibold">${p.name}</div>
            <div class="text-success">₱${parseFloat(p.price).toFixed(2)}</div>
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
        const items = getItems().filter(p => String(p.id) !== String(id));
        setItems(items);
        render();
      });
    });
    updateRemoveButton();
  }
  function updateRemoveButton() {
    const anyChecked = !!container.querySelector('input[type="checkbox"]:checked');
    removeSelectedBtn.disabled = !anyChecked;
  }

  removeSelectedBtn.addEventListener('click', function() {
    const checkedIds = Array.from(container.querySelectorAll('input[type="checkbox"]:checked'))
      .map(cb => cb.closest('.card').getAttribute('data-id'));
    if (!checkedIds.length) return;
    const items = getItems().filter(p => !checkedIds.includes(String(p.id)));
    setItems(items);
    render();
  });

  clearAllBtn.addEventListener('click', function() {
    setItems([]);
    render();
  });


  render();
});
</script>
@endpush
@endsection



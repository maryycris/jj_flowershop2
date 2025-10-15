@extends('layouts.clerk_app')

@section('content')
<div class="pt-2 pb-4" style="background: #f4faf4; min-height: 100vh;">
  <div class="container" style="max-width: 1400px;">
    <form action="{{ route('clerk.orders.store') }}" method="POST" id="walkinDeliveryForm">
      @csrf
      <input type="hidden" name="order_type" value="walk-in">
      <input type="hidden" name="order_method" value="delivery">
      <div class="row justify-content-center mt-2">
        <div class="col-12 col-lg-8">
          <div class="bg-white rounded-3 p-3 mb-4" style="box-shadow: none;">
            <div class="mb-3">
              <a href="{{ url()->previous() }}" class="btn btn-outline-success">&larr; Back</a>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-2">
              <div style="font-weight: 600; font-size: 1.15rem;">Sender Information</div>
              <div class="d-flex align-items-center gap-2">
                <label class="me-2">Order Method</label>
                <select id="orderMethodSelect" class="form-select" style="width: 160px;">
                  <option value="delivery" selected>Delivery</option>
                  <option value="pickup">Pick up</option>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label class="form-label">Customer Name</label>
                <input type="text" class="form-control" name="customer_name" placeholder="Enter customer's name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Order Date</label>
                <input type="date" class="form-control" name="order_date" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>

            <div class="mb-3" style="font-weight: 600;">Recipient Information</div>
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label class="form-label">Recipient Name</label>
                <input type="text" class="form-control" name="recipient_name" placeholder="Enter recipient's full name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Recipient Contact Number</label>
                <input type="text" class="form-control" name="recipient_phone" placeholder="09XXXXXXXXX" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Relationship to Recipient</label>
                <select class="form-select" name="recipient_relationship" required>
                  <option value="" selected disabled>Select relationship</option>
                  <option value="Friend">Friend</option>
                  <option value="Family">Family</option>
                  <option value="Spouse/Partner">Spouse/Partner</option>
                  <option value="Parent">Parent</option>
                  <option value="Sibling">Sibling</option>
                  <option value="Child">Child</option>
                  <option value="Colleague">Colleague</option>
                  <option value="Neighbor">Neighbor</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Delivery Message/Card Message</label>
                <textarea class="form-control" name="delivery_message" rows="2" placeholder="Write a personal message for the recipient..."></textarea>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Special Instructions</label>
                <input type="text" class="form-control" name="recipient_instructions" placeholder="Any delivery notes...">
              </div>
            </div>

            <div class="mb-4">
              @php $preAddress = '' @endphp
              <x-delivery-map :selectedAddress="$preAddress" />
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="bg-white rounded-3 p-3" style="box-shadow:none;">
            <div class="d-flex align-items-center mb-3">
              <img src="{{ asset('public/images/logo.png') }}" width="36" class="me-2" alt=""> 
              <div class="fw-semibold">Purchase Summary</div>
            </div>
            <div class="mb-2">
              <label class="form-label">Product</label>
              <select class="form-select" name="products[0][product_id]" id="productSelect" required>
                <option value="" selected disabled>Select product</option>
                @isset($catalogProducts)
                  @foreach($catalogProducts as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-compositions='@json($p->compositions)'>{{ $p->name }}</option>
                  @endforeach
                @endisset
              </select>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="products[0][quantity]" id="quantityInput" min="1" value="1" required>
              </div>
              <div class="col-6">
                <label class="form-label">Unit Price</label>
                <input type="text" class="form-control" id="unitPrice" value="0.00" readonly>
              </div>
            </div>
            <div class="mb-3">
              <div class="fw-semibold mb-1">Materials</div>
              <div id="componentsList" class="small text-muted">Select a product to see its components.</div>
            </div>
            <div class="mb-3 p-2 rounded" style="background:#f5fff5;border:1px solid #e3f3e3;">
              <div class="small">Subtotal: ₱<span id="subtotal">0.00</span></div>
              <div class="small">Shipping Fee: ₱<span id="shippingFee">0.00</span></div>
              <hr class="my-2">
              <div class="fw-semibold">Total: ₱<span id="grandTotal">0.00</span></div>
            </div>
            <div class="mb-3">
              <label class="form-label">Delivery Date</label>
              <input type="date" class="form-control" name="delivery_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Delivery Time</label>
              <input type="time" class="form-control" name="delivery_time" required>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">Create Order</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const orderMethodSelect = document.getElementById('orderMethodSelect');
  orderMethodSelect?.addEventListener('change', function(){
    if (this.value === 'pickup') {
      window.location = "{{ route('clerk.orders.create') }}";
    } else {
      window.location = "{{ route('clerk.orders.walkin.delivery') }}";
    }
  });

  const productSelect = document.getElementById('productSelect');
  const quantityInput = document.getElementById('quantityInput');
  const unitPrice = document.getElementById('unitPrice');
  const subtotal = document.getElementById('subtotal');
  const shippingFee = document.getElementById('shippingFee');
  const grandTotal = document.getElementById('grandTotal');
  const addressInput = document.getElementById('deliveryAddressInput');
  const componentsList = document.getElementById('componentsList');
  const addressSearchInput = document.getElementById('addressSearchInput');
  const addressSearchBtn = document.getElementById('addressSearchBtn');
  const showMapBtn = document.getElementById('showMapBtn');

  function recalc(){
    const option = productSelect.options[productSelect.selectedIndex];
    const price = parseFloat(option?.getAttribute('data-price') || '0');
    const qty = parseInt(quantityInput.value || '1', 10);
    unitPrice.value = price.toFixed(2);
    const sub = price * qty;
    subtotal.textContent = sub.toFixed(2);
    const ship = parseFloat(shippingFee.textContent || '0');
    grandTotal.textContent = (sub + (isNaN(ship)?0:ship)).toFixed(2);
  }

  function renderComponents(){
    const option = productSelect.options[productSelect.selectedIndex];
    const comps = option?.getAttribute('data-compositions');
    try {
      const arr = comps ? JSON.parse(comps) : [];
      if (arr && arr.length > 0) {
        const html = '<ul class="mb-0">' + arr.map(c => {
          const name = (c.componentProduct?.name || c.component_name || c.name || 'Item');
          const qty = (c.quantity ?? 0);
          const unit = (c.unit ?? '').trim();
          return `<li>${name} — ${qty} ${unit}</li>`;
        }).join('') + '</ul>';
        componentsList.innerHTML = html;
      } else {
        componentsList.textContent = 'No components listed for this product.';
      }
    } catch (e) {
      componentsList.textContent = 'No components listed for this product.';
    }
  }

  productSelect.addEventListener('change', function(){ renderComponents(); recalc(); });
  quantityInput.addEventListener('input', recalc);

  // Calculate shipping fee via API when address changes
  let timer = null;
  addressInput.addEventListener('input', function(){
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => {
      const address = addressInput.value.trim();
      if (!address) { shippingFee.textContent = '0.00'; recalc(); return; }
      fetch('/api/map/shipping-calculate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ origin_address: 'Bangbang, Cordova, Cebu', destination_address: address })
      })
      .then(r => r.json())
      .then(data => {
        const fee = parseFloat(data.shipping_fee ?? data.fee ?? 0);
        shippingFee.textContent = (isNaN(fee)?0:fee).toFixed(2);
        recalc();
      })
      .catch(() => {});
    }, 500);
  });

  // FIND button uses the search input and copies to delivery address
  addressSearchBtn?.addEventListener('click', function(){
    if (addressSearchInput && addressSearchInput.value.trim()) {
      addressInput.value = addressSearchInput.value.trim();
      const event = new Event('input');
      addressInput.dispatchEvent(event);
    }
  });
  showMapBtn?.addEventListener('click', function(){
    window.open('https://www.openstreetmap.org/search?query=' + encodeURIComponent(addressSearchInput.value || 'Cordova Cebu'), '_blank');
  });
});
</script>
@endpush
@endsection



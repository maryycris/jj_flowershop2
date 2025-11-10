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
                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" placeholder="Enter customer's name" required value="{{ old('customer_name') }}">
                @error('customer_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Order Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('order_date') is-invalid @enderror" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                @error('order_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Recipient Type Toggle -->
            <div class="mb-3 d-flex gap-3">
              <button type="button" class="btn btn-outline-success flex-fill recipient-btn active" id="btnSomeone">Someone will receive the order</button>
              <button type="button" class="btn btn-outline-success flex-fill recipient-btn" id="btnSelf">I will receive the order.</button>
            </div>
            
            <input type="hidden" name="recipient_type" id="recipientType" value="someone">

            <!-- Recipient Information Section -->
            <div id="recipientFields" class="mb-3" style="display: block;">
              <div class="mb-3" style="font-weight: 600;">
                <i class="fas fa-user me-2"></i>Recipient Information
              </div>
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label class="form-label">Recipient Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" name="recipient_name" placeholder="Enter recipient's full name" required value="{{ old('recipient_name') }}">
                @error('recipient_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Recipient Contact Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" name="recipient_phone" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" maxlength="11" value="{{ old('recipient_phone') }}">
                @error('recipient_phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Relationship to Recipient <span class="text-danger">*</span></label>
                <select class="form-select @error('recipient_relationship') is-invalid @enderror" name="recipient_relationship">
                  <option value="" selected disabled>Select relationship</option>
                  <option value="Friend" {{ old('recipient_relationship') == 'Friend' ? 'selected' : '' }}>Friend</option>
                  <option value="Family" {{ old('recipient_relationship') == 'Family' ? 'selected' : '' }}>Family</option>
                  <option value="Spouse/Partner" {{ old('recipient_relationship') == 'Spouse/Partner' ? 'selected' : '' }}>Spouse/Partner</option>
                  <option value="Parent" {{ old('recipient_relationship') == 'Parent' ? 'selected' : '' }}>Parent</option>
                  <option value="Sibling" {{ old('recipient_relationship') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                  <option value="Child" {{ old('recipient_relationship') == 'Child' ? 'selected' : '' }}>Child</option>
                  <option value="Colleague" {{ old('recipient_relationship') == 'Colleague' ? 'selected' : '' }}>Colleague</option>
                  <option value="Neighbor" {{ old('recipient_relationship') == 'Neighbor' ? 'selected' : '' }}>Neighbor</option>
                  <option value="Other" {{ old('recipient_relationship') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('recipient_relationship')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Special Instructions</label>
                <input type="text" class="form-control" name="recipient_instructions" placeholder="Any delivery notes..." value="{{ old('recipient_instructions') }}">
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Delivery Message/Card Message</label>
                <textarea class="form-control" name="delivery_message" rows="2" placeholder="Write a personal message for the recipient...">{{ old('delivery_message') }}</textarea>
              </div>
            </div>
            </div>

            <!-- Self Information Section -->
            <div id="selfFields" class="mb-3" style="display: none;">
              <div class="mb-3" style="font-weight: 600;">
                <i class="fas fa-user me-2"></i>Your Contact Information
              </div>
              <div class="row mb-3">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Your Contact Number <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" name="recipient_phone" id="selfPhone" placeholder="09XXXXXXXXX" pattern="^09\d{9}$" maxlength="11" value="{{ old('recipient_phone') }}">
                  @error('recipient_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">Mobile number for delivery updates</small>
                </div>
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
              <label class="form-label">Product <span class="text-danger">*</span></label>
              <select class="form-select @error('products.0.product_id') is-invalid @enderror" name="products[0][product_id]" id="productSelect" required>
                <option value="" selected disabled>Select product</option>
                @isset($catalogProducts)
                  @foreach($catalogProducts as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-compositions='@json($p->compositions)' {{ old('products.0.product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                  @endforeach
                @endisset
              </select>
              @error('products.0.product_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
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
            <!-- Delivery Schedule Section -->
            <div class="mb-4">
              <div class="p-3" style="background: linear-gradient(135deg, #e8f5e8, #f0f8f0); border-radius: 8px; border-left: 4px solid #8ACB88;">
                <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                  <i class="fas fa-calendar-check me-2 text-success"></i>Choose Your Delivery Schedule
                </h6>
                <p class="text-muted small mb-3">
                  <i class="fas fa-info-circle me-2"></i>
                  Select your preferred delivery date and time. We'll deliver your flowers when you need them most!
                </p>
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="delivery_date" class="form-label">
                      <i class="fas fa-calendar me-2"></i>Delivery Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('delivery_date') is-invalid @enderror" 
                           id="delivery_date" 
                           name="delivery_date" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           required
                           value="{{ old('delivery_date') }}">
                    @error('delivery_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Select a date at least 1 day from now</small>
                  </div>
                  <div class="col-md-6">
                    <label for="delivery_time" class="form-label">
                      <i class="fas fa-clock me-2"></i>Delivery Time <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('delivery_time') is-invalid @enderror" id="delivery_time" name="delivery_time" required>
                      <option value="">Choose time...</option>
                      <option value="08:00 AM" {{ old('delivery_time') == '08:00 AM' ? 'selected' : '' }}>8:00 AM - 9:00 AM</option>
                      <option value="09:00 AM" {{ old('delivery_time') == '09:00 AM' ? 'selected' : '' }}>9:00 AM - 10:00 AM</option>
                      <option value="10:00 AM" {{ old('delivery_time') == '10:00 AM' ? 'selected' : '' }}>10:00 AM - 11:00 AM</option>
                      <option value="11:00 AM" {{ old('delivery_time') == '11:00 AM' ? 'selected' : '' }}>11:00 AM - 12:00 PM</option>
                      <option value="12:00 PM" {{ old('delivery_time') == '12:00 PM' ? 'selected' : '' }}>12:00 PM - 1:00 PM</option>
                      <option value="01:00 PM" {{ old('delivery_time') == '01:00 PM' ? 'selected' : '' }}>1:00 PM - 2:00 PM</option>
                      <option value="02:00 PM" {{ old('delivery_time') == '02:00 PM' ? 'selected' : '' }}>2:00 PM - 3:00 PM</option>
                      <option value="03:00 PM" {{ old('delivery_time') == '03:00 PM' ? 'selected' : '' }}>3:00 PM - 4:00 PM</option>
                      <option value="04:00 PM" {{ old('delivery_time') == '04:00 PM' ? 'selected' : '' }}>4:00 PM - 5:00 PM</option>
                      <option value="05:00 PM" {{ old('delivery_time') == '05:00 PM' ? 'selected' : '' }}>5:00 PM - 6:00 PM</option>
                      <option value="06:00 PM" {{ old('delivery_time') == '06:00 PM' ? 'selected' : '' }}>6:00 PM - 7:00 PM</option>
                    </select>
                    @error('delivery_time')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Choose your preferred time slot</small>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-end">
              <button type="submit" id="createOrderBtn" class="btn btn-primary">Create Order</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@push('styles')
<style>
  .recipient-btn {
    border: 2px solid #7bb47b;
    color: #7bb47b;
    background: white;
    transition: all 0.3s ease;
    font-weight: 500;
  }
  
  .recipient-btn:hover {
    background: #f0f8f0;
    border-color: #5a9c5a;
    color: #5a9c5a;
  }
  
  .recipient-btn.active {
    background: #7bb47b !important;
    color: white !important;
    border-color: #7bb47b !important;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(123, 180, 123, 0.3);
  }
  
  .recipient-btn.active:hover {
    background: #5a9c5a !important;
    border-color: #5a9c5a !important;
  }
  
  /* Proper capitalization for text inputs */
  .form-control[type="text"]:not([name*="phone"]):not([name*="email"]):not([name*="address"]) {
    text-transform: capitalize;
  }
  
  .form-control[type="text"][name*="name"] {
    text-transform: capitalize;
  }
  
  .form-control[type="text"][name*="instructions"] {
    text-transform: capitalize;
  }
  
  .form-control[type="text"][name*="message"] {
    text-transform: capitalize;
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Auto capitalization is now handled by the global script

  // Recipient type toggle functionality
  const btnSomeone = document.getElementById('btnSomeone');
  const btnSelf = document.getElementById('btnSelf');
  
  if (btnSomeone && btnSelf) {
    console.log('Clerk recipient toggle buttons found, setting up event listeners');
    
    btnSomeone.onclick = function() {
      console.log('Someone will receive clicked');
      btnSomeone.classList.add('active');
      btnSelf.classList.remove('active');
      document.getElementById('recipientType').value = 'someone';
      
      // Show recipient fields, require inputs
      const recipientFields = document.getElementById('recipientFields');
      const recipientName = document.querySelector('input[name="recipient_name"]');
      const recipientPhone = document.querySelector('input[name="recipient_phone"]:not(#selfPhone)');
      const recipientRelationship = document.querySelector('select[name="recipient_relationship"]');
      
      if (recipientFields) {
        recipientFields.style.display = 'block';
        console.log('Showing recipient fields');
      }
      if (recipientName) {
        recipientName.required = true;
        recipientName.disabled = false;
      }
      if (recipientPhone) {
        recipientPhone.required = true;
        recipientPhone.disabled = false;
      }
      if (recipientRelationship) {
        recipientRelationship.required = true;
        recipientRelationship.disabled = false;
      }
      
      // Hide self fields and remove required from self phone
      const selfFields = document.getElementById('selfFields');
      const selfPhone = document.getElementById('selfPhone');
      
      if (selfFields) {
        selfFields.style.display = 'none';
        console.log('Hiding self fields');
      }
      if (selfPhone) {
        selfPhone.required = false;
      }
    };
    
    btnSelf.onclick = function() {
      console.log('I will receive clicked');
      btnSelf.classList.add('active');
      btnSomeone.classList.remove('active');
      document.getElementById('recipientType').value = 'self';
      
      // Hide recipient fields, remove required
      const recipientFields = document.getElementById('recipientFields');
      const recipientName = document.querySelector('input[name="recipient_name"]');
      const recipientPhone = document.querySelector('input[name="recipient_phone"]:not(#selfPhone)');
      
      if (recipientFields) {
        recipientFields.style.display = 'none';
        console.log('Hiding recipient fields');
      }
      if (recipientName) {
        recipientName.required = false;
      }
      if (recipientPhone) {
        recipientPhone.required = false;
      }
      
      // Show self fields and make phone required
      const selfFields = document.getElementById('selfFields');
      const selfPhone = document.getElementById('selfPhone');
      const recipientRelationship = document.querySelector('select[name="recipient_relationship"]');
      
      if (selfFields) {
        selfFields.style.display = 'block';
        console.log('Showing self fields');
      }
      if (selfPhone) {
        selfPhone.required = true;
      }
      if (recipientRelationship) {
        recipientRelationship.required = false;
        recipientRelationship.disabled = true; // disable to avoid browser validation when hidden
        recipientRelationship.classList.remove('is-invalid');
      }
    };
  }

  // Prevent accidental form submit while typing in inputs (especially Delivery Location)
  const form = document.getElementById('walkinDeliveryForm');
  if (form) {
    form.addEventListener('keydown', function(e){
      if (e.key === 'Enter') {
        // Allow Enter only when focused on an element explicitly permitting it
        const allowEnter = e.target?.getAttribute && e.target.getAttribute('data-allow-enter') === 'true';
        if (!allowEnter) {
          e.preventDefault();
          return false;
        }
      }
    });

    // Handle form submission
    form.addEventListener('submit', function(ev){
      console.log('Form submit event triggered');
      const btn = document.getElementById('createOrderBtn');
      if (!btn || btn.disabled) {
        console.log('Form submission prevented - button disabled or not found');
        ev.preventDefault();
        return;
      }
    
    // Check for required fields before submission
    const requiredFields = form.querySelectorAll('[required]');
    let hasErrors = false;
    let errorMessage = 'Please fill in all required fields:\n';
    
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.classList.add('is-invalid');
        hasErrors = true;
        const label = field.previousElementSibling?.textContent || field.name;
        errorMessage += `- ${label}\n`;
      } else {
        field.classList.remove('is-invalid');
      }
    });
    
      // Special validation for recipient phone based on recipient type
      const recipientType = document.getElementById('recipientType').value;
      const recipientPhone = document.querySelector('input[name="recipient_phone"]:not(#selfPhone)');
      const selfPhone = document.getElementById('selfPhone');
      const recipientRelationship = document.querySelector('select[name="recipient_relationship"]');
      
      if (recipientType === 'someone' && recipientPhone && !recipientPhone.value.trim()) {
        recipientPhone.classList.add('is-invalid');
        hasErrors = true;
        errorMessage += '- Recipient Contact Number\n';
      }
      
      if (recipientType === 'self' && selfPhone && !selfPhone.value.trim()) {
        selfPhone.classList.add('is-invalid');
        hasErrors = true;
        errorMessage += '- Your Contact Number\n';
      }
      
      // Remove required attribute from recipient_relationship when "self" is selected
      if (recipientType === 'self' && recipientRelationship) {
        recipientRelationship.required = false;
        recipientRelationship.classList.remove('is-invalid');
      } else if (recipientType === 'someone' && recipientRelationship) {
        recipientRelationship.required = true;
        if (!recipientRelationship.value.trim()) {
          recipientRelationship.classList.add('is-invalid');
          hasErrors = true;
          errorMessage += '- Relationship to Recipient\n';
        }
      }
    
      if (hasErrors) {
        ev.preventDefault();
        console.log('Form validation failed:', errorMessage);
        alert(errorMessage);
        return;
      }
      
      console.log('Form validation passed, proceeding with submission');
    
    // Disable button to prevent double submission
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Order...';
    
    // Let the form submit normally - the server will redirect to quotation page
    console.log('Form submitting to:', form.action);
    console.log('Form method:', form.method);
    
    // Submit immediately without delay for testing
    console.log('Submitting form now...');
    form.submit();
  });
  }
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
    const total = sub + (isNaN(ship)?0:ship);
    grandTotal.textContent = total.toFixed(2);
    
    console.log('Recalc - Subtotal:', sub, 'Shipping:', ship, 'Total:', total);
    console.log('Shipping fee element text:', shippingFee.textContent);
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
  

  // Debounced shipping fee calculation (same as admin form)
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



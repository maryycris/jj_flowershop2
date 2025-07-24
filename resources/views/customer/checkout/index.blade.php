@extends('layouts.customer_app')

@section('content')
<div class="py-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="mb-3">
        <a href="{{ url('/cart') }}" class="btn btn-outline-success">
            &larr; Back
        </a>
    </div>
    <form action="{{ route('customer.checkout.payment_method') }}" method="GET" id="checkoutForm">
        @csrf
        <input type="hidden" name="recipient_type" id="recipientType" value="someone">
        <input type="hidden" name="promo_code" id="promoCodeInputHidden" value="">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="bg-white rounded-3 p-4 mb-4" style="box-shadow: none;">
                    <div class="mb-4" style="font-weight: 600; font-size: 1.15rem;">Sender Information:</div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->contact_number }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->first_name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->last_name }}" readonly>
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-3">
                        <button type="button" class="btn btn-outline-success flex-fill recipient-btn active" id="btnSomeone">Someone will receive the order</button>
                        <button type="button" class="btn btn-outline-success flex-fill recipient-btn" id="btnSelf">I will receive the order.</button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Addresses</label>
                        <select class="form-select" style="min-height: 120px;">
                            <option>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}\nBang-bang Cordova\nCebu City\n+{{ Auth::user()->contact_number }}\nRegion VII</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-white rounded-3 p-4 mb-4" style="box-shadow: none;">
                    <div class="mb-3" style="font-weight: 600; font-size: 1.15rem;">Purchase Summary:</div>
                    @foreach($cartItems as $item)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px;">
                        <div class="flex-grow-1 ms-2">
                            <div style="font-weight: 500;">{{ $item->product->name }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="decrement">-</button>
                            <input type="text" class="form-control form-control-sm text-center quantity-input" value="{{ $item->quantity }}" readonly style="width: 40px;">
                            <button class="btn btn-outline-success btn-sm quantity-btn" type="button" data-action="increment">+</button>
                        </div>
                        <div class="ms-3" style="font-weight: 500; font-size: 1.08rem;">₱{{ number_format($item->quantity * $item->product->price, 2) }}</div>
                    </div>
                    @endforeach
                    <div class="input-group mb-3 mt-3">
                        <input type="text" class="form-control" placeholder="Promo code" id="promoCodeInput">
                        <button class="btn btn-success" type="button" id="applyPromoBtn">Apply</button>
                    </div>
                    <div id="promoFeedback" class="mb-2" style="font-size: 0.98rem;"></div>
                    <div class="d-flex justify-content-between mb-2 mt-4">
                        <span style="color: #888;">Subtotal ({{ count($cartItems) }} Item{{ count($cartItems) == 1 ? '' : 's' }})</span>
                        <span style="color: #222;">₱<span id="cartSubtotal">{{ number_format($subtotal, 2) }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #888;">Shipping Fee</span>
                        <span style="color: #222;">₱<span id="shippingFeeDisplay">{{ number_format($shippingFee ?? 50, 2) }}</span></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight: 600;">Total</span>
                        <span style="color: #7bb47b; font-weight: 600; font-size: 1.15rem;">₱<span id="cartTotalFinal">{{ number_format($subtotal + ($shippingFee ?? 50), 2) }}</span></span>
                    </div>
                    <button type="submit" class="btn btn-success w-100" style="border-radius: 25px; font-weight: 600; font-size: 1.08rem;">Proceed to Payment Method</button>
                </div>
            </div>
        </div>
    </form>
</div>
@push('styles')
<style>
    body { background: #f4faf4; }
    .bg-white { box-shadow: none !important; }
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
    .form-control[readonly] {
        background: #f4faf4;
    }
    .recipient-btn.active, .recipient-btn:focus {
        background: #cbe7cb !important;
        color: #222 !important;
        border-color: #7bb47b !important;
        font-weight: 600;
    }
    #applyPromoBtn.active {
        box-shadow: 0 0 0 2px #cbe7cb;
    }
</style>
@endpush

@push('scripts')
<script>
const orsApiKey = 'YOUR_ORS_API_KEY_HERE'; // <-- Replace with your actual ORS API key
const shopLat = 10.2447; // Cordova, Cebu
const shopLng = 123.9633;
const baseFee = 30; // Minimum shipping fee
const perKmRate = 10; // Shipping rate per km after base

function updateShippingFee() {
    const address = document.getElementById('delivery_address').value.trim();
    if (!address) return;
    // Geocode the address using ORS
    fetch(`https://api.openrouteservice.org/geocode/search?api_key=${orsApiKey}&text=${encodeURIComponent(address)}&boundary.country=PH`)
        .then(res => res.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                const coords = data.features[0].geometry.coordinates;
                const destLng = coords[0];
                const destLat = coords[1];
                // Get distance from shop to destination
                fetch('https://api.openrouteservice.org/v2/directions/driving-car', {
                    method: 'POST',
                    headers: {
                        'Authorization': orsApiKey,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        coordinates: [
                            [shopLng, shopLat],
                            [destLng, destLat]
                        ]
                    })
                })
                .then(res => res.json())
                .then(route => {
                    if (route && route.features && route.features.length > 0) {
                        const meters = route.features[0].properties.summary.distance;
                        const km = meters / 1000;
                        let shippingFee = baseFee + Math.ceil(km) * perKmRate;
                        if (shippingFee < baseFee) shippingFee = baseFee;
                        document.getElementById('shippingFeeDisplay').textContent = `₱${shippingFee.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                        const subtotal = parseFloat({{ $subtotal }});
                        document.getElementById('totalDisplay').textContent = `₱${(subtotal + shippingFee).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                        document.getElementById('shipping_fee').value = shippingFee;
                    }
                });
            }
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const addressInput = document.getElementById('delivery_address');
    addressInput.addEventListener('blur', updateShippingFee);
    addressInput.addEventListener('change', updateShippingFee);
    // Optionally, update on keyup for more responsiveness
    addressInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') updateShippingFee();
    });
});

// Recipient type toggle (visual only)
const btnSomeone = document.getElementById('btnSomeone');
const btnSelf = document.getElementById('btnSelf');
btnSomeone.onclick = function() {
    btnSomeone.classList.add('active');
    btnSelf.classList.remove('active');
};
btnSelf.onclick = function() {
    btnSelf.classList.add('active');
    btnSomeone.classList.remove('active');
};
// Promo code Apply (visual only)
document.getElementById('applyPromoBtn').onclick = function() {
    // No functional logic, just visual feedback (optional highlight)
    this.classList.add('active');
    setTimeout(() => this.classList.remove('active'), 300);
};
</script>
@endpush 
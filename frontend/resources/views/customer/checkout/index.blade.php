@extends('layouts.customer_app')

@section('content')
@include('components.customer.alt_nav', ['active' => 'home'])
<div class="pt-0 checkout-container" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">
    <form action="{{ route('customer.checkout.payment_method') }}" method="GET" id="checkoutForm">
        @csrf
        <input type="hidden" name="recipient_type" id="recipientType" value="someone">
        <input type="hidden" name="delivery_address" id="deliveryAddressHidden" value="">
        <input type="hidden" name="loyalty_discount" id="loyaltyDiscountHidden" value="{{ $loyaltyDiscount ?? 0 }}">
        <input type="hidden" name="shipping_fee" id="shippingFeeInput" value="0">
        @if(request('product_id'))
            <input type="hidden" name="product_id" value="{{ request('product_id') }}">
            <input type="hidden" name="quantity" value="{{ request('quantity', 1) }}">
        @endif
        @if(request('catalog_product_id'))
            <input type="hidden" name="catalog_product_id" value="{{ request('catalog_product_id') }}">
            <input type="hidden" name="quantity" value="{{ request('quantity', 1) }}">
        @endif
        @if(request('custom_bouquet_id'))
            <input type="hidden" name="custom_bouquet_id" value="{{ request('custom_bouquet_id') }}">
            <input type="hidden" name="quantity" value="{{ request('quantity', 1) }}">
        @endif
        @if(request('selected_items'))
            @foreach(request('selected_items') as $itemId)
                <input type="hidden" name="selected_items[]" value="{{ $itemId }}">
            @endforeach
        @endif
        <div class="row justify-content-center mt-2">
            <div class="col-12 col-lg-8 col-xl-6 order-1 order-lg-1 checkout-form-section">
                <div class="bg-white rounded-3 p-3 p-md-4 mb-3 mb-lg-4 scrollable-content" style="box-shadow: none; overflow-y: auto;">
                    <div class="mb-3">
                        <button type="button" id="checkoutBackBtn" class="btn btn-outline-success">&larr; Back</button>
                    </div>
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
                    
                    <!-- Error Alert for Phone Number -->
                    @error('recipient_phone')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Phone Number Required!</strong> {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    <div id="recipientFields" class="mb-3" style="display: block;">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-user me-2"></i>Recipient Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Recipient Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="recipient_name" id="recipientName" placeholder="Enter recipient's full name" required>
                                <small class="text-muted">Full name as it appears on ID</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Recipient Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" name="recipient_phone" id="recipientPhone" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" maxlength="11" value="{{ old('recipient_phone') }}">
                                @error('recipient_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Mobile number for delivery updates</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Relationship to Recipient</label>
                                <select class="form-select" name="recipient_relationship" id="recipientRelationship">
                                    <option value="">Select relationship</option>
                                    <option value="family">Family Member</option>
                                    <option value="friend">Friend</option>
                                    <option value="colleague">Colleague</option>
                                    <option value="partner">Partner/Spouse</option>
                                    <option value="other">Other</option>
                                </select>
                                <div id="otherRelationshipInput" class="mt-2" style="display: none;">
                                    <label class="form-label fw-semibold">Please specify relationship <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="recipient_relationship_other" id="recipientRelationshipOther" placeholder="e.g., Neighbor, Classmate, etc.">
                                    <small class="text-muted">Enter the specific relationship</small>
                                </div>
                                <input type="hidden" name="recipient_relationship_final" id="recipientRelationshipFinal" value="">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Special Instructions</label>
                                <textarea class="form-control" name="recipient_instructions" id="recipientInstructions" rows="2" placeholder="Any special delivery instructions..."></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Delivery Message/Card Message</label>
                                <textarea class="form-control" name="delivery_message" id="deliveryMessage" rows="3" placeholder="Write a personal message for the recipient..."></textarea>
                                <small class="text-muted">This message will be included with the delivery</small>
                            </div>
                        </div>
                    </div>
                    <div id="selfFields" class="mb-3" style="display:none">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-user me-2"></i>Your Contact Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Your Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" name="recipient_phone" id="selfPhone" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" maxlength="11" value="{{ old('recipient_phone', Auth::user()->contact_number) }}">
                                @error('recipient_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Mobile number for delivery updates</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Addresses</label>
                        <select class="form-select" name="address_id" id="addressSelect" style="min-height: 120px;">
                            @forelse(($addresses ?? []) as $addr)
                                @php
                                    // Construct a geocoding-friendly full address string
                                    $fullAddress = trim(collect([
                                        $addr->street_address,
                                        $addr->barangay,
                                        $addr->municipality ?: $addr->city,
                                        'Cebu, Philippines'
                                    ])->filter()->implode(', '));
                                @endphp
                                <option value="{{ $addr->id }}" data-address="{{ $fullAddress }}" @selected(optional($deliveryAddress)->id === $addr->id)>
                                    {{ $addr->street_address }}, {{ $addr->barangay }}, {{ $addr->municipality ?? $addr->city }}
                                </option>
                            @empty
                                <option disabled>No saved addresses. Add one in Address Book.</option>
                            @endforelse
                        </select>
                    </div>

                    {{-- Delivery Map Component --}}
                    <div class="mb-4">
                        <x-delivery-map :selectedAddress="optional($deliveryAddress) ? 
                            trim(collect([
                                optional($deliveryAddress)->street_address,
                                optional($deliveryAddress)->barangay,
                                optional($deliveryAddress)->municipality ?: optional($deliveryAddress)->city,
                                'Cebu, Philippines'
                            ])->filter()->implode(', ')) : ''" />
                    </div>
                </div>
            </div>
            <!-- Purchase Summary Box - Always Visible -->
            <div class="col-12 col-lg-4 col-xl-4 order-2 order-lg-2 purchase-summary-section">
                <div class="bg-white rounded-3 p-3 p-md-4 purchase-summary-card" style="box-shadow: none; display: flex; flex-direction: column;">
                    <!-- Purchase Summary Header -->
                    <div class="purchase-summary-header mb-3">
                        <div class="mb-3" style="font-weight: 600; font-size: 1.1rem;">Purchase Summary</div>
                        @foreach($cartItems as $item)
                        <div class="d-flex align-items-center mb-3">
                            @if($item->item_type === 'custom_bouquet')
                                <!-- Custom Bouquet Display -->
                                <div class="flex-grow-1 ms-2">
                                    @php
                                        $modalId = $item->customBouquet ? $item->customBouquet->id : (isset($item->custom_bouquet_id) ? $item->custom_bouquet_id : 'temp');
                                    @endphp
                                    <button type="button" class="btn btn-link p-0 text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#customBouquetModal{{ $modalId }}" style="font-weight: 500; color: #7bb47b; font-size: 1rem;">
                                        Custom Bouquet
                                    </button>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success" style="font-size: 0.9rem; padding: 8px 12px;">{{ $item->quantity }}</span>
                                </div>
                                <div class="ms-3" style="font-weight: 500; font-size: 1.08rem;">₱{{ number_format($item->quantity * ($item->customBouquet ? ($item->customBouquet->unit_price ?? ($item->customBouquet->total_price / max($item->customBouquet->quantity, 1))) : 0), 2) }}</div>
                            @else
                                <!-- Regular Product Display -->
                                <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px;">
                                <div class="flex-grow-1 ms-2">
                                    <div style="font-weight: 500;">{{ $item->product->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success" style="font-size: 0.9rem; padding: 8px 12px;">{{ $item->quantity }}</span>
                                </div>
                                <div class="ms-3" style="font-weight: 500; font-size: 1.08rem;">₱{{ number_format($item->quantity * ($item->product ? $item->product->price : 0), 2) }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Scrollable Content Area -->
                    <div class="scrollable-right-content purchase-summary-content" style="flex: 1; overflow-y: auto;">
                    {{-- Loyalty Stamps Section --}}
                    <div class="mb-3 mt-3">
                        <div class="card" style="border: 2px solid #e8f5e8; background: linear-gradient(135deg, #f8f9fa, #e8f5e8);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-success fw-bold">
                                        <i class="fas fa-star me-2"></i>Loyalty Stamps
                                    </h6>
                                    @if($loyaltyCard)
                                        <span class="badge bg-success fs-6">{{ $loyaltyCard->stamps_count }}/5</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">0/5</span>
                                    @endif
                                </div>
                                
                                @if($loyaltyCard && $loyaltyCard->stamps_count >= 5)
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fas fa-gift text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        <p class="text-success fw-bold mb-2">🎉 You have 5 stamps! You can redeem a 50% discount on your most expensive bouquet!</p>
                                        <button type="button" class="btn btn-success btn-sm" id="redeemLoyaltyBtn">
                                            <i class="fas fa-gift me-1"></i>Redeem Discount
                                        </button>
                                    </div>
                                @elseif($loyaltyCard && $loyaltyCard->stamps_count > 0)
                                    <div class="text-center">
                                        <p class="text-muted mb-2">You need {{ 5 - $loyaltyCard->stamps_count }} more stamp(s) to redeem a discount</p>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ ($loyaltyCard->stamps_count / 5) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-muted mb-2">Start earning stamps with your first order!</p>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="loyaltyFeedback" class="mb-2" style="font-size: 0.98rem;"></div>
                    <div class="d-flex justify-content-between mb-2 mt-4">
                        <span style="color: #888;">Subtotal ({{ count($cartItems) }} Item{{ count($cartItems) == 1 ? '' : 's' }})</span>
                        <span style="color: #222;">₱<span id="cartSubtotal">{{ number_format($subtotal, 2) }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #888;">Shipping Fee</span>
                        <span style="color: #222;">₱<span id="shippingFeeDisplay">{{ number_format($shippingFee ?? 0, 2) }}</span></span>
                    </div>
                    @if($loyaltyDiscount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #28a745;">Loyalty Discount</span>
                        <span style="color: #28a745; font-weight: 600;">-₱<span id="loyaltyDiscountDisplay">{{ number_format($loyaltyDiscount, 2) }}</span></span>
                    </div>
                    @endif
                    
                    <!-- Store Credit Section -->
                    @if($storeCreditBalance > 0)
                    <div class="mb-3 mt-3">
                        <div class="card" style="border: 2px solid #e8f5e8; background: linear-gradient(135deg, #f8f9fa, #e8f5e8);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-wallet me-2" style="color: #28a745; font-size: 1.2rem;"></i>
                                        <span style="font-weight: 600; color: #2c3e50;">Store Credit Available</span>
                                    </div>
                                    <span style="color: #28a745; font-weight: bold; font-size: 1.1rem;">₱{{ number_format($storeCreditBalance, 2) }}</span>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="useStoreCredit" name="use_store_credit" value="1">
                                    <label class="form-check-label" for="useStoreCredit" style="font-weight: 500; color: #2c3e50;">
                                        Use Store Credit
                                    </label>
                                </div>
                                <div class="mt-2" id="storeCreditAmountDiv" style="display: none;">
                                    <label for="storeCreditAmount" class="form-label" style="font-size: 0.9rem; color: #666;">Amount to use (max ₱{{ number_format($storeCreditBalance, 2) }}):</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="storeCreditAmount" name="store_credit_amount" 
                                               min="0" max="{{ $storeCreditBalance }}" step="0.01" 
                                               placeholder="0.00" style="font-weight: 500;">
                                    </div>
                                    <small class="text-muted">You can use up to ₱{{ number_format($storeCreditBalance, 2) }} of your store credit.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="storeCreditDiscount" class="d-flex justify-content-between mb-2" style="display: none;">
                        <span style="color: #28a745;">Store Credit Used</span>
                        <span style="color: #28a745; font-weight: 600;">-₱<span id="storeCreditDiscountDisplay">0.00</span></span>
                    </div>
                    @endif
                    
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight: 600;">Total</span>
                        <span style="color: #7bb47b; font-weight: 600; font-size: 1.15rem;">₱<span id="cartTotalFinal">{{ number_format($subtotal - ($loyaltyDiscount ?? 0) + ($shippingFee ?? 0), 2) }}</span></span>
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
                                        <i class="fas fa-calendar me-2"></i>Delivery Date *
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="delivery_date" 
                                           name="delivery_date" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                           required>
                                    <small class="text-muted">Select a date at least 1 day from now</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="delivery_time" class="form-label">
                                        <i class="fas fa-clock me-2"></i>Delivery Time *
                                    </label>
                                    <select class="form-control" id="delivery_time" name="delivery_time" required>
                                        <option value="">Choose time...</option>
                                        <option value="08:00 AM">8:00 AM - 9:00 AM</option>
                                        <option value="09:00 AM">9:00 AM - 10:00 AM</option>
                                        <option value="10:00 AM">10:00 AM - 11:00 AM</option>
                                        <option value="11:00 AM">11:00 AM - 12:00 PM</option>
                                        <option value="12:00 PM">12:00 PM - 1:00 PM</option>
                                        <option value="01:00 PM">1:00 PM - 2:00 PM</option>
                                        <option value="02:00 PM">2:00 PM - 3:00 PM</option>
                                        <option value="03:00 PM">3:00 PM - 4:00 PM</option>
                                        <option value="04:00 PM">4:00 PM - 5:00 PM</option>
                                        <option value="05:00 PM">5:00 PM - 6:00 PM</option>
                                        <option value="06:00 PM">6:00 PM - 7:00 PM</option>
                                    </select>
                                    <small class="text-muted">Choose your preferred time slot</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="proceedBtn" style="border-radius: 25px; font-weight: 600; font-size: 1.08rem;">
                        <i class="fas fa-arrow-right me-2"></i>Proceed to Payment Method
                    </button>
                    </div> <!-- End scrollable right content -->
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
    
    /* Disabled delivery address field styling */
    #deliveryAddressInput:disabled {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }
    
    #geocodeBtn:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
    #applyPromoBtn.active {
        box-shadow: 0 0 0 2px #cbe7cb;
    }
</style>
@endpush

@push('scripts')
<script>
// Auto-populate delivery address with default address only for "I will receive the order"
function populateDefaultAddress() {
    const recipientType = document.getElementById('recipientType').value;
    const deliveryAddressInput = document.getElementById('deliveryAddressInput');
    const geocodeBtn = document.getElementById('geocodeBtn');
    
    if (recipientType === 'self') {
        const addressSelect = document.getElementById('addressSelect');
        const defaultAddressOption = addressSelect.querySelector('option[selected]');
        if (defaultAddressOption && !defaultAddressOption.disabled) {
            const address = defaultAddressOption.getAttribute('data-address');
            if (deliveryAddressInput && address && address !== 'No saved addresses. Add one in Address Book.') {
                deliveryAddressInput.value = address;
                // Disable the delivery address field when using default address
                deliveryAddressInput.disabled = true;
                deliveryAddressInput.readOnly = true;
                deliveryAddressInput.style.backgroundColor = '#f8f9fa';
                deliveryAddressInput.style.cursor = 'not-allowed';
                
                // Keep the Find button ENABLED for shipping fee calculation
                if (geocodeBtn) {
                    geocodeBtn.disabled = false;
                    geocodeBtn.style.opacity = '';
                    geocodeBtn.style.cursor = '';
                }
                
                // Trigger the map component's calculation
                deliveryAddressInput.dispatchEvent(new Event('input'));
            } else {
                // No valid address, clear the input and enable it
                if (deliveryAddressInput) {
                    deliveryAddressInput.value = '';
                    deliveryAddressInput.disabled = false;
                    deliveryAddressInput.readOnly = false;
                    deliveryAddressInput.style.backgroundColor = '';
                    deliveryAddressInput.style.cursor = '';
                }
            }
        } else {
            // No selected address, clear the input and enable it
            if (deliveryAddressInput) {
                deliveryAddressInput.value = '';
                deliveryAddressInput.disabled = false;
                deliveryAddressInput.readOnly = false;
                deliveryAddressInput.style.backgroundColor = '';
                deliveryAddressInput.style.cursor = '';
            }
        }
    } else {
        // Enable the delivery address field when "Someone will receive" is selected
        if (deliveryAddressInput) {
            deliveryAddressInput.disabled = false;
            deliveryAddressInput.readOnly = false;
            deliveryAddressInput.style.backgroundColor = '';
            deliveryAddressInput.style.cursor = '';
        }
        
        // Keep the Find button enabled
        if (geocodeBtn) {
            geocodeBtn.disabled = false;
            geocodeBtn.style.opacity = '';
            geocodeBtn.style.cursor = '';
        }
    }
}

// Update shipping fee when address selection changes
document.addEventListener('DOMContentLoaded', function() {
    // Handle "Other" relationship input field
    const relationshipSelect = document.getElementById('recipientRelationship');
    const otherRelationshipInput = document.getElementById('otherRelationshipInput');
    const otherRelationshipField = document.getElementById('recipientRelationshipOther');
    
    if (relationshipSelect && otherRelationshipInput) {
        // Check initial value on page load
        if (relationshipSelect.value === 'other') {
            otherRelationshipInput.style.display = 'block';
            if (otherRelationshipField) {
                otherRelationshipField.required = true;
            }
        }
        
        // Handle change event
        relationshipSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                otherRelationshipInput.style.display = 'block';
                if (otherRelationshipField) {
                    otherRelationshipField.required = true;
                    otherRelationshipField.focus();
                }
            } else {
                otherRelationshipInput.style.display = 'none';
                if (otherRelationshipField) {
                    otherRelationshipField.required = false;
                    otherRelationshipField.value = '';
                }
                // Update hidden field with selected value
                const finalField = document.getElementById('recipientRelationshipFinal');
                if (finalField) {
                    finalField.value = this.value;
                }
            }
        });
        
        // Update hidden field when other relationship field changes
        if (otherRelationshipField) {
            otherRelationshipField.addEventListener('input', function() {
                const finalField = document.getElementById('recipientRelationshipFinal');
                if (finalField && relationshipSelect.value === 'other') {
                    finalField.value = this.value.trim();
                }
            });
        }
    }
    
    // Back button: go to previous page if exists; fallback to cart
    const backBtn = document.getElementById('checkoutBackBtn');
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '{{ url('/cart') }}';
            }
        });
    }
    const addressSelect = document.getElementById('addressSelect');
    const checkoutForm = document.getElementById('checkoutForm');
    
    // Check on page load if "I will receive" is selected
    populateDefaultAddress();
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Ensure shipping_fee is always sent
            const feeInput = document.getElementById('shippingFeeInput');
            const fee = feeInput ? feeInput.value : '';
            
            // Get delivery address from the map component
            const deliveryAddressInput = document.getElementById('deliveryAddressInput');
            const deliveryAddress = deliveryAddressInput ? deliveryAddressInput.value : '';
            
            // Get delivery schedule fields
            const deliveryDate = document.getElementById('delivery_date').value;
            const deliveryTime = document.getElementById('delivery_time').value;
            
            // Get loyalty discount
            const loyaltyDiscount = document.getElementById('loyaltyDiscountHidden').value;
            
            // Handle "Other" relationship - if "Other" is selected, use the custom value
            const relationshipSelect = document.getElementById('recipientRelationship');
            const otherRelationshipField = document.getElementById('recipientRelationshipOther');
            const finalRelationshipField = document.getElementById('recipientRelationshipFinal');
            
            if (relationshipSelect && relationshipSelect.value === 'other' && otherRelationshipField && otherRelationshipField.value.trim()) {
                // Set the final relationship value to the custom text
                if (finalRelationshipField) {
                    finalRelationshipField.value = otherRelationshipField.value.trim();
                }
            } else if (relationshipSelect && relationshipSelect.value !== 'other') {
                // Use the selected relationship value
                if (finalRelationshipField) {
                    finalRelationshipField.value = relationshipSelect.value;
                }
            }
            
            // Override recipient_relationship with the final value if it exists
            if (finalRelationshipField && finalRelationshipField.value) {
                formData.set('recipient_relationship', finalRelationshipField.value);
            }
            
            // Update the hidden delivery address field
            const deliveryAddressHidden = document.getElementById('deliveryAddressHidden');
            if (deliveryAddressHidden) {
                deliveryAddressHidden.value = deliveryAddress;
            }
            
            const formData = new FormData(checkoutForm);
            if (fee !== '') {
                formData.set('shipping_fee', fee);
            }
            if (deliveryAddress !== '') {
                formData.set('delivery_address', deliveryAddress);
            }
            if (deliveryDate !== '') {
                formData.set('delivery_date', deliveryDate);
            }
            if (deliveryTime !== '') {
                formData.set('delivery_time', deliveryTime);
            }
            if (loyaltyDiscount !== '') {
                formData.set('loyalty_discount', loyaltyDiscount);
            }
            const params = new URLSearchParams(formData);
            e.preventDefault();
            window.location.href = checkoutForm.action + '?' + params.toString();
        });
    }
    
    addressSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const address = selectedOption.getAttribute('data-address');
        const recipientType = document.getElementById('recipientType').value;
        const deliveryAddressInput = document.getElementById('deliveryAddressInput');
        const geocodeBtn = document.getElementById('geocodeBtn');
        
        // Only auto-populate delivery address if "I will receive" is selected
        if (address && address !== 'No saved addresses. Add one in Address Book.' && recipientType === 'self') {
            // Update the delivery address input in the map component
            if (deliveryAddressInput) {
                deliveryAddressInput.value = address;
                // Disable the delivery address field when using default address
                deliveryAddressInput.disabled = true;
                deliveryAddressInput.readOnly = true;
                deliveryAddressInput.style.backgroundColor = '#f8f9fa';
                deliveryAddressInput.style.cursor = 'not-allowed';
                
                // Keep the Find button ENABLED for shipping fee calculation
                if (geocodeBtn) {
                    geocodeBtn.disabled = false;
                    geocodeBtn.style.opacity = '';
                    geocodeBtn.style.cursor = '';
                }
                
                // Trigger the map component's calculation
                deliveryAddressInput.dispatchEvent(new Event('input'));
            }
        }
    });
    
    // Initial calculation if there's a selected address
    // (Removed auto-calc on load; fee will update only after user provides/chooses an address)
});

// Function to update shipping fee display (called by map component)
function updateShippingFeeDisplay(fee) {
    console.log('updateShippingFeeDisplay called with fee:', fee);
    
    // Update the shipping fee display in the checkout summary
    const shippingDisplay = document.getElementById('shippingFeeDisplay');
    if (shippingDisplay) {
        console.log('Found shipping display element, updating to:', fee);
        shippingDisplay.textContent = fee.toFixed(2);
    } else {
        console.log('Shipping display element not found!');
    }
    
    // Update the hidden input for form submission
    const shippingInput = document.getElementById('shippingFeeInput');
    if (shippingInput) {
        console.log('Found shipping input element, updating to:', fee);
        shippingInput.value = fee;
    } else {
        console.log('Shipping input element not found!');
    }
    
    // Update the total only if a valid fee is provided
    const subtotalElement = document.getElementById('cartSubtotal');
    let total = 0;
    if (subtotalElement) {
        const subtotal = parseFloat(subtotalElement.textContent.replace(/,/g, ''));
        total = subtotal + (fee || 0);
        const totalDisplay = document.getElementById('cartTotalFinal');
        if (totalDisplay) {
            console.log('Updating total from', subtotal, 'to', total);
            totalDisplay.textContent = total.toFixed(2);
        } else {
            console.log('Total display element not found!');
        }
    } else {
        console.log('Subtotal element not found!');
    }
    
    console.log('Updated shipping fee:', fee, 'Total:', total);
}

// Recipient type toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const btnSomeone = document.getElementById('btnSomeone');
    const btnSelf = document.getElementById('btnSelf');
    
    if (!btnSomeone || !btnSelf) {
        console.error('Recipient toggle buttons not found!');
        return;
    }
    
    console.log('Recipient toggle buttons found, setting up event listeners');
    
    // Set initial state
    const recipientFields = document.getElementById('recipientFields');
    const selfFields = document.getElementById('selfFields');
    
    console.log('Initial state - recipientFields:', recipientFields ? 'found' : 'not found');
    console.log('Initial state - selfFields:', selfFields ? 'found' : 'not found');
    
    if (recipientFields) {
        console.log('Recipient fields display:', recipientFields.style.display);
    }
    if (selfFields) {
        console.log('Self fields display:', selfFields.style.display);
    }
    
    btnSomeone.onclick = function() {
        console.log('Someone will receive clicked');
        btnSomeone.classList.add('active');
        btnSelf.classList.remove('active');
        document.getElementById('recipientType').value = 'someone';
        
        // Show recipient fields, require inputs
        const recipientFields = document.getElementById('recipientFields');
        const recipientName = document.getElementById('recipientName');
        const recipientPhone = document.getElementById('recipientPhone');
        
        if (recipientFields) {
            recipientFields.style.display = 'block';
            console.log('Showing recipient fields');
        }
        if (recipientName) {
            recipientName.required = true;
        }
        if (recipientPhone) {
            recipientPhone.required = true;
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
        
        // Enable delivery address field and clear it when someone else will receive
        const deliveryAddressInput = document.getElementById('deliveryAddressInput');
        const geocodeBtn = document.getElementById('geocodeBtn');
        if (deliveryAddressInput) {
            deliveryAddressInput.value = '';
            deliveryAddressInput.disabled = false;
            deliveryAddressInput.readOnly = false;
            deliveryAddressInput.style.backgroundColor = '';
            deliveryAddressInput.style.cursor = '';
        }
        // Keep Find button enabled for shipping fee calculation
        if (geocodeBtn) {
            geocodeBtn.disabled = false;
            geocodeBtn.style.opacity = '';
            geocodeBtn.style.cursor = '';
        }
    };
    
    btnSelf.onclick = function() {
        console.log('I will receive clicked');
        btnSelf.classList.add('active');
        btnSomeone.classList.remove('active');
        document.getElementById('recipientType').value = 'self';
        
        // Hide recipient fields, remove required
        const recipientFields = document.getElementById('recipientFields');
        const recipientName = document.getElementById('recipientName');
        const recipientPhone = document.getElementById('recipientPhone');
        
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
        
        if (selfFields) {
            selfFields.style.display = 'block';
            console.log('Showing self fields');
        }
        if (selfPhone) {
            selfPhone.required = true;
        }
        
        // Auto-populate default address when "I will receive" is selected
        populateDefaultAddress();
        
        // Ensure Find button remains enabled for shipping fee calculation
        const geocodeBtn = document.getElementById('geocodeBtn');
        if (geocodeBtn) {
            geocodeBtn.disabled = false;
            geocodeBtn.style.opacity = '';
            geocodeBtn.style.cursor = '';
        }
    };
});

// Loyalty redemption functionality
document.addEventListener('DOMContentLoaded', function() {
    const redeemBtn = document.getElementById('redeemLoyaltyBtn');
    if (redeemBtn) {
        redeemBtn.addEventListener('click', function() {
            // Visual feedback
            this.classList.add('active');
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Redeeming...';
            this.disabled = true;
            
            // Simulate redemption process
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check me-1"></i>Redeemed!';
                this.classList.remove('active');
                this.classList.add('btn-outline-success');
                
                // Show success message
                const feedback = document.getElementById('loyaltyFeedback');
                if (feedback) {
                    feedback.innerHTML = '<div class="alert alert-success alert-sm mb-0"><i class="fas fa-gift me-1"></i>Loyalty discount applied! 50% off your most expensive bouquet.</div>';
                }
                
                // Update totals (this would be handled by server in real implementation)
                updateTotals();
            }, 1500);
        });
    }
});

function updateTotals() {
    // This function would recalculate totals with loyalty discount
    // For now, just show visual feedback
    console.log('Totals updated with loyalty discount');
}

// Auto-scroll to error field if validation fails
document.addEventListener('DOMContentLoaded', function() {
    // Auto capitalization is now handled by the global script

    // Reset button state if there are validation errors
    const submitBtn = document.getElementById('proceedBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Proceed to Payment Method';
        submitBtn.disabled = false;
    }
    
    @error('recipient_phone')
        // Scroll to the phone number field
        setTimeout(() => {
            const phoneField = document.getElementById('recipientPhone') || document.getElementById('selfPhone');
            if (phoneField) {
                phoneField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                phoneField.focus();
                phoneField.classList.add('is-invalid');
            }
        }, 500);
    @enderror
});

// Add loading state to submit button
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('proceedBtn');
    
    // Debug: Log form data
    const formData = new FormData(this);
    console.log('Form submission data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    submitBtn.disabled = true;
    
    // Reset button state after 5 seconds in case of errors
    setTimeout(() => {
        submitBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Proceed to Payment Method';
        submitBtn.disabled = false;
    }, 5000);
});

// Store Credit Functionality
document.addEventListener('DOMContentLoaded', function() {
    const useStoreCreditCheckbox = document.getElementById('useStoreCredit');
    const storeCreditAmountDiv = document.getElementById('storeCreditAmountDiv');
    const storeCreditAmountInput = document.getElementById('storeCreditAmount');
    const storeCreditDiscount = document.getElementById('storeCreditDiscount');
    const storeCreditDiscountDisplay = document.getElementById('storeCreditDiscountDisplay');
    
    if (useStoreCreditCheckbox) {
        useStoreCreditCheckbox.addEventListener('change', function() {
            if (this.checked) {
                storeCreditAmountDiv.style.display = 'block';
                storeCreditDiscount.style.display = 'flex';
                // Set default amount to the total amount needed (subtotal + shipping fee)
                const subtotal = parseFloat(document.getElementById('cartSubtotal').textContent.replace(/,/g, '')) || 0;
                const shippingFee = parseFloat(document.getElementById('shippingFeeDisplay').textContent.replace(/,/g, '')) || 0;
                const totalNeeded = subtotal + shippingFee;
                const maxAmount = parseFloat(storeCreditAmountInput.getAttribute('max'));
                
                // Use the smaller of: total needed or max balance
                const amountToUse = Math.min(totalNeeded, maxAmount);
                storeCreditAmountInput.value = amountToUse.toFixed(2);
                updateStoreCreditDiscount();
                
                // Show info message if store credit is insufficient
                if (maxAmount < totalNeeded) {
                    const remainingAmount = totalNeeded - maxAmount;
                    showStoreCreditInfo(`Store Credit (₱${maxAmount.toFixed(2)}) will be used. You'll need to pay ₱${remainingAmount.toFixed(2)} via other payment method.`);
                } else {
                    hideStoreCreditInfo();
                }
            } else {
                storeCreditAmountDiv.style.display = 'none';
                storeCreditDiscount.style.display = 'none';
                storeCreditAmountInput.value = '';
                updateTotal();
            }
        });
    }
    
    if (storeCreditAmountInput) {
        storeCreditAmountInput.addEventListener('input', function() {
            updateStoreCreditDiscount();
        });
    }
    
    function updateStoreCreditDiscount() {
        const amount = parseFloat(storeCreditAmountInput.value) || 0;
        const maxAmount = parseFloat(storeCreditAmountInput.getAttribute('max'));
        
        // Calculate total needed (subtotal + shipping fee)
        const subtotal = parseFloat(document.getElementById('cartSubtotal').textContent.replace(/,/g, '')) || 0;
        const shippingFee = parseFloat(document.getElementById('shippingFeeDisplay').textContent.replace(/,/g, '')) || 0;
        const totalNeeded = subtotal + shippingFee;
        
        // Validate amount - cannot exceed balance or total needed
        const maxAllowed = Math.min(maxAmount, totalNeeded);
        
        if (amount > maxAllowed) {
            storeCreditAmountInput.value = maxAllowed.toFixed(2);
            amount = maxAllowed;
        }
        
        if (amount < 0) {
            storeCreditAmountInput.value = '0.00';
            amount = 0;
        }
        
        // Update display
        storeCreditDiscountDisplay.textContent = amount.toFixed(2);
        
        // Update total
        updateTotal();
    }
    
    function showStoreCreditInfo(message) {
        let infoDiv = document.getElementById('storeCreditInfo');
        if (!infoDiv) {
            infoDiv = document.createElement('div');
            infoDiv.id = 'storeCreditInfo';
            infoDiv.className = 'alert alert-info mt-2';
            infoDiv.style.cssText = 'font-size: 0.9rem; padding: 0.75rem; border-radius: 6px;';
            document.getElementById('storeCreditAmountDiv').appendChild(infoDiv);
        }
        infoDiv.innerHTML = `<i class="fas fa-info-circle me-2"></i>${message}`;
        infoDiv.style.display = 'block';
    }
    
    function hideStoreCreditInfo() {
        const infoDiv = document.getElementById('storeCreditInfo');
        if (infoDiv) {
            infoDiv.style.display = 'none';
        }
    }
    
    function updateTotal() {
        const subtotalElement = document.getElementById('cartSubtotal');
        const shippingFeeElement = document.getElementById('shippingFeeDisplay');
        const loyaltyDiscountElement = document.getElementById('loyaltyDiscountDisplay');
        const totalElement = document.getElementById('cartTotalFinal');
        
        if (!subtotalElement || !totalElement) return;
        
        const subtotal = parseFloat(subtotalElement.textContent.replace(/,/g, '')) || 0;
        const shippingFee = parseFloat(shippingFeeElement.textContent.replace(/,/g, '')) || 0;
        const loyaltyDiscount = parseFloat(loyaltyDiscountElement.textContent.replace(/,/g, '')) || 0;
        const storeCreditAmount = parseFloat(storeCreditAmountInput.value) || 0;
        
        const total = subtotal + shippingFee - loyaltyDiscount - storeCreditAmount;
        
        totalElement.textContent = total.toFixed(2);
    }
});
</script>
@endpush

<style>
/* Custom scrollbar styling for the left content area */
.scrollable-content::-webkit-scrollbar {
    width: 8px;
}

.scrollable-content::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 4px;
}

.scrollable-content::-webkit-scrollbar-thumb {
    background: #8ACB88;
    border-radius: 4px;
}

.scrollable-content::-webkit-scrollbar-thumb:hover {
    background: #7bb47b;
}

/* For Firefox */
.scrollable-content {
    scrollbar-width: thin;
    scrollbar-color: #8ACB88 transparent;
}

/* Custom scrollbar styling for the right content area */
.scrollable-right-content::-webkit-scrollbar {
    width: 8px;
}

.scrollable-right-content::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 4px;
}

.scrollable-right-content::-webkit-scrollbar-thumb {
    background: #8ACB88;
    border-radius: 4px;
}

.scrollable-right-content::-webkit-scrollbar-thumb:hover {
    background: #7bb47b;
}

/* For Firefox */
.scrollable-right-content {
    scrollbar-width: thin;
    scrollbar-color: #8ACB88 transparent;
}

/* Ensure equal height columns on desktop */
@media (min-width: 992px) {
    .row.justify-content-center {
        align-items: stretch;
    }
    .checkout-form-section,
    .purchase-summary-section {
        display: flex;
        flex-direction: column;
    }
    .scrollable-content {
        flex: 1;
        max-height: 83vh; /* taller to remove bottom gap while keeping scroll */
        overflow-y: auto;
    }
    .purchase-summary-card {
        max-height: 83vh; /* taller to match left section */
        overflow-y: auto;
    }
    .purchase-summary-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
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

/* Mobile Responsive Styles for Checkout */
@media (max-width: 991.98px) {
    .checkout-container {
        padding-bottom: 1rem;
    }
    .checkout-form-section {
        margin-bottom: 1rem;
    }
    .purchase-summary-section {
        margin-top: 0 !important;
        margin-bottom: 2rem;
    }
    .purchase-summary-card {
        position: relative !important;
        max-height: none !important;
    }
    .purchase-summary-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    .purchase-summary-content {
        max-height: none !important;
        overflow-y: visible !important;
    }
    .scrollable-content {
        max-height: none !important;
        overflow-y: visible !important;
    }
}

@media (max-width: 650px) {
    .checkout-container {
        padding: 0.5rem 0.25rem 1rem 0.25rem !important; /* No need for extra bottom padding since navbar is hidden */
    }
    .checkout-form-section,
    .purchase-summary-section {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
    .purchase-summary-section {
        margin-bottom: 1rem !important; /* Reduced margin since navbar is hidden */
    }
    .purchase-summary-card {
        padding: 0.75rem !important;
        padding-bottom: 2rem !important; /* Extra bottom padding in card */
        font-size: 0.9rem;
        margin-bottom: 30px;
    }
    .purchase-summary-header {
        font-size: 1rem !important;
    }
    .purchase-summary-header .mb-3 {
        margin-bottom: 0.75rem !important;
        font-size: 1rem !important;
    }
    .purchase-summary-content {
        font-size: 0.9rem;
        padding-bottom: 30px !important; /* Extra space at bottom */
    }
    .purchase-summary-content .d-flex {
        font-size: 0.85rem;
    }
    .purchase-summary-content img {
        width: 45px !important;
        height: 45px !important;
    }
    .purchase-summary-content .badge {
        font-size: 0.75rem !important;
        padding: 6px 10px !important;
    }
    #proceedBtn {
        font-size: 0.95rem !important;
        padding: 12px !important;
        margin-bottom: 1rem !important; /* Normal spacing since navbar is hidden */
        margin-top: 20px !important; /* Space above button */
    }
    .scrollable-content {
        padding-bottom: 30px; /* Extra space for scrollable content */
    }
    /* Ensure purchase summary section has proper spacing */
    .purchase-summary-section .purchase-summary-card {
        margin-bottom: 0;
    }
    
    /* Custom Bouquet Modal Responsive */
    @media (max-width: 767.98px) {
        .custom-bouquet-modal .modal-dialog {
            width: 95vw !important;
            max-width: 95vw !important;
            margin: 5vh auto !important;
        }
        
        .custom-bouquet-modal .modal-body {
            padding: 1rem !important;
            max-height: 75vh !important;
        }
        
        .custom-bouquet-modal .modal-body img {
            max-height: 250px !important;
        }
        
        .custom-bouquet-modal .table {
            font-size: 0.85rem;
        }
        
        .custom-bouquet-modal .table th,
        .custom-bouquet-modal .table td {
            padding: 0.5rem 0.25rem !important;
        }
        
        .custom-bouquet-modal {
            z-index: 6000 !important;
        }
        
        .modal-backdrop.show {
            z-index: 5990 !important;
        }
        
        .component-preview-container {
            width: 90vw !important;
            max-width: 350px !important;
            height: 350px !important;
        }
    }
    
    /* Mobile optimization for 650px and below - Make modal smaller and more compact */
    @media (max-width: 650px) {
        .custom-bouquet-modal .modal-dialog {
            width: 95vw !important;
            max-width: 95vw !important;
            margin: 10px auto !important;
            max-height: 90vh;
        }
        
        .custom-bouquet-modal .modal-content {
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            border-radius: 10px !important;
        }
        
        .custom-bouquet-modal .modal-header {
            padding: 8px 12px !important;
        }
        
        .custom-bouquet-modal .modal-title {
            font-size: 0.85rem !important;
        }
        
        .custom-bouquet-modal .modal-body {
            padding: 12px !important;
            max-height: calc(90vh - 100px) !important;
        }
        
        /* Reduce bouquet preview image size */
        .custom-bouquet-modal .modal-body img {
            max-width: 180px !important;
            max-height: 180px !important;
        }
        
        .custom-bouquet-modal .component-preview-container {
            width: 180px !important;
            height: 180px !important;
        }
        
        /* Reduce text sizes - keep original table design */
        .custom-bouquet-modal .modal-body h6 {
            font-size: 0.8rem !important;
            margin-bottom: 10px !important;
            padding-bottom: 5px !important;
        }
        
        .custom-bouquet-modal .table {
            font-size: 0.75rem !important;
        }
        
        .custom-bouquet-modal .table th {
            font-size: 0.7rem !important;
            padding: 0.4rem 0.3rem !important;
        }
        
        .custom-bouquet-modal .table td {
            font-size: 0.75rem !important;
            padding: 0.4rem 0.3rem !important;
        }
        
        .custom-bouquet-modal .table tfoot td {
            font-size: 0.75rem !important;
            padding: 0.5rem 0.3rem !important;
        }
        
        .custom-bouquet-modal .modal-footer {
            padding: 8px 12px !important;
        }
        
        .custom-bouquet-modal .modal-footer .btn {
            padding: 6px 14px !important;
            font-size: 0.8rem !important;
        }
    }
}
</style>
    </div>
</div>

<!-- Custom Bouquet Detail Modal -->
@foreach($cartItems as $item)
    @if($item->item_type === 'custom_bouquet')
        @php
            $bouquet = $item->customBouquet ?? null;
            if (!$bouquet && isset($item->custom_bouquet_id)) {
                $bouquet = \App\Models\CustomBouquet::find($item->custom_bouquet_id);
            }
            $modalId = $bouquet ? $bouquet->id : (isset($item->custom_bouquet_id) ? $item->custom_bouquet_id : 'temp');
            $items = \App\Models\CustomizeItem::where('status', true)->get();
            $assemblyFee = \App\Models\Setting::get('assembling_fee', 150);
            $components = [];
            $totalPrice = $assemblyFee;
            
            // Get wrapper
            if ($bouquet->wrapper) {
                $item_data = $items->firstWhere('name', $bouquet->wrapper);
                if ($item_data) {
                    $components[] = [
                        'category' => 'Wrapper',
                        'name' => $bouquet->wrapper,
                        'quantity' => 1,
                        'price' => $item_data->price ?? 0
                    ];
                    $totalPrice += $item_data->price ?? 0;
                }
            }
            
            // Get flowers
            $freshQty = data_get($bouquet->customization_data, 'fresh_flower_qty', 1);
            foreach (['focal_flower_1', 'focal_flower_2', 'focal_flower_3'] as $flowerField) {
                if ($bouquet->$flowerField) {
                    $item_data = $items->firstWhere('name', $bouquet->$flowerField);
                    if ($item_data) {
                        $components[] = [
                            'category' => 'Fresh Flowers',
                            'name' => $bouquet->$flowerField,
                            'quantity' => $freshQty,
                            'price' => $item_data->price ?? 0
                        ];
                        $totalPrice += ($item_data->price ?? 0) * max(1, (int) $freshQty);
                    }
                }
            }
            
            // Get greenery
            if ($bouquet->greenery) {
                $item_data = $items->firstWhere('name', $bouquet->greenery);
                if ($item_data) {
                    $components[] = [
                        'category' => 'Greenery',
                        'name' => $bouquet->greenery,
                        'quantity' => 1,
                        'price' => $item_data->price ?? 0
                    ];
                    $totalPrice += $item_data->price ?? 0;
                }
            }
            
            // Get filler
            if ($bouquet->filler) {
                $item_data = $items->firstWhere('name', $bouquet->filler);
                if ($item_data) {
                    $artQty = data_get($bouquet->customization_data, 'artificial_flower_qty', 1);
                    $components[] = [
                        'category' => 'Artificial Flowers',
                        'name' => $bouquet->filler,
                        'quantity' => $artQty,
                        'price' => $item_data->price ?? 0
                    ];
                    $totalPrice += ($item_data->price ?? 0) * max(1, (int) $artQty);
                }
            }
            
            // Get ribbon
            if ($bouquet->ribbon) {
                $item_data = $items->firstWhere('name', $bouquet->ribbon);
                if ($item_data) {
                    $components[] = [
                        'category' => 'Ribbon',
                        'name' => $bouquet->ribbon,
                        'quantity' => 1,
                        'price' => $item_data->price ?? 0
                    ];
                    $totalPrice += $item_data->price ?? 0;
                }
            }
            
            // Add assembly fee
            $components[] = [
                'category' => 'Service',
                'name' => 'Assembly Fee',
                'quantity' => 1,
                'price' => $assemblyFee
            ];
        @endphp
        
        <div class="modal fade custom-bouquet-modal" id="customBouquetModal{{ $modalId }}" tabindex="-1" aria-labelledby="customBouquetModalLabel{{ $modalId }}" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content" style="border-radius: 12px;">
                    <div class="modal-header" style="border-bottom: 1px solid #e9ecef;">
                        <h6 class="modal-title" id="customBouquetModalLabel{{ $modalId }}" style="font-weight: 600; color: #222; font-size: 1rem;">Custom Bouquet Details</h6>
                    </div>
                    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                        <!-- Bouquet Preview Image -->
                        <div class="text-center mb-3">
                            @php
                                // Get raw preview_image value from database (not accessor)
                                $rawPreviewPath = $bouquet->getAttributes()['preview_image'] ?? null;
                                $isComposite = $rawPreviewPath && (strpos($rawPreviewPath, 'custom_bouquets/') === 0 || strpos($rawPreviewPath, 'custom_bouquet_') !== false);
                                
                                // Build component images array
                                $componentImages = [];
                                $flowerIndex = 0;
                                
                                // Wrapper (background) - matches customize page: fills container
                                if ($bouquet->wrapper) {
                                    $wrapperItem = $items->firstWhere('name', $bouquet->wrapper);
                                    if ($wrapperItem && $wrapperItem->image && file_exists(storage_path('app/public/' . $wrapperItem->image))) {
                                        $componentImages[] = [
                                            'image' => asset('storage/' . $wrapperItem->image), 
                                            'type' => 'wrapper',
                                            'z' => 10,
                                            'style' => 'inset: 0; width: 100%; height: 100%; object-fit: cover;'
                                        ];
                                    }
                                }
                                
                                // Flowers (overlay) - matches customize page positioning
                                foreach (['focal_flower_1', 'focal_flower_2', 'focal_flower_3'] as $flowerField) {
                                    if ($bouquet->$flowerField) {
                                        $flowerItem = $items->firstWhere('name', $bouquet->$flowerField);
                                        if ($flowerItem && $flowerItem->image && file_exists(storage_path('app/public/' . $flowerItem->image))) {
                                            // Match customize page: first flower centered, others positioned relative
                                            if ($flowerIndex == 0) {
                                                // First flower: matches customize page style (centered, 45% from bottom)
                                                $style = 'width: 20%; height: auto; object-fit: contain; bottom: 45%; left: 44%; transform: translateX(-50%);';
                                            } elseif ($flowerIndex == 1) {
                                                // Second flower: positioned to the right
                                                $style = 'width: 18%; height: auto; object-fit: contain; bottom: 45%; left: 56%; transform: translateX(-50%);';
                                            } else {
                                                // Third flower: positioned to the left
                                                $style = 'width: 18%; height: auto; object-fit: contain; bottom: 45%; left: 32%; transform: translateX(-50%);';
                                            }
                                            $componentImages[] = [
                                                'image' => asset('storage/' . $flowerItem->image), 
                                                'type' => 'flower',
                                                'z' => 60,
                                                'style' => $style
                                            ];
                                            $flowerIndex++;
                                        }
                                    }
                                }
                                
                                // Greenery - matches customize page: 42% width, centered, 44% from bottom
                                if ($bouquet->greenery) {
                                    $greeneryItem = $items->firstWhere('name', $bouquet->greenery);
                                    if ($greeneryItem && $greeneryItem->image && file_exists(storage_path('app/public/' . $greeneryItem->image))) {
                                        $componentImages[] = [
                                            'image' => asset('storage/' . $greeneryItem->image), 
                                            'type' => 'greenery',
                                            'z' => 20,
                                            'style' => 'width: 42%; height: auto; object-fit: contain; bottom: 44%; left: 50%; transform: translateX(-50%); opacity: 0.95;'
                                        ];
                                    }
                                }
                                
                                // Filler - matches customize page: 20% width, right side, 45% from bottom
                                if ($bouquet->filler) {
                                    $fillerItem = $items->firstWhere('name', $bouquet->filler);
                                    if ($fillerItem && $fillerItem->image && file_exists(storage_path('app/public/' . $fillerItem->image))) {
                                        $componentImages[] = [
                                            'image' => asset('storage/' . $fillerItem->image), 
                                            'type' => 'filler',
                                            'z' => 25,
                                            'style' => 'width: 20%; height: auto; object-fit: contain; bottom: 45%; left: 56%; transform: translateX(-50%);'
                                        ];
                                    }
                                }
                                
                                // Ribbon (top layer) - matches customize page: 20% width, centered, 29% from bottom
                                if ($bouquet->ribbon) {
                                    $ribbonItem = $items->firstWhere('name', $bouquet->ribbon);
                                    if ($ribbonItem && $ribbonItem->image && file_exists(storage_path('app/public/' . $ribbonItem->image))) {
                                        $componentImages[] = [
                                            'image' => asset('storage/' . $ribbonItem->image), 
                                            'type' => 'ribbon',
                                            'z' => 80,
                                            'style' => 'width: 20%; height: auto; object-fit: contain; bottom: 29%; left: 50%; transform: translateX(-50%);'
                                        ];
                                    }
                                }
                            @endphp
                            
                            @if($isComposite && $rawPreviewPath && file_exists(storage_path('app/public/' . $rawPreviewPath)))
                                <!-- Composite image (GD generated) -->
                                <img src="{{ $bouquet->preview_image }}" alt="Custom Bouquet Preview" style="max-width: 100%; max-height: 250px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            @elseif(count($componentImages) > 0)
                                <!-- CSS-based component layering (fallback when GD not available) -->
                                <div class="component-preview-container" style="position: relative; width: 250px; height: 250px; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; background: #fff;">
                                    @foreach($componentImages as $comp)
                                        <img src="{{ $comp['image'] }}" 
                                             alt="Component {{ $comp['type'] }}" 
                                             style="position: absolute; z-index: {{ $comp['z'] }}; {{ $comp['style'] }}">
                                    @endforeach
                                </div>
                            @else
                                <!-- Fallback: generic image -->
                                <img src="{{ asset('images/landingpage_bouquet/bokk.png') }}" alt="Custom Bouquet Preview" style="max-width: 100%; max-height: 250px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            @endif
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div class="mb-3">
                            <h6 style="font-weight: 600; color: #222; margin-bottom: 12px; border-bottom: 2px solid #7bb47b; padding-bottom: 6px; font-size: 0.95rem;">Price Summary</h6>
                            
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th style="font-size: 0.75rem; font-weight: 600; color: #555;">Category</th>
                                            <th style="font-size: 0.75rem; font-weight: 600; color: #555;">Component</th>
                                            <th style="font-size: 0.75rem; font-weight: 600; color: #555; text-align: center;">Quantity</th>
                                            <th style="font-size: 0.75rem; font-weight: 600; color: #555; text-align: right;">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($components as $component)
                                        <tr>
                                            <td style="font-size: 0.8rem; color: #666;">{{ $component['category'] }}</td>
                                            <td style="font-size: 0.8rem; color: #222; font-weight: 500;">{{ $component['name'] }}</td>
                                            <td style="font-size: 0.8rem; color: #666; text-align: center;">{{ $component['quantity'] }}</td>
                                            <td style="font-size: 0.8rem; color: #222; text-align: right; font-weight: 500;">₱{{ number_format($component['price'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="border-top: 2px solid #dee2e6;">
                                        <tr>
                                            <td colspan="3" style="font-size: 0.85rem; font-weight: 600; color: #222; padding-top: 10px;">Total Price:</td>
                                            <td style="font-size: 0.9rem; font-weight: 600; color: #7bb47b; text-align: right; padding-top: 10px;">₱{{ number_format($totalPrice, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 0.8rem; color: #666; padding-top: 6px;">Quantity:</td>
                                            <td style="font-size: 0.8rem; color: #222; text-align: right; padding-top: 6px; font-weight: 500;">{{ $item->quantity }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 0.85rem; font-weight: 600; color: #222; padding-top: 6px;">Subtotal:</td>
                                            <td style="font-size: 0.9rem; font-weight: 600; color: #7bb47b; text-align: right; padding-top: 6px;">₱{{ number_format($totalPrice * $item->quantity, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection 
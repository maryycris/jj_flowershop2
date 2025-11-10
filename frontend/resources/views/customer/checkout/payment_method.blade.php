@extends('layouts.customer_app')

@section('content')
@include('components.customer.alt_nav', ['active' => 'home'])
<style>
    /* Custom scrollbar styling for transparent tracks */
    .scrollable-content::-webkit-scrollbar {
        width: 8px;
        background: transparent;
    }
    
    .scrollable-content::-webkit-scrollbar-track {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    .scrollable-content::-webkit-scrollbar-thumb {
        background: #7bb47b !important;
        border-radius: 4px;
        border: none !important;
    }
    
    .scrollable-content::-webkit-scrollbar-thumb:hover {
        background: #5a9c5a !important;
    }
    
    /* Force transparent track */
    .scrollable-content::-webkit-scrollbar-corner {
        background: transparent !important;
    }
    
    /* Additional overrides for complete transparency */
    .scrollable-content::-webkit-scrollbar-track-piece {
        background: transparent !important;
    }
</style>
<div class="pt-0 payment-method-container" style="background: #f4faf4; min-height: 100vh;">
    <div class="container" style="max-width: 1400px;">
    
    <form action="{{ route('customer.checkout.process') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="recipient_type" value="{{ session('checkout_data.recipient_type', 'someone') }}">
        <input type="hidden" name="delivery_address" value="{{ session('checkout_data.delivery_address', '') }}">
        <input type="hidden" name="recipient_name" value="{{ session('checkout_data.recipient_name', '') }}">
        <input type="hidden" name="recipient_phone" value="{{ session('checkout_data.recipient_phone', '') }}">
        <input type="hidden" name="delivery_date" value="{{ session('checkout_data.delivery_date', '') }}">
        <input type="hidden" name="delivery_time" value="{{ session('checkout_data.delivery_time', '') }}">
        <input type="hidden" name="shipping_fee" value="{{ $shippingFee }}">
        <input type="hidden" name="promo_code" value="{{ session('checkout_data.promo_code', '') }}">
        <input type="hidden" name="use_store_credit" value="{{ $useStoreCredit ? '1' : '0' }}">
        <input type="hidden" name="store_credit_amount" value="{{ $storeCreditAmount }}">
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
        
        
        <div class="row justify-content-center">
            <!-- Payment Methods Column -->
            <div class="col-12 col-lg-8 col-xl-6 order-1 order-lg-1 payment-methods-section">
                <div class="bg-white rounded-3 p-3 p-md-4 mb-3 mb-lg-4 scrollable-content payment-methods-card" style="box-shadow: none; overflow-y: auto;">
                    <div class="mb-3">
                        <a href="{{ route('customer.checkout.index') }}" class="btn btn-outline-success">
                            &larr; Return to delivery information
                        </a>
                    </div>
                    <h4 class="mb-2" style="font-weight: 600; color: #222;">Payment Methods</h4>
                    @php
                        $stamps = optional(App\Models\LoyaltyCard::firstWhere('user_id', Auth::id()))->stamps_count ?? 0;
                    @endphp
                    <div class="mb-3 small text-muted">Loyalty Stamps: <strong>{{ $stamps }}/5</strong></div>
                    @if($stamps >= 5)
                        <div class="alert alert-success py-2 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="useLoyalty" />
                                <label class="form-check-label" for="useLoyalty">
                                    Use 50% Loyalty Discount on one bouquet
                                </label>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Delivery Schedule Display -->
                    @if(session('checkout_data.delivery_date') && session('checkout_data.delivery_time'))
                    <div class="mb-4">
                        <div class="p-3" style="background: linear-gradient(135deg, #e8f5e8, #f0f8f0); border-radius: 8px; border-left: 4px solid #8ACB88;">
                            <h6 class="mb-2" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-calendar-check me-2 text-success"></i>Delivery Schedule
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Delivery Date</small>
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse(session('checkout_data.delivery_date'))->format('M d, Y') }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Delivery Time</small>
                                    <span class="fw-semibold">{{ session('checkout_data.delivery_time') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Credit/Debit Cards Section -->
                    <h6 class="mb-3" style="font-weight: 600; color: #555; border-bottom: 1px solid #eee; padding-bottom: 8px;">💳 Credit/Debit Cards</h6>
                    
                    <!-- Credit/Debit Card Selection (Opens Modal) -->
                    <div class="payment-option-card mb-3" data-payment="card_modal" data-bs-toggle="modal" data-bs-target="#cardSelectionModal">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #1a1a1a; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-credit-card" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">Credit/Debit Card</div>
                                    <div style="font-size: 0.9rem; color: #666;">Choose your bank and card type</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    
                    <!-- Payment Restrictions Warning -->
                    <div class="alert alert-warning mb-3" style="border-left: 4px solid #ffc107; background-color: #fff3cd; border-color: #ffeaa7;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle text-warning me-2 mt-1"></i>
                            <div>
                                <strong>Payment Restrictions:</strong>
                                <ul class="mb-0 mt-1" style="font-size: 0.9rem;">
                                    <li><strong>Credit Cards:</strong> Can only be used to pay with debit cards</li>
                                    <li><strong>Debit Cards:</strong> Can only be used to pay with debit cards</li>
                                    <li><strong>Not Allowed:</strong> Credit to Credit payments</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- E-Wallets Section -->
                    <h6 class="mb-3 mt-4" style="font-weight: 600; color: #555; border-bottom: 1px solid #eee; padding-bottom: 8px;">📱 E-Wallets</h6>
                    
                    <!-- GCash Payment Option -->
                    <div class="payment-option-card mb-3" data-payment="gcash">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #00d4aa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: white; font-weight: bold; font-size: 1.2rem;">G</span>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">GCash</div>
                                    <div style="font-size: 0.9rem; color: #666;">Pay with GCash wallet</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    
                    <!-- PayMaya Payment Option -->
                    <div class="payment-option-card mb-3" data-payment="paymaya">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #00a3ef; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: white; font-weight: bold; font-size: 1.2rem;">PM</span>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">PayMaya</div>
                                    <div style="font-size: 0.9rem; color: #666;">Pay with PayMaya wallet</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>

                    
                    
                    <!-- Other Payment Methods Section -->
                    <h6 class="mb-3 mt-4" style="font-weight: 600; color: #555; border-bottom: 1px solid #eee; padding-bottom: 8px;">💰 Other Payment Methods</h6>
                    
                    <!-- Store Credit Payment Option -->
                    @if($useStoreCredit && $storeCreditAmount > 0)
                    <div class="payment-option-card mb-3" data-payment="store_credit">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #ffc107; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-wallet" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">Store Credit Payment</div>
                                    @if($finalTotal <= 0)
                                        <div style="font-size: 0.9rem; color: #28a745;">✅ FREE ORDER - Store Credit covers everything!</div>
                                    @else
                                        <div style="font-size: 0.9rem; color: #666;">Store Credit (₱{{ number_format($storeCreditAmount, 2) }}) + Pay ₱{{ number_format($finalTotal, 2) }} via other method</div>
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    @endif
                    
                    <!-- COD Payment Option -->
                    <div class="payment-option-card mb-3" data-payment="cod">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #f39c12; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-money-bill-wave" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">COD (Cash on Delivery)</div>
                                    <div style="font-size: 0.9rem; color: #666;">Pay when you receive your order</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    
                    <input type="hidden" name="payment_method" id="selectedPaymentMethod" required>
                    <input type="hidden" name="use_loyalty" id="useLoyaltyInput" value="0">
                    
                    <div class="d-flex justify-content-between flex-wrap gap-2 mt-4">
                        <a href="{{ route('customer.checkout.index') }}" class="btn btn-outline-success btn-sm">
                            &larr; Return
                        </a>
                        <button type="submit" class="btn btn-success" id="completeOrderBtn">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Purchase Summary Column - Always Visible -->
            @php
                $feeParam = request()->query('shipping_fee');
                if (!is_null($feeParam)) {
                    $sanitized = is_numeric($feeParam) ? (string)$feeParam : preg_replace('/[^0-9.]/', '', (string)$feeParam);
                    if ($sanitized !== '' && is_numeric($sanitized)) {
                        $shippingFee = (float)$sanitized;
                    }
                }
            @endphp
            <div class="col-12 col-lg-4 col-xl-4 order-2 order-lg-2 purchase-summary-section">
                <div class="bg-white rounded-3 p-3 p-md-4 purchase-summary-card" style="box-shadow: none;">
                    <h4 class="mb-3 mb-md-4" style="font-weight: 600; color: #222; font-size: 1.15rem;">Purchase Summary</h4>
                    
                    @foreach($cartItems as $item)
                    <div class="d-flex align-items-center mb-3">
                        @if($item->item_type === 'custom_bouquet')
                            <!-- Custom Bouquet Display -->
                            <div class="flex-grow-1">
                                @php
                                    $modalId = $item->customBouquet ? $item->customBouquet->id : (isset($item->custom_bouquet_id) ? $item->custom_bouquet_id : 'temp');
                                @endphp
                                <button type="button" class="btn btn-link p-0 text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#customBouquetModal{{ $modalId }}" style="font-weight: 500; color: #7bb47b; font-size: 1rem;">
                                    Custom Bouquet
                                </button>
                                <div style="font-size: 0.9rem; color: #666;">Quantity: {{ $item->quantity }}</div>
                            </div>
                            <div class="text-end">
                                <div style="font-weight: 600; color: #222;">₱{{ number_format($item->quantity * ($item->customBouquet ? ($item->customBouquet->unit_price ?? $item->customBouquet->total_price) : 0), 2) }}</div>
                            </div>
                        @else
                            <!-- Regular Product Display -->
                            <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            <div class="flex-grow-1 ms-3">
                                <div style="font-weight: 500; color: #222;">{{ $item->product->name }}</div>
                                <div style="font-size: 0.9rem; color: #666;">Quantity: {{ $item->quantity }}</div>
                            </div>
                            <div class="text-end">
                                <div style="font-weight: 600; color: #222;">₱{{ number_format($item->quantity * ($item->product ? $item->product->price : 0), 2) }}</div>
                            </div>
                        @endif
                    </div>
                    @endforeach
                    
                    <hr>
                    
                    <!-- Loyalty Stamps Section -->
                    <div class="mb-3">
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
                                
                                @if($loyaltyCard && $loyaltyCard->stamps_count >= 4)
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fas fa-gift text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        @if($loyaltyDiscount > 0)
                                            <p class="text-success fw-bold mb-2">🎉 Automatic 50% discount applied to your most expensive item!</p>
                                            <div class="alert alert-success py-2 px-3 mb-2">
                                                <i class="fas fa-check-circle me-2"></i>
                                                @if($discountedItem->item_type === 'custom_bouquet')
                                                    <strong>Custom Bouquet</strong> - 50% OFF!
                                                @else
                                                    <strong>{{ $discountedItem->product->name }}</strong> - 50% OFF!
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-success fw-bold mb-2">🎉 You have {{ $loyaltyCard->stamps_count }}/5 stamps! 50% discount will be applied automatically!</p>
                                        @endif
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
                    
                    <!-- Cost Breakdown -->
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #666;">Subtotal ({{ count($cartItems) }} Item{{ count($cartItems) == 1 ? '' : 's' }})</span>
                        <span style="color: #222;">₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #666;">Shipping Fee</span>
                        <span style="color: #222;">₱{{ number_format($shippingFee, 2) }}</span>
                    </div>
                    @if($loyaltyDiscount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #28a745; font-weight: 600;">
                            <i class="fas fa-gift me-1"></i>Loyalty Discount (50% OFF)
                        </span>
                        <span style="color: #28a745; font-weight: 600;">-₱{{ number_format($loyaltyDiscount, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($useStoreCredit && $storeCreditAmount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #28a745;">Store Credit Used</span>
                        <span style="color: #28a745; font-weight: 600;">-₱{{ number_format($storeCreditAmount, 2) }}</span>
                    </div>
                    @endif
                    
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight: 600; font-size: 1.1rem;">Total</span>
                        <span style="color: #7bb47b; font-weight: 600; font-size: 1.2rem;">₱{{ number_format($finalTotal, 2) }}</span>
                    </div>
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
    .payment-option-card:hover > div {
        border-color: #7bb47b !important;
        background: #f8faf8;
    }
    .payment-option-card.selected > div {
        border-color: #7bb47b !important;
        background: #f0f8f0;
    }
    .payment-option-card.selected .payment-icon {
        background: #7bb47b !important;
    }
    
    /* Desktop Styles for Payment Method Page */
    @media (min-width: 992px) {
        .row.justify-content-center {
            align-items: stretch;
        }
        .payment-methods-section,
        .purchase-summary-section {
            display: flex;
            flex-direction: column;
        }
        .payment-methods-card {
            flex: 1;
            max-height: 85vh;
        }
        .purchase-summary-card {
            max-height: 85vh;
            position: sticky;
            top: 20px;
        }
    }
    
    /* Mobile Responsive Styles for Payment Method Page */
    @media (max-width: 991.98px) {
        .payment-method-container {
            padding-bottom: 1rem;
        }
        .payment-methods-section {
            margin-bottom: 1rem;
        }
        .purchase-summary-section {
            margin-top: 0 !important;
            margin-bottom: 2rem;
        }
        .payment-methods-card {
            max-height: none !important;
            overflow-y: visible !important;
        }
        .purchase-summary-card {
            position: relative !important;
        }
        .scrollable-content {
            max-height: none !important;
            overflow-y: visible !important;
        }
    }
    
    @media (max-width: 650px) {
        .payment-method-container {
            padding: 0.5rem 0.25rem 1rem 0.25rem !important;
        }
        .payment-methods-section,
        .purchase-summary-section {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        .payment-methods-card,
        .purchase-summary-card {
            padding: 0.75rem !important;
            font-size: 0.9rem;
        }
        .payment-option-card > div {
            padding: 0.75rem !important;
        }
        .payment-icon {
            width: 40px !important;
            height: 40px !important;
        }
        .payment-icon i,
        .payment-icon span {
            font-size: 1rem !important;
        }
        h4 {
            font-size: 1.1rem !important;
        }
        h6 {
            font-size: 0.95rem !important;
        }
        .purchase-summary-card img {
            width: 50px !important;
            height: 50px !important;
        }
        #completeOrderBtn {
            font-size: 0.95rem !important;
            padding: 10px !important;
        }
        .alert {
            padding: 0.75rem !important;
            font-size: 0.85rem !important;
        }
        .purchase-summary-card .d-flex {
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// This script is moved to the main script section below
</script>
@endpush


<style>
.card-type-option {
    transition: all 0.2s ease;
    cursor: pointer;
}
.card-type-option:hover {
    border-color: #7bb47b !important;
    background-color: #f8faf8;
}
.card-type-option.selected {
    border-color: #7bb47b !important;
    background-color: #f0f8f0;
}
.bank-option {
    transition: all 0.2s ease;
    cursor: pointer;
}
.bank-option:hover {
    border-color: #7bb47b !important;
    background-color: #f8faf8;
}
.bank-option.selected {
    border-color: #7bb47b !important;
    background-color: #f0f8f0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main payment options logic
    const paymentOptions = document.querySelectorAll('.payment-option-card');
    const selectedPaymentInput = document.getElementById('selectedPaymentMethod');
    const completeOrderBtn = document.getElementById('completeOrderBtn');
    const paymentForm = document.getElementById('paymentForm');
    
    // Debug: Check if Store Credit option exists
    const storeCreditOption = document.querySelector('[data-payment="store_credit"]');
    console.log('Store Credit option found:', storeCreditOption);
    console.log('Total payment options found:', paymentOptions.length);
    
    // Test if Store Credit option is clickable
    if (storeCreditOption) {
        console.log('Store Credit option is clickable:', storeCreditOption.style.pointerEvents);
        console.log('Store Credit option cursor:', storeCreditOption.style.cursor);
    }
    
    // Payment option selection
    paymentOptions.forEach((option, index) => {
        console.log(`Payment option ${index}:`, option, 'data-payment:', option.dataset.payment);
        
        option.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Payment option clicked:', this.dataset.payment);
            
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            const paymentMethod = this.dataset.payment;
            selectedPaymentInput.value = paymentMethod;
            completeOrderBtn.disabled = false;
            
            console.log('Payment method set to:', paymentMethod);
            
            // For Store Credit payment, submit immediately
            if (paymentMethod === 'store_credit') {
                console.log('Store Credit payment selected - submitting form immediately');
                console.log('Payment method set to:', selectedPaymentInput.value);
                console.log('Form element:', document.getElementById('paymentForm'));
                console.log('Form action:', document.getElementById('paymentForm').action);
                
                setTimeout(() => {
                    console.log('About to submit form...');
                    try {
                        document.getElementById('paymentForm').submit();
                        console.log('Form submitted successfully');
                    } catch (error) {
                        console.error('Error submitting form:', error);
                    }
                }, 100);
            }
        });
    });
    
    // Loyalty toggle handler
    const useLoyalty = document.getElementById('useLoyalty');
    if (useLoyalty) {
        useLoyalty.addEventListener('change', function() {
            document.getElementById('useLoyaltyInput').value = this.checked ? '1' : '0';
        });
    }
    
    // Card selection modal logic
    const cardTypeOptions = document.querySelectorAll('.card-type-option');
    const bankSelection = document.getElementById('bankSelection');
    const bankOptions = document.getElementById('bankOptions');
    const confirmCardBtn = document.getElementById('confirmCardSelection');
    
    let selectedCardType = null;
    let selectedBank = null;
    
    // Card type selection
    cardTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            cardTypeOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Get card type
            selectedCardType = this.getAttribute('data-type');
            
            // Show bank selection
            bankSelection.style.display = 'block';
            
            // Populate bank options based on card type
            populateBankOptions(selectedCardType);
        });
    });
    
    // Bank selection
    function populateBankOptions(cardType) {
        const banks = [
            { id: 'bpi', name: 'BPI', icon: '🏦' },
            { id: 'bdo', name: 'BDO', icon: '🏦' },
            { id: 'metrobank', name: 'Metrobank', icon: '🏦' },
            { id: 'security_bank', name: 'Security Bank', icon: '🏦' },
            { id: 'seabank', name: 'Seabank', icon: '🏦' },
            { id: 'rcbc', name: 'RCBC', icon: '🏦' },
            { id: 'other', name: 'Other Banks', icon: '🏦' }
        ];
        
        bankOptions.innerHTML = '';
        banks.forEach(bank => {
            const bankOption = document.createElement('div');
            bankOption.className = 'col-md-6 mb-3';
            bankOption.innerHTML = `
                <div class="bank-option" data-bank="${bank.id}">
                    <div class="p-3 text-center" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">${bank.icon}</div>
                        <div style="font-weight: 600; color: #222;">${bank.name}</div>
                    </div>
                </div>
            `;
            
            // Add click event
            bankOption.querySelector('.bank-option').addEventListener('click', function() {
                // Remove selected class from all bank options
                document.querySelectorAll('.bank-option').forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Set selected bank
                selectedBank = this.getAttribute('data-bank');
                
                // Enable confirm button
                confirmCardBtn.disabled = false;
            });
            
            bankOptions.appendChild(bankOption);
        });
    }
    
    // Confirm card selection
    confirmCardBtn.addEventListener('click', function() {
        if (selectedCardType && selectedBank) {
            const paymentMethod = `${selectedBank}_${selectedCardType}_card`;
            selectedPaymentInput.value = paymentMethod;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cardSelectionModal'));
            modal.hide();
            
            // Update UI to show selected card
            const cardOption = document.querySelector('[data-payment="card_modal"]');
            cardOption.querySelector('div').style.borderColor = '#7bb47b';
            cardOption.querySelector('div').style.backgroundColor = '#f0f8f0';
            
            // Directly submit the form to proceed to PayMongo
            console.log('Proceeding to PayMongo with payment method:', paymentMethod);
            document.getElementById('paymentForm').submit();
        }
    });
    
    // Form submission debugging
    paymentForm.addEventListener('submit', function(e) {
        console.log('Form submission started');
        console.log('Selected payment method:', selectedPaymentInput.value);
        
        // For Store Credit, allow submission even if value is not set yet
        if (selectedPaymentInput.value === 'store_credit') {
            console.log('Store Credit payment - proceeding to order creation');
            return true; // Allow submission
        }
        
        // Check if payment method is selected for other methods
        if (!selectedPaymentInput.value) {
            console.log('No payment method selected, preventing submission');
            e.preventDefault();
            // No alert; keep UX clean
            return false;
        }
        
        console.log('Form submitting with payment method:', selectedPaymentInput.value);
        
        // For other payment methods
        console.log('Other payment method - proceeding to PayMongo');
    });
});
</script>

    </div>
</div>

<!-- Credit/Debit Card Selection Modal -->
<div class="modal fade" id="cardSelectionModal" tabindex="-1" aria-labelledby="cardSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardSelectionModalLabel">Select Your Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Card Type Selection -->
                <div class="mb-4">
                    <h6 class="mb-3" style="font-weight: 600; color: #555;">Card Type</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-type-option" data-type="debit">
                                <div class="p-3 text-center" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                    <i class="fas fa-credit-card fa-2x mb-2" style="color: #007bff;"></i>
                                    <div style="font-weight: 600; color: #222;">Debit Card</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-type-option" data-type="credit">
                                <div class="p-3 text-center" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                    <i class="fas fa-credit-card fa-2x mb-2" style="color: #28a745;"></i>
                                    <div style="font-weight: 600; color: #222;">Credit Card</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Restrictions Warning -->
                <div class="alert mb-4" style="border-left: 4px solid #7bb47b; background-color: #e8f5e8; border-color: #7bb47b; color: #2d5a2d;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                        <div>
                            <strong>Important:</strong>
                            <ul class="mb-0 mt-1" style="font-size: 0.9rem;">
                                <li>If you select <strong>Credit Card</strong>, you can only pay with <strong>Debit Cards</strong></li>
                                <li>If you select <strong>Debit Card</strong>, you can only pay with <strong>Debit Cards</strong></li>
                                <li><strong>Credit to Credit payments are not allowed</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Bank Selection -->
                <div class="mb-4" id="bankSelection" style="display: none;">
                    <h6 class="mb-3" style="font-weight: 600; color: #555;">Select Bank</h6>
                    <div class="row" id="bankOptions">
                        <!-- Bank options will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCardSelection" disabled>Confirm Selection</button>
            </div>
        </div>
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
            foreach (['focal_flower_1', 'focal_flower_2', 'focal_flower_3'] as $flowerField) {
                if ($bouquet->$flowerField) {
                    $item_data = $items->firstWhere('name', $bouquet->$flowerField);
                    if ($item_data) {
                        $components[] = [
                            'category' => 'Fresh Flowers',
                            'name' => $bouquet->$flowerField,
                            'quantity' => 1,
                            'price' => $item_data->price ?? 0
                        ];
                        $totalPrice += $item_data->price ?? 0;
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
                    $components[] = [
                        'category' => 'Artificial Flowers',
                        'name' => $bouquet->filler,
                        'quantity' => 1,
                        'price' => $item_data->price ?? 0
                    ];
                    $totalPrice += $item_data->price ?? 0;
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection 
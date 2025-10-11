@extends('layouts.customer_app')

@section('content')
<div class="py-4" style="background: #f4faf4; min-height: 100vh;">
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
        @if(request('product_id'))
            <input type="hidden" name="product_id" value="{{ request('product_id') }}">
            <input type="hidden" name="quantity" value="{{ request('quantity', 1) }}">
        @endif
        @if(request('catalog_product_id'))
            <input type="hidden" name="catalog_product_id" value="{{ request('catalog_product_id') }}">
            <input type="hidden" name="quantity" value="{{ request('quantity', 1) }}">
        @endif
        @if(request('selected_items'))
            @foreach(request('selected_items') as $itemId)
                <input type="hidden" name="selected_items[]" value="{{ $itemId }}">
            @endforeach
        @endif
        
        
        <div class="row justify-content-center">
            <!-- Payment Methods Column -->
            <div class="col-12 col-lg-8 col-xl-6" style="max-width: 1200px;">
                <div class="bg-white rounded-3 p-3 mb-4 scrollable-content" style="box-shadow: none; max-height: 85vh; overflow-y: auto;">
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
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('customer.checkout.index') }}" class="btn btn-outline-success">
                            Return to delivery information
                        </a>
                        <button type="submit" class="btn btn-success" id="completeOrderBtn">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Purchase Summary Column -->
            @php
                $feeParam = request()->query('shipping_fee');
                if (!is_null($feeParam)) {
                    $sanitized = is_numeric($feeParam) ? (string)$feeParam : preg_replace('/[^0-9.]/', '', (string)$feeParam);
                    if ($sanitized !== '' && is_numeric($sanitized)) {
                        $shippingFee = (float)$sanitized;
                    }
                }
            @endphp
            <div class="col-12 col-lg-4 col-xl-4">
                <div class="bg-white rounded-3 p-3 mb-4" style="box-shadow: none;">
                    <h4 class="mb-4" style="font-weight: 600; color: #222;">Purchase Summary</h4>
                    
                    @foreach($cartItems as $item)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('storage/' . $item->product->image) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        <div class="flex-grow-1 ms-3">
                            <div style="font-weight: 500; color: #222;">{{ $item->product->name }}</div>
                            <div style="font-size: 0.9rem; color: #666;">Quantity: {{ $item->quantity }}</div>
                        </div>
                        <div class="text-end">
                            <div style="font-weight: 600; color: #222;">₱{{ number_format($item->quantity * $item->product->price, 2) }}</div>
                        </div>
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
                                                <strong>{{ $discountedItem->product->name }}</strong> - 50% OFF!
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
    
    /* Custom scrollbar styling for the payment methods content area */
    .scrollable-content::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-content::-webkit-scrollbar-track {
        background: #f1f1f1;
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
        scrollbar-color: #8ACB88 #f1f1f1;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option-card');
    const selectedPaymentInput = document.getElementById('selectedPaymentMethod');
    const completeOrderBtn = document.getElementById('completeOrderBtn');
    const paymentForm = document.getElementById('paymentForm');
    
    // No payment type toggle on this page

    // Payment option selection
    paymentOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            const paymentMethod = this.dataset.payment;
            selectedPaymentInput.value = paymentMethod;
            completeOrderBtn.disabled = false;
        });
    });
    
    // Loyalty stamps are now automatically applied - no manual redemption needed

    // Loyalty toggle handler
    const useLoyalty = document.getElementById('useLoyalty');
    if (useLoyalty) {
        useLoyalty.addEventListener('change', function() {
            document.getElementById('useLoyaltyInput').value = this.checked ? '1' : '0';
        });
    }
});
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
    const selectedPaymentInput = document.getElementById('selectedPaymentMethod');
    const completeOrderBtn = document.getElementById('completeOrderBtn');
    const paymentOptions = document.querySelectorAll('.payment-option-card');
    
    // Payment option selection
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            paymentOptions.forEach(opt => {
                opt.querySelector('div').style.borderColor = '#e0e0e0';
                opt.querySelector('div').style.backgroundColor = 'transparent';
            });
            
            // Add active class to selected option
            this.querySelector('div').style.borderColor = '#7bb47b';
            this.querySelector('div').style.backgroundColor = '#f0f8f0';
            
            // Set payment method
            const paymentMethod = this.getAttribute('data-payment');
            selectedPaymentInput.value = paymentMethod;
            
            // If it's not a card modal, directly submit the form
            if (paymentMethod !== 'card_modal') {
                console.log('Proceeding to PayMongo with payment method:', paymentMethod);
                document.getElementById('paymentForm').submit();
            }
        });
    });
    
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
    const paymentForm = document.getElementById('paymentForm');
    paymentForm.addEventListener('submit', function(e) {
        console.log('Form submission started');
        console.log('Selected payment method:', selectedPaymentInput.value);
        console.log('Form data:', new FormData(this));
        
        // Check if payment method is selected
        if (!selectedPaymentInput.value) {
            e.preventDefault();
            alert('Please select a payment method');
            return false;
        }
        
        console.log('Form submitting to PayMongo...');
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
                <div class="alert alert-info mb-4" style="border-left: 4px solid #17a2b8; background-color: #d1ecf1; border-color: #bee5eb;">
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

@endsection 
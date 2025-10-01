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
                    <h4 class="mb-4" style="font-weight: 600; color: #222;">Payment Methods</h4>
                    
                    
                    <!-- PayMaya Payment Option -->
                    <div class="payment-option-card mb-3" data-payment="paymaya">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #6c5ce7; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: white; font-weight: bold; font-size: 1.2rem;">M</span>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">PayMaya</div>
                                    <div style="font-size: 0.9rem; color: #666;">Pay with PayMaya wallet</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    
                    <!-- Seabank Payment Option -->
                    <div class="payment-option-card mb-3" data-payment="seabank">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #00a8ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-ship" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">Seabank</div>
                                    <div style="font-size: 0.9rem; color: #666;">Pay with Seabank wallet</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    
                    <!-- RCBC Payment Option -->
                    <div class="payment-option-card mb-3" data-payment="rcbc">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="payment-icon me-3" style="width: 50px; height: 50px; background: #8b4513; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: white; font-weight: bold; font-size: 1.2rem;">R</span>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #222;">RCBC</div>
                                    <div style="font-size: 0.9rem; color: #666;">Pay with RCBC bank</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                    
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
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('customer.checkout.index') }}" class="btn btn-outline-success">
                            Return to delivery information
                        </a>
                        <button type="submit" class="btn btn-success" id="completeOrderBtn" disabled>
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
                    
                    <!-- Promo Code Section -->
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Promo code" id="promoCodeInput">
                        <button class="btn btn-success" type="button" id="applyPromoBtn">Apply</button>
                    </div>
                    
                    <!-- Cost Breakdown -->
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #666;">Subtotal ({{ count($cartItems) }} Item{{ count($cartItems) == 1 ? '' : 's' }})</span>
                        <span style="color: #222;">₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #666;">Shipping Fee</span>
                        <span style="color: #222;">₱{{ number_format($shippingFee, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight: 600; font-size: 1.1rem;">Total</span>
                        <span style="color: #7bb47b; font-weight: 600; font-size: 1.2rem;">₱{{ number_format($subtotal + $shippingFee, 2) }}</span>
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
    
    // Promo code apply (visual only)
    document.getElementById('applyPromoBtn').addEventListener('click', function() {
        const promoCode = document.getElementById('promoCodeInput').value.trim();
        if (promoCode) {
            this.classList.add('active');
            setTimeout(() => this.classList.remove('active'), 300);
            // In a real implementation, you would validate the promo code here
        }
    });
});
</script>
@endpush
    </div>
</div>
@endsection 
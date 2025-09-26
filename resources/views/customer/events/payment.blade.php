@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2 py-md-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 px-3 px-md-0">
            <!-- Header -->
            <div class="text-center mb-4 mb-md-5">
                <h1 class="display-6 display-md-4 fw-bold text-dark mb-2 mb-md-3" style="font-family: 'Playfair Display', serif;">
                    <span class="d-block d-md-inline">Payment Method</span>
                </h1>
                <p class="lead text-muted d-none d-md-block">Choose your preferred payment method</p>
                <p class="text-muted d-md-none fs-6">Choose your preferred payment method</p>
            </div>

            <div class="row g-3 g-md-4">
                <!-- Event Summary -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-lg border-0" style="border-radius: 20px;">
                        <div class="card-header bg-light border-0 py-3 py-md-4">
                            <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Event Summary</h4>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3 g-md-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-calendar-alt text-success me-3 fa-lg"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Event Date</h6>
                                            <p class="text-muted mb-0 small">{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-clock text-success me-3 fa-lg"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Event Time</h6>
                                            <p class="text-muted mb-0 small">{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-map-marker-alt text-success me-3 fa-lg mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Delivery Address</h6>
                                            <p class="text-muted mb-0 small">{{ $event->venue }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-user text-success me-3 fa-lg"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Recipient</h6>
                                            <p class="text-muted mb-0 small">{{ $event->recipient_name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-lg border-0" style="border-radius: 20px;">
                        <div class="card-header bg-light border-0 py-3 py-md-4">
                            <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Select Payment Method</h4>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <form action="{{ route('customer.events.process_payment', $event->id) }}" method="POST" id="paymentForm">
                                @csrf
                                <div class="mb-4">
                                    <div class="row g-2 g-md-3">
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="payment-method-card" data-method="gcash">
                                                <input type="radio" name="payment_method" value="gcash" id="gcash" class="d-none" required>
                                                <label for="gcash" class="payment-method-label">
                                                    <div class="d-flex align-items-center p-2 p-md-3 border rounded-3" style="background: #f8f9fa; transition: all 0.3s ease;">
                                                        <i class="fas fa-mobile-alt text-success me-2 me-md-3 fa-lg fa-md-2x"></i>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-1 fs-6">GCash</h6>
                                                            <p class="text-muted mb-0 small d-none d-sm-block">Pay with GCash</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="payment-method-card" data-method="paymaya">
                                                <input type="radio" name="payment_method" value="paymaya" id="paymaya" class="d-none" required>
                                                <label for="paymaya" class="payment-method-label">
                                                    <div class="d-flex align-items-center p-2 p-md-3 border rounded-3" style="background: #f8f9fa; transition: all 0.3s ease;">
                                                        <i class="fas fa-credit-card text-success me-2 me-md-3 fa-lg fa-md-2x"></i>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-1 fs-6">PayMaya</h6>
                                                            <p class="text-muted mb-0 small d-none d-sm-block">Pay with PayMaya</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="payment-method-card" data-method="seabank">
                                                <input type="radio" name="payment_method" value="seabank" id="seabank" class="d-none" required>
                                                <label for="seabank" class="payment-method-label">
                                                    <div class="d-flex align-items-center p-2 p-md-3 border rounded-3" style="background: #f8f9fa; transition: all 0.3s ease;">
                                                        <i class="fas fa-university text-success me-2 me-md-3 fa-lg fa-md-2x"></i>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-1 fs-6">Seabank</h6>
                                                            <p class="text-muted mb-0 small d-none d-sm-block">Pay with Seabank</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="payment-method-card" data-method="rcbc">
                                                <input type="radio" name="payment_method" value="rcbc" id="rcbc" class="d-none" required>
                                                <label for="rcbc" class="payment-method-label">
                                                    <div class="d-flex align-items-center p-2 p-md-3 border rounded-3" style="background: #f8f9fa; transition: all 0.3s ease;">
                                                        <i class="fas fa-building text-success me-2 me-md-3 fa-lg fa-md-2x"></i>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-1 fs-6">RCBC</h6>
                                                            <p class="text-muted mb-0 small d-none d-sm-block">Pay with RCBC</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Breakdown -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-3 fs-6">Cost Breakdown</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Subtotal:</span>
                                        <span class="fw-bold small">₱{{ number_format($event->subtotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Delivery Fee:</span>
                                        <span class="fw-bold small">₱{{ number_format($event->delivery_fee, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Service Fee:</span>
                                        <span class="fw-bold small">₱{{ number_format($event->service_fee, 2) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold fs-6 text-dark">Total:</span>
                                        <span class="fw-bold fs-6 text-success">₱{{ number_format($event->total, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 gap-md-3">
                                    <button type="submit" class="btn btn-success btn-lg py-2 py-md-3 fw-bold" id="processPaymentBtn">
                                        <i class="fas fa-credit-card me-2"></i><span class="d-none d-sm-inline">Process Payment</span><span class="d-sm-none">Pay Now</span>
                                    </button>
                                    <a href="{{ route('customer.events.order_summary', $event->id) }}" class="btn btn-outline-secondary btn-lg py-2 py-md-3 fw-bold">
                                        <i class="fas fa-arrow-left me-2"></i><span class="d-none d-sm-inline">Back to Order Summary</span><span class="d-sm-none">Back</span>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lato:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Lato', sans-serif;
}

.payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-card:hover .payment-method-label div {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #28a745 !important;
}

.payment-method-card input:checked + .payment-method-label div {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
    color: white !important;
    border-color: #28a745 !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.payment-method-card input:checked + .payment-method-label div i {
    color: white !important;
}

.payment-method-card input:checked + .payment-method-label div h6,
.payment-method-card input:checked + .payment-method-label div p {
    color: white !important;
}

.btn-lg {
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    padding: 0.75rem 1.5rem;
}

@media (min-width: 768px) {
    .btn-lg {
        font-size: 1rem;
        padding: 1rem 2rem;
    }
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
}

.card {
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

/* Responsive text sizes */
@media (max-width: 767px) {
    .display-6 {
        font-size: 2rem;
    }
    
    .fs-5 {
        font-size: 1.1rem !important;
    }
    
    .fs-6 {
        font-size: 0.9rem !important;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 0.85rem;
    }
}

/* Mobile-specific adjustments */
@media (max-width: 575px) {
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .btn-lg {
        width: 100%;
        margin-bottom: 0.5rem;
        padding: 0.6rem 1rem !important;
        font-size: 0.85rem;
    }
    
    .d-grid .btn-lg:last-child {
        margin-bottom: 0;
    }
    
    .payment-method-card .d-flex {
        padding: 0.75rem !important;
    }
    
    .payment-method-card i {
        font-size: 1.2rem !important;
    }
    
    .payment-method-card h6 {
        font-size: 0.85rem !important;
    }
}

/* Tablet responsive */
@media (min-width: 576px) and (max-width: 991px) {
    .btn-lg {
        padding: 0.8rem 1.5rem;
        font-size: 0.9rem;
    }
    
    .payment-method-card .d-flex {
        padding: 1rem !important;
    }
}

/* Desktop responsive */
@media (min-width: 992px) {
    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1rem;
    }
    
    .payment-method-card .d-flex {
        padding: 1.5rem !important;
    }
}

/* Watercolor background effect */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 20%, rgba(40, 167, 69, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 182, 193, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 60%, rgba(255, 215, 0, 0.05) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentCards = document.querySelectorAll('.payment-method-card');
    const processPaymentBtn = document.getElementById('processPaymentBtn');
    const paymentForm = document.getElementById('paymentForm');

    // Handle payment method selection
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Remove active class from all cards
            paymentCards.forEach(c => c.classList.remove('active'));
            // Add active class to selected card
            this.classList.add('active');
        });
    });

    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) {
            alert('Please select a payment method.');
            return;
        }

        // Show processing state
        processPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESSING...';
        processPaymentBtn.disabled = true;

        // Add a small delay to ensure the button state is updated
        setTimeout(() => {
            // Submit the form
            this.submit();
        }, 100);
    });
});
</script>
@endsection

@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2 py-md-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 px-3 px-md-0">
            <!-- Header -->
            <div class="text-center mb-4 mb-md-5">
                <h1 class="display-6 display-md-4 fw-bold text-dark mb-2 mb-md-3" style="font-family: 'Playfair Display', serif;">
                    <span class="d-block d-md-inline">Order Summary</span>
                </h1>
                <p class="lead text-muted d-none d-md-block">Review your event details and flower selections</p>
                <p class="text-muted d-md-none fs-6">Review your event details and flower selections</p>
            </div>

            <div class="row g-3 g-md-4">
                <!-- Event Details Card -->
                <div class="col-12 col-lg-8">
                    <div class="card shadow-lg border-0" style="border-radius: 20px;">
                        <div class="card-header bg-light border-0 py-3 py-md-4">
                            <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Event Details</h4>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3 g-md-4">
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-calendar-alt text-success me-3 fa-lg"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Event Date</h6>
                                            <p class="text-muted mb-0 small">{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
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
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-user text-success me-3 fa-lg"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Recipient</h6>
                                            <p class="text-muted mb-0 small">{{ $event->recipient_name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-users text-success me-3 fa-lg"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Guest Count</h6>
                                            <p class="text-muted mb-0 small">{{ $event->guest_count ?? 'N/A' }} guests</p>
                                        </div>
                                    </div>
                                </div>
                                @if($event->personalized_message)
                                <div class="col-12">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-heart text-success me-3 fa-lg mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Personalized Message</h6>
                                            <p class="text-muted mb-0 small">{{ $event->personalized_message }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($event->special_instructions)
                                <div class="col-12">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-leaf text-success me-3 fa-lg mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">Special Instructions</h6>
                                            <p class="text-muted mb-0 small">{{ $event->special_instructions }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Flower Selections -->
                    <div class="card shadow-lg border-0 mt-3 mt-md-4" style="border-radius: 20px;">
                        <div class="card-header bg-light border-0 py-3 py-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Your Flower Selections</h4>
                                @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show mb-0 py-2" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            @php
                                $flowerSelections = session()->get('event_' . $event->id . '_flowers', []);
                            @endphp
                            @if(count($flowerSelections) > 0)
                                <div class="mb-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-6">Selected Products ({{ count($flowerSelections) }})</h6>
                                        <a href="{{ route('customer.dashboard') }}?event_id={{ $event->id }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">Add More Products</span><span class="d-sm-none">Add More</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row g-2 g-md-3">
                                    @foreach($flowerSelections as $selection)
                                    <div class="col-12 col-md-6">
                                        <div class="d-flex align-items-center p-2 p-md-3 border rounded-3" style="background: #f8f9fa;">
                                            <img src="{{ asset('storage/' . $selection['image']) }}" alt="{{ $selection['name'] }}" class="rounded me-2 me-md-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold text-dark mb-1 fs-6">{{ $selection['name'] }}</h6>
                                                <p class="text-muted mb-1 small">Qty: {{ $selection['quantity'] }}</p>
                                                <p class="text-success fw-bold mb-0 small">₱{{ number_format($selection['price'] * $selection['quantity'], 2) }}</p>
                                            </div>
                                            <div class="ms-1 ms-md-2">
                                                <form action="{{ route('customer.events.remove_product', $event->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="product_id" value="{{ $selection['product_id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this product from your event?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 py-md-5">
                                    <i class="fas fa-shopping-cart fa-2x fa-md-3x text-muted mb-3"></i>
                                    <h5 class="text-muted fs-6 fs-md-5">No flower selections yet</h5>
                                    <p class="text-muted small">Browse our products to add flowers to your event</p>
                                    <a href="{{ route('customer.dashboard') }}?event_id={{ $event->id }}" class="btn btn-success btn-sm btn-md">
                                        <i class="fas fa-shopping-bag me-2"></i>Browse Products
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-12 col-lg-4">
                    <div class="card shadow-lg border-0" style="border-radius: 20px;">
                        <div class="card-header bg-light border-0 py-3 py-md-4">
                            <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Cost Breakdown</h4>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Subtotal:</span>
                                <span class="fw-bold small">₱{{ number_format($event->subtotal ?? 0, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Delivery Fee:</span>
                                <span class="fw-bold small">₱{{ number_format($event->delivery_fee ?? 0, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Service Fee:</span>
                                <span class="fw-bold small">₱{{ number_format($event->service_fee ?? 0, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold fs-6 fs-md-5 text-dark">Total:</span>
                                <span class="fw-bold fs-6 fs-md-5 text-success">₱{{ number_format($event->total ?? 0, 2) }}</span>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3 fs-6">Payment Method</h6>
                                <div class="d-flex align-items-center p-3 border rounded-3" style="background: #f8f9fa;">
                                    <i class="fas fa-wallet text-success me-3 fa-lg"></i>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1 fs-6">Digital Wallets</h6>
                                        <p class="text-muted mb-0 small">GCash, PayMaya, GrabPay, ShopeePay</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 gap-md-3">
                                @if($event->total > 0)
                                <a href="{{ route('customer.events.payment', $event->id) }}" class="btn btn-success btn-lg py-2 py-md-3 fw-bold w-100" id="placeOrderBtn">
                                    <i class="fas fa-credit-card me-2"></i><span class="d-none d-sm-inline">Proceed to Payment</span><span class="d-sm-none">Pay Now</span>
                                </a>
                                @else
                                <button class="btn btn-success btn-lg py-2 py-md-3 fw-bold" disabled>
                                    <i class="fas fa-check me-2"></i><span class="d-none d-sm-inline">Add Products First</span><span class="d-sm-none">Add Products</span>
                                </button>
                                @endif
                                <a href="{{ route('customer.events.edit', $event->id) }}" class="btn btn-outline-secondary btn-lg py-2 py-md-3 fw-bold">
                                    <i class="fas fa-edit me-2"></i><span class="d-none d-sm-inline">Edit Event</span><span class="d-sm-none">Edit</span>
                                </a>
                            </div>
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

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
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
    }
    
    .d-grid .btn-lg:last-child {
        margin-bottom: 0;
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
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    
    placeOrderBtn.addEventListener('click', function() {
        // Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        this.disabled = true;
        
        // Simulate processing time
        setTimeout(() => {
            // Redirect to order confirmation
            window.location.href = "{{ route('customer.events.confirmation', $event->id) }}";
        }, 2000);
    });
});
</script>
@endsection

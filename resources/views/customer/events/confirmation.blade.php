@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2 py-md-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 px-3 px-md-0">
            <!-- Confirmation Header -->
            <div class="text-center mb-3 mb-md-5">
                <div class="mb-3 mb-md-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem; font-size-md: 4rem;"></i>
                </div>
                <h1 class="display-6 display-md-4 fw-bold text-dark mb-2 mb-md-3" style="font-family: 'Playfair Display', serif;">Order Confirmed!</h1>
                <p class="lead text-muted d-none d-md-block">Your event has been successfully booked</p>
                <p class="text-muted d-md-none">Your event has been successfully booked</p>
            </div>

            <!-- Order Details Card -->
            <div class="card shadow-lg border-0 mb-4" style="border-radius: 20px;">
                <div class="card-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-2">Order Number</h6>
                            <p class="text-muted fs-5">#FLORA{{ str_pad($event->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-2">Estimated Delivery</h6>
                            <p class="text-muted fs-5">{{ \Carbon\Carbon::parse($event->event_date)->format('M j, Y') }} by 3 PM</p>
                        </div>
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-2">Event Type</h6>
                            <p class="text-muted fs-5">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</p>
                        </div>
                        @if($event->payment_method)
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-2">Payment Method</h6>
                            <p class="text-muted fs-5">{{ strtoupper($event->payment_method) }}</p>
                        </div>
                        @endif
                        @if($event->order_id)
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-2">Order ID</h6>
                            <p class="text-muted fs-5">{{ $event->order_id }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Progress Tracker -->
            <div class="card shadow-lg border-0 mb-3 mb-md-4" style="border-radius: 20px;">
                <div class="card-body p-3 p-md-5">
                    <h4 class="fw-bold text-dark mb-3 mb-md-4 text-center fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Order Progress</h4>
                    <div class="row g-2 g-md-3">
                        <div class="col-6 col-md-2">
                            <div class="text-center">
                                <div class="progress-step completed mb-2 mb-md-3">
                                    <i class="fas fa-shopping-cart text-white"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">Order Received</h6>
                                <p class="small text-muted d-none d-md-block">We've received your order</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="text-center">
                                <div class="progress-step active mb-2 mb-md-3">
                                    <i class="fas fa-seedling text-white"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">Preparing</h6>
                                <p class="small text-muted d-none d-md-block">Selecting fresh flowers</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="text-center">
                                <div class="progress-step pending mb-2 mb-md-3">
                                    <i class="fas fa-hands text-white"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">Arranging</h6>
                                <p class="small text-muted d-none d-md-block">Creating your arrangement</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="text-center">
                                <div class="progress-step pending mb-2 mb-md-3">
                                    <i class="fas fa-truck text-white"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">Out for Delivery</h6>
                                <p class="small text-muted d-none d-md-block">On the way to you</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="text-center">
                                <div class="progress-step pending mb-2 mb-md-3">
                                    <i class="fas fa-gift text-white"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">Delivered</h6>
                                <p class="small text-muted d-none d-md-block">Enjoy your flowers!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Summary -->
            <div class="card shadow-lg border-0 mb-4" style="border-radius: 20px;">
                <div class="card-header bg-light border-0 py-4">
                    <h4 class="fw-bold text-dark mb-0" style="font-family: 'Playfair Display', serif;">Event Summary</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calendar-alt text-success me-3 fa-lg"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Event Date</h6>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock text-success me-3 fa-lg"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Event Time</h6>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-map-marker-alt text-success me-3 fa-lg mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Delivery Address</h6>
                                    <p class="text-muted mb-0">{{ $event->venue }}</p>
                                </div>
                            </div>
                        </div>
                        @if($event->personalized_message)
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-heart text-success me-3 fa-lg mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Personalized Message</h6>
                                    <p class="text-muted mb-0">{{ $event->personalized_message }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mb-3 mb-md-5">
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4 px-md-5 py-3 fw-bold">
                        <i class="fas fa-shopping-bag me-2"></i><span class="d-none d-sm-inline">Continue Shopping</span><span class="d-sm-none">Continue</span>
                    </a>
                    <a href="{{ route('customer.events.index') }}" class="btn btn-success btn-lg px-4 px-md-5 py-3 fw-bold">
                        <i class="fas fa-calendar-check me-2"></i><span class="d-none d-sm-inline">View My Events</span><span class="d-sm-none">My Events</span>
                    </a>
                </div>
                <p class="text-muted mt-3 small">
                    <i class="fas fa-envelope me-2"></i>You will receive an email update shortly.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lato:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Lato', sans-serif;
}

.progress-step {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
    transition: all 0.3s ease;
}

@media (min-width: 768px) {
    .progress-step {
        width: 60px;
        height: 60px;
    }
}

.progress-step.completed {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.progress-step.active {
    background: linear-gradient(135deg, #ffc107, #ff8c00);
    color: white;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
    animation: pulse 2s infinite;
}

.progress-step.pending {
    background: #e9ecef;
    color: #6c757d;
    border: 2px solid #dee2e6;
}

@keyframes pulse {
    0% {
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
    }
    50% {
        box-shadow: 0 4px 25px rgba(255, 193, 7, 0.5);
    }
    100% {
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
    }
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
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
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
    
    .d-flex.flex-column .btn-lg:last-child {
        margin-bottom: 0;
    }
}

/* Decorative elements */
body::after {
    content: '';
    position: fixed;
    top: 20px;
    left: 20px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(40, 167, 69, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: -1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some interactive effects
    const progressSteps = document.querySelectorAll('.progress-step');
    
    progressSteps.forEach((step, index) => {
        step.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        step.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Show success animation
    setTimeout(() => {
        const checkIcon = document.querySelector('.fa-check-circle');
        checkIcon.style.animation = 'bounce 1s ease-in-out';
    }, 500);
});

// Add bounce animation
const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection

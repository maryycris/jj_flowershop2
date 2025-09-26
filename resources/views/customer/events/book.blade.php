@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2 py-md-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <!-- Hero Section -->
    <div class="row justify-content-center mb-3 mb-md-5">
        <div class="col-12 col-lg-10">
            <div class="text-center mb-3 mb-md-5 px-3">
                <h1 class="display-6 display-md-4 fw-bold text-dark mb-2 mb-md-3" style="font-family: 'Playfair Display', serif;">CELEBRATE YOUR MOMENTS</h1>
                <p class="lead text-muted d-none d-md-block">Tell us about your special occasion</p>
                <p class="text-muted d-md-none">Tell us about your special occasion</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 px-3 px-md-0">
            <div class="card shadow-lg border-0" style="border-radius: 20px; background: #fefefe;">
                <div class="card-body p-3 p-md-4 p-lg-5">
                    <form action="{{ route('customer.events.store') }}" method="POST" id="eventBookingForm">
                    @csrf
                        
                        <!-- Event Type Selection -->
                        <div class="mb-4 mb-md-5">
                            <h4 class="fw-bold text-dark mb-3 mb-md-4 fs-5 fs-md-4 text-center text-md-start" style="font-family: 'Playfair Display', serif;">FIND FLOWERS FOR ANY OCCASION</h4>
                            <p class="text-muted mb-3 mb-md-4 text-center text-md-start">Express Your Sentiment</p>
                            <div class="row g-2 g-md-3 g-lg-4">
                                <div class="col-6 col-sm-4 col-md-4 col-lg-4">
                                    <div class="event-type-card" data-event-type="wedding">
                                        <div class="card h-100 border-0 event-card" style="border-radius: 15px; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-2 p-md-3 p-lg-4">
                                                <div class="occasion-icon mb-2 mb-md-3">
                                                    <i class="fas fa-heart fa-lg fa-md-2x text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1 mb-md-2 fs-6">Wedding</h6>
                                                <p class="small text-muted d-none d-md-block">Elegant arrangements for your special day</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-4">
                                    <div class="event-type-card" data-event-type="birthday">
                                        <div class="card h-100 border-0 event-card" style="border-radius: 15px; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-2 p-md-3 p-lg-4">
                                                <div class="occasion-icon mb-2 mb-md-3">
                                                    <i class="fas fa-birthday-cake fa-lg fa-md-2x text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1 mb-md-2 fs-6">Birthday</h6>
                                                <p class="small text-muted d-none d-md-block">Celebrate with beautiful arrangements</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-4">
                                    <div class="event-type-card" data-event-type="anniversary">
                                        <div class="card h-100 border-0 event-card" style="border-radius: 15px; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-2 p-md-3 p-lg-4">
                                                <div class="occasion-icon mb-2 mb-md-3">
                                                    <i class="fas fa-gem fa-lg fa-md-2x text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1 mb-md-2 fs-6">Anniversary</h6>
                                                <p class="small text-muted d-none d-md-block">Romantic arrangements for milestones</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-4">
                                    <div class="event-type-card" data-event-type="corporate">
                                        <div class="card h-100 border-0 event-card" style="border-radius: 15px; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-2 p-md-3 p-lg-4">
                                                <div class="occasion-icon mb-2 mb-md-3">
                                                    <i class="fas fa-briefcase fa-lg fa-md-2x text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1 mb-md-2 fs-6">Corporate Event</h6>
                                                <p class="small text-muted d-none d-md-block">Professional arrangements</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-4">
                                    <div class="event-type-card" data-event-type="funeral">
                                        <div class="card h-100 border-0 event-card" style="border-radius: 15px; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-2 p-md-3 p-lg-4">
                                                <div class="occasion-icon mb-2 mb-md-3">
                                                    <i class="fas fa-dove fa-lg fa-md-2x text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1 mb-md-2 fs-6">Funeral</h6>
                                                <p class="small text-muted d-none d-md-block">Sympathy arrangements</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-4">
                                    <div class="event-type-card" data-event-type="just_because">
                                        <div class="card h-100 border-0 event-card" style="border-radius: 15px; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-2 p-md-3 p-lg-4">
                                                <div class="occasion-icon mb-2 mb-md-3">
                                                    <i class="fas fa-sparkles fa-lg fa-md-2x text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1 mb-md-2 fs-6">Just Because</h6>
                                                <p class="small text-muted d-none d-md-block">Spread joy anytime</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="event_type" id="selectedEventType" required>
                        </div>

                        <!-- Event Details Form -->
                        <div class="mb-4 mb-md-5">
                            <h4 class="fw-bold text-dark mb-3 mb-md-4 fs-5 fs-md-4 text-center text-md-start" style="font-family: 'Playfair Display', serif;">EVENT DETAILS</h4>
                            <div class="row g-3 g-md-4">
                                <div class="col-12 col-md-6">
                                    <label for="event_date" class="form-label fw-bold text-dark fs-6">EVENT DATE</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                        </span>
                                        <input type="date" class="form-control form-control-lg border-0 bg-light" id="event_date" name="event_date" placeholder="MM/DD/YYYY" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="event_time" class="form-label fw-bold text-dark fs-6">TIME</label>
                                    <div class="input-group">
                                        <input type="time" class="form-control form-control-lg border-0 bg-light" id="event_time" name="event_time" placeholder="HH:MM AM/PM" required>
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-clock text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="venue" class="form-label fw-bold text-dark fs-6">DELIVERY ADDRESS</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg border-0 bg-light" id="venue" name="venue" placeholder="Street, City, Zip Code" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="recipient_name" class="form-label fw-bold text-dark fs-6">RECIPIENT'S NAME</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg border-0 bg-light" id="recipient_name" name="recipient_name" placeholder="First Name" required>
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="guest_count" class="form-label fw-bold text-dark fs-6">EXPECTED GUEST COUNT</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg border-0 bg-light" id="guest_count" name="guest_count" placeholder="Number of guests" min="1" required>
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-users text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personalization -->
                        <div class="mb-4 mb-md-5">
                            <h4 class="fw-bold text-dark mb-3 mb-md-4 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">PERSONALIZE YOUR MESSAGE</h4>
                            <div class="row g-3 g-md-4">
                                <div class="col-12">
                                    <label for="personalized_message" class="form-label fw-bold text-dark fs-6">PERSONALIZED MESSAGE FOR CARD</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 align-items-start pt-3 d-none d-md-flex">
                                            <i class="fas fa-heart text-muted"></i>
                                        </span>
                                        <textarea class="form-control form-control-lg border-0 bg-light" id="personalized_message" name="personalized_message" rows="3" rows-md="4" placeholder="Write your heartfelt message here..." style="resize: none;"></textarea>
                                        <span class="input-group-text bg-light border-0 align-items-start pt-3 d-none d-md-flex">
                                            <i class="fas fa-heart text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="special_instructions" class="form-label fw-bold text-dark fs-6">SPECIAL INSTRUCTIONS / PREFERENCES</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 align-items-start pt-3 d-none d-md-flex">
                                            <i class="fas fa-leaf text-muted"></i>
                                        </span>
                                        <textarea class="form-control form-control-lg border-0 bg-light" id="special_instructions" name="special_instructions" rows="2" rows-md="3" placeholder="E.g., 'No lilies', Prefer pastel roses, etc." style="resize: none;"></textarea>
                                        <span class="input-group-text bg-light border-0 align-items-start pt-3 d-none d-md-flex">
                                            <i class="fas fa-leaf text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="color_scheme" class="form-label fw-bold text-dark fs-6">PREFERRED COLOR SCHEME</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-palette text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg border-0 bg-light" id="color_scheme" name="color_scheme" placeholder="e.g., White and blush pink, Deep forest green">
                                    </div>
                                </div>
                            </div>
                    </div>

                        <!-- Contact Information -->
                        <div class="mb-4 mb-md-5">
                            <h4 class="fw-bold text-dark mb-3 mb-md-4 fs-5 fs-md-4 text-center text-md-start" style="font-family: 'Playfair Display', serif;">CONTACT INFORMATION</h4>
                            <div class="row g-3 g-md-4">
                                <div class="col-12 col-md-6">
                                    <label for="contact_phone" class="form-label fw-bold text-dark fs-6">PHONE NUMBER</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="tel" class="form-control form-control-lg border-0 bg-light" id="contact_phone" name="contact_phone" placeholder="+63 912 345 6789" required>
                    </div>
                    </div>
                                <div class="col-12 col-md-6">
                                    <label for="contact_email" class="form-label fw-bold text-dark fs-6">EMAIL ADDRESS</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" class="form-control form-control-lg border-0 bg-light" id="contact_email" name="contact_email" placeholder="your.email@example.com" required>
                    </div>
                    </div>
                    </div>
                    </div>

                        <!-- Action Buttons -->
                        <div class="text-center">
                            <div class="d-flex flex-column flex-md-row gap-2 gap-md-3 justify-content-center">
                                <button type="button" class="btn btn-outline-secondary btn-lg px-3 px-md-4 px-lg-5 py-2 py-md-3 fw-bold" id="saveDraftBtn">
                                    <i class="fas fa-save me-2"></i><span class="d-none d-sm-inline">SAVE DRAFT</span><span class="d-sm-none">SAVE</span>
                                </button>
                                <button type="submit" class="btn btn-success btn-lg px-3 px-md-4 px-lg-5 py-2 py-md-3 fw-bold" id="submitOrderBtn" disabled>
                                    <i class="fas fa-check me-2"></i><span class="d-none d-sm-inline">PROCEED TO ORDER</span><span class="d-sm-none">PROCEED</span>
                                </button>
                            </div>
                        </div>
                </form>
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

.event-type-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.event-type-card:hover .event-card {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border: 2px solid #28a745 !important;
}

.event-type-card.selected .event-card {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
    color: white !important;
    border: 2px solid #28a745 !important;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
}

.event-type-card.selected .occasion-icon i {
    color: white !important;
}

.event-type-card.selected h6,
.event-type-card.selected p {
    color: white !important;
}

.occasion-icon {
    transition: transform 0.3s ease;
}

.event-type-card:hover .occasion-icon {
    transform: scale(1.05);
}

.form-control-lg {
    border-radius: 12px;
    transition: all 0.3s ease;
    font-size: 1rem;
}

@media (min-width: 768px) {
    .form-control-lg {
        font-size: 1.1rem;
    }
}

.form-control-lg:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    background-color: #fff !important;
}

.input-group-text {
    border-radius: 12px 0 0 12px;
    font-size: 0.9rem;
}

@media (min-width: 768px) {
    .input-group-text {
        font-size: 1rem;
    }
}

.input-group .form-control:not(:first-child) {
    border-radius: 0 12px 12px 0;
}

.input-group .form-control:first-child {
    border-radius: 12px 0 0 12px;
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
        font-size: 1.8rem;
    }
    
    .fs-5 {
        font-size: 1rem !important;
    }
    
    .fs-6 {
        font-size: 0.85rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .form-control-lg {
        padding: 0.6rem 0.8rem;
        font-size: 0.9rem;
    }
    
    .btn-lg {
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
    }
    
    .event-type-card .card-body {
        padding: 0.75rem !important;
    }
    
    .event-type-card h6 {
        font-size: 0.8rem !important;
    }
    
    .event-type-card i {
        font-size: 1.2rem !important;
    }
}

/* Tablet responsive */
@media (min-width: 768px) and (max-width: 1023px) {
    .display-6 {
        font-size: 2.2rem;
    }
    
    .card-body {
        padding: 2rem !important;
    }
    
    .btn-lg {
        padding: 0.8rem 1.8rem;
        font-size: 0.9rem;
    }
}

/* Desktop responsive */
@media (min-width: 1024px) {
    .display-6 {
        font-size: 2.5rem;
    }
    
    .card-body {
        padding: 3rem !important;
    }
    
    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1rem;
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

/* Mobile-specific adjustments */
@media (max-width: 575px) {
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .event-type-card .card-body {
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventCards = document.querySelectorAll('.event-type-card');
    const selectedEventTypeInput = document.getElementById('selectedEventType');
    const form = document.getElementById('eventBookingForm');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const submitOrderBtn = document.getElementById('submitOrderBtn');
    
    // Event category selection
    eventCards.forEach(card => {
        card.addEventListener('click', function() {
            console.log('Event type card clicked');
            
            // Remove selected class from all cards
            eventCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Set the selected event type
            const eventType = this.getAttribute('data-event-type');
            selectedEventTypeInput.value = eventType;
            console.log('Event type set to:', eventType);
            
            // Enable form submission
            submitOrderBtn.disabled = false;
            submitOrderBtn.classList.remove('btn-secondary');
            submitOrderBtn.classList.add('btn-success');
            console.log('Button enabled');
        });
    });
    
    // Save draft functionality
    saveDraftBtn.addEventListener('click', function() {
        // Save form data to localStorage
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        localStorage.setItem('eventDraft', JSON.stringify(data));
        
        // Show success message
        Swal.fire({
            title: 'Draft Saved!',
            text: 'Your event details have been saved as draft.',
            icon: 'success',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    });
    
    // Load draft on page load
    const savedDraft = localStorage.getItem('eventDraft');
    if (savedDraft) {
        const data = JSON.parse(savedDraft);
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key];
            }
        });
        
        // Select the event type if saved
        if (data.event_type) {
            const eventCard = document.querySelector(`[data-event-type="${data.event_type}"]`);
            if (eventCard) {
                eventCard.classList.add('selected');
            }
        }
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        console.log('Form submitted');
        console.log('Selected event type:', selectedEventTypeInput.value);
        
        if (!selectedEventTypeInput.value) {
            e.preventDefault();
            Swal.fire({
                title: 'Event Type Required',
                text: 'Please select an event type first.',
                icon: 'warning',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Clear draft after successful submission
        localStorage.removeItem('eventDraft');
    });
    
    // Real-time form validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });
});
</script>
@endsection
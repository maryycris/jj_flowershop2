@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('customer.events.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <div>
                    <h1 class="h3 mb-0 fw-bold text-success">Event Details</h1>
                    <small class="text-muted">View your flower arrangement booking</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Event Information Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-{{ $event->event_type === 'Wedding' ? 'heart-fill' : ($event->event_type === 'Birthday' ? 'cake2-fill' : ($event->event_type === 'Funeral' ? 'flower1' : 'gift-fill')) }} me-2"></i>
                            {{ $event->event_type }} Arrangement
                        </h5>
                        <span class="badge bg-white text-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }} fs-6 px-3 py-2">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Event Date & Time -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-calendar-event text-success fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Event Date</h6>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}</p>
                                    @if($event->event_time)
                                    <small class="text-success">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Venue -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-geo-alt text-info fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Venue</h6>
                                    <p class="text-muted mb-0">{{ $event->venue }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient -->
                        @if($event->recipient_name)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-person text-warning fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Recipient</h6>
                                    <p class="text-muted mb-0">{{ $event->recipient_name }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Contact Number -->
                        @if($event->contact_number)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-telephone text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Contact Number</h6>
                                    <p class="text-muted mb-0">{{ $event->contact_number }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Special Instructions -->
                        @if($event->notes)
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-chat-quote text-secondary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2">Special Instructions</h6>
                                    <div class="alert alert-light border-0">
                                        <p class="mb-0">{{ $event->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Payment Information -->
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-credit-card text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-3">Payment Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3" style="background: #f8f9fa;">
                                                <span class="fw-bold text-muted">Payment Status:</span>
                                                @if($event->payment_status === 'paid')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Fully Paid
                                                    </span>
                                                @elseif($event->payment_status === 'partial')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock me-1"></i>Partially Paid
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Unpaid
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3" style="background: #f8f9fa;">
                                                <span class="fw-bold text-muted">Payment Method:</span>
                                                @if($event->payment_method)
                                                    <span class="badge bg-info text-uppercase">{{ $event->payment_method }}</span>
                                                @else
                                                    <span class="text-muted">Not selected</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($event->order_id)
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3" style="background: #f8f9fa;">
                                                <span class="fw-bold text-muted">Order ID:</span>
                                                <span class="badge bg-primary">{{ $event->order_id }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3" style="background: #f8f9fa;">
                                                <span class="fw-bold text-muted">Total Amount:</span>
                                                <span class="fw-bold text-success fs-5">₱{{ number_format($event->total ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Actions Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-file-text me-2"></i>
                        Invoice Options
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.events.invoice.view', $event->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-eye"></i> View Invoice
                        </a>
                        <a href="{{ route('customer.events.invoice.download', $event->id) }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Actions Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>
                        Booking Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted mb-2">Booking Status</h6>
                        <div class="d-flex align-items-center">
                            <div class="bg-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }} bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-{{ $event->status === 'pending' ? 'clock' : ($event->status === 'confirmed' ? 'check-circle' : ($event->status === 'completed' ? 'check-circle-fill' : 'x-circle')) }} text-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }}"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold text-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }}">
                                    {{ ucfirst($event->status) }}
                                </p>
                                @if($event->status === 'pending')
                                <small class="text-muted">Waiting for confirmation</small>
                                @elseif($event->status === 'confirmed')
                                <small class="text-muted">Event confirmed by staff</small>
                                @elseif($event->status === 'completed')
                                <small class="text-muted">Event completed successfully</small>
                                @else
                                <small class="text-muted">Event was cancelled</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-muted mb-2">Booking Date</h6>
                        <p class="mb-0">
                            <i class="bi bi-calendar-plus text-success me-2"></i>
                            {{ $event->created_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>

                    @if($event->updated_at != $event->created_at)
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted mb-2">Last Updated</h6>
                        <p class="mb-0">
                            <i class="bi bi-clock-history text-info me-2"></i>
                            {{ $event->updated_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.events.book') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>
                            Book Another Event
                        </a>
                        <a href="{{ route('customer.events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-2"></i>
                            View All Events
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Support Card -->
            <div class="card border-0 shadow-lg mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-headset me-2"></i>
                        Need Help?
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-3">Have questions about your event booking? Our support team is here to help.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.chat.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-chat-dots me-2"></i>
                            Chat Support
                        </a>
                        <a href="tel:+1234567890" class="btn btn-outline-primary">
                            <i class="bi bi-telephone me-2"></i>
                            Call Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.bg-success.bg-opacity-10 {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.bg-info.bg-opacity-10 {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.bg-secondary.bg-opacity-10 {
    background-color: rgba(108, 117, 125, 0.1) !important;
}

.alert-light {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
    border: none;
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.badge {
    border-radius: 20px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .d-flex.align-items-start {
        flex-direction: column;
        text-align: center;
    }
    
    .d-flex.align-items-start .bg-opacity-10 {
        margin-bottom: 1rem;
        margin-right: 0 !important;
    }
}
</style>
@endsection

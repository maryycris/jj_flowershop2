@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-6 fw-bold text-success mb-2">MY EVENT BOOKINGS</h1>
            <p class="lead text-muted">Track and manage your flower arrangements</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('customer.events.book') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>
                    Book New Event
                </a>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="filterEvents('all')">
                        <i class="bi bi-list-ul me-1"></i> All
                    </button>
                    <button class="btn btn-outline-warning" onclick="filterEvents('pending')">
                        <i class="bi bi-clock me-1"></i> Pending
                    </button>
                    <button class="btn btn-outline-info" onclick="filterEvents('confirmed')">
                        <i class="bi bi-check-circle me-1"></i> Confirmed
                    </button>
                    <button class="btn btn-outline-success" onclick="filterEvents('completed')">
                        <i class="bi bi-check-circle-fill me-1"></i> Completed
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div class="row" id="eventsContainer">
        @forelse($events as $event)
        <div class="col-lg-6 col-xl-4 mb-4 event-card" data-status="{{ $event->status }}">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-{{ $event->event_type === 'Wedding' ? 'heart-fill' : ($event->event_type === 'Birthday' ? 'cake2-fill' : ($event->event_type === 'Funeral' ? 'flower1' : 'gift-fill')) }} me-2"></i>
                            {{ $event->event_type }}
                        </h6>
                        <span class="badge bg-white text-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-event text-success me-2"></i>
                                <strong>Date:</strong>
                                <span class="ms-2">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</span>
                            </div>
                            @if($event->event_time)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-clock text-success me-2"></i>
                                <strong>Time:</strong>
                                <span class="ms-2">{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</span>
                            </div>
                            @endif
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-geo-alt text-success me-2"></i>
                                <strong>Venue:</strong>
                                <span class="ms-2">{{ Str::limit($event->venue, 30) }}</span>
                            </div>
                            @if($event->recipient_name)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person text-success me-2"></i>
                                <strong>Recipient:</strong>
                                <span class="ms-2">{{ $event->recipient_name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($event->notes)
                    <div class="mt-3">
                        <div class="alert alert-light border-0">
                            <small class="text-muted">
                                <i class="bi bi-chat-quote me-1"></i>
                                <strong>Notes:</strong> {{ Str::limit($event->notes, 100) }}
                            </small>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Booked on {{ $event->created_at->format('M d, Y \a\t g:i A') }}
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.events.show', $event) }}" class="btn btn-outline-success">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h4 class="text-muted mb-3">No Event Bookings Yet</h4>
                <p class="text-muted mb-4">Start by booking your first flower arrangement for any special occasion.</p>
                <a href="{{ route('customer.events.book') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>
                    Book Your First Event
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $events->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

<style>
.event-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

.btn-lg {
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-outline-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.alert-light {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
}

.badge {
    border-radius: 20px;
    font-weight: 600;
    padding: 0.5rem 1rem;
}

.display-6 {
    background: linear-gradient(135deg, #28a745, #20c997);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.lead {
    color: #6c757d !important;
}
</style>

<script>
function filterEvents(status) {
    const eventCards = document.querySelectorAll('.event-card');
    const buttons = document.querySelectorAll('[onclick^="filterEvents"]');
    
    // Update button states
    buttons.forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
    });
    
    if (status !== 'all') {
        const activeBtn = document.querySelector(`[onclick="filterEvents('${status}')"]`);
        if (activeBtn) {
            activeBtn.classList.remove('btn-outline-success');
            activeBtn.classList.add('btn-success');
        }
    } else {
        const allBtn = document.querySelector('[onclick="filterEvents(\'all\')"]');
        if (allBtn) {
            allBtn.classList.remove('btn-outline-success');
            allBtn.classList.add('btn-success');
        }
    }
    
    // Filter cards
    eventCards.forEach(card => {
        if (status === 'all' || card.getAttribute('data-status') === status) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Add fadeIn animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// Initialize with all events shown
document.addEventListener('DOMContentLoaded', function() {
    const allBtn = document.querySelector('[onclick="filterEvents(\'all\')"]');
    if (allBtn) {
        allBtn.classList.remove('btn-outline-success');
        allBtn.classList.add('btn-success');
    }
});
</script>
@endsection


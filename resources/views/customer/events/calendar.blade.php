@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2 py-md-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <!-- Header -->
    <div class="row mb-3 mb-md-5">
        <div class="col-12 px-3">
            <div class="text-center">
                <h1 class="display-6 display-md-4 fw-bold text-dark mb-2 mb-md-3" style="font-family: 'Playfair Display', serif;">Event Calendar</h1>
                <p class="lead text-muted d-none d-md-block">View and manage your upcoming events</p>
                <p class="text-muted d-md-none">View and manage your upcoming events</p>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <!-- Calendar Sidebar -->
        <div class="col-12 col-lg-3 order-2 order-lg-1">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header bg-light border-0 py-4">
                    <h4 class="fw-bold text-dark mb-0" style="font-family: 'Playfair Display', serif;">Quick Actions</h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <a href="{{ route('customer.events.book') }}" class="btn btn-success btn-lg py-3 fw-bold">
                            <i class="fas fa-plus me-2"></i>Book New Event
                        </a>
                        <a href="{{ route('customer.events.index') }}" class="btn btn-outline-secondary btn-lg py-3 fw-bold">
                            <i class="fas fa-list me-2"></i>View All Events
                        </a>
                    </div>
                </div>
            </div>

            <!-- Event Stats -->
            <div class="card shadow-lg border-0 mt-4" style="border-radius: 20px;">
                <div class="card-header bg-light border-0 py-4">
                    <h4 class="fw-bold text-dark mb-0" style="font-family: 'Playfair Display', serif;">Event Statistics</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded-3" style="background: #e8f5e8;">
                                <h3 class="fw-bold text-success mb-1">{{ $events->where('status', 'confirmed')->count() }}</h3>
                                <p class="small text-muted mb-0">Confirmed</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3" style="background: #fff3cd;">
                                <h3 class="fw-bold text-warning mb-1">{{ $events->where('status', 'pending')->count() }}</h3>
                                <p class="small text-muted mb-0">Pending</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3" style="background: #d1ecf1;">
                                <h3 class="fw-bold text-info mb-1">{{ $events->where('status', 'completed')->count() }}</h3>
                                <p class="small text-muted mb-0">Completed</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3" style="background: #f8d7da;">
                                <h3 class="fw-bold text-danger mb-1">{{ $events->where('status', 'cancelled')->count() }}</h3>
                                <p class="small text-muted mb-0">Cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Main Content -->
        <div class="col-12 col-lg-9 order-1 order-lg-2">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header bg-light border-0 py-3 py-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4" style="font-family: 'Playfair Display', serif;">Event Calendar</h4>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-md" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-md" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="eventDetails">
                    <!-- Event details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-success" id="editEventBtn">Edit Event</a>
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
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
}

/* FullCalendar Custom Styling */
.fc {
    font-family: 'Lato', sans-serif;
}

.fc-toolbar-title {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    color: #2c3e50;
}

.fc-button {
    border-radius: 8px !important;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.fc-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.fc-button-primary {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.fc-button-primary:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
}

.fc-event {
    border-radius: 8px !important;
    border: none !important;
    padding: 4px 8px !important;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.fc-event-title {
    font-weight: 600;
}

/* Event status colors */
.event-confirmed {
    background-color: #28a745 !important;
    color: white !important;
}

.event-pending {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.event-completed {
    background-color: #17a2b8 !important;
    color: white !important;
}

.event-cancelled {
    background-color: #dc3545 !important;
    color: white !important;
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

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    
    // Event data from Laravel
    const events = @json($events->map(function($event) {
        return [
            'id' => $event->id,
            'title' => ucfirst(str_replace('_', ' ', $event->event_type)),
            'start' => $event->event_date,
            'className' => 'event-' . $event->status,
            'extendedProps' => [
                'event_type' => $event->event_type,
                'venue' => $event->venue,
                'time' => $event->event_time,
                'status' => $event->status,
                'recipient_name' => $event->recipient_name,
                'guest_count' => $event->guest_count,
                'personalized_message' => $event->personalized_message,
                'special_instructions' => $event->special_instructions,
                'created_at' => $event->created_at
            ]
        ];
    }));
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        events: events,
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', info.event.title + ' - ' + info.event.extendedProps.status);
        },
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        height: 'auto',
        aspectRatio: 1.8
    });
    
    calendar.render();
    
    // Navigation buttons
    document.getElementById('prevMonth').addEventListener('click', function() {
        calendar.prev();
    });
    
    document.getElementById('nextMonth').addEventListener('click', function() {
        calendar.next();
    });
    
    function showEventDetails(event) {
        const props = event.extendedProps;
        const eventDetails = document.getElementById('eventDetails');
        
        eventDetails.innerHTML = `
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Event Type</h6>
                    <p class="text-muted">${event.title}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Status</h6>
                    <span class="badge bg-${getStatusColor(props.status)} text-uppercase">${props.status}</span>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Date</h6>
                    <p class="text-muted">${event.start.toLocaleDateString()}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Time</h6>
                    <p class="text-muted">${props.time ? new Date('2000-01-01T' + props.time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A'}</p>
                </div>
                <div class="col-12">
                    <h6 class="fw-bold text-dark mb-2">Venue</h6>
                    <p class="text-muted">${props.venue || 'N/A'}</p>
                </div>
                ${props.recipient_name ? `
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Recipient</h6>
                    <p class="text-muted">${props.recipient_name}</p>
                </div>
                ` : ''}
                ${props.guest_count ? `
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Guest Count</h6>
                    <p class="text-muted">${props.guest_count} guests</p>
                </div>
                ` : ''}
                ${props.personalized_message ? `
                <div class="col-12">
                    <h6 class="fw-bold text-dark mb-2">Personalized Message</h6>
                    <p class="text-muted">${props.personalized_message}</p>
                </div>
                ` : ''}
                ${props.special_instructions ? `
                <div class="col-12">
                    <h6 class="fw-bold text-dark mb-2">Special Instructions</h6>
                    <p class="text-muted">${props.special_instructions}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('editEventBtn').href = `/customer/events/${event.id}/edit`;
        eventModal.show();
    }
    
    function getStatusColor(status) {
        const colors = {
            'confirmed': 'success',
            'pending': 'warning',
            'completed': 'info',
            'cancelled': 'danger'
        };
        return colors[status] || 'secondary';
    }
});
</script>
@endsection
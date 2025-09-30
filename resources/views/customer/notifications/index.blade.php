@extends('layouts.customer_app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid py-4" style="min-height: calc(100vh - 200px);">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="fas fa-bell text-primary me-2"></i>
                        Notifications
                    </h2>
                    <p class="text-muted mb-0">Stay updated with your event status</p>
    </div>
                <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-1"></i> Mark All Read
                </button>
            </div>

            <!-- Notifications List -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div id="notificationsList">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                        </div>
                        </div>
                </div>
                </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
    .notification-item {
        transition: background-color 0.2s ease;
    }
    .notification-item:hover {
        background-color: #f8f9fa !important;
    }
    .notification-item:last-child {
        border-bottom: none !important;
    }
    .card {
        border-radius: 0.5rem;
    }
    .card-body {
        padding: 0;
    }
    .notification-item {
        min-height: auto;
    }
    .card {
        max-width: 100%;
    }
    .notification-item .d-flex {
        gap: 0.25rem;
    }
    .notification-item .flex-grow-1 {
        min-width: 0;
    }
    
    /* Ensure footer stays at bottom */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    main {
        flex: 1;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

function loadNotifications() {
    fetch('{{ route("customer.notifications.list") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            displayNotifications(Array.isArray(data) ? data : []);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationsList').innerHTML = `
                <div class="text-center py-5 text-muted">
                    Failed to load notifications. Please refresh the page.
                </div>`;
        });
}

function displayNotifications(notifications) {
    const container = document.getElementById('notificationsList');
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No notifications yet</h5>
                <p class="text-muted">You'll receive notifications when your event status changes</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = notifications.map(notification => `
        <div class="notification-item p-3 border-bottom ${notification.read_at ? '' : 'bg-light'}" 
             onclick="markAsRead(${notification.id})" style="cursor: pointer;">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-${getNotificationIcon(notification.type)} text-primary"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 ${notification.read_at ? 'text-muted' : 'fw-bold'}">${notification.title}</h6>
                    <p class="mb-1 text-muted">${notification.message}</p>
                    <small class="text-muted">${formatDate(notification.created_at)}</small>
                </div>
                ${!notification.read_at ? '<div class="badge bg-primary rounded-pill">New</div>' : ''}
            </div>
        </div>
    `).join('');
}

function getNotificationIcon(type) {
    switch(type) {
        case 'event_status_change': return 'calendar-check';
        default: return 'bell';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + ' minutes ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + ' hours ago';
    return date.toLocaleDateString();
}

function markAsRead(notificationId) {
    fetch(`{{ url('customer/notifications') }}/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
            loadNotifications();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("customer.notifications.markAllAsRead") }}', {
                    method: 'POST',
                    headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
            loadNotifications();
        }
    });
}
</script>
@endsection
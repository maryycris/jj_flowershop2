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
                <button class="btn btn-outline-primary" id="markAllReadBtn" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-1"></i> Mark All Read
                </button>
            </div>

            <!-- Notifications List -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div id="notificationsList">
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
                                <div class="notification-item p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}" 
                                     onclick="markAsRead({{ $notification->id }})" style="cursor: pointer;">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-{{ $notification->data['type'] ?? 'bell' === 'event_status_change' ? 'calendar-check' : 'bell' }} text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                                {{ $notification->data['title'] ?? $notification->data['message'] ?? 'Notification' }}
                                            </h6>
                                            <p class="mb-1 text-muted">{{ $notification->data['message'] ?? $notification->data['body'] ?? 'No message' }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->read_at)
                                            <div class="badge bg-primary rounded-pill">New</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No notifications yet</h5>
                                <p class="text-muted">You'll receive notifications when your event status changes</p>
                            </div>
                        @endif
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
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }
    .notification-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .notification-item:last-child {
        border-bottom: none !important;
    }
    .notification-item::after {
        content: 'Click to mark as read';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        color: #6c757d;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .notification-item:hover::after {
        opacity: 1;
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
function markAsRead(notificationId) {
    console.log('Marking notification as read:', notificationId);
    
    // Find the notification item and add loading state
    const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`);
    if (notificationItem) {
        notificationItem.style.opacity = '0.6';
        notificationItem.style.pointerEvents = 'none';
    }
    
    fetch(`{{ url('customer/notifications') }}/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Mark as read response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Mark as read response data:', data);
        if (data.success) {
            // Show success message
            alert('Notification marked as read!');
            // Reload the page to show updated notifications
            window.location.reload();
        } else {
            alert('Failed to mark notification as read.');
            // Reset loading state
            if (notificationItem) {
                notificationItem.style.opacity = '1';
                notificationItem.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        alert('Error: ' + error.message);
        // Reset loading state
        if (notificationItem) {
            notificationItem.style.opacity = '1';
            notificationItem.style.pointerEvents = 'auto';
        }
    });
}

function markAllAsRead() {
    console.log('Marking all notifications as read');
    
    if (!confirm('Are you sure you want to mark all notifications as read?')) {
        return;
    }
    
    // Add loading state to button
    const markAllBtn = document.getElementById('markAllReadBtn');
    const originalText = markAllBtn.innerHTML;
    markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Marking...';
    markAllBtn.disabled = true;
    
    fetch('{{ route("customer.notifications.markAllAsRead") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Mark all as read response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Mark all as read response data:', data);
        if (data.success) {
            // Show success message
            alert('All notifications marked as read!');
            // Reload the page to show updated notifications
            window.location.reload();
        } else {
            alert('Failed to mark all notifications as read.');
            // Reset button state
            markAllBtn.innerHTML = originalText;
            markAllBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
        alert('Error: ' + error.message);
        // Reset button state
        markAllBtn.innerHTML = originalText;
        markAllBtn.disabled = false;
    });
}

// Add click event listeners when the page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Notifications page loaded');
    
    // Add click event listener to the Mark All Read button
    const markAllButton = document.querySelector('button[onclick="markAllAsRead()"]');
    if (markAllButton) {
        markAllButton.addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });
    }
    
    // Add click event listeners to notification items
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.getAttribute('onclick').match(/\d+/)[0];
            markAsRead(notificationId);
        });
    });
});
</script>
@endsection
@extends('layouts.customer_app')

@section('customer_content')
<div class="container-fluid pt-4">
    <h1 class="h3 mb-4 text-gray-800">Notifications</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1"><i class="fas fa-bell me-2"></i>Notifications</h4>
                    <p class="text-muted mb-0">Stay updated with your event status</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i>Mark All Read
                    </button>
                </div>
            </div>

            <div class="notifications-container">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            
                            // Handle different notification data formats
                            if (isset($data['title'])) {
                                // New format with rich data
                                $isClickable = !empty($data['action_url']);
                                $icon = $data['icon'] ?? 'fas fa-bell';
                                $color = $data['color'] ?? 'primary';
                                $title = $data['title'];
                                $message = $data['message'] ?? 'No message';
                            } else {
                                // Legacy format - create rich display from basic data
                                $isClickable = false;
                                $icon = 'fas fa-bell';
                                $color = 'primary';
                                $title = 'Order Update';
                                $message = $data['message'] ?? 'No message';
                                
                                // Add order-specific styling
                                if (isset($data['order_id'])) {
                                    $title = 'Order #' . $data['order_id'];
                                    $isClickable = true;
                                    $icon = 'fas fa-shopping-bag';
                                    $color = 'success';
                                }
                            }
                        @endphp
                        
                        <div class="notification-item p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}" 
                             data-notification-id="{{ $notification->id }}"
                             data-clickable="{{ $isClickable ? 'true' : 'false' }}"
                             data-action-url="{{ $isClickable ? $data['action_url'] : '' }}"
                             style="cursor: pointer; transition: all 0.2s ease; {{ $isClickable ? 'border-left: 4px solid #007bff;' : '' }}"
                             onclick="handleNotificationClick('{{ $notification->id }}', '{{ $isClickable ? $data['action_url'] : '' }}')">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="{{ $icon }} text-{{ $color }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                        {{ $title }}
                                    </h6>
                                    <p class="mb-1 text-muted">{{ $message }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    @if(!$notification->read_at)
                                        <div class="badge bg-{{ $color }} rounded-pill me-2">New</div>
                                    @endif
                                    @if($isClickable)
                                        <i class="fas fa-external-link-alt text-muted"></i>
                                    @endif
                                </div>
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
@endsection

@section('scripts')
<script>
function markAllAsRead() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Marking as read...';
    button.disabled = true;

    // Make AJAX request
    fetch('{{ route("customer.notifications.markAllAsRead") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated notifications
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to mark notifications as read'));
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking notifications as read');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function handleNotificationClick(notificationId, actionUrl) {
    // Mark notification as read first
    markNotificationAsRead(notificationId);
    
    // Then navigate if there's an action URL
    if (actionUrl) {
        setTimeout(() => {
            window.location.href = actionUrl;
        }, 500);
    }
}

function markNotificationAsRead(notificationId) {
    fetch(`{{ url('customer/notifications') }}/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update visual state
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-light');
                notificationItem.classList.add('text-muted');
                
                // Remove "New" badge
                const badge = notificationItem.querySelector('.badge');
                if (badge) {
                    badge.remove();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Add hover effects
document.addEventListener('DOMContentLoaded', function() {
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('bg-light')) {
                this.style.backgroundColor = '';
            }
        });
    });
});
</script>
@endsection
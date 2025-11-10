@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4 notifications-page" style="min-height: 60vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow mb-4" style="background: white; border-radius: 8px;">
        <div class="card-body notification-card-body" style="padding: 1.5rem;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1 notification-title"><i class="fas fa-bell me-2"></i>Notifications</h4>
                </div>
            </div>

            <!-- Tabs for All and Hidden Notifications -->
            <ul class="nav nav-tabs mb-4 notification-tabs" id="notificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active notification-tab-btn" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-notifications" type="button" role="tab" aria-controls="all-notifications" aria-selected="true" style="color: #000 !important; font-weight: 600 !important;">
                        <i class="fas fa-list me-1" style="color: #000 !important;"></i>
                        <span class="notification-tab-text" style="color: #000 !important; font-weight: 600 !important;">All Notifications</span>
                        <span class="badge bg-primary ms-2 notification-badge" id="all-count">{{ $notifications->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link notification-tab-btn" id="hidden-tab" data-bs-toggle="tab" data-bs-target="#hidden-notifications" type="button" role="tab" aria-controls="hidden-notifications" aria-selected="false" style="color: #000 !important; font-weight: 600 !important;">
                        <i class="fas fa-eye-slash me-1" style="color: #000 !important;"></i>
                        <span class="notification-tab-text" style="color: #000 !important; font-weight: 600 !important;">Hidden</span>
                        <span class="badge bg-secondary ms-2 notification-badge" id="hidden-count">0</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="notificationTabsContent">
                <!-- All Notifications Tab -->
                <div class="tab-pane fade show active" id="all-notifications" role="tabpanel" aria-labelledby="all-tab">
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
                             style="cursor: pointer; transition: all 0.2s ease; {{ $isClickable ? 'border-left: 4px solid #007bff;' : '' }}; border-radius: 8px; margin-bottom: 8px;"
                             onclick="handleNotificationClick('{{ $notification->id }}', '{{ $isClickable ? $data['action_url'] : '' }}')"
                             onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.transform='translateX(5px)';"
                             onmouseout="this.style.backgroundColor='{{ $notification->read_at ? '' : '#f8f9fa' }}'; this.style.transform='translateX(0px)';">
                            <div class="d-flex align-items-start notification-content-wrapper">
                                <div class="me-2 me-md-3 notification-icon-wrapper">
                                    <i class="{{ $icon }} text-{{ $color }} notification-icon"></i>
                                </div>
                                <div class="flex-grow-1 notification-text-wrapper">
                                    <h6 class="mb-1 notification-title-text {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}" style="font-size: 1.1rem; color: #2c3e50;">
                                        {{ $title }}
                                    </h6>
                                    <p class="mb-1 notification-message" style="color: #555; font-size: 0.95rem; line-height: 1.4;">{{ $message }}</p>
                                    <small class="text-muted notification-time" style="font-size: 0.8rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex align-items-center flex-column notification-actions-wrapper">
                                    <div class="d-flex align-items-center mb-1">
                                    @if(!$notification->read_at)
                                            <div class="badge bg-{{ $color }} rounded-pill me-2 notification-new-badge" style="font-size: 0.7rem;">New</div>
                                    @endif
                                    @if($isClickable)
                                            <div class="d-flex align-items-center text-primary me-2 notification-click-link" style="font-size: 0.8rem;">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                                <span class="d-none d-sm-inline">Click to view</span>
                                                <span class="d-inline d-sm-none">View</span>
                                        </div>
                                    @endif
                                    </div>
                                    
                                    <!-- Three dots menu -->
                                    <div class="dropdown notification-menu" style="position: relative;">
                                        <button class="btn btn-link text-muted p-1 notification-menu-btn" type="button" id="notificationMenu{{ $notification->id }}" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu notification-dropdown-right" aria-labelledby="notificationMenu{{ $notification->id }}">
                                            @if($notification->read_at)
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); event.stopPropagation(); markAsUnread('{{ $notification->id }}')">
                                                        <i class="fas fa-envelope me-2"></i>Mark as Unread
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); event.stopPropagation(); markAsRead('{{ $notification->id }}')">
                                                        <i class="fas fa-envelope-open me-2"></i>Mark as Read
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); event.stopPropagation(); hideNotification('{{ $notification->id }}')">
                                                    <i class="fas fa-eye-slash me-2"></i>Hide
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); event.stopPropagation(); deleteNotification('{{ $notification->id }}')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px; margin: 2rem 0;">
                        <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No notifications yet</h5>
                        <p class="text-muted">You'll receive notifications when your order status changes</p>
                    </div>
                @endif
                    </div>
                </div>

                <!-- Hidden Notifications Tab -->
                <div class="tab-pane fade" id="hidden-notifications" role="tabpanel" aria-labelledby="hidden-tab">
                    <div class="notifications-container" id="hidden-notifications-container">
                        <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px; margin: 2rem 0;">
                            <i class="fas fa-eye-slash text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No hidden notifications</h5>
                            <p class="text-muted">Hidden notifications will appear here</p>
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
.nav-tabs#notificationTabs .nav-link,
#notificationTabs .nav-link {
    color: #000 !important; /* make tab labels black for visibility */
    font-weight: 500 !important;
}

#notificationTabs .nav-link.active {
    color: #000 !important;
    font-weight: 600 !important;
    background-color: #e9ecef !important;
}

#notificationTabs .nav-link:hover {
    color: #000 !important;
}

/* Ensure text is always visible */
#notificationTabs .nav-link span,
#notificationTabs .nav-link i {
    color: #000 !important;
}

.notification-dropdown-right {
    position: absolute !important;
    right: 0 !important;
    top: 0 !important;
    transform: translateX(100%) !important;
    margin-left: 8px !important;
    z-index: 1050 !important;
    min-width: 180px !important;
}

/* Mobile Responsive Styles for Notifications */
@media (max-width: 650px) {
    .notifications-page {
        padding: 0.5rem 0.25rem 5rem 0.25rem !important; /* extra bottom space for sticky nav */
    }
    
    .notification-card-body {
        padding: 1rem !important;
    }
    /* Constrain card width on small screens */
    .notifications-page .card { width: 95%; margin-left: auto; margin-right: auto; }
    
    .notification-title {
        font-size: 1.3rem !important;
    }
    
    .notification-tabs {
        font-size: 0.85rem !important;
    }
    
    .notification-tab-btn {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.85rem !important;
    }
    
    .notification-tab-text {
        font-size: 0.85rem !important;
    }
    
    .notification-badge {
        font-size: 0.65rem !important;
        padding: 0.2rem 0.4rem !important;
    }
    
    .notification-item {
        padding: 0.75rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    .notification-content-wrapper {
        flex-wrap: nowrap !important;
    }
    
    .notification-icon-wrapper {
        margin-right: 0.5rem !important;
        flex-shrink: 0;
    }
    
    .notification-icon {
        font-size: 1rem !important;
    }
    
    .notification-text-wrapper {
        min-width: 0;
        flex: 1;
    }
    
    .notification-title-text {
        font-size: 0.95rem !important;
        line-height: 1.3 !important;
        margin-bottom: 0.25rem !important;
    }
    
    .notification-message {
        font-size: 0.85rem !important;
        line-height: 1.4 !important;
        margin-bottom: 0.25rem !important;
    }
    
    .notification-time {
        font-size: 0.7rem !important;
    }
    
    .notification-actions-wrapper {
        flex-shrink: 0;
        align-items: flex-end !important;
    }
    
    .notification-new-badge {
        font-size: 0.6rem !important;
        padding: 0.15rem 0.35rem !important;
    }
    
    .notification-click-link {
        font-size: 0.7rem !important;
    }
    
    .notification-click-link span {
        font-size: 0.7rem !important;
    }
    
    .notification-menu-btn {
        padding: 0.25rem !important;
        font-size: 0.85rem !important;
    }
    
    /* Ensure the dropdown appears correctly on mobile */
    .notification-dropdown-right {
        transform: translateX(0) !important;
        right: auto !important;
        left: 0 !important;
        margin-left: 0 !important;
        min-width: 160px !important;
    }
    
    /* Remove hover transform on mobile */
    .notification-item:hover {
        transform: none !important;
    }
    
    /* Empty state adjustments */
    .notification-item + div.text-center {
        padding: 2rem 1rem !important;
    }
    
    .notification-item + div.text-center i {
        font-size: 2rem !important;
    }
    
    .notification-item + div.text-center h5 {
        font-size: 1rem !important;
    }
    
    .notification-item + div.text-center p {
        font-size: 0.85rem !important;
    }
}

@media (max-width: 768px) {
    .notification-dropdown-right {
        transform: translateX(0) !important;
        right: auto !important;
        left: 0 !important;
        margin-left: 0 !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Individual notification actions
function markAsRead(notificationId) {
    fetch(`{{ url('customer/notifications') }}/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            // Update the notification item visually
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-light');
                notificationItem.style.backgroundColor = '';
                
                // Update the title to show as read
                const title = notificationItem.querySelector('h6');
                if (title) {
                    title.classList.add('text-muted');
                    title.classList.remove('fw-bold');
                }
                
                // Remove "New" badge
                const newBadge = notificationItem.querySelector('.badge');
                if (newBadge) {
                    newBadge.remove();
                }
                
                // Update the menu
                const menuButton = notificationItem.querySelector(`#notificationMenu${notificationId}`);
                if (menuButton) {
                    const menu = menuButton.nextElementSibling;
                    const readOption = menu.querySelector('a[onclick*="markAsRead"]');
                    const unreadOption = menu.querySelector('a[onclick*="markAsUnread"]');
                    if (readOption && unreadOption) {
                        readOption.style.display = 'none';
                        unreadOption.style.display = 'block';
                    }
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAsUnread(notificationId) {
    fetch(`{{ url('customer/notifications') }}/${notificationId}/unread`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            // Update the notification item visually
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.add('bg-light');
                notificationItem.style.backgroundColor = '#f8f9fa';
                
                // Update the title to show as unread
                const title = notificationItem.querySelector('h6');
                if (title) {
                    title.classList.remove('text-muted');
                    title.classList.add('fw-bold');
                }
                
                // Add "New" badge
                const badgeContainer = notificationItem.querySelector('.d-flex.align-items-center');
                if (badgeContainer && !badgeContainer.querySelector('.badge')) {
                    const newBadge = document.createElement('div');
                    newBadge.className = 'badge bg-primary rounded-pill me-2';
                    newBadge.style.fontSize = '0.7rem';
                    newBadge.textContent = 'New';
                    badgeContainer.insertBefore(newBadge, badgeContainer.firstChild);
                }
                
                // Update the menu
                const menuButton = notificationItem.querySelector(`#notificationMenu${notificationId}`);
                if (menuButton) {
                    const menu = menuButton.nextElementSibling;
                    const readOption = menu.querySelector('a[onclick*="markAsRead"]');
                    const unreadOption = menu.querySelector('a[onclick*="markAsUnread"]');
                    if (readOption && unreadOption) {
                        readOption.style.display = 'block';
                        unreadOption.style.display = 'none';
                    }
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as unread:', error);
    });
}

function hideNotification(notificationId) {
    if (confirm('Are you sure you want to hide this notification?')) {
        fetch(`{{ url('customer/notifications') }}/${notificationId}/hide`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Hide the notification item
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error hiding notification:', error);
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification? This action cannot be undone.')) {
        fetch(`{{ url('customer/notifications') }}/${notificationId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Remove the notification item
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.remove();
                }
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
        });
    }
}

function unhideNotification(notificationId) {
    fetch(`{{ url('customer/notifications') }}/${notificationId}/unhide`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            // Remove the notification from hidden list
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.remove();
            }
            
            // Reload the page to show the unhidden notification in the main list
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error unhiding notification:', error);
    });
}

function loadHiddenNotifications() {
    fetch('{{ route("customer.notifications.hidden") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(notifications => {
        const container = document.getElementById('hidden-notifications-container');
        const hiddenCount = document.getElementById('hidden-count');
        
        // Update hidden count
        hiddenCount.textContent = notifications.length;
        
        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px; margin: 2rem 0;">
                    <i class="fas fa-eye-slash text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No hidden notifications</h5>
                    <p class="text-muted">Hidden notifications will appear here</p>
                </div>
            `;
        } else {
            let html = '';
            notifications.forEach(notification => {
                const data = notification.data;
                const title = data.title || 'Notification';
                const message = data.message || 'No message';
                const icon = data.icon || 'fas fa-bell';
                const color = data.color || 'primary';
                const createdAt = new Date(notification.created_at).toLocaleString();
                
                html += `
                    <div class="notification-item p-3 border-bottom bg-light" 
                         data-notification-id="${notification.id}"
                         style="cursor: pointer; transition: all 0.2s ease; border-radius: 8px; margin-bottom: 8px; opacity: 0.7;">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="${icon} text-${color}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted" style="font-size: 1.1rem; color: #2c3e50;">
                                    ${title}
                                </h6>
                                <p class="mb-1" style="color: #555; font-size: 0.95rem; line-height: 1.4;">${message}</p>
                                <small class="text-muted" style="font-size: 0.8rem;">${createdAt}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="badge bg-secondary rounded-pill me-2" style="font-size: 0.7rem;">Hidden</div>
                                
                                <!-- Three dots menu for hidden notifications -->
                                <div class="dropdown" style="position: relative;">
                                    <button class="btn btn-link text-muted p-1" type="button" id="hiddenMenu${notification.id}" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu notification-dropdown-right" aria-labelledby="hiddenMenu${notification.id}">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); event.stopPropagation(); unhideNotification('${notification.id}')">
                                                <i class="fas fa-eye me-2"></i>Unhide
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); event.stopPropagation(); deleteNotification('${notification.id}')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    })
    .catch(error => {
        console.error('Error loading hidden notifications:', error);
    });
}

function handleNotificationClick(notificationId, actionUrl) {
    // Add visual feedback
    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationItem) {
        notificationItem.style.backgroundColor = '#d4edda';
        notificationItem.style.borderLeft = '4px solid #28a745';
    }
    
    // Mark notification as read first
    markNotificationAsRead(notificationId);
    
    // Then navigate if there's an action URL
    if (actionUrl) {
        // Show "Open" status without loading spinner
        if (notificationItem) {
            const clickIndicator = notificationItem.querySelector('.text-primary');
            if (clickIndicator) {
                clickIndicator.innerHTML = '<i class="fas fa-external-link-alt me-1"></i><span>Open</span>';
            }
        }
        
        setTimeout(() => {
            window.location.href = actionUrl;
        }, 300);
    } else {
        // If no action URL, just mark as read
        setTimeout(() => {
            if (notificationItem) {
                notificationItem.style.backgroundColor = '#f8f9fa';
            }
        }, 1000);
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

// Add hover effects and reset any loading states
document.addEventListener('DOMContentLoaded', function() {
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        // Reset any loading states that might be persisted
        const clickIndicator = item.querySelector('.text-primary');
        if (clickIndicator && clickIndicator.innerHTML.includes('Opening')) {
            clickIndicator.innerHTML = '<i class="fas fa-external-link-alt me-1"></i><span>Click to view</span>';
        }
        
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('bg-light')) {
                this.style.backgroundColor = '';
            }
        });
    });

    // Load hidden notifications when hidden tab is clicked
    const hiddenTab = document.getElementById('hidden-tab');
    if (hiddenTab) {
        hiddenTab.addEventListener('click', function() {
            loadHiddenNotifications();
        });
    }
});
</script>
@endsection
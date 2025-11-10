@extends('layouts.clerk_app')
@section('content')
<div class="container-fluid pt-4">
    <h1 class="h3 mb-4 text-gray-800">Notifications</h1>

    <!-- Simple Search Bar (button inside the input, no surrounding box) -->
    <form method="GET" action="{{ route('clerk.notifications.index') }}" class="mb-3">
        <div class="position-relative">
            <input type="text" class="form-control search-input-with-button" id="search" name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by date, user name, or notification type...">
            <button type="submit" class="btn btn-primary position-absolute top-0 end-0 h-100 px-4 rounded-start-0">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="notifications-container">
                <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isClickable = !empty($data['action_url'] ?? null);
                    $icon = $data['icon'] ?? 'fas fa-bell';
                    $color = $data['color'] ?? 'primary';
                    $title = $data['title'] ?? ucfirst($data['type'] ?? 'Notification');
                    $message = $data['message'] ?? $data['body'] ?? 'No message';
                    $targetUrl = $data['action_url'] ?? 'javascript:void(0)';
                    
                    // Override target URLs for specific notification types
                    if (isset($data['type'])) {
                        if ($data['type'] === 'order_completed' && isset($data['order_id'])) {
                            $targetUrl = route('sales-orders.show', $data['order_id']);
                        } elseif ($data['type'] === 'product_changes_approved') {
                            $targetUrl = route('product_catalog.index');
                        } elseif ($data['type'] === 'inventory_changes_approved') {
                            $targetUrl = route('inventory.manage');
                        }
                    }
                @endphp
                <a href="{{ $targetUrl }}" class="list-group-item d-flex justify-content-between align-items-start text-decoration-none notification-item {{ $notification->read() ? 'bg-light text-muted' : '' }}" 
                   data-notification-id="{{ $notification->id }}"
                   data-is-clickable="{{ $isClickable ? 'true' : 'false' }}">
                    <div class="ms-2 me-auto">
                        <div class="d-flex align-items-center mb-1">
                            <i class="{{ $icon }} text-{{ $color }} me-2"></i>
                            <div class="fw-bold">{{ $title }}</div>
                        </div>
                        <span class="text-reset">{{ $message }}</span>
                        <div class="text-muted small">Date: {{ $notification->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="d-flex align-items-center">
                        @if(!$notification->read())
                            <div class="badge bg-{{ $color }} rounded-pill me-2">New</div>
                        @endif
                        @if($isClickable)
                            <i class="fas fa-external-link-alt text-muted me-2"></i>
                        @endif
                        <div class="form-check">
                            <input class="form-check-input mark-as-read-checkbox" type="checkbox" data-notification-id="{{ $notification->id }}" {{ $notification->read() ? 'checked disabled' : '' }}>
                            <label class="form-check-label" for="notificationCheck{{ $notification->id }}"></label>
                        </div>
                    </div>
                </a>
                @empty
                <div class="list-group-item">
                    <p class="text-center mb-0">No notifications found.</p>
                </div>
                @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle checkbox clicks
        document.querySelectorAll('.mark-as-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                e.stopPropagation(); // Prevent triggering the link click
                const notificationId = this.dataset.notificationId;
                const isChecked = this.checked;

                if (isChecked) {
                    fetch(`/clerk/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.list-group-item').classList.add('bg-light', 'text-muted');
                            this.disabled = true;
                        } else {
                            alert('Failed to mark notification as read.');
                            this.checked = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while marking notification as read.');
                        this.checked = false;
                    });
                }
            });
        });

        // Handle notification link clicks using event delegation
        document.querySelectorAll('.notification-item').forEach(notificationLink => {
            notificationLink.addEventListener('click', function(e) {
                // Don't interfere with checkbox clicks
                if (e.target.type === 'checkbox' || e.target.closest('.form-check')) {
                    return;
                }

                const isClickable = this.dataset.isClickable === 'true';
                if (!isClickable) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const notificationId = this.dataset.notificationId;
                const targetUrl = this.getAttribute('href');

                console.log('Notification clicked:', notificationId, 'Redirecting to:', targetUrl);

                // Immediately update the UI
                this.classList.add('bg-light', 'text-muted');

                // Remove "New" badge
                const newBadge = this.querySelector('.badge');
                if (newBadge) {
                    newBadge.remove();
                }

                // Check the checkbox
                const checkbox = this.querySelector('.mark-as-read-checkbox');
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.disabled = true;
                }

                // Mark as read in background
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    fetch(`/clerk/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Notification marked as read:', notificationId);
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                    });
                }

                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 100);
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .notifications-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        background: #fff;
    }

    /* Search input with button inside */
    .search-input-with-button {
        padding-right: 3.25rem; /* space for the button */
        box-shadow: none;
        font-size: 0.85rem;
    }

    /* Custom scrollbar styling */
    .notifications-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .notifications-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb {
        background: #5E8458;
        border-radius: 4px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb:hover {
        background: #4a6b45;
    }
    
    .list-group-item {
        border-color: #e3e6f0;
        padding: 1rem 1.25rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s ease;
        border-left: none;
        border-right: none;
        font-size: 0.85rem;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fc;
    }
    
    .list-group-item .fw-bold {
        color: #385E42; /* Dark green for headings */
        font-size: 0.95rem;
        font-weight: 600;
    }

    .list-group-item .text-muted {
        font-size: 0.8rem;
    }

    .search-form .form-label {
        font-weight: 600;
        color: #385E42;
    }

    .clerk-sidebar-link { 
        color: #222; 
        font-weight: 500; 
        font-size: 1.08rem; 
        text-decoration: none; 
        transition: color 0.18s; 
        border-radius: 6px; 
        padding: 8px 12px; 
    }
    .clerk-sidebar-link.active, .clerk-sidebar-link:hover { 
        background: #e6f2e6; 
        color: #4CAF50 !important;
    }
</style>
@endpush
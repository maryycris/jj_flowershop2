@extends('layouts.admin_app')

@section('admin_content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Notifications</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                <div class="list-group-item d-flex justify-content-between align-items-start {{ $notification->read() ? 'bg-light text-muted' : '' }}">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">{{ ucfirst($notification->data['type'] ?? 'N/A') }}</div>
                        {{ $notification->data['message'] ?? 'N/A' }}
                        <div class="text-muted small">Date: {{ $notification->created_at->format('Y-m-d') }}</div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input mark-as-read-checkbox" type="checkbox" data-notification-id="{{ $notification->id }}" {{ $notification->read() ? 'checked disabled' : '' }}>
                        <label class="form-check-label" for="notificationCheck{{ $notification->id }}"></label>
                    </div>
                </div>
                @empty
                <div class="list-group-item">
                    <p class="text-center mb-0">No new notifications.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.mark-as-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const notificationId = this.dataset.notificationId;
                const isChecked = this.checked;

                if (isChecked) {
                    fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
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
                            // Optionally, visually update the notification item
                            this.closest('.list-group-item').classList.add('bg-light', 'text-muted');
                            this.disabled = true; // Disable the checkbox after marking as read
                            // You might want to remove the checkbox or change its appearance further
                        } else {
                            // Handle error
                            alert('Failed to mark notification as read.');
                            this.checked = false; // Revert checkbox state
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while marking notification as read.');
                        this.checked = false; // Revert checkbox state
                    });
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .list-group-item {
        border-color: #e3e6f0;
        padding: 1rem 1.25rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fc;
    }
    .list-group-item .fw-bold {
        color: #385E42; /* Dark green for headings */
    }
</style>
@endpush 
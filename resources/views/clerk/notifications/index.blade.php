@extends('layouts.clerk_app')
@section('content')
<div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Notifications</h3>
            @if($notifications->count() > 0)
                <form action="{{ route('clerk.notifications.deleteAll') }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete all notifications?')">
                        <i class="bi bi-trash"></i> Delete All
                    </button>
                </form>
            @endif
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('clerk.notifications.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Notifications</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread Only</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>Orders</option>
                            <option value="inventory" {{ request('type') == 'inventory' ? 'selected' : '' }}>Inventory</option>
                            <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Events</option>
                            <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payments</option>
                            <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        <a href="{{ route('clerk.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if($notifications->count() > 0)
            <div class="row">
                @foreach($notifications as $notification)
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-primary">
                                            <i class="bi bi-bell-fill me-2"></i>
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                        </h6>
                                        <p class="card-text text-muted mb-2">
                                            {{ $notification->data['message'] ?? $notification->data['body'] ?? 'No message available' }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        @if($notification->read_at)
                                            <span class="badge bg-success">Read</span>
                                        @else
                                            <span class="badge bg-warning">Unread</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-bell-slash" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="text-muted mt-3">No Notifications</h4>
                <p class="text-muted">You don't have any notifications at the moment.</p>
            </div>
        @endif
</div>
@push('styles')
<style>
.clerk-sidebar-link { color: #222; font-weight: 500; font-size: 1.08rem; text-decoration: none; transition: color 0.18s; border-radius: 6px; padding: 8px 12px; }
.clerk-sidebar-link.active, .clerk-sidebar-link:hover { background: #e6f2e6; color: #4CAF50 !important;}
</style>
@endpush
@endsection

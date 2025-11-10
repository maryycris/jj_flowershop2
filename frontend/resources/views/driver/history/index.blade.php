@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Delivery History</h4>
    <div class="d-flex gap-2">
        <span class="badge bg-success">{{ isset($completedTotal) ? $completedTotal : $completedDeliveries->count() }} completed</span>
    </div>
</div>

@if($completedDeliveries->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-check-circle display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No completed deliveries yet</h5>
    <p class="text-muted">Your completed deliveries will appear here.</p>
</div>
@else
<div class="row">
    @foreach($completedDeliveries as $order)
    <div class="col-12 mb-3">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #{{ $order->id }}</h6>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Completed
                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong>{{ $order->user->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Completed:</small><br>
                        <strong>{{ $order->updated_at->format('M d, Y g:i A') }}</strong>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Phone:</small><br>
                        <strong>{{ $order->user->contact_number ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Email:</small><br>
                        <strong>{{ $order->user->email ?? 'N/A' }}</strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong>{{ optional($order->delivery)->delivery_address ?? 'Address not specified' }}</strong>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Scheduled Date:</small><br>
                        <strong>{{ optional($order->delivery) && $order->delivery->delivery_date ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') : '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Scheduled Time:</small><br>
                        <strong>{{ optional($order->delivery)->delivery_time ?? 'Not specified' }}</strong>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('driver.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
@if($completedDeliveries->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $completedDeliveries->links() }}
</div>
@endif
@endif

<!-- Returned Orders Section removed -->

<script>
function showDeliveryNotes(deliveryId) {
    // This would open a modal or navigate to a notes page
    alert('Delivery notes feature coming soon!');
}

// Return-related features removed
</script>
@endsection 
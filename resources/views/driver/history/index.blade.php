@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Delivery History</h4>
    <span class="badge bg-success">{{ $completedDeliveries->count() }} completed</span>
</div>

@if($completedDeliveries->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-check-circle display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No completed deliveries yet</h5>
    <p class="text-muted">Your completed deliveries will appear here.</p>
</div>
@else
<div class="row">
    @foreach($completedDeliveries as $delivery)
    <div class="col-12 mb-3">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #{{ $delivery->order->id }}</h6>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Completed
                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong>{{ $delivery->order->user->name }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Completed:</small><br>
                        <strong>{{ $delivery->updated_at->format('M d, Y g:i A') }}</strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong>{{ $delivery->delivery_address ?? 'Address not specified' }}</strong>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Scheduled Date:</small><br>
                        <strong>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Scheduled Time:</small><br>
                        <strong>{{ $delivery->delivery_time ?? 'Not specified' }}</strong>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('driver.history.show', $delivery->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="showDeliveryNotes({{ $delivery->id }})">
                        <i class="bi bi-chat me-1"></i>Notes
                    </button>
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

<script>
function showDeliveryNotes(deliveryId) {
    // This would open a modal or navigate to a notes page
    alert('Delivery notes feature coming soon!');
}
</script>
@endsection 
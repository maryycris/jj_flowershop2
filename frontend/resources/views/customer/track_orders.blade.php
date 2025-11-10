@extends('layouts.customer_app')

@section('content')
@include('components.customer.alt_nav', ['active' => 'profile'])
<div class="container-fluid py-4 position-relative" style="background: #f4faf4; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="d-md-none page-label-mobile">Track Order</div>
        <div class="col-md-3 col-lg-3 d-none d-md-block">
            @include('customer.sidebar')
        </div>
        <div class="col-12 col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 25%; max-width: calc(75% - 30px);">
            <div class="row" id="trackOrdersContent">
        <div class="col-12 col-md-5 order-2 order-md-1" style="max-width: 670px; margin: 0 auto;">
            @if ($orders->isEmpty())
                <div class="alert" role="alert" style="background-color: #e8f5e8; border-color: #7bb47b; color: #2d5a2d;">
                    You have no orders to track.
                </div>
            @else
                <div class="orders-scroll-container d-flex flex-column gap-3">
                    @foreach ($orders as $order)
                        <div class="card shadow-sm border" style="border-radius: 8px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-bold" style="color: #4a9448; font-size: 1.1rem;">Order {{ $order->id }}#</span>
                                    </div>
                                </div>
                                <div class="mb-2" style="font-size: 1rem; color: #222;">
                                    <div><strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}</div>
                                    <div><strong>Time to Deliver:</strong> Anytime (8AM to 8PM)</div>
                                    <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
                                    <div><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</div>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('customer.orders.invoice.view', $order->id) }}" class="btn btn-success flex-fill" target="_blank" style="background: #7cc47f; border: none;">
                                        <i class="fas fa-file-invoice me-2"></i>View Invoice
                                    </a>
                                    <button type="button" class="btn btn-success flex-fill track-order-btn" data-order-id="{{ $order->id }}" style="background: #7cc47f; border: none;">
                                        <i class="fas fa-route me-2"></i>Track Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
                </div>
                <div class="col-12 col-md-7 order-1 order-md-2" style="max-width: 680px; margin: 0 auto;">
            <div class="card timeline-card" style="border-radius: 8px;">
                <div class="card-header d-flex align-items-center" style="background: #eafbe7; border-radius: 8px 8px 0 0;">
                    <span class="fw-bold" id="timeline-order-id" style="color: #4a9448;">Order #</span>
                    <span class="ms-3" style="color: #888;">Customer</span>
                </div>
                <div class="card-body timeline-scroll-container" style="min-height: 350px;">
                    <ul class="timeline list-unstyled" id="order-timeline">
                        <li class="text-center text-muted">Select an order to view its timeline.</li>
                    </ul>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.timeline {
    border-left: 2px solid #b6e2b7;
    margin-left: 1.5rem;
    padding-left: 1.5rem;
}
.timeline li {
    position: relative;
    margin-bottom: 1.5rem;
}
.timeline li:before {
    content: '';
    position: absolute;
    left: -1.6rem;
    top: 0.5rem;
    width: 0.75rem;
    height: 0.75rem;
    background: #7cc47f;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #b6e2b7;
}
.card {
    border: 1px solid #e0e0e0;
}
.card-header {
    font-size: 1.08rem;
}
.btn-success {
    background: #7cc47f !important;
    border: none !important;
}
.btn-success:hover {
    background: #4a9448 !important;
}

/* Scrollbar styles for orders list */
.orders-scroll-container {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 8px;
}

.orders-scroll-container::-webkit-scrollbar {
    width: 6px;
}

.orders-scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.orders-scroll-container::-webkit-scrollbar-thumb {
    background: #7cc47f;
    border-radius: 3px;
}

.orders-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #4a9448;
}

/* Scrollbar styles for timeline */
.timeline-scroll-container {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 8px;
}

.timeline-scroll-container::-webkit-scrollbar {
    width: 6px;
}

.timeline-scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.timeline-scroll-container::-webkit-scrollbar-thumb {
    background: #7cc47f;
    border-radius: 3px;
}

.timeline-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #4a9448;
}

/* Mobile enhancements */
@media (max-width: 767.98px) {
    /* Mobile page label */
    .page-label-mobile { position: absolute; top: 6px; left: 30px; font-weight: 700; font-size: 1.2rem; color: #4a9448; }
    /* Sticky alt navbars */
    .alt-topbar { position: fixed !important; }
    .mobile-bottom-nav { position: fixed !important; }
    /* Bring content closer to label */
    #trackOrdersContent { margin-top: 18px; }
    #trackOrdersContent .card { margin-left: auto; margin-right: auto; width: 93%; }
    /* Add clear separation between sticky timeline and orders list */
    .timeline-card { margin-bottom: 12px; }
    /* Make timeline sticky at top under the label */
    .timeline-card { position: sticky; top: 52px; z-index: 2; background: #fff; }
}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const timeline = document.getElementById('order-timeline');
    const timelineOrderId = document.getElementById('timeline-order-id');
    document.querySelectorAll('.track-order-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');
            timeline.innerHTML = '<li class="text-center text-muted">Loading...</li>';
            timelineOrderId.textContent = 'Order ' + orderId + '#';
            fetch(`/customer/orders/${orderId}/status-history`)
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch timeline');
                    return response.json();
                })
                .then(history => {
                    if (history.length === 0) {
                        timeline.innerHTML = '<li class="text-center text-muted">No status history available.</li>';
                        return;
                    }
                    timeline.innerHTML = history.map(item => `
                        <li>
                            <div class="d-flex align-items-center mb-1">
                                <span class="text-muted small me-3">${new Date(item.created_at).toLocaleString()}</span>
                                <span class="badge bg-success me-2">${item.status}</span>
                                <span>${item.message ? item.message : ''}</span>
                            </div>
                        </li>
                    `).join('');
                })
                .catch(() => {
                    timeline.innerHTML = '<li class="text-danger">Failed to load timeline.</li>';
                });
        });
    });
});
</script>
@endpush 
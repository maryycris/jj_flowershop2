@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            
            <!-- Order Stats -->
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 pending-card">
                            <div class="card-body py-3">
                                <h3 class="text-warning mb-1">{{ $pendingOrdersCount ?? 0 }}</h3>
                                <p class="mb-0 small">Pending Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'approved']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 approved-card">
                            <div class="card-body py-3">
                                <h3 class="text-info mb-1">{{ $approvedOrdersCount ?? 0 }}</h3>
                                <p class="mb-0 small">Approved Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'on_delivery']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 delivery-card">
                            <div class="card-body py-3">
                                <h3 class="text-primary mb-1">{{ $onDeliveryCount ?? 0 }}</h3>
                                <p class="mb-0 small">On Delivery</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'completed', 'today' => 1]) }}" class="text-decoration-none">
                        <div class="card text-center h-100 completed-card">
                            <div class="card-body py-3">
                                <h3 class="text-success mb-1">{{ $completedTodayCount ?? 0 }}</h3>
                                <p class="mb-0 small">Completed Today</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Top Products This Month & Order Type Distribution -->
            <div class="row mb-3 g-2">
                <div class="col-md-6">
                    <div class="card h-100 light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-trophy me-2"></i>Top Products This Month
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($topProductsThisMonth) && count($topProductsThisMonth) > 0)
                                @foreach($topProductsThisMonth as $product)
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span>{{ $product->name }}</span>
                                        <div>
                                            <span class="badge bg-success me-1">{{ $product->total_sold }} sold</span>
                                            <span class="badge bg-secondary">₱{{ number_format($product->total_revenue, 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No sales data for this month</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-pie-chart me-2"></i>Order Type Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><i class="bi bi-globe me-2"></i>Online Orders</span>
                                <span class="badge bg-primary fs-6">{{ number_format($onlineOrdersCount ?? 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-shop me-2"></i>Walk-in Orders</span>
                                <span class="badge bg-info fs-6">{{ number_format($walkinOrdersCount ?? 0) }}</span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Orders</strong>
                                <strong class="text-success">{{ number_format(($onlineOrdersCount ?? 0) + ($walkinOrdersCount ?? 0)) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Most Popular Products & Restock Alerts -->
            <div class="row mb-3 g-2">
                <div class="col-md-6">
                    <div class="card h-100 light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-star me-2"></i>Most Popular Products (All Time)
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($popularProducts) && count($popularProducts))
                                @foreach($popularProducts as $p)
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span>{{ $p->name }}</span>
                                        <span class="badge bg-secondary">{{ $p->total_quantity }} sold</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No sales data available</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <a href="{{ route('clerk.inventory.manage') }}" class="text-decoration-none">
                        <div class="card h-100 restock-alert-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Restock Alerts
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($restockProducts) && count($restockProducts))
                                    @foreach($restockProducts as $product)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span>{{ $product->name }}</span>
                                            <span class="badge bg-danger">{{ $product->stock }} left</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">All products are well stocked</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row">
                <div class="col-12">
                    <div class="card light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>Recent Activity
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($recentMovements) && count($recentMovements) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>User</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentMovements as $movement)
                                                <tr>
                                                    <td>{{ $movement->product->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $movement->movement_type == 'OUT' ? 'danger' : 'success' }}">
                                                            {{ $movement->movement_type }}
                                                        </span>
                                                    </td>
                                                    <td class="{{ $movement->movement_type == 'OUT' ? 'text-danger' : 'text-success' }}">
                                                        {{ $movement->movement_type == 'OUT' ? '-' : '+' }}{{ $movement->quantity }}
                                                    </td>
                                                    <td>{{ $movement->user->name ?? 'N/A' }}</td>
                                                    <td>{{ $movement->created_at->format('M d, H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No recent activity</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

a:hover .card {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Pending Orders - Pale Orange */
.pending-card {
    background-color: #fef9e7;
    border: 1px solid #f4d03f;
}

/* Approved Orders - Pale Sky Blue */
.approved-card {
    background-color: #f0f8ff;
    border: 1px solid #85c1e9;
}

/* On Delivery - Pale Blue */
.delivery-card {
    background-color: #f0f4ff;
    border: 1px solid #a8d8ff;
}

/* Completed Today - Pale Green */
.completed-card {
    background-color: #f0f9f0;
    border: 1px solid #a8e6a8;
}

/* Restock Alert - Pale Red */
.restock-alert-card {
    background-color: #fdf2f2;
    border: 1px solid #f5b7b1;
}

/* Light Green Cards - Default for other cards */
.light-green-card {
    background-color: #f0fdf4;
    border: 1px solid #bbf7d0;
}

.card-body {
    padding: 1rem;
}

.card-header {
    padding: 0.75rem 1rem;
    background-color: rgba(255, 255, 255, 0.5);
    border-bottom: 1px solid #bbf7d0;
}

.card-header h5 {
    font-size: 0.95rem;
    font-weight: 600;
}

.table {
    font-size: 0.85rem;
}

.table thead th {
    font-size: 0.8rem;
    font-weight: 600;
    background-color: #e6f4ea;
}

.table tbody td {
    font-size: 0.85rem;
}
</style>
@endpush

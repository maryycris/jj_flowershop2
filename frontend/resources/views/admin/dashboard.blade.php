@extends('layouts.admin_app')

@section('admin_content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            @if(!isset($cloudinaryConfigured) || !$cloudinaryConfigured)
            <!-- URGENT WARNING: Cloudinary Not Configured -->
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #dc3545; color: white; border: none; font-weight: 600;">
                <h5 class="alert-heading mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>⚠️ URGENT: Images Will Disappear!</h5>
                <p class="mb-2"><strong>Your images are being deleted on every deployment because Cloudinary is not configured.</strong></p>
                <p class="mb-2">To fix this permanently (5 minutes):</p>
                <ol class="mb-2">
                    <li>Go to <a href="https://cloudinary.com/users/register/free" target="_blank" style="color: #ffeb3b; text-decoration: underline;">https://cloudinary.com/users/register/free</a> (FREE account)</li>
                    <li>Get your Cloud Name, API Key, and API Secret from Cloudinary Dashboard</li>
                    <li>Add them to Railway Variables (jj_flowershop2 service → Variables tab)</li>
                </ol>
                <p class="mb-0"><strong>See <code>URGENT_IMAGE_FIX.md</code> for detailed instructions.</strong></p>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <!-- Order Stats -->
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 pending-card">
                            <div class="card-body py-3">
                                <h3 class="text-warning mb-1">{{ $pendingOrdersCount ?? 0 }}</h3>
                                <p class="mb-0 small">Pending Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.orders.index', ['status' => 'approved']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 approved-card">
                            <div class="card-body py-3">
                                <h3 class="text-info mb-1">{{ $approvedOrdersCount ?? 0 }}</h3>
                                <p class="mb-0 small">Approved Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.orders.index', ['status' => 'on_delivery']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 delivery-card">
                            <div class="card-body py-3">
                                <h3 class="text-primary mb-1">{{ $onDeliveryCount ?? 0 }}</h3>
                                <p class="mb-0 small">On Delivery</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.orders.index', ['status' => 'completed', 'today' => 1]) }}" class="text-decoration-none">
                        <div class="card text-center h-100 completed-card">
                            <div class="card-body py-3">
                                <h3 class="text-success mb-1">{{ $completedTodayCount ?? 0 }}</h3>
                                <p class="mb-0 small">Completed Today</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Revenue Stats -->
            <div class="row mb-3 g-2">
                <div class="col-md-4">
                    <div class="card text-center h-100 revenue-card">
                        <div class="card-body py-3">
                            <h4 class="text-success mb-1">₱{{ number_format($todayRevenue ?? 0, 2) }}</h4>
                            <p class="mb-0 small text-muted">Revenue Today</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100 revenue-card">
                        <div class="card-body py-3">
                            <h4 class="text-success mb-1">₱{{ number_format($thisWeekRevenue ?? 0, 2) }}</h4>
                            <p class="mb-0 small text-muted">Revenue This Week</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100 revenue-card">
                        <div class="card-body py-3">
                            <h4 class="text-success mb-1">₱{{ number_format($thisMonthRevenue ?? 0, 2) }}</h4>
                            <p class="mb-0 small text-muted">Revenue This Month</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Business Stats -->
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <div class="card text-center h-100 light-green-card">
                        <div class="card-body py-3">
                            <h4 class="mb-1">{{ number_format($totalCustomers ?? 0) }}</h4>
                            <p class="mb-0 small text-muted">Total Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 light-green-card">
                        <div class="card-body py-3">
                            <h4 class="mb-1">{{ number_format($totalProducts ?? 0) }}</h4>
                            <p class="mb-0 small text-muted">Total Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 light-green-card">
                        <div class="card-body py-3">
                            <h4 class="mb-1">{{ number_format($totalOrders ?? 0) }}</h4>
                            <p class="mb-0 small text-muted">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 light-green-card">
                        <div class="card-body py-3">
                            <h4 class="mb-1">{{ $totalMovementsToday ?? 0 }}</h4>
                            <p class="mb-0 small text-muted">Movements Today</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sales Chart -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Sales & Orders Analytics (Last 7 Days)
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Type Distribution -->
            <div class="row mb-3 g-2">
                <div class="col-md-6">
                    <div class="card light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Type Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="bi bi-globe me-2"></i>Online Orders</span>
                                <span class="badge bg-primary">{{ number_format($onlineOrdersCount ?? 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-shop me-2"></i>Walk-in Orders</span>
                                <span class="badge bg-info">{{ number_format($walkinOrdersCount ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">Top Products This Month</h5>
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
            </div>
            
            <!-- Popular Products & Restock -->
            <div class="row mb-3 g-2">
                <div class="col-md-6">
                    <div class="card h-100 light-green-card">
                        <div class="card-header">
                            <h5 class="mb-0">Most Popular Products (All Time)</h5>
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
                    <a href="{{ route('admin.inventory.index') }}" class="text-decoration-none">
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
                            <h5 class="mb-0">Recent Activity</h5>
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

/* Revenue Cards */
.revenue-card {
    background-color: #f0fdf4;
    border: 1px solid #86efac;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($salesChartLabels ?? []),
            datasets: [
                {
                    label: 'Revenue (₱)',
                    data: @json($salesChartData ?? []),
                    backgroundColor: 'rgba(34, 197, 94, 0.6)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                    yAxisID: 'y',
                },
                {
                    label: 'Orders Count',
                    data: @json($ordersChartData ?? []),
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    type: 'line',
                    yAxisID: 'y1',
                    tension: 0.4,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue (₱)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});
</script>
@endpush


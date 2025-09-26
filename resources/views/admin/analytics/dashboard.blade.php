@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-sm btn-primary" onclick="exportData()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Revenue Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">This Week's Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($thisWeekRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month's Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($thisMonthRevenue, 2) }}</div>
                            @if($revenueGrowth != 0)
                                <div class="text-xs text-{{ $revenueGrowth > 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $revenueGrowth > 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($revenueGrowth) }}% from last month
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Average Order Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($avgOrderValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Orders</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $todayOrders }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">This Week</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $thisWeekOrders }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $thisMonthOrders }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Online Orders</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $onlineOrders }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Walk-in Orders</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $walkinOrders }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-2 col-md-4 mb-3">
                            <div class="text-center">
                                <div class="h3 text-warning">{{ $pendingOrders }}</div>
                                <div class="text-xs text-uppercase text-muted">Pending</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 mb-3">
                            <div class="text-center">
                                <div class="h3 text-info">{{ $approvedOrders }}</div>
                                <div class="text-xs text-uppercase text-muted">Approved</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 mb-3">
                            <div class="text-center">
                                <div class="h3 text-primary">{{ $outForDeliveryOrders }}</div>
                                <div class="text-xs text-uppercase text-muted">Out for Delivery</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 mb-3">
                            <div class="text-center">
                                <div class="h3 text-success">{{ $completedOrders }}</div>
                                <div class="text-xs text-uppercase text-muted">Completed</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 mb-3">
                            <div class="text-center">
                                <div class="h3 text-danger">{{ $cancelledOrders }}</div>
                                <div class="text-xs text-uppercase text-muted">Cancelled</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Sales Chart -->
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Sales (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Sales Chart -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Sales (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products (This Month)</h6>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Quantity Sold</th>
                                        <th>Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>₱{{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->total_quantity }}</td>
                                            <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>No sales data available for this month.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Sales Chart
const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
const dailySalesChart = new Chart(dailySalesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($dailySales, 'day')) !!},
        datasets: [{
            label: 'Revenue (₱)',
            data: {!! json_encode(array_column($dailySales, 'revenue')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Monthly Sales Chart
const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
const monthlySalesChart = new Chart(monthlySalesCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($monthlySales, 'month')) !!},
        datasets: [{
            label: 'Revenue (₱)',
            data: {!! json_encode(array_column($monthlySales, 'revenue')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

function refreshData() {
    location.reload();
}

function exportData() {
    // Simple CSV export functionality
    const data = {
        dailySales: {!! json_encode($dailySales) !!},
        monthlySales: {!! json_encode($monthlySales) !!},
        topProducts: {!! json_encode($topProducts) !!}
    };
    
    // Create CSV content
    let csvContent = "Date,Revenue\n";
    data.dailySales.forEach(item => {
        csvContent += item.day + "," + item.revenue + "\n";
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sales_data_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endpush

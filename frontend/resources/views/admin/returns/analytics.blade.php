@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">
            <i class="fas fa-chart-line me-2"></i>Return Analytics
        </h2>
        <div class="d-flex gap-2">
            <select class="form-select" id="dateRange" onchange="updateAnalytics()">
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last 90 Days</option>
                <option value="365">Last Year</option>
            </select>
            <button class="btn btn-success" onclick="exportAnalytics()">
                <i class="fas fa-download me-1"></i>Export Report
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1" id="totalReturns">0</h3>
                    <small>Total Returns</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1" id="returnRate">0%</h3>
                    <small>Return Rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1" id="totalRefundAmount">₱0</h3>
                    <small>Total Refunded</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1" id="avgResolutionTime">0h</h3>
                    <small>Avg Resolution Time</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Return Reasons Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Return Reasons Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="returnReasonsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Return Trends Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Return Trends Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="returnTrendsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Driver Performance -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Driver Return Performance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="driverPerformanceTable">
                            <thead>
                                <tr>
                                    <th>Driver</th>
                                    <th>Total Deliveries</th>
                                    <th>Returns</th>
                                    <th>Return Rate</th>
                                    <th>Most Common Reason</th>
                                    <th>Performance Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Return Patterns -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Customer Return Patterns</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="customerPatternsTable">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Total Orders</th>
                                    <th>Returns</th>
                                    <th>Return Rate</th>
                                    <th>Last Return</th>
                                    <th>Risk Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let returnReasonsChart, returnTrendsChart;

document.addEventListener('DOMContentLoaded', function() {
    updateAnalytics();
});

function updateAnalytics() {
    const dateRange = document.getElementById('dateRange').value;
    
    // Fetch analytics data
    fetch(`/admin/returns/analytics?days=${dateRange}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        updateMetrics(data.metrics);
        updateCharts(data.charts);
        updateDriverPerformance(data.driverPerformance);
        updateCustomerPatterns(data.customerPatterns);
    })
    .catch(error => {
        console.error('Error fetching analytics:', error);
    });
}

function updateMetrics(metrics) {
    document.getElementById('totalReturns').textContent = metrics.totalReturns;
    document.getElementById('returnRate').textContent = metrics.returnRate + '%';
    document.getElementById('totalRefundAmount').textContent = '₱' + metrics.totalRefundAmount.toLocaleString();
    document.getElementById('avgResolutionTime').textContent = metrics.avgResolutionTime + 'h';
}

function updateCharts(charts) {
    // Return Reasons Chart
    if (returnReasonsChart) {
        returnReasonsChart.destroy();
    }
    const reasonsCtx = document.getElementById('returnReasonsChart').getContext('2d');
    returnReasonsChart = new Chart(reasonsCtx, {
        type: 'doughnut',
        data: {
            labels: charts.returnReasons.labels,
            datasets: [{
                data: charts.returnReasons.data,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Return Trends Chart
    if (returnTrendsChart) {
        returnTrendsChart.destroy();
    }
    const trendsCtx = document.getElementById('returnTrendsChart').getContext('2d');
    returnTrendsChart = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: charts.returnTrends.labels,
            datasets: [{
                label: 'Returns',
                data: charts.returnTrends.data,
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateDriverPerformance(drivers) {
    const tbody = document.querySelector('#driverPerformanceTable tbody');
    tbody.innerHTML = '';
    
    drivers.forEach(driver => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${driver.name}</strong></td>
            <td>${driver.totalDeliveries}</td>
            <td>${driver.returns}</td>
            <td><span class="badge bg-${driver.returnRate > 10 ? 'danger' : driver.returnRate > 5 ? 'warning' : 'success'}">${driver.returnRate}%</span></td>
            <td>${driver.mostCommonReason}</td>
            <td><span class="badge bg-${driver.score > 80 ? 'success' : driver.score > 60 ? 'warning' : 'danger'}">${driver.score}/100</span></td>
        `;
        tbody.appendChild(row);
    });
}

function updateCustomerPatterns(customers) {
    const tbody = document.querySelector('#customerPatternsTable tbody');
    tbody.innerHTML = '';
    
    customers.forEach(customer => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${customer.name}</strong></td>
            <td>${customer.totalOrders}</td>
            <td>${customer.returns}</td>
            <td><span class="badge bg-${customer.returnRate > 20 ? 'danger' : customer.returnRate > 10 ? 'warning' : 'success'}">${customer.returnRate}%</span></td>
            <td>${customer.lastReturn}</td>
            <td><span class="badge bg-${customer.riskLevel === 'high' ? 'danger' : customer.riskLevel === 'medium' ? 'warning' : 'success'}">${customer.riskLevel}</span></td>
        `;
        tbody.appendChild(row);
    });
}

function exportAnalytics() {
    const dateRange = document.getElementById('dateRange').value;
    window.open(`/admin/returns/analytics/export?days=${dateRange}`, '_blank');
}
</script>
@endsection

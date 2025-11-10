@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Reports</h1>

    <div class="row">
        <!-- Sales Report Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Report (Completed Transactions)</h6>
                </div>
                <div class="card-body">
                    <p>Generate reports on completed sales transactions only.</p>
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="sales_start_date">Start Date:</label>
                            <input type="date" class="form-control" id="sales_start_date" name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="sales_end_date">End Date:</label>
                            <input type="date" class="form-control" id="sales_end_date" name="end_date">
                        </div>
                        <div class="form-group">
                            <label for="sales_category">Product Category:</label>
                            <select class="form-control" id="sales_category" name="category_name">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="transaction_status">Transaction Status:</label>
                            <select class="form-control" id="transaction_status" name="status">
                                <option value="completed">Completed Only</option>
                                <option value="delivered">Delivered Only</option>
                                <option value="paid">Paid Only</option>
                                <option value="all_completed">All Completed Statuses</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" id="generateSalesReportBtn">Generate Report</button>
                        <button type="button" class="btn btn-success">Download CSV</button>
                        <button type="button" class="btn btn-info">Download PDF</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Status Report Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Report</h6>
                </div>
                <div class="card-body">
                    <p>Track orders by their current status.</p>
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="order_start_date">Start Date:</label>
                            <input type="date" class="form-control" id="order_start_date" name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="order_end_date">End Date:</label>
                            <input type="date" class="form-control" id="order_end_date" name="end_date">
                        </div>
                        <div class="form-group">
                            <label for="order_status">Order Status:</label>
                            <select class="form-control" id="order_status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="on_delivery">On Delivery</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" id="generateOrderStatusReportBtn">Generate Report</button>
                        <button type="button" class="btn btn-success">Download CSV</button>
                        <button type="button" class="btn btn-info">Download PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Product Performance Report Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Performance Report</h6>
                </div>
                <div class="card-body">
                    <p>Analyze the performance of individual products.</p>
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="product_start_date">Start Date:</label>
                            <input type="date" class="form-control" id="product_start_date" name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="product_end_date">End Date:</label>
                            <input type="date" class="form-control" id="product_end_date" name="end_date">
                        </div>
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Search product">
                        </div>
                        <button type="submit" class="btn btn-primary" id="generateProductPerformanceReportBtn">Generate Report</button>
                        <button type="button" class="btn btn-success">Download CSV</button>
                        <button type="button" class="btn btn-info">Download PDF</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Activity Report Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Activity Report</h6>
                </div>
                <div class="card-body">
                    <p>Monitor user registrations and activity.</p>
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="user_start_date">Start Date:</label>
                            <input type="date" class="form-control" id="user_start_date" name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="user_end_date">End Date:</label>
                            <input type="date" class="form-control" id="user_end_date" name="end_date">
                        </div>
                        <div class="form-group">
                            <label for="user_role">User Role:</label>
                            <select class="form-control" id="user_role" name="role">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="clerk">Clerk</option>
                                <option value="customer">Customer</option>
                                <option value="driver">Driver</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" id="generateUserActivityReportBtn">Generate Report</button>
                        <button type="button" class="btn btn-success">Download CSV</button>
                        <button type="button" class="btn btn-info">Download PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Display Area (could be dynamic or show summary) -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Preview / Summary</h6>
                </div>
                <div class="card-body" id="reportResults">
                    <p>Generated report data will appear here or download automatically.</p>
                    <!-- This area can be dynamically updated via AJAX or display confirmation of download -->
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Report Section -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory Report</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Stock</th>
                                    <th>Units Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->stock ?? 0 }}</td>
                                    <td>{{ $product->units_sold ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Report Generation
        document.getElementById('generateSalesReportBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const startDate = document.getElementById('sales_start_date').value;
            const endDate = document.getElementById('sales_end_date').value;
            const categoryName = document.getElementById('sales_category').value;
            const transactionStatus = document.getElementById('transaction_status').value;
            const reportResultsDiv = document.getElementById('reportResults');

            reportResultsDiv.innerHTML = '<p class="text-center">Generating sales report...</p>';

            fetch('{{ route('admin.reports.sales.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ start_date: startDate, end_date: endDate, category_name: categoryName, status: transactionStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let tableHtml = `<h5 class="mb-3">Sales Report from ${startDate || 'Start'} to ${endDate || 'End'}</h5>`;
                    tableHtml += `<table class="table table-bordered mt-3">`;
                    tableHtml += `<thead><tr><th>Date</th><th>Total Sales</th><th>Total Orders</th></tr></thead>`;
                    tableHtml += `<tbody>`;
                    data.forEach(row => {
                        tableHtml += `<tr><td>${row.date}</td><td>₱${parseFloat(row.total_sales).toFixed(2)}</td><td>${row.total_orders}</td></tr>`;
                    });
                    tableHtml += `</tbody></table>`;
                    reportResultsDiv.innerHTML = tableHtml;
                } else {
                    reportResultsDiv.innerHTML = '<p class="text-center">No sales data found for the selected criteria.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching sales report:', error);
                reportResultsDiv.innerHTML = '<p class="text-center text-danger">Failed to generate sales report. Please try again.</p>';
            });
        });

        // Order Status Report Generation
        document.getElementById('generateOrderStatusReportBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const startDate = document.getElementById('order_start_date').value;
            const endDate = document.getElementById('order_end_date').value;
            const status = document.getElementById('order_status').value;
            const reportResultsDiv = document.getElementById('reportResults');

            reportResultsDiv.innerHTML = '<p class="text-center">Generating order status report...</p>';

            fetch('{{ route('admin.reports.orderStatus') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ start_date: startDate, end_date: endDate, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let tableHtml = `<h5 class="mb-3">Order Status Report from ${startDate || 'Start'} to ${endDate || 'End'}</h5>`;
                    tableHtml += `<table class="table table-bordered mt-3">`;
                    tableHtml += `<thead><tr><th>Date</th><th>Total Orders</th></tr></thead>`;
                    tableHtml += `<tbody>`;
                    data.forEach(row => {
                        tableHtml += `<tr><td>${row.date}</td><td>${row.total_orders}</td></tr>`;
                    });
                    tableHtml += `</tbody></table>`;
                    reportResultsDiv.innerHTML = tableHtml;
                } else {
                    reportResultsDiv.innerHTML = '<p class="text-center">No order status data found for the selected criteria.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching order status report:', error);
                reportResultsDiv.innerHTML = '<p class="text-center text-danger">Failed to generate order status report. Please try again.</p>';
            });
        });

        // Product Performance Report Generation
        document.getElementById('generateProductPerformanceReportBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const startDate = document.getElementById('product_start_date').value;
            const endDate = document.getElementById('product_end_date').value;
            const productName = document.getElementById('product_name').value;
            const reportResultsDiv = document.getElementById('reportResults');

            reportResultsDiv.innerHTML = '<p class="text-center">Generating product performance report...</p>';

            fetch('{{ route('admin.reports.productPerformance') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ start_date: startDate, end_date: endDate, product_name: productName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let tableHtml = `<h5 class="mb-3">Product Performance Report from ${startDate || 'Start'} to ${endDate || 'End'}</h5>`;
                    tableHtml += `<table class="table table-bordered mt-3">`;
                    tableHtml += `<thead><tr><th>Product Name</th><th>Units Sold</th><th>Revenue</th></tr></thead>`;
                    tableHtml += `<tbody>`;
                    data.forEach(row => {
                        tableHtml += `<tr><td>${row.product_name}</td><td>${row.units_sold}</td><td>₱${parseFloat(row.revenue).toFixed(2)}</td></tr>`;
                    });
                    tableHtml += `</tbody></table>`;
                    reportResultsDiv.innerHTML = tableHtml;
                } else {
                    reportResultsDiv.innerHTML = '<p class="text-center">No product performance data found for the selected criteria.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching product performance report:', error);
                reportResultsDiv.innerHTML = '<p class="text-center text-danger">Failed to generate product performance report. Please try again.</p>';
            });
        });

        // User Activity Report Generation
        document.getElementById('generateUserActivityReportBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const startDate = document.getElementById('user_start_date').value;
            const endDate = document.getElementById('user_end_date').value;
            const role = document.getElementById('user_role').value;
            const reportResultsDiv = document.getElementById('reportResults');

            reportResultsDiv.innerHTML = '<p class="text-center">Generating user activity report...</p>';

            fetch('{{ route('admin.reports.userActivity') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ start_date: startDate, end_date: endDate, role: role })
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let tableHtml = `<h5 class="mb-3">User Activity Report from ${startDate || 'Start'} to ${endDate || 'End'}</h5>`;
                    tableHtml += `<table class="table table-bordered mt-3">`;
                    tableHtml += `<thead><tr><th>Date</th><th>Total Users</th></tr></thead>`;
                    tableHtml += `<tbody>`;
                    data.forEach(row => {
                        tableHtml += `<tr><td>${row.date}</td><td>${row.total_users}</td></tr>`;
                    });
                    tableHtml += `</tbody></table>`;
                    reportResultsDiv.innerHTML = tableHtml;
                } else {
                    reportResultsDiv.innerHTML = '<p class="text-center">No user activity data found for the selected criteria.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching user activity report:', error);
                reportResultsDiv.innerHTML = '<p class="text-center text-danger">Failed to generate user activity report. Please try again.</p>';
            });
        });
    });
</script>
@endpush 
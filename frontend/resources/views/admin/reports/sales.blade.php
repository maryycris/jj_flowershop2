@extends('layouts.admin_app')
@section('content')
@push('styles')
<style>
/* Sales Report Styling - matching invoice page hierarchy */
.card-title {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.card-header h5, .card-header h6 {
    font-size: 0.95rem !important;
    font-weight: 600;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 500;
}

.form-control {
    font-size: 0.85rem;
}

.btn-success {
    font-size: 0.85rem;
    padding: 0.35rem 0.7rem;
}

.btn-success i {
    font-size: 0.85rem;
}

/* Table styling */
.table {
    font-size: 0.85rem;
}

.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
    background-color: #e6f4ea;
}

.table tbody td {
    font-size: 0.85rem;
    padding: 0.4rem 0.3rem;
    vertical-align: middle;
}

/* Grand Total styling */
.mt-3.text-end strong {
    font-size: 0.85rem;
    font-weight: 600;
}

.mt-3.text-end span {
    font-size: 0.85rem;
}
</style>
@endpush

<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
    <!-- Date Selection Card -->
    <div class="card shadow mb-4">
        <div class="card-header" style="background: #e6f4ea;">
            <h5 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">Date Range Filter</h5>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form id="salesReportForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-success px-5" id="retrieveBtn">
                                <i class="bi bi-search"></i> Retrieve
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Results Table -->
    <div class="card shadow mb-4" id="reportResultsCard" style="display: none;">
        <div class="card-header" style="background: #e6f4ea;">
            <h5 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">Sales Report Results</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="salesReportTable">
                    <thead>
                        <tr>
                            <th>SO#</th>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Discount</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <!-- Results will be populated here -->
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-end">
                <strong>Grand Total: <span id="grandTotal">₱0.00</span></strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const retrieveBtn = document.getElementById('retrieveBtn');
    const reportCard = document.getElementById('reportResultsCard');
    const reportTableBody = document.getElementById('reportTableBody');
    const grandTotalSpan = document.getElementById('grandTotal');
    
    retrieveBtn.addEventListener('click', function() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (!startDate || !endDate) {
            alert('Please select both Start Date and End Date');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start Date cannot be later than End Date');
            return;
        }
        
        // Show loading state
        retrieveBtn.disabled = true;
        retrieveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Retrieving...';
        
        // Fetch report data
        fetch('{{ route("admin.reports.generateDetailedSales") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate
            })
        })
        .then(response => response.json())
        .then(data => {
            // Clear previous results
            reportTableBody.innerHTML = '';
            
            if (data.length === 0) {
                reportTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No sales data found for the selected date range.</td></tr>';
                grandTotalSpan.textContent = '₱0.00';
            } else {
                let grandTotal = 0;
                let previousSoNumber = null;
                
                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    const soNumber = item.so_number || 'N/A';
                    
                    // Only show SO# if it's different from the previous one
                    const displaySoNumber = (soNumber === previousSoNumber) ? '' : soNumber;
                    
                    row.innerHTML = `
                        <td>${displaySoNumber}</td>
                        <td>${item.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>₱${parseFloat(item.discount || 0).toFixed(2)}</td>
                        <td>₱${parseFloat(item.price || 0).toFixed(2)}</td>
                        <td>₱${parseFloat(item.total || 0).toFixed(2)}</td>
                    `;
                    reportTableBody.appendChild(row);
                    grandTotal += parseFloat(item.total || 0);
                    
                    // Update previous SO number for next iteration
                    previousSoNumber = soNumber;
                });
                
                grandTotalSpan.textContent = '₱' + grandTotal.toFixed(2);
            }
            
            // Show results
            reportCard.style.display = 'block';
            
            // Scroll to results
            reportCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating the report. Please try again.');
        })
        .finally(() => {
            // Reset button
            retrieveBtn.disabled = false;
            retrieveBtn.innerHTML = '<i class="bi bi-search"></i> Retrieve';
        });
    });
    
    // Set default dates (today for end date, 30 days ago for start date)
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
    document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
});
</script>
@endpush
@endsection

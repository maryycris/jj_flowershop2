@extends('layouts.clerk_app')

@section('title', 'Invoice Management')

@push('styles')
<style>
/* Invoice Table Styling - matching inventory */
#invoicesTable.table {
    font-size: 0.85rem;
    background-color: white;
}

#invoicesTable.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
    background-color: #e6f4ea;
}

#invoicesTable.table tbody td {
    font-size: 0.85rem;
    padding: 0.4rem 0.3rem;
    vertical-align: middle;
    background-color: white;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

/* Order link styling */
.order-link {
    color: #7bb47b !important;
    text-decoration: none;
    transition: all 0.2s ease;
}

.order-link:hover {
    color: #5aa65a !important;
    text-decoration: underline;
}

/* Actions column - center icons */
#invoicesTable.table thead th:last-child,
#invoicesTable.table tbody td:last-child {
    text-align: center;
}

/* Action buttons - black icon only, background on hover - HIGH SPECIFICITY */
#invoicesTable tbody td .btn-group .invoice-action-btn,
#invoicesTable tbody td .btn-group a.invoice-action-btn,
table#invoicesTable tbody td .btn-group .invoice-action-btn,
table#invoicesTable tbody td .btn-group a.invoice-action-btn {
    background: transparent !important;
    border: none !important;
    background-color: transparent !important;
    color: #000000 !important;
    padding: 0.3rem 0.4rem !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    border-radius: 3px;
    margin: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    min-width: auto !important;
    width: auto !important;
    height: auto !important;
}

#invoicesTable tbody td .btn-group .invoice-action-btn i,
#invoicesTable tbody td .btn-group .invoice-action-btn i.fas,
table#invoicesTable tbody td .btn-group .invoice-action-btn i {
    font-size: 0.85rem !important;
    color: #000000 !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
}

#invoicesTable tbody td .btn-group .invoice-action-btn:hover,
#invoicesTable tbody td .btn-group a.invoice-action-btn:hover,
table#invoicesTable tbody td .btn-group .invoice-action-btn:hover {
    background-color: #7bb47b !important;
    background: #7bb47b !important;
}

#invoicesTable tbody td .btn-group .invoice-action-btn:hover i,
#invoicesTable tbody td .btn-group .invoice-action-btn:hover i.fas,
table#invoicesTable tbody td .btn-group .invoice-action-btn:hover i {
    color: #ffffff !important;
}

/* Remove btn-group spacing and override Bootstrap */
#invoicesTable.table .btn-group,
#invoicesTable.table tbody .btn-group {
    display: flex !important;
    justify-content: center !important;
    align-items: center;
    gap: 0.1rem !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Invoice Pagination - Smaller and No White Background - Exclusive to Invoice Page */
.card-body .pagination-container {
    background: transparent !important;
    padding: 0.1rem 0 !important;
    box-shadow: none !important;
    margin: 0.10rem 0 0 0 !important;
}

.card-body .pagination-custom {
    font-size: 0.7rem !important;
    margin: 0 !important;
}

.card-body .pagination-custom .page-link {
    color: #7bb47b !important;
    background-color: white !important;
    border: 1px solid #e6f4ea !important;
    padding: 0.3rem 0.5rem !important;
    font-size: 0.7rem !important;
    margin: 0 2px !important;
    border-radius: 4px !important;
    transition: all 0.2s ease !important;
    font-weight: 500 !important;
    min-width: 28px !important;
    height: 28px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.card-body .pagination-custom .page-link:hover {
    color: #fff !important;
    background-color: #7bb47b !important;
    border-color: #7bb47b !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(123, 180, 123, 0.3) !important;
}

.card-body .pagination-custom .page-item.active .page-link {
    color: #fff !important;
    background-color: #7bb47b !important;
    border-color: #7bb47b !important;
    box-shadow: 0 2px 8px rgba(123, 180, 123, 0.4) !important;
}

.card-body .pagination-custom .page-item.disabled .page-link {
    color: #6c757d !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed !important;
}

.card-body .pagination-custom .page-item.disabled .page-link:hover {
    color: #6c757d !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    transform: none !important;
    box-shadow: none !important;
}

/* Search form styling */
.card-body .form-control, 
.card-body .form-select {
    font-size: 0.85rem;
}

.card-body .btn-success {
    font-size: 0.85rem;
}

</style>
@endpush

@section('content')
<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">Invoice Management</h3>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search by invoice number or customer name..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-success">Search</button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                <select class="form-select" name="status" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="invoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('clerk.orders.show', $invoice->order_id) }}" class="order-link">
                                            #{{ $invoice->order_id }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->order->user->name }}</td>
                                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            ₱{{ number_format($invoice->total_amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($invoice->status === 'paid')
                                            <span class="badge" style="background-color: #28a745; color: white;">Paid</span>
                                        @elseif($invoice->status === 'ready')
                                            <span class="badge" style="background-color: #90ee90; color: black;">Ready</span>
                                        @elseif($invoice->status === 'draft')
                                            <span class="badge" style="background-color: #c8e6c9; color: black;">Draft</span>
                                        @else
                                            <span class="badge" style="background-color: #2d5016; color: white;">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->payment_type === 'online')
                                            <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                                        @else
                                            <span class="badge" style="background-color: #66bb6a; color: white;">COD</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" style="display: flex; justify-content: center; gap: 0.15rem;">
                                            <a href="{{ route('clerk.invoices.show', $invoice->id) }}" 
                                               class="invoice-action-btn" 
                                               title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($invoice->status === 'ready' && $invoice->payment_type === 'cod')
                                                <a href="{{ route('clerk.invoices.show', $invoice->id) }}" 
                                                   class="invoice-action-btn" 
                                                   title="Register Payment">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <br>
                                        No invoices found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($invoices->hasPages())
                        <x-pagination 
                            :currentPage="$invoices->currentPage()"
                            :totalPages="$invoices->lastPage()"
                            :baseUrl="request()->url()" 
                            :queryParams="array_filter(request()->only(['search', 'status']))" 
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Wizard Modal -->
<div class="modal fade" id="paymentWizardModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Payment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mode_of_payment">Mode of Payment <span class="text-danger">*</span></label>
                                <select class="form-control" id="mode_of_payment" name="mode_of_payment" required>
                                    <option value="">Select Payment Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="card">Card Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="memo">Memo (Optional)</label>
                                <input type="text" class="form-control" id="memo" name="memo" 
                                       placeholder="Payment reference or notes">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="validatePayment()">
                    <i class="fas fa-check"></i> Validate Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentInvoiceId = null;

function openPaymentWizard(invoiceId) {
    currentInvoiceId = invoiceId;
    $('#paymentWizardModal').modal('show');
}

function validatePayment() {
    if (!currentInvoiceId) {
        alert('No invoice selected');
        return;
    }

    const formData = new FormData(document.getElementById('paymentForm'));
    
    // Show loading state
    const validateBtn = document.querySelector('button[onclick="validatePayment()"]');
    const originalText = validateBtn.innerHTML;
    validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    validateBtn.disabled = true;

    fetch(`/clerk/invoices/${currentInvoiceId}/register-payment`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment registered successfully!');
            $('#paymentWizardModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while registering payment');
    })
    .finally(() => {
        validateBtn.innerHTML = originalText;
        validateBtn.disabled = false;
    });
}

// Remove DataTable initialization - using Laravel pagination instead
</script>
@endsection
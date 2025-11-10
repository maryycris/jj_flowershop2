@extends('layouts.admin_app')

@section('title', 'Invoice Management')

@push('styles')
<style>
/* Invoice Table Styling - matching clerk invoice page */
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
.form-control, .form-select {
    font-size: 0.85rem;
}

.btn-success {
    font-size: 0.85rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
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
                                @php
                                    $notes = $invoice->order->notes ?? '';
                                    $customerName = $invoice->order->user->name ?? 'Walk-in Customer';
                                    if (!empty($notes) && preg_match('/Customer:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                                        $customerName = trim($m[1]);
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.sales-orders.show', $invoice->order_id) }}" class="order-link">
                                            #{{ $invoice->order_id }}
                                        </a>
                                    </td>
                                    <td>{{ $customerName }}</td>
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
                                            <a href="{{ route('invoices.show', $invoice->id) }}" 
                                               class="invoice-action-btn" 
                                               title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($invoice->status === 'ready')
                                                <a href="{{ route('invoices.payment', $invoice) }}" 
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
                            :queryParams="request()->query()" 
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Success Message -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
@endif

@endsection

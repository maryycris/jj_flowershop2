@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <!-- Sidebar -->
        <div class="col-12 col-md-3 col-lg-3">
            @include('customer.sidebar')
        </div>
        
        <!-- Main Content -->
        <div class="col-12 col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 25%; max-width: calc(75% - 30px);">
            <div class="card shadow mb-4" style="background: white; border-radius: 8px;">
                <div class="card-body" style="padding: 1.5rem;">
                    <!-- Header -->
                    <div class="mb-3">
                        <h5 class="mb-1" style="font-size: 1.1rem;">
                            <i class="fas fa-wallet me-2 text-success" style="font-size: 1rem;"></i>Store Credit History
                        </h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Track your store credit transactions and balance</p>
                    </div>

                    <!-- Store Credit Balance Summary -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <div class="bg-success text-white rounded p-3" style="font-size: 0.9rem;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-wallet me-2" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.8rem;">Total Store Credit</div>
                                        <div class="fw-bold" style="font-size: 1.1rem;">₱{{ number_format($totalStoreCredit, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="bg-primary text-white rounded p-3" style="font-size: 0.9rem;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-receipt me-2" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.8rem;">Total Transactions</div>
                                        <div class="fw-bold" style="font-size: 1.1rem;">{{ $storeCreditTransactions->count() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Store Credit Transactions -->
                    @if($storeCreditTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size: 0.8rem; font-weight: 600;">Order #</th>
                                        <th style="font-size: 0.8rem; font-weight: 600;">Products</th>
                                        <th style="font-size: 0.8rem; font-weight: 600;">Amount</th>
                                        <th style="font-size: 0.8rem; font-weight: 600;">Reason</th>
                                        <th style="font-size: 0.8rem; font-weight: 600;">Date</th>
                                        <th style="font-size: 0.8rem; font-weight: 600;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($storeCreditTransactions as $transaction)
                                    <tr>
                                        <td style="font-size: 0.8rem;">
                                            <strong>#{{ $transaction->id }}</strong>
                                        </td>
                                        <td style="font-size: 0.75rem;">
                                            <div class="d-flex flex-column">
                                                @foreach($transaction->products->take(2) as $product)
                                                <span class="text-muted">{{ $product->name }}</span>
                                                @endforeach
                                                @if($transaction->products->count() > 2)
                                                <span class="text-muted">+{{ $transaction->products->count() - 2 }} more</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="font-size: 0.8rem;">
                                            <span class="badge bg-success" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">₱{{ number_format($transaction->refund_amount, 2) }}</span>
                                        </td>
                                        <td style="font-size: 0.75rem;">
                                            <span class="text-muted">{{ $transaction->refund_reason ?? 'N/A' }}</span>
                                        </td>
                                        <td style="font-size: 0.75rem;">
                                            <span class="text-muted">
                                                @if($transaction->refund_processed_at)
                                                    {{ $transaction->refund_processed_at instanceof \Carbon\Carbon ? 
                                                        $transaction->refund_processed_at->format('M d, Y g:i A') : 
                                                        \Carbon\Carbon::parse($transaction->refund_processed_at)->format('M d, Y g:i A') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </td>
                                        <td style="font-size: 0.75rem;">
                                            <span class="badge bg-success" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                                <i class="fas fa-check-circle me-1"></i>Processed
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($storeCreditTransactions->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $storeCreditTransactions->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-wallet text-muted" style="font-size: 2.5rem;"></i>
                            <h6 class="text-muted mt-2" style="font-size: 0.9rem;">No Store Credit Yet</h6>
                            <p class="text-muted" style="font-size: 0.8rem;">You'll see your store credit transactions here when you receive refunds.</p>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-shopping-bag me-1"></i>Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content-with-sidebar {
            margin-left: 0 !important;
            max-width: 100% !important;
            padding: 15px;
        }
        
        .sidebar-container {
            position: relative;
            top: 0;
            width: 100%;
            max-width: none;
            min-height: auto;
            margin-bottom: 20px;
        }
        
        .table-responsive {
            font-size: 0.75rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
    }
    
    @media (max-width: 576px) {
        .table th,
        .table td {
            font-size: 0.7rem;
            padding: 0.25rem;
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
    }
</style>
@endsection

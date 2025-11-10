@extends('layouts.admin_app')

@section('content')
<style>
    /* Custom styles for Sales Orders tabs to match inventory tab design exactly */
    .sales-order-custom-tabs {
        margin-bottom: 1.5rem;
        padding: 0;
        list-style: none;
        display: flex;
        border-radius: 0;
        box-shadow: none;
    }

    .sales-order-custom-tabs .nav-item {
        flex: 1;
        text-align: center;
    }

    .sales-order-custom-tabs .nav-link {
        color: #28a745;
        background-color: transparent;
        border: none;
        padding: 0.75rem 1rem;
        border-radius: 0;
        transition: all 0.3s ease;
        font-weight: 500;
        text-decoration: none;
        font-size: 0.95rem;
        display: block;
        width: 100%;
        text-align: center;
    }

    .sales-order-custom-tabs .nav-link:hover:not(.active) {
        color: #28a745;
        background-color: transparent;
    }

    .sales-order-custom-tabs .nav-link.active {
        color: #28a745;
        background-color: transparent;
        background-color: #f8f9fa;
    border-color: #dee2e6 #dee2e6 #f8f9fa;
        font-weight: 400;
    }

    /* Order History Action Buttons - matching invoice page */
    #historyTable tbody td .btn-group .history-action-btn,
    #historyTable tbody td .btn-group button.history-action-btn,
    table#historyTable tbody td .btn-group .history-action-btn,
    table#historyTable tbody td .btn-group button.history-action-btn {
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

    #historyTable tbody td .btn-group .history-action-btn i,
    #historyTable tbody td .btn-group .history-action-btn i.fas,
    table#historyTable tbody td .btn-group .history-action-btn i {
        font-size: 0.85rem !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1 !important;
    }

    #historyTable tbody td .btn-group .history-action-btn:hover,
    #historyTable tbody td .btn-group button.history-action-btn:hover,
    table#historyTable tbody td .btn-group .history-action-btn:hover {
        background-color: #7bb47b !important;
        background: #7bb47b !important;
    }

    #historyTable tbody td .btn-group .history-action-btn:hover i,
    #historyTable tbody td .btn-group .history-action-btn:hover i.fas,
    table#historyTable tbody td .btn-group .history-action-btn:hover i {
        color: #ffffff !important;
    }

    /* Remove btn-group spacing and override Bootstrap */
    #historyTable.table .btn-group,
    #historyTable.table tbody .btn-group {
        display: flex !important;
        justify-content: center !important;
        align-items: center;
        gap: 0.1rem !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Table body styling */
    #historyTable.table tbody td {
        font-size: 0.85rem;
        padding: 0.4rem 0.3rem;
        vertical-align: middle;
        background-color: white;
    }

    /* Pagination styling - matching invoice page */
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
</style>
<div class="container-fluid">
    <!-- Nav Tabs -->
    <ul class="nav nav-tabs sales-order-custom-tabs" id="orderTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link @if($activeTab == 'online') active @endif" 
                    id="online-orders-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#online-orders" 
                    type="button" 
                    role="tab" 
                    aria-controls="online-orders" 
                    aria-selected="{{ $activeTab == 'online' ? 'true' : 'false' }}"
                    onclick="switchTab('online')">Online Orders</button>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('admin.orders.create') }}" class="nav-link">
                Walk-in Orders
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link @if($activeTab == 'history') active @endif" 
                    id="history-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#history" 
                    type="button" 
                    role="tab" 
                    aria-controls="history" 
                    aria-selected="{{ $activeTab == 'history' ? 'true' : 'false' }}"
                    onclick="switchTab('history')">Order History</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="orderTabsContent">
        <!-- Online Orders Pane -->
        <div class="tab-pane fade @if($activeTab == 'online') show active @endif" id="online-orders" role="tabpanel" aria-labelledby="online-orders-tab">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="" class="d-flex">
                                <input type="hidden" name="tab" value="online">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
                            </form>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select">
                                <option selected>Region</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($onlineOrders as $order)
                                    @php
                                        $statusClass = 'bg-warning text-dark';
                                        $statusText = ucfirst($order->status ?? 'pending');
                                        $redirectUrl = route('admin.orders.online.invoice', $order);

                                        if ($order->order_status) {
                                            switch($order->order_status) {
                                                case 'pending':
                                                    $statusClass = 'bg-warning text-dark';
                                                    $statusText = 'Pending';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'bg-info text-dark';
                                                    $statusText = 'Approved';
                                                    break;
                                                case 'on_delivery':
                                                    $statusClass = 'bg-primary text-white';
                                                    $statusText = 'On Delivery';
                                                    break;
                                                case 'completed':
                                                    $statusClass = 'bg-success text-white';
                                                    $statusText = 'Completed';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <tr style="cursor: pointer;" onclick="window.location.href='{{ $redirectUrl }}'">
                                        <td>{{ optional($order->user)->name ?? 'N/A' }}</td>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                        <td>₱{{ number_format($order->total_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No online orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History Pane -->
        <div class="tab-pane fade @if($activeTab == 'history') show active @endif" id="history" role="tabpanel" aria-labelledby="history-tab">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">
                        <i class="fas fa-history me-2"></i>Completed Orders History
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="" class="d-flex">
                                <input type="hidden" name="tab" value="history">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search orders..." value="{{ request('search') }}" style="font-size: 0.85rem; padding: 0.25rem 0.5rem; height: auto; line-height: 1;">
                                <button type="submit" class="btn btn-success" style="font-size: 0.85rem; padding: 0.25rem 0.75rem; height: auto; line-height: 1;">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Completed Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="historyTable" style="font-size: 0.85rem; background-color: white;">
                            <thead>
                                <tr>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Order #</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Customer</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Type</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Order Date</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Completed Date</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Total Amount</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Driver</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Proof of Delivery</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea;">Driver Notes</th>
                                    <th style="font-size: 0.8rem !important; font-weight: 600; padding: 0.5rem 0.3rem; background-color: #e6f4ea; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedOrders as $order)
                                <tr>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        <strong>#{{ $order->id }}</strong>
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        <div>
                                            <strong>{{ optional($order->user)->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ optional($order->user)->email ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        @if($order->type === 'online')
                                            <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                                        @else
                                            <span class="badge" style="background-color: #66bb6a; color: white;">Walk-in</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        <small style="font-size: 0.85rem;">{{ $order->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        <small style="font-size: 0.85rem;">{{ $order->updated_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        <strong class="text-success">₱{{ number_format($order->total_price, 2) }}</strong>
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        @php
                                            $driverName = null;
                                            if ($order->delivery && $order->delivery->driver) {
                                                $driverName = $order->delivery->driver->name;
                                            } elseif ($order->assignedDriver) {
                                                $driverName = $order->assignedDriver->name;
                                            }
                                        @endphp
                                        @if($driverName)
                                            <strong>{{ $driverName }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        @if($order->delivery && $order->delivery->proof_of_delivery_image)
                                            @php
                                                $proofPath = $order->delivery->proof_of_delivery_image;
                                                $proofUrl = asset('storage/' . $proofPath);
                                            @endphp
                                            <button class="btn btn-sm" onclick="viewProof('{{ $proofUrl }}')" style="font-size: 0.75rem; padding: 0.2rem 0.4rem;">
                                                <i class="fas fa-image"></i> View Photo
                                            </button>
                                        @else
                                            <span class="text-muted" style="font-size: 0.85rem;">No proof</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle;">
                                        @if($order->delivery && $order->delivery->delivery_notes)
                                            <span class="text-truncate d-inline-block" style="max-width: 150px; font-size: 0.85rem;" title="{{ $order->delivery->delivery_notes }}">
                                                {{ Str::limit($order->delivery->delivery_notes, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.85rem;">No notes</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem; padding: 0.4rem 0.3rem; vertical-align: middle; text-align: center;">
                                        <div class="btn-group" role="group" style="display: flex; justify-content: center; gap: 0.15rem;">
                                            <button class="history-action-btn" onclick="viewOrderDetails({{ $order->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="history-action-btn" onclick="downloadReceipt({{ $order->id }})" title="Download Receipt">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fas fa-history" style="font-size: 3rem; color: #6c757d;"></i>
                                        <h4 class="mt-3">No Completed Orders</h4>
                                        <p class="text-muted">No completed orders found in the history.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($completedOrders->hasPages())
                        <x-pagination 
                            :currentPage="$completedOrders->currentPage()"
                            :totalPages="$completedOrders->lastPage()"
                            :baseUrl="request()->url()" 
                            :queryParams="array_filter(array_merge(['tab' => 'history'], request()->only(['search'])))" 
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(tabName) {
    // Update URL without page reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
    
    // Update active tab
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        link.setAttribute('aria-selected', 'false');
    });
    
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
    });
    
    // Activate selected tab
    let activeTab, activePane;
    
    if (tabName === 'online' || tabName === 'walkin') {
        activeTab = document.getElementById(tabName + '-orders-tab');
        activePane = document.getElementById(tabName + '-orders');
    } else {
        activeTab = document.getElementById(tabName + '-tab');
        activePane = document.getElementById(tabName);
    }
    
    if (activeTab && activePane) {
        activeTab.classList.add('active');
        activeTab.setAttribute('aria-selected', 'true');
        activePane.classList.add('show', 'active');
    }
}

// Order History Functions - Removed filterHistory as filter button was removed

function viewProof(proofUrl) {
    // Open proof image in a modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Proof of Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${proofUrl}" class="img-fluid" alt="Proof of Delivery" style="max-height: 70vh; border-radius: 8px;">
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

function viewOrderDetails(orderId) {
    // Redirect to order details page
    window.location.href = `/admin/orders/${orderId}`;
}

function downloadReceipt(orderId) {
    // Download receipt for the order
    window.open(`/admin/orders/${orderId}/receipt`, '_blank');
}
</script>
@endsection

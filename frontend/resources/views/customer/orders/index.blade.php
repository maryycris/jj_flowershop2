@extends('layouts.customer_app')

@section('content')
@include('components.customer.alt_nav', ['active' => 'profile'])
<style>
    .sidebar {
        background: #f4f9f4;
        border-radius: 10px;
        padding: 30px 20px;
        min-height: 500px;
    }
    .sidebar .profile-img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        background: #e0e0e0;
        display: block;
        margin: 0 auto 10px auto;
    }
    .sidebar .active-link {
        background: #cfe3d8;
        border-radius: 4px;
        font-weight: bold;
    }
    .order-tabs .nav-link {
        color: #222 !important;
        background: #f4f9f4;
    }
    .order-tabs .nav-link.active {
        border-bottom: 3px solid #7bb47b;
        color: #222 !important;
        font-weight: bold;
        background: #f4f9f4;
    }
    .order-list-row {
        border-bottom: 1px solid #e0e0e0;
        padding: 18px 0;
        align-items: center;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    .order-list-row:last-child {
        border-bottom: none;
    }
    .order-list-row:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        border-radius: 8px;
        border: 1px solid #e3f2fd;
    }
    .order-list-row.clickable {
        position: relative;
    }
    .order-list-row.clickable::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        z-index: 1;
    }
    .order-product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        background: #e0e0e0;
    }
    .order-status-btn {
        min-width: auto;
        white-space: nowrap;
        font-size: 0.8rem;
        padding: 4px 8px;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .order-search-bar {
        background: #f4f9f4;
        border-radius: 6px;
        padding: 8px 12px;
        margin-bottom: 10px;
        border: 1px solid #e0e0e0;
    }
    
    /* Custom Pagination Styling */
    .pagination {
        margin: 0;
        gap: 8px;
    }
    
    .pagination .page-link {
        background: #f4f9f4;
        border: 1px solid #7bb47b;
        color: #2d5a2d;
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 40px;
        text-align: center;
    }
    
    .pagination .page-link:hover {
        background: #7bb47b;
        color: white;
        border-color: #7bb47b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(123, 180, 123, 0.3);
    }
    
    .pagination .page-item.active .page-link {
        background: #7bb47b;
        color: white;
        border-color: #7bb47b;
        box-shadow: 0 4px 12px rgba(123, 180, 123, 0.4);
    }
    
    .pagination .page-item.disabled .page-link {
        background: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    .pagination .page-item.disabled .page-link:hover {
        background: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
        transform: none;
        box-shadow: none;
    }
    
    /* Responsive Design */
    
    /* Tablet View (768px - 1024px) */
    @media (max-width: 1024px) and (min-width: 769px) {
        .main-content-with-sidebar {
            margin-left: 30% !important;
            max-width: calc(70% - 20px) !important;
            padding: 20px;
        }
        
        .sidebar-container {
            width: 30%;
            max-width: 280px;
        }
        
        .order-list-row {
            padding: 15px;
        }
        
        .order-product-img {
            width: 55px;
            height: 55px;
        }
        
        .order-tabs .nav-link {
            padding: 10px 16px;
            font-size: 0.9rem;
        }
    }
    
    /* Phone View (576px - 768px) */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 10px;
        }
        /* Mobile page label */
        .page-label-mobile { position: absolute; top: 6px; left: 30px; font-weight: 700; font-size: 1.2rem; color: #4a9448; }
        /* Make navbars sticky (alt_nav) */
        .alt-topbar { position: fixed !important; }
        .mobile-bottom-nav { position: fixed !important; }
        /* Give content space below the label */
        #ordersContent { margin-top: 4px; }
        
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
        
        .order-tabs {
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .order-tabs .nav-link {
            padding: 8px 12px;
            font-size: 0.8rem;
            margin-bottom: 5px;
        }
        
        .order-list-row {
            padding: 12px;
            margin-bottom: 10px;
        }
        
        .order-product-img {
            width: 50px;
            height: 50px;
        }
        
        .order-status-btn {
            font-size: 0.7rem;
            padding: 6px 10px;
        }
        
        .order-search-bar {
            margin-bottom: 15px;
        }
        
        .pagination .page-link {
            padding: 6px 10px;
            font-size: 0.9rem;
            min-width: 35px;
        }
    }
    
    /* Extra Small Phone View (320px - 576px) */
    @media (max-width: 576px) {
        .container-fluid {
            padding: 5px;
        }
        
        .main-content-with-sidebar {
            padding: 10px;
        }
        
        .sidebar-container {
            padding: 20px 15px;
        }
        
        .sidebar .profile-img {
            width: 60px;
            height: 60px;
        }
        
        .sidebar .fw-bold {
            font-size: 1rem;
        }
        
        .order-tabs {
            padding: 5px;
        }
        
        .order-tabs .nav-link {
            padding: 6px 8px;
            font-size: 0.75rem;
            margin-bottom: 3px;
        }
        
        .order-list-row {
            padding: 10px;
            margin-bottom: 8px;
        }
        
        .order-product-img {
            width: 45px;
            height: 45px;
        }
        
        .order-status-btn {
            font-size: 0.65rem;
            padding: 4px 8px;
        }
        
        .order-search-bar {
            padding: 6px 10px;
            margin-bottom: 10px;
        }
        
        .order-search-bar input {
            font-size: 0.9rem;
        }
        
        .pagination {
            gap: 4px;
        }
        
        .pagination .page-link {
            padding: 5px 8px;
            font-size: 0.8rem;
            min-width: 30px;
        }
        
        /* Stack columns on very small screens */
        .order-list-row .col-md-1,
        .order-list-row .col-md-2,
        .order-list-row .col-md-3 {
            margin-bottom: 8px;
        }
        
        .order-list-row .col-md-2:last-child {
            text-align: left !important;
        }
    }
    
    /* Extra Extra Small Phone View (320px and below) */
    @media (max-width: 320px) {
        .order-tabs .nav-link {
            padding: 4px 6px;
            font-size: 0.7rem;
        }
        
        .order-list-row {
            padding: 8px;
        }
        
        .order-product-img {
            width: 40px;
            height: 40px;
        }
        
        .pagination .page-link {
            padding: 4px 6px;
            font-size: 0.75rem;
            min-width: 28px;
        }
        
        .sidebar .profile-img {
            width: 50px;
            height: 50px;
        }
        
        .sidebar .fw-bold {
            font-size: 0.9rem;
        }
    }
    .review-dropdown {
        position: relative;
        display: inline-block;
    }
    .review-dropdown-toggle {
        background: #f4f9f4;
        border: none;
        color: #222;
        font-weight: bold;
        padding: 6px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 4px;
    }
    .review-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background: #fff;
        min-width: 120px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        z-index: 10;
        border-radius: 4px;
        border: 1px solid #e0e0e0;
    }
    .review-dropdown-menu.show {
        display: block;
    }
    .review-dropdown-menu button {
        width: 100%;
        padding: 6px 12px;
        border: none;
        background: none;
        text-align: left;
        font-size: 12px;
        color: #333;
        cursor: pointer;
    }
    .review-dropdown-menu button:hover {
        background: #f8f9fa;
    }
    .review-dropdown-menu button.active {
        background: #e3f2fd;
        color: #1976d2;
    }
    .star-rating {
        direction: rtl;
        display: inline-flex;
        font-size: 1.2rem;
    }
    .star-rating input[type="radio"] {
        display: none;
    }
    .star-rating label {
        color: #bbb;
        cursor: pointer;
        margin: 0 1px;
    }
    .star-rating input[type="radio"]:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #f5b301;
    }
    .star-rating.readonly label {
        cursor: default;
    }
    .star-rating.readonly label {
        color: #f5b301;
    }
    .star-rating.readonly label:not(.filled) {
        color: #ddd;
    }
    
    /* Modal Star Rating Styles */
    .star-rating-modal {
        direction: rtl;
        display: inline-flex;
        font-size: 2rem;
        gap: 5px;
    }
    .star-rating-modal input[type="radio"] {
        display: none;
    }
    .star-label {
        color: #ddd;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 2.5rem;
    }
    .star-label:hover,
    .star-label:hover ~ .star-label,
    .star-rating-modal input[type="radio"]:checked ~ .star-label {
        color: #ffc107;
        transform: scale(1.1);
    }
    
    /* Review item hover effects */
    .review-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .review-comment-box {
        width: 100%;
        min-height: 50px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 6px 10px;
        margin-top: 6px;
        font-size: 0.95rem;
        background: #fafafa;
    }
</style>

<div class="container-fluid py-4 position-relative">
    <div class="row justify-content-center">
        <div class="d-md-none page-label-mobile">My Purchase</div>
        <!-- Sidebar -->
        <div class="col-12 col-md-3 col-lg-3 d-none d-md-block">
            @include('customer.sidebar')
        </div>
        
        <!-- Main Content -->
        <div class="col-12 col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 25%; max-width: calc(75% - 30px);">
            <div class="py-4 px-3" id="ordersContent">
                <div class="mb-3 d-flex align-items-center">
                    <ul class="nav nav-tabs order-tabs" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('status', 'all') === 'all' ? 'active' : '' }}" id="tab-all" data-status="all" type="button" role="tab">All</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('status') === 'to_pay' ? 'active' : '' }}" id="tab-to-pay" data-status="to_pay" type="button" role="tab">To Pay</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('status') === 'to_ship' ? 'active' : '' }}" id="tab-to-ship" data-status="to_ship" type="button" role="tab">To Ship</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('status') === 'to_receive' ? 'active' : '' }}" id="tab-to-receive" data-status="to_receive" type="button" role="tab">To Receive</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ request('status') === 'receive' ? 'active' : '' }}" id="tab-receive" data-status="receive" type="button" role="tab">RECEIVED</button>
                        </li>
                        <li class="nav-item position-relative" role="presentation">
                            <div class="d-inline-block" id="toReviewTabWrapper">
                                <button class="nav-link {{ request('status') === 'to_review' ? 'active' : '' }}" id="tab-to-review" data-status="to_review" type="button" role="tab">
                                    <span id="toReviewTabLabel">To Review</span>
                                </button>
                                <div class="review-dropdown-menu" id="reviewDropdownMenu" style="display:none; position:absolute; top:100%; left:0; min-width:180px;">
                                    <button type="button" class="dropdown-item active" data-review-type="to_be_review">To be Review Product</button>
                                    <button type="button" class="dropdown-item" data-review-type="reviewed">Reviewed Products</button>
                                    <button type="button" class="dropdown-item" data-review-type="shop_review">Review the Shop</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <form method="GET" id="orderFilterForm">
                    <div class="order-search-bar mb-2">
                        <input type="text" id="orderSearchInput" name="search" class="form-control border-0 bg-transparent" 
                               placeholder="Search orders by product name..." value="{{ request('search') }}">
                    </div>
                    <input type="hidden" name="status" id="statusFilter" value="{{ request('status', 'all') }}">
                </form>
                <div id="reviewSectionHeader" class="d-flex align-items-center mb-0" style="display:none;">
                </div>
                @if ($orders->isEmpty())
                    <div class="alert" role="alert" style="background-color: #e8f5e8; border-color: #7bb47b; color: #2d5a2d;">
                        @if(request('status') && request('status') !== 'all')
                            @switch(request('status'))
                                @case('to_pay')
                                    No orders pending payment.
                                    @break
                                @case('to_ship')
                                    No orders ready to ship.
                                    @break
                                @case('to_receive')
                                    No orders currently on delivery.
                                    @break
                                @case('receive')
                                    No orders received yet.
                                    @break
                                @case('to_review')
                                    No orders ready for review.
                                    @break
                                @default
                                    No orders found for this status.
                            @endswitch
                        @else
                            You haven't placed any orders yet.
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded shadow-sm p-3">
                        <div class="row fw-bold text-muted mb-2 d-none d-md-flex" style="border-bottom:1px solid #e0e0e0;">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">Product Info</div>
                            <div class="col-md-2">Price</div>
                            <div class="col-md-2">Quantity</div>
                            <div class="col-md-2">Date Received</div>
                            <div class="col-md-2 text-end">Status</div>
                        </div>
                        <div id="orderList">
                            @foreach ($orders as $order)
                                @foreach ($order->products as $product)
                                    <div class="row order-list-row order-row clickable" 
                                        data-status="{{ \App\Services\OrderStatusService::getCustomerDisplayStatus($order->order_status ?? $order->status) }}"
                                        data-product="{{ strtolower($product->name) }}"
                                        data-review="{{ isset($product->pivot->reviewed) && $product->pivot->reviewed ? 'reviewed' : 'to_be_review' }}"
                                        style="cursor: pointer; transition: all 0.3s ease;"
                                        onclick="window.location.href='{{ route('customer.orders.show', $order->id) }}'">
                                        
                                        <!-- Desktop View -->
                                        <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" class="order-product-img" alt="{{ $product->name }}">
                                            @elseif($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="order-product-img" alt="{{ $product->name }}">
                                            @else
                                                <div class="order-product-img d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-3 d-none d-md-flex flex-column justify-content-center">
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            <div class="text-muted small">Order #{{ $order->id }}</div>
                                        </div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center">₱{{ number_format($product->price, 2) }}</div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center">x{{ $product->pivot->quantity }}</div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center">{{ $order->created_at->format('M d, Y') }}</div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center justify-content-end">
                                            @php
                                                $orderStatus = $order->order_status ?? $order->status;
                                                $statusLabel = \App\Services\OrderStatusService::getStatusLabel($orderStatus);
                                            @endphp
                                            
                                            <!-- Desktop Status -->
                                            <div class="d-flex flex-column align-items-end">
                                                @if($orderStatus === 'pending')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'approved')
                                                    <span class="btn btn-sm btn-info order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'on_delivery')
                                                    <span class="btn btn-sm btn-primary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'delivered')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'completed')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'cancelled')
                                                    <span class="btn btn-sm btn-danger order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'returned')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @endif
                                                
                                                <!-- Refund Information for Desktop -->
                                                @if($order->refund_amount && $order->refund_processed_at)
                                                <div class="mt-2">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <i class="fas fa-money-bill-wave me-1 text-success" style="font-size: 0.8rem;"></i>
                                                        <small class="text-success fw-bold">Refunded ₱{{ number_format($order->refund_amount, 2) }}</small>
                                                    </div>
                                                    <small class="text-muted">
                                                        @if($order->refund_processed_at)
                                                            {{ $order->refund_processed_at instanceof \Carbon\Carbon ? $order->refund_processed_at->format('M d, Y') : \Carbon\Carbon::parse($order->refund_processed_at)->format('M d, Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                            
                                        <!-- Mobile View -->
                                        <div class="col-12 d-md-none">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    @if($product->image_url)
                                                        <img src="{{ $product->image_url }}" class="order-product-img" alt="{{ $product->name }}">
                                                    @elseif($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" class="order-product-img" alt="{{ $product->name }}">
                                                    @else
                                                        <div class="order-product-img d-flex align-items-center justify-content-center bg-light text-muted">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold mb-1">{{ $product->name }}</div>
                                                    <div class="text-muted small mb-2">Order #{{ $order->id }}</div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</span>
                                                            <span class="text-muted ms-2">x{{ $product->pivot->quantity }}</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="text-muted small mb-1">{{ $order->created_at->format('M d, Y') }}</div>
                                            
                                            <!-- Mobile Status -->
                                            <div class="d-md-none">
                                                @if($orderStatus === 'pending')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'approved')
                                                    <span class="btn btn-sm btn-info order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'on_delivery')
                                                    <span class="btn btn-sm btn-primary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'delivered')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'completed')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'cancelled')
                                                    <span class="btn btn-sm btn-danger order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'returned')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @endif
                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @foreach ($order->customBouquets as $customBouquet)
                                    <div class="row order-list-row order-row clickable" 
                                        data-status="{{ \App\Services\OrderStatusService::getCustomerDisplayStatus($order->order_status ?? $order->status) }}"
                                        data-product="custom bouquet"
                                        style="cursor: pointer; transition: all 0.3s ease;"
                                        onclick="window.location.href='{{ route('customer.orders.show', $order->id) }}'">
                                        
                                        <!-- Desktop View -->
                                        <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                                            <div class="order-product-img d-flex align-items-center justify-content-center" style="background: linear-gradient(45deg, #ff6b6b, #4ecdc4); color: white; font-weight: bold; font-size: 12px;">
                                                CUSTOM
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-none d-md-flex flex-column justify-content-center">
                                            <div class="fw-bold">Custom Bouquet</div>
                                            <div class="text-muted small">Order #{{ $order->id }}</div>
                                        </div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center">₱{{ number_format($customBouquet->total_price, 2) }}</div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center">x{{ $customBouquet->pivot->quantity }}</div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center">{{ $order->created_at->format('M d, Y') }}</div>
                                        <div class="col-md-2 d-none d-md-flex align-items-center justify-content-end">
                                            @php
                                                $orderStatus = $order->order_status ?? $order->status;
                                                $statusLabel = \App\Services\OrderStatusService::getStatusLabel($orderStatus);
                                            @endphp
                                            
                                            <!-- Desktop Status -->
                                            <div class="d-flex flex-column align-items-end">
                                                @if($orderStatus === 'pending')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'approved')
                                                    <span class="btn btn-sm btn-info order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'on_delivery')
                                                    <span class="btn btn-sm btn-primary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'delivered')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'completed')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'cancelled')
                                                    <span class="btn btn-sm btn-danger order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'returned')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile View -->
                                        <div class="col-12 d-md-none">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <div class="order-product-img d-flex align-items-center justify-content-center" style="background: linear-gradient(45deg, #ff6b6b, #4ecdc4); color: white; font-weight: bold; font-size: 12px;">
                                                        CUSTOM
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold mb-1">Custom Bouquet</div>
                                                    <div class="text-muted small mb-2">Order #{{ $order->id }}</div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="text-success fw-bold">₱{{ number_format($customBouquet->total_price, 2) }}</span>
                                                            <span class="text-muted ms-2">x{{ $customBouquet->pivot->quantity }}</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="text-muted small mb-1">{{ $order->created_at->format('M d, Y') }}</div>
                                            
                                            <!-- Mobile Status -->
                                            <div class="d-md-none">
                                                @php
                                                    $orderStatus = $order->order_status ?? $order->status;
                                                    $statusLabel = \App\Services\OrderStatusService::getStatusLabel($orderStatus);
                                                @endphp
                                                
                                                @if($orderStatus === 'pending')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'approved')
                                                    <span class="btn btn-sm btn-info order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'on_delivery')
                                                    <span class="btn btn-sm btn-primary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'delivered')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'completed')
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'cancelled')
                                                    <span class="btn btn-sm btn-danger order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @elseif($orderStatus === 'returned')
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary order-status-btn" style="font-weight:bold;">{{ $statusLabel }}</span>
                                                @endif
                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <!-- Review Section (hidden by default, shown for To Review tab) -->
                    <div id="reviewList" style="display:none;">
                        <div class="bg-white rounded shadow-sm p-3">
                            <div class="row fw-bold text-muted mb-2 mt-2" style="border-bottom:1px solid #e0e0e0;">
                                <div class="col-md-6">Products</div>
                                <div class="col-md-6">Review</div>
                            </div>
                            
                            <!-- To be Review Section -->
                            <div id="toBeReviewSection">
                                @php
                                // Get completed orders that haven't been reviewed yet
                                $completedOrders = $orders->filter(function($order) {
                                    $orderStatus = $order->order_status ?? $order->status;
                                    return $orderStatus === 'completed';
                                });
                                $toBeReviewedProducts = [];
                                foreach($completedOrders as $order) {
                                    foreach($order->products as $product) {
                                        if(!isset($product->pivot->reviewed) || !$product->pivot->reviewed) {
                                            $toBeReviewedProducts[] = [
                                                'product' => $product,
                                                'order' => $order,
                                                'pivot' => $product->pivot
                                            ];
                                        }
                                    }
                                }
                                @endphp
                                
                                @if(count($toBeReviewedProducts) > 0)
                                    @foreach($toBeReviewedProducts as $item)
                                    <div class="row order-list-row align-items-center mb-3 review-item" 
                                         data-order-id="{{ $item['order']->id }}" 
                                         data-product-id="{{ $item['product']->id }}"
                                         style="cursor: pointer; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; transition: all 0.3s ease;"
                                         onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                         onmouseout="this.style.backgroundColor='white'"
                                         onclick="openReviewModal({{ $item['order']->id }}, {{ $item['product']->id }}, {{ json_encode($item['product']->name) }}, {{ json_encode($item['product']->image_url ?? asset('storage/' . $item['product']->image)) }})">
                                        <div class="col-md-6 d-flex align-items-center">
                                            @if($item['product']->image_url)
                                                <img src="{{ $item['product']->image_url }}" class="order-product-img me-3" alt="{{ $item['product']->name }}">
                                            @elseif($item['product']->image)
                                                <img src="{{ asset('storage/' . $item['product']->image) }}" class="order-product-img me-3" alt="{{ $item['product']->name }}">
                                            @else
                                                <div class="order-product-img me-3 d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item['product']->name }}</div>
                                                <div class="text-muted small">Order #{{ $item['order']->id }}</div>
                                                <div class="text-primary small mt-1">
                                                    <i class="fas fa-star"></i> Click to rate this product
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-star me-1"></i> Rate Product
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-star" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No products to review yet</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Reviewed Section -->
                            <div id="reviewedSection" style="display:none;">
                                @php
                                // Get completed orders that have been reviewed
                                $reviewedProducts = [];
                                foreach($completedOrders as $order) {
                                    foreach($order->products as $product) {
                                        if(isset($product->pivot->reviewed) && $product->pivot->reviewed) {
                                            $reviewedProducts[] = [
                                                'product' => $product,
                                                'order' => $order,
                                                'pivot' => $product->pivot
                                            ];
                                        }
                                    }
                                }
                                @endphp
                                
                                @if(count($reviewedProducts) > 0)
                                    @foreach($reviewedProducts as $item)
                                    <div class="row order-list-row align-items-center mb-3">
                                        <div class="col-md-6 d-flex align-items-center">
                                            @if($item['product']->image_url)
                                                <img src="{{ $item['product']->image_url }}" class="order-product-img me-3" alt="{{ $item['product']->name }}">
                                            @elseif($item['product']->image)
                                                <img src="{{ asset('storage/' . $item['product']->image) }}" class="order-product-img me-3" alt="{{ $item['product']->name }}">
                                            @else
                                                <div class="order-product-img me-3 d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item['product']->name }}</div>
                                                <div class="text-muted small">Order #{{ $item['order']->id }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="star-rating readonly mb-2">
                                                @for($i=5;$i>=1;$i--)
                                                    <label class="{{ $i <= ($item['pivot']->rating ?? 0) ? 'filled' : '' }}">★</label>
                                                @endfor
                                            </div>
                                            <div class="review-comment-display p-2 bg-light rounded">
                                                {{ $item['pivot']->review_comment ?? 'No comment provided' }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No reviewed products yet</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Shop Review Section -->
                            <div id="shopReviewSection" style="display:none;">
                                <div class="text-center py-4">
                                    <div class="mb-2">
                                        <i class="fas fa-store text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="text-success mb-1" style="font-weight: 700;">Review Our Shop</h5>
                                    <p class="text-muted mb-3" style="font-size: .95rem;">Share your overall experience with J'J Flower Shop</p>
                                    
                                    <!-- Shop Review Form -->
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body p-3">
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold small mb-1">Overall Rating</label>
                                                        <div class="star-rating" id="shopRating" style="font-size: 1.4rem;">
                                                            <label for="star5">★</label>
                                                            <input type="radio" id="star5" name="shop_rating" value="5">
                                                            <label for="star4">★</label>
                                                            <input type="radio" id="star4" name="shop_rating" value="4">
                                                            <label for="star3">★</label>
                                                            <input type="radio" id="star3" name="shop_rating" value="3">
                                                            <label for="star2">★</label>
                                                            <input type="radio" id="star2" name="shop_rating" value="2">
                                                            <label for="star1">★</label>
                                                            <input type="radio" id="star1" name="shop_rating" value="1">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <label for="shopReviewComment" class="form-label fw-semibold small mb-1">Your Review</label>
                                                        <textarea class="form-control" id="shopReviewComment" rows="3" 
                                                                placeholder="Tell us about your experience with our shop..."></textarea>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold small mb-2">What did you like most?</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="quality" value="quality">
                                                                    <label class="form-check-label" for="quality">Product Quality</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="delivery" value="delivery">
                                                                    <label class="form-check-label" for="delivery">Fast Delivery</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="packaging" value="packaging">
                                                                    <label class="form-check-label" for="packaging">Beautiful Packaging</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="service" value="service">
                                                                    <label class="form-check-label" for="service">Customer Service</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="pricing" value="pricing">
                                                                    <label class="form-check-label" for="pricing">Fair Pricing</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="variety" value="variety">
                                                                    <label class="form-check-label" for="variety">Product Variety</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-center mt-3">
                                                        <button type="button" class="btn btn-success btn-sm px-4" id="submitShopReview">
                                                            <i class="fas fa-paper-plane me-2"></i>Submit Shop Review
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-0 mb-4 col-12 col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 31.25%; width: 830px;">
        <div class="bg-white rounded shadow-sm p-3" style="width: 100%;">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                @if ($orders->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">&laquo;</a>
                    </li>
                @endif

                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if ($page == $orders->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                @if ($orders->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">&raquo;</span>
                    </li>
                @endif
                </ul>
            </nav>
        </div>
    </div>
    @endif
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">
                    <i class="fas fa-star text-warning me-2"></i>Rate Your Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div id="modalProductImage" class="text-center">
                            <!-- Product image will be loaded here -->
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 id="modalProductName" class="mb-3"></h6>
                        <p class="text-muted small mb-4">Order #<span id="modalOrderId"></span></p>
                        
                        <form id="reviewModalForm">
                            @csrf
                            <input type="hidden" id="modalOrderIdInput" name="order_id">
                            <input type="hidden" id="modalProductIdInput" name="product_id">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Rate this product:</label>
                                <div class="star-rating-modal">
                                    <input type="radio" id="modal-star5" name="rating" value="5">
                                    <label for="modal-star5" class="star-label">★</label>
                                    <input type="radio" id="modal-star4" name="rating" value="4">
                                    <label for="modal-star4" class="star-label">★</label>
                                    <input type="radio" id="modal-star3" name="rating" value="3">
                                    <label for="modal-star3" class="star-label">★</label>
                                    <input type="radio" id="modal-star2" name="rating" value="2">
                                    <label for="modal-star2" class="star-label">★</label>
                                    <input type="radio" id="modal-star1" name="rating" value="1">
                                    <label for="modal-star1" class="star-label">★</label>
                                </div>
                                <div id="ratingText" class="mt-2 text-muted"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modalComment" class="form-label fw-bold">Your Review:</label>
                                <textarea class="form-control" id="modalComment" name="comment" rows="4" 
                                          placeholder="Share your experience with this product and our service..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitReviewBtn" onclick="submitReview()">
                    <i class="fas fa-paper-plane me-1"></i> Submit Review
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // Review Modal Functions
    function openReviewModal(orderId, productId, productName, productImage) {
        // Set modal content
        document.getElementById('modalOrderId').textContent = orderId;
        document.getElementById('modalOrderIdInput').value = orderId;
        document.getElementById('modalProductIdInput').value = productId;
        document.getElementById('modalProductName').textContent = productName;
        
        // Set product image
        const imageContainer = document.getElementById('modalProductImage');
        if (productImage && productImage !== '') {
            imageContainer.innerHTML = `<img src="${productImage}" class="img-fluid rounded" style="max-height: 200px;" alt="${productName}">`;
        } else {
            imageContainer.innerHTML = `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-image text-muted" style="font-size: 3rem;"></i></div>`;
        }
        
        // Reset form
        document.getElementById('reviewModalForm').reset();
        document.getElementById('ratingText').textContent = '';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }
    
    function submitReview() {
        const form = document.getElementById('reviewModalForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitReviewBtn');
        
        // Validate rating
        const rating = formData.get('rating');
        if (!rating) {
            alert('Please select a rating before submitting.');
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
        submitBtn.disabled = true;
        
        // Submit review
        fetch('{{ route("customer.orders.submitReview") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCuteAlert('Review submitted successfully!');
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                modal.hide();
                // Reload page to update the sections
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showCuteAlert('Error: ' + (data.message || 'Failed to submit review'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCuteAlert('An error occurred while submitting the review.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Review';
            submitBtn.disabled = false;
        });
    }
    
    // Star rating text updates
    document.addEventListener('DOMContentLoaded', function() {
        const starInputs = document.querySelectorAll('.star-rating-modal input[type="radio"]');
        const ratingText = document.getElementById('ratingText');
        
        starInputs.forEach(input => {
            input.addEventListener('change', function() {
                const rating = this.value;
                const texts = {
                    '1': 'Poor',
                    '2': 'Fair', 
                    '3': 'Good',
                    '4': 'Very Good',
                    '5': 'Excellent'
                };
                ratingText.textContent = texts[rating] || '';
            });
        });
    });
    
    // Tab filtering with server-side requests
    document.querySelectorAll('.order-tabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            let status = this.getAttribute('data-status');
            
            // Handle To Review tab specially - don't submit form, show review sections
            if (status === 'to_review') {
                e.preventDefault();
                e.stopPropagation();
                // Show review list and hide order list
                const orderList = document.getElementById('orderList');
                const reviewList = document.getElementById('reviewList');
                if (orderList) orderList.style.display = 'none';
                if (reviewList) reviewList.style.display = 'block';
                return; // Don't submit form for review tab
            } else {
                // For other tabs, allow normal form submission
                // Show order list and hide review list for other tabs
                const orderList = document.getElementById('orderList');
                const reviewList = document.getElementById('reviewList');
                if (orderList) orderList.style.display = 'block';
                if (reviewList) reviewList.style.display = 'none';
                
                // Update the hidden status filter input
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) statusFilter.value = status;
                
                // Submit the form to reload with new filter
                const orderFilterForm = document.getElementById('orderFilterForm');
                if (orderFilterForm) orderFilterForm.submit();
            }
        });
    });
    // Review dropdown logic for To Review tab
    const toReviewTab = document.getElementById('tab-to-review');
    const reviewDropdownMenu = document.getElementById('reviewDropdownMenu');
    const toReviewTabLabel = document.getElementById('toReviewTabLabel');
    
    // Handle To Review tab click to show/hide dropdown
    if (toReviewTab && reviewDropdownMenu) {
        toReviewTab.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle dropdown visibility
            const isVisible = reviewDropdownMenu.style.display === 'block';
            reviewDropdownMenu.style.display = isVisible ? 'none' : 'block';
            
            // If showing dropdown, also show the review list
            if (!isVisible) {
                const orderList = document.getElementById('orderList');
                const reviewList = document.getElementById('reviewList');
                if (orderList) orderList.style.display = 'none';
                if (reviewList) reviewList.style.display = 'block';
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (reviewDropdownMenu && !e.target.closest('#toReviewTabWrapper')) {
            reviewDropdownMenu.style.display = 'none';
        }
    });
    
    // Handle dropdown option clicks
    if (reviewDropdownMenu) {
        document.querySelectorAll('#reviewDropdownMenu button').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Remove active class from all dropdown buttons
                document.querySelectorAll('#reviewDropdownMenu button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update tab label (without arrow)
                if (toReviewTabLabel) {
                    toReviewTabLabel.textContent = this.textContent;
                }
                
                // Hide dropdown
                reviewDropdownMenu.style.display = 'none';
                
                // Show/hide appropriate sections
                const toBeReviewSection = document.getElementById('toBeReviewSection');
                const reviewedSection = document.getElementById('reviewedSection');
                const shopReviewSection = document.getElementById('shopReviewSection');
                
                if(this.getAttribute('data-review-type') === 'to_be_review') {
                    if (toBeReviewSection) toBeReviewSection.style.display = '';
                    if (reviewedSection) reviewedSection.style.display = 'none';
                    if (shopReviewSection) shopReviewSection.style.display = 'none';
                } else if(this.getAttribute('data-review-type') === 'reviewed') {
                    if (toBeReviewSection) toBeReviewSection.style.display = 'none';
                    if (reviewedSection) reviewedSection.style.display = '';
                    if (shopReviewSection) shopReviewSection.style.display = 'none';
                } else if(this.getAttribute('data-review-type') === 'shop_review') {
                    if (toBeReviewSection) toBeReviewSection.style.display = 'none';
                    if (reviewedSection) reviewedSection.style.display = 'none';
                    if (shopReviewSection) shopReviewSection.style.display = '';
                }
            });
        });
    }
    
    // Default: show To be Review section when To Review tab is active
    const toBeReviewSection = document.getElementById('toBeReviewSection');
    const reviewedSection = document.getElementById('reviewedSection');
    if(toBeReviewSection) {
        toBeReviewSection.style.display = '';
    }
    if(reviewedSection) {
        reviewedSection.style.display = 'none';
    }
    // Search filtering with debouncing
    let searchTimeout;
    const searchInput = document.getElementById('orderSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('orderFilterForm').submit();
            }
        });
        // Remove live typing search - only search on Enter key press
        // searchInput.addEventListener('input', function() {
        //     clearTimeout(searchTimeout);
        //     searchTimeout = setTimeout(function() {
        //         document.getElementById('orderFilterForm').submit();
        //     }, 500); // Wait 500ms after user stops typing
        // });
    }

    // Debug: Log current status on page load
    console.log('Current status filter:', document.getElementById('statusFilter').value);
    console.log('Current search term:', document.getElementById('orderSearchInput').value);

    // Cute Alert Function
    function showCuteAlert(message, type = 'success') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.cute-alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'cute-alert';
        alertDiv.style.cssText = `
            background: #e8f5e8;
            border: 1px solid #7bb47b;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 0;
            max-width: 500px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1060;
            box-shadow: 0 4px 12px rgba(123, 180, 123, 0.25);
            animation: slideInDown 0.3s ease-out;
        `;
        
        alertDiv.innerHTML = `
            <div style="background: #7bb47b; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                <i class="fas fa-check"></i>
            </div>
            <div style="color: #2d5a2d; font-weight: 500; flex: 1; font-size: 14px;">${message}</div>
            <button type="button" onclick="dismissCuteAlert()" style="background: none; border: none; color: #666; cursor: pointer; padding: 4px; border-radius: 4px; transition: background-color 0.2s; flex-shrink: 0;">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto dismiss after 4 seconds
        setTimeout(() => {
            dismissCuteAlert();
        }, 4000);
    }
    
    function dismissCuteAlert() {
        const alert = document.querySelector('.cute-alert');
        if (alert) {
            alert.style.animation = 'slideOutUp 0.3s ease-in';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }
    
    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInDown {
            from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
            to { transform: translateX(-50%) translateY(0); opacity: 1; }
        }
        @keyframes slideOutUp {
            from { transform: translateX(-50%) translateY(0); opacity: 1; }
            to { transform: translateX(-50%) translateY(-20px); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Review form submission
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('review-form')) {
            e.preventDefault();
            
            const form = e.target;
            const orderId = form.getAttribute('data-order-id');
            const productId = form.getAttribute('data-product-id');
            const formData = new FormData(form);
            
            // Add order and product IDs to form data
            formData.append('order_id', orderId);
            formData.append('product_id', productId);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;
            
            fetch('{{ route("customer.orders.submitReview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCuteAlert('Review submitted successfully!');
                    // Reload the page to show updated reviews
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showCuteAlert('Failed to submit review: ' + (data.message || 'Unknown error'));
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error submitting review:', error);
                showCuteAlert('Error submitting review. Please try again.');
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    });
    
    // Shop Review Submission
    document.addEventListener('DOMContentLoaded', function() {
        const submitShopReviewBtn = document.getElementById('submitShopReview');
        if (submitShopReviewBtn) {
            submitShopReviewBtn.addEventListener('click', function() {
                // Get form data
                const shopRating = document.querySelector('input[name="shop_rating"]:checked');
                const shopComment = document.getElementById('shopReviewComment').value;
                const likedMost = document.querySelector('input[name="liked_most"]:checked');
                
                // Validate required fields
                if (!shopRating) {
                    showCuteAlert('Please select a rating for the shop.');
                    return;
                }
                
                if (!shopComment.trim()) {
                    showCuteAlert('Please write a review comment.');
                    return;
                }
                
                // Show loading state
                const originalText = submitShopReviewBtn.innerHTML;
                submitShopReviewBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
                submitShopReviewBtn.disabled = true;
                
                // Prepare form data
                const formData = new FormData();
                formData.append('shop_rating', shopRating.value);
                formData.append('shop_comment', shopComment);
                if (likedMost) {
                    formData.append('liked_most', likedMost.value);
                }
                formData.append('_token', '{{ csrf_token() }}');
                
                // Submit shop review
                fetch('{{ route("customer.orders.submitShopReview") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showCuteAlert('Shop review submitted successfully!');
                        // Reset form
                        document.querySelectorAll('input[name="shop_rating"]').forEach(radio => radio.checked = false);
                        document.getElementById('shopReviewComment').value = '';
                        document.querySelectorAll('input[name="liked_most"]').forEach(radio => radio.checked = false);
                    } else {
                        showCuteAlert('Error: ' + (data.message || 'Failed to submit shop review'));
                    }
                })
                .catch(error => {
                    console.error('Error submitting shop review:', error);
                    showCuteAlert('An error occurred while submitting the shop review.');
                })
                .finally(() => {
                    // Reset button state
                    submitShopReviewBtn.innerHTML = originalText;
                    submitShopReviewBtn.disabled = false;
                });
            });
        }
    });
</script>
@endsection 
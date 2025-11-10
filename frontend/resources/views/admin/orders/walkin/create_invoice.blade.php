@extends('layouts.admin_app')

@php
    // Extract customer name from notes field
    $customerName = '';
    if ($order->notes && str_contains($order->notes, 'Customer: ')) {
        $customerName = str_replace('Customer: ', '', $order->notes);
    }
    
    // Check if sales order already exists, if not show empty
    $salesOrder = \App\Models\SalesOrder::where('order_id', $order->id)->first();
    $soNumber = $salesOrder ? $salesOrder->so_number : '';
@endphp

@section('content')
<style>
    /* Font and Icon Hierarchy */
    .card-header-title {
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .section-header {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .table-header {
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .table-body {
        font-size: 0.85rem;
    }
    
    .form-label-small {
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .input-sm {
        font-size: 0.85rem;
    }
    
    .btn-sm-custom {
        font-size: 0.85rem;
        padding: 0.35rem 0.7rem;
    }
    
    .icon-sm {
        font-size: 0.85rem;
    }
    
    .icon-md {
        font-size: 1rem;
    }
    
    .back-btn {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .back-btn i {
        font-size: 1rem;
    }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Header with Back Button, Tabs and Workflow -->
                    <div class="p-3" style="border-bottom:1px solid #e6f0e6;">
                        <!-- Back Button -->
                        <div class="mb-3">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary back-btn">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                        
                        <!-- Tabs -->
                        <div class="d-flex align-items-center mb-3">
                            <div>
                                <div class="nav nav-tabs border-0" id="orderTabs" role="tablist">
                                    <a href="{{ route('admin.orders.create') }}" class="nav-link active bg-light" id="new-tab" style="font-size: 0.9rem;">
                                        New
                                    </a>
                                    <button class="btn btn-outline-success btn-sm-custom ms-2" id="saveIconBtn" title="Save Sales Order">
                                        <i class="bi bi-download icon-sm"></i>
                                    </button>
                                    <button class="nav-link" id="quotations-tab" data-bs-toggle="tab" data-bs-target="#quotations" type="button" role="tab" aria-controls="quotations" aria-selected="false" style="font-size: 0.9rem;">
                                        Quotations
                                    </button>
                                </div>
                                <!-- SO Number Display - directly below NEW tab -->
                                @if($soNumber)
                                <div class="mt-2">
                                    <h5 class="mb-0 text-primary fw-bold" id="soNumberDisplay" style="font-size: 1rem;">{{ $soNumber }}</h5>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Workflow Indicator -->
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2" style="font-size: 0.8rem;">Quotation</span>
                                    <i class="bi bi-arrow-right text-muted icon-sm"></i>
                            
                                    <span class="badge bg-secondary" style="font-size: 0.8rem;">Sales Order</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons and Workflow -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm-custom" id="sendEmailBtn">
                                    <i class="bi bi-envelope icon-sm me-1"></i>Send by Email
                                </button>
                                <button class="btn btn-success btn-sm-custom" id="confirmBtn">
                                    <i class="bi bi-check-circle icon-sm me-1"></i>CONFIRM
                                </button>
                                <button class="btn btn-outline-dark btn-sm-custom" id="cancelBtn">
                                    <i class="bi bi-x-circle icon-sm me-1"></i>Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="orderTabContent">
                        <!-- New Tab -->
                        <div class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new-tab">
                            <!-- Customer and Order Details Grid -->
                            <div class="row g-0 border mb-3" style="border-color:#d9ecd9 !important;">
                                <div class="col-md-2" style="background:#e6f5e6;">
                                    <div class="p-3 section-header" style="border-bottom:1px solid #d9ecd9;">Customer</div>
                                    <div class="p-3">
                                        <input type="text" class="form-control form-control-sm input-sm" id="customerName" placeholder="Enter customer name" value="{{ $customerName }}">
                                    </div>
                                </div>
                                <div class="col-md-3" style="background:#e6f5e6;">
                                    <div class="p-3 section-header" style="border-bottom:1px solid #d9ecd9;">Delivery Address</div>
                                    <div class="p-3">
                                        <input type="text" class="form-control form-control-sm input-sm" id="invoiceAddress" placeholder="Enter delivery address">
                                    </div>
                                </div>
                                <div class="col-md-3" style="background:#e6f5e6;">
                                    <div class="p-3 section-header" style="border-bottom:1px solid #d9ecd9;">Email Address</div>
                                    <div class="p-3">
                                        <input type="email" class="form-control form-control-sm input-sm" id="deliveryAddress" placeholder="Enter email address">
                                    </div>
                                </div>
                                <div class="col-md-2" style="background:#e6f5e6;">
                                    <div class="p-3 section-header" style="border-bottom:1px solid #d9ecd9;">Order Date</div>
                                    <div class="p-3">
                                        <input type="date" class="form-control form-control-sm input-sm" id="orderDate" value="{{ $order->created_at->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Order Line Section -->
                            <div class="mt-3 px-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="px-3 py-2 section-header" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Order Line</div>
                                    <button class="btn btn-success btn-sm-custom" id="addOrderBtn" style="margin-right: 0;">
                                        <i class="bi bi-plus-circle icon-sm me-1"></i>Add Order
                                    </button>
                                </div>
                                <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                    <table class="table mb-0 table-body" id="orderLineTable">
                                        <thead style="background:#e6f5e6;">
                                            <tr>
                                                <th class="table-header" style="width:20%; padding: 0.5rem 0.75rem;">Product</th>
                                                <th class="table-header" style="width:25%; padding: 0.5rem 0.75rem;">Description</th>
                                                <th class="table-header" style="width:10%; padding: 0.5rem 0.75rem;">Quantity</th>
                                                <th class="table-header" style="width:10%; padding: 0.5rem 0.75rem;">UoM</th>
                                                <th class="table-header" style="width:15%; padding: 0.5rem 0.75rem;">Unit Price</th>
                                                <th class="table-header" style="width:10%; padding: 0.5rem 0.75rem;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderLineBody" class="table-body">
                                            @foreach($order->products as $product)
                                                <tr>
                                                    <td style="padding: 0.4rem 0.75rem;">
                                                        <select class="form-select form-select-sm product-select input-sm">
                                                            <option value="{{ $product->id }}" selected>{{ $product->name }}</option>
                                                        </select>
                                                    </td>
                                                    <td style="padding: 0.4rem 0.75rem;">
                                                        <input type="text" class="form-control form-control-sm description-input input-sm" value="{{ $product->description }}">
                                                    </td>
                                                    <td style="padding: 0.4rem 0.75rem;">
                                                        <input type="number" class="form-control form-control-sm quantity-input input-sm" value="{{ $product->pivot->quantity }}" min="1">
                                                    </td>
                                                    <td style="padding: 0.4rem 0.75rem;">
                                                        <select class="form-select form-select-sm uom-select input-sm">
                                                            <option value="pcs" selected>PCS</option>
                                                            <option value="dozen">Dozen</option>
                                                            <option value="box">Box</option>
                                                            <option value="kg">KG</option>
                                                            <option value="g">G</option>
                                                        </select>
                                                    </td>
                                                    <td style="padding: 0.4rem 0.75rem;">
                                                        <input type="number" class="form-control form-control-sm unit-price-input input-sm" value="{{ $product->price }}" min="0" step="0.01">
                                                    </td>
                                                    <td style="padding: 0.4rem 0.75rem;">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOrderLine(this)" style="padding: 0.25rem 0.5rem;">
                                                            <i class="bi bi-trash icon-sm"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Delivery Options and Total -->
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm-custom" id="pickupBtn">
                                    <i class="bi bi-box-seam icon-sm me-1"></i>Pick up
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm-custom" id="deliveryBtn">
                                    <i class="bi bi-truck icon-sm me-1"></i>Delivery
                                </button>
                                    </div>
                                    <div class="fw-semibold" style="font-size: 0.95rem;">
                                        Total: ₱<span id="totalPrice">{{ number_format($order->total_price, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Pickup Form (Hidden by default) -->
                                <div id="pickupForm" class="mt-3" style="display: none;">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Pickup Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Customer Name</label>
                                                    <input type="text" class="form-control" id="pickupCustomerName" placeholder="Enter customer name">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Contact Number</label>
                                                    <input type="text" class="form-control" id="pickupContact" placeholder="Enter contact number">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Pickup Date</label>
                                                    <input type="date" class="form-control" id="pickupDate">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Pickup Time</label>
                                                    <select class="form-select" id="pickupTime">
                                                        <option value="">Select time</option>
                                                        <!-- Options will be populated dynamically based on current time -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label">Special Instructions</label>
                                                <textarea class="form-control" id="pickupInstructions" rows="2" placeholder="Any special instructions for pickup..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Form (Hidden by default) -->
                                <div id="deliveryForm" class="mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <!-- Recipient Type Buttons -->
                                                    <div class="row mb-4">
                                                        <div class="col-12">
                                                            <div class="d-flex gap-2">
                                                                <button type="button" class="btn btn-success flex-fill recipient-type-btn active" data-type="someone">
                                                                    <i class="bi bi-person-check me-2"></i>Someone will receive the order
                                                                </button>
                                                                <button type="button" class="btn btn-outline-success flex-fill recipient-type-btn" data-type="self">
                                                                    <i class="bi bi-person me-2"></i>I will receive the order.
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div id="selfContactSection" style="display:none;">
                                                        <h6 class="fw-bold mb-3 text-success"><i class="bi bi-person me-2"></i>Your Contact Information</h6>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Your Contact Number *</label>
                                                                <input type="text" class="form-control" id="selfContactNumber" placeholder="09XXXXXXXXX">
                                                                <small class="text-muted">Mobile number for delivery updates</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h6 id="recipientInfoTitle" class="fw-bold mb-3 text-success"><i class="bi bi-person me-2"></i>Recipient Information</h6>
                                                    
                                                    <div id="recipientInfoSection" class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Recipient Name *</label>
                                                            <input type="text" class="form-control" id="deliveryRecipientName" placeholder="Enter Recipient's Full Name">
                                                            <small class="text-muted">Full name as it appears on ID</small>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Recipient Contact Number *</label>
                                                            <input type="text" class="form-control" id="deliveryContact" placeholder="09XXXXXXXXX">
                                                            <small class="text-muted">Mobile number for delivery updates</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Relationship to Recipient *</label>
                                                            <select class="form-select" id="deliveryRelationship">
                                                                <option value="">Select relationship</option>
                                                                <option value="family">Family</option>
                                                                <option value="friend">Friend</option>
                                                                <option value="colleague">Colleague</option>
                                                                <option value="partner">Partner</option>
                                                                <option value="other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Special Instructions</label>
                                                            <textarea class="form-control" id="deliveryInstructions" rows="2" placeholder="Any special delivery instructions..."></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Delivery Message/Card Message</label>
                                                        <textarea class="form-control" id="deliveryMessage" rows="3" placeholder="Write a personal message for the recipient..."></textarea>
                                                        <small class="text-muted">This message will be included with the delivery</small>
                                                    </div>
                                                    
                                                    <!-- Pickup Location Card -->
                                                    <div class="card mb-3" style="border: 1px solid #e0e0e0;">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-truck text-primary me-2"></i>
                                                                    <div>
                                                                        <h6 class="mb-1 fw-bold">Pickup Location</h6>
                                                                        <p class="mb-0 text-muted small">J'J Flower Shop, Bangbang, Cordova, Cebu</p>
                                                                        <small class="text-muted">Our shop location (fixed)</small>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-outline-primary btn-sm">
                                                                    <i class="bi bi-geo-alt me-1"></i>SHOP
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Drop-off Location Card -->
                                                    <div class="card mb-3" style="border: 1px solid #e0e0e0;">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="d-flex align-items-center flex-grow-1 me-3">
                                                                    <i class="bi bi-flag text-success me-2"></i>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1 fw-bold">Drop-off Location</h6>
                                                                        <input type="text" class="form-control form-control-sm" id="deliveryFullAddress" placeholder="Where To Deliver The Item...">
                                                                        <small class="text-muted">Customer's delivery address</small>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-success btn-sm" id="findDeliveryAddressBtn">
                                                                    <i class="bi bi-search me-1"></i>FIND
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Shipping Fee Display -->
                                                    <div class="card mb-3" id="shippingFeeCard" style="display: none;">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1 fw-bold text-success">Shipping Fee</h6>
                                                                    <small class="text-muted">Calculated based on delivery address</small>
                                                                </div>
                                                                <div class="text-end">
                                                                    <span class="h5 mb-0 text-success" id="shippingFeeAmount">₱0.00</span>
                                                                    <br>
                                                                    <small class="text-muted" id="shippingFeeDetails">Standard delivery</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-success w-100 mb-3" id="showMapBtn">
                                                        <i class="bi bi-map me-2"></i>SHOW MAP
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="card border-success" style="border-width: 2px;">
                                                <div class="card-body">
                                                    <h6 class="fw-bold text-success mb-3">
                                                        <i class="bi bi-check-circle me-2"></i>Choose Your Delivery Schedule
                                                    </h6>
                                                    <p class="text-muted small mb-3">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        Select your preferred delivery date and time. We'll deliver your flowers when you need them most!
                                                    </p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            <i class="bi bi-calendar me-1"></i>Delivery Date *
                                                        </label>
                                                        <input type="date" class="form-control" id="deliveryDate" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                                        <small class="text-muted">Select a date at least 1 day from now</small>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            <i class="bi bi-clock me-1"></i>Delivery Time *
                                                        </label>
                                                        <select class="form-select" id="deliveryTime">
                                                            <option value="">Choose time...</option>
                                                            <option value="09:00">9:00 AM</option>
                                                            <option value="10:00">10:00 AM</option>
                                                            <option value="11:00">11:00 AM</option>
                                                            <option value="12:00">12:00 PM</option>
                                                            <option value="13:00">1:00 PM</option>
                                                            <option value="14:00">2:00 PM</option>
                                                            <option value="15:00">3:00 PM</option>
                                                            <option value="16:00">4:00 PM</option>
                                                            <option value="17:00">5:00 PM</option>
                                                        </select>
                                                        <small class="text-muted">Choose your preferred time slot</small>
                                                    </div>
                                                    
                                                    <!-- Driver Selection -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            <i class="bi bi-truck me-2"></i>Assign Driver *
                                                        </label>
                                                        <select class="form-select" id="assignedDriver" name="delivery_data[driver_id]">
                                                            <option value="">Select driver...</option>
                                                            @foreach(\App\Models\User::where('role', 'driver')->get() as $driver)
                                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Choose the driver who will handle this delivery</small>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quotations Tab -->
                        <div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="quotations-tab">
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No quotations available</h5>
                                <p class="text-muted">Quotations will appear here once created.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Search Modal -->
<div class="modal fade" id="productSearchModal" tabindex="-1" aria-labelledby="productSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productSearchModalLabel">
                    <i class="bi bi-search me-2"></i>Search Products from Catalog
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Bar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="productSearchInput" placeholder="Search products by name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\CatalogProduct::distinct()->pluck('category') as $category)
                                @if($category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="row" id="productsGrid">
                    @foreach(\App\Models\CatalogProduct::where('status', true)->where('is_approved', true)->whereIn('category', ['Bouquets', 'Packages', 'Gifts', 'Other Offers'])->get() as $product)
                        @php
                            $availabilityService = new \App\Services\ProductAvailabilityService();
                            $availability = $availabilityService->checkCatalogProductAvailability($product->id, 1);
                            $isOutOfStock = !$availability['can_fulfill'];
                        @endphp
                        <div class="col-md-4 mb-3 product-card" data-name="{{ strtolower($product->name) }}" data-category="{{ strtolower($product->category ?? '') }}">
                            <div class="card h-100 product-item {{ $isOutOfStock ? 'out-of-stock' : '' }}" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-description="{{ $product->description }}">
                                <!-- Product Image -->
                                <div class="card-img-top-container position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <div class="text-center">
                                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                <p class="mt-2 mb-0">No Image</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Out of Stock Overlay -->
                                    @if($isOutOfStock)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7);">
                                            <div class="text-center text-white">
                                                <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                                                <div class="fw-bold mt-2">OUT OF STOCK</div>
                                                <small class="text-light">Insufficient materials</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title {{ $isOutOfStock ? 'text-muted' : '' }}">{{ $product->name }}</h6>
                                    <p class="card-text text-muted small">{{ $product->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold {{ $isOutOfStock ? 'text-muted' : 'text-success' }}">₱{{ number_format($product->price, 2) }}</span>
                                        <span class="badge {{ $isOutOfStock ? 'bg-danger' : 'bg-secondary' }}">{{ $product->category }}</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    @if($isOutOfStock)
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" disabled>
                                            <i class="bi bi-x-circle me-1"></i>Out of Stock
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm w-100 select-product-btn">
                                            <i class="bi bi-plus-circle me-1"></i>Select Product
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- No Results Message -->
                <div id="noResultsMessage" class="text-center py-5" style="display: none;">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No products found</h5>
                    <p class="text-muted">Try adjusting your search criteria</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
/* Out of Stock Product Styling */
.product-item.out-of-stock {
    opacity: 0.7;
    filter: grayscale(50%);
}

.product-item.out-of-stock .card {
    border-color: #dc3545;
}

.product-item.out-of-stock .card-img-top {
    filter: grayscale(100%);
}

.product-item.out-of-stock:hover {
    transform: none;
    cursor: not-allowed;
}

/* Disabled button styling */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
// Global functions
function addNewOrderLine() {
    console.log('addNewOrderLine function called');
    
    // Add a new row directly to the table
    const tbody = document.getElementById('orderLineBody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td style="padding: 0.4rem 0.75rem;">
            <select class="form-select form-select-sm product-select input-sm">
                <option value="">Select Product</option>
                <option value="search-more" class="text-primary fw-bold">🔍 Search More Products...</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td style="padding: 0.4rem 0.75rem;">
            <input type="text" class="form-control form-control-sm description-input input-sm" placeholder="Enter description">
        </td>
        <td style="padding: 0.4rem 0.75rem;">
            <input type="number" class="form-control form-control-sm quantity-input input-sm" value="1" min="1">
        </td>
        <td style="padding: 0.4rem 0.75rem;">
            <select class="form-select form-select-sm uom-select input-sm">
                <option value="pcs" selected>PCS</option>
                <option value="dozen">Dozen</option>
                <option value="box">Box</option>
                <option value="kg">KG</option>
                <option value="g">G</option>
            </select>
        </td>
        <td style="padding: 0.4rem 0.75rem;">
            <input type="number" class="form-control form-control-sm unit-price-input input-sm" value="0" min="0" step="0.01">
        </td>
        <td style="padding: 0.4rem 0.75rem;">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOrderLine(this)" style="padding: 0.25rem 0.5rem;">
                <i class="bi bi-trash icon-sm"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    
    // Recalculate total
    calculateTotal();
    
    // Show success message
    showToast('Order line added successfully', 'success');
}


function removeOrderLine(button) {
    const row = button.closest('tr');
    row.remove();
    calculateTotal();
    showToast('Order line removed', 'info');
}

function calculateTotal() {
    const rows = document.querySelectorAll('#orderLineBody tr');
    let total = 0;
    let itemCount = 0;
    
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input')?.value || 0);
        const unitPrice = parseFloat(row.querySelector('.unit-price-input')?.value || 0);
        const rowTotal = quantity * unitPrice;
        total += rowTotal;
        
        if (quantity > 0 && unitPrice > 0) {
            itemCount++;
        }
    });
    
    document.getElementById('totalPrice').textContent = total.toFixed(2);
    
    // Update item count in header (guarded if element exists)
    const itemCountElement = document.getElementById('itemCount');
    if (itemCountElement) {
        itemCountElement.textContent = itemCount;
    }
    
    // Update total display with formatting
    const totalElement = document.getElementById('totalPrice');
    if (total > 0) {
        totalElement.innerHTML = `<span class="text-success fw-bold">₱${total.toFixed(2)}</span>`;
    } else {
        totalElement.innerHTML = '<span class="text-muted">₱0.00</span>';
    }
}

function selectDeliveryMethod(method) {
    const pickupBtn = document.getElementById('pickupBtn');
    const deliveryBtn = document.getElementById('deliveryBtn');
    const pickupForm = document.getElementById('pickupForm');
    const deliveryForm = document.getElementById('deliveryForm');
    
    if (method === 'pickup') {
        // Update button styles
        pickupBtn.classList.add('btn-warning');
        pickupBtn.classList.remove('btn-outline-secondary');
        deliveryBtn.classList.remove('btn-warning');
        deliveryBtn.classList.add('btn-outline-secondary');
        
        // Show pickup form, hide delivery form
        pickupForm.style.display = 'block';
        deliveryForm.style.display = 'none';
        
        // Update email address field for pickup - keep value if it exists
        const emailAddressField = document.getElementById('deliveryAddress');
        if (emailAddressField) {
            // Don't clear the value - keep what user typed
            emailAddressField.readOnly = false;
            emailAddressField.placeholder = 'Enter email address';
        }
        
        showToast('Pickup form displayed', 'info');
    } else if (method === 'delivery') {
        // Update button styles
        deliveryBtn.classList.add('btn-warning');
        deliveryBtn.classList.remove('btn-outline-secondary');
        pickupBtn.classList.remove('btn-warning');
        pickupBtn.classList.add('btn-outline-secondary');
        
        // Show delivery form, hide pickup form
        deliveryForm.style.display = 'block';
        pickupForm.style.display = 'none';
        
        // Update email address field for delivery - keep value if it exists
        const emailAddressField = document.getElementById('deliveryAddress');
        if (emailAddressField) {
            // Don't clear the value - keep what user typed
            emailAddressField.readOnly = false;
            emailAddressField.placeholder = 'Enter email address';
        }
        
        showToast('Delivery form displayed', 'info');
    }
}

function createInvoice() {
    // Validate form before creating invoice
    if (!validateForm()) {
        showToast('Please fix validation errors before creating invoice', 'error');
        return;
    }
    
    const formData = collectFormData();
    
    // Show confirmation dialog
    Swal.fire({
        title: 'Save Sales Order?',
        html: `
            <div class="text-start">
                <p><strong>Customer Name:</strong> ${formData.customerName || 'Not specified'}</p>
                <p><strong>Delivery Address:</strong> ${formData.invoiceAddress || 'Not specified'}</p>
                <p><strong>Email Address:</strong> ${formData.deliveryAddress || 'Not specified'}</p>
                <p><strong>Order Date:</strong> ${formData.orderDate || 'Not specified'}</p>
                <p><strong>Total Items:</strong> ${formData.orderLines.length}</p>
                <p><strong>Total Amount:</strong> ₱${calculateTotalAmount()}</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Yes, Save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            // Save order data and stay on page
            saveOrderData().then((response) => {
                console.log('Save response:', response);
                if (response.success && response.order_id) {
                    // Update the SO number display if sales order was created
                    if (response.so_number) {
                        let soNumberElement = document.getElementById('soNumberDisplay');
                        if (!soNumberElement) {
                            // Find the tabs container and insert SO number below it
                            const tabsContainer = document.querySelector('.nav.nav-tabs');
                            if (tabsContainer) {
                                const soNumberHtml = `
                                    <div class="mt-2">
                                        <h4 class="mb-0 text-primary fw-bold" id="soNumberDisplay">${response.so_number}</h4>
                                    </div>
                                `;
                                tabsContainer.insertAdjacentHTML('afterend', soNumberHtml);
                            }
                        } else {
                            // If already exists, just update the text
                            soNumberElement.textContent = response.so_number;
                        }
                        showToast('Sales Order saved successfully! SO: ' + response.so_number, 'success');
                    } else {
                        showToast('Invoice saved successfully!', 'success');
                    }
                } else {
                    showToast('Invalid response from server', 'error');
                }
            }).catch((error) => {
                console.error('Error saving order:', error);
                showToast('Error saving order data: ' + error.message, 'error');
            });
        }
    });
}

function sendInvoiceByEmail() {
    showToast('Email functionality would be implemented here', 'info');
}

function confirmInvoice() {
    // Validate form before confirming sales order
    if (!validateForm()) {
        showToast('Please fix validation errors before confirming sales order', 'error');
        return;
    }
    
    const formData = collectFormData();
    
    // Show confirmation dialog
    Swal.fire({
        title: 'Confirm Sales Order?',
        html: `
            <div class="text-start">
                <p><strong>Customer Name:</strong> ${formData.customerName || 'Not specified'}</p>
                <p><strong>Delivery Address:</strong> ${formData.invoiceAddress || 'Not specified'}</p>
                <p><strong>Email Address:</strong> ${formData.deliveryAddress || 'Not specified'}</p>
                <p><strong>Order Date:</strong> ${formData.orderDate || 'Not specified'}</p>
                <p><strong>Total Items:</strong> ${formData.orderLines.length}</p>
                <p><strong>Total Amount:</strong> ₱${calculateTotalAmount()}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Confirm Sales Order',
        cancelButtonText: 'No, Review Again',
        confirmButtonColor: '#0d6efd'
    }).then((result) => {
                if (result.isConfirmed) {
            // First save the order data
            saveOrderData().then((response) => {
                console.log('Confirm save response:', response);
                if (response.success && response.order_id) {
                    // Removed the extra truck badge UI
                    
                    showToast('Sales Order confirmed! Inventory triggered. Redirecting to Sales Order view...', 'success');
                    
                    // Redirect to Sales Order view after a short delay
                    setTimeout(() => {
                        // sales-orders.show expects the Order ID
                        window.location.href = `/admin/sales-orders/${response.order_id}`;
                    }, 1000);
                } else {
                    showToast('Error confirming sales order', 'error');
                }
            }).catch((error) => {
                console.error('Error confirming sales order:', error);
                showToast('Error confirming sales order: ' + error.message, 'error');
            });
        }
    });
}

function lockAllFormFields() {
    // Disable all input fields
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.disabled = true;
    });
    
    // Disable all buttons except essential ones
    const actionButtons = document.querySelectorAll('button');
    actionButtons.forEach(button => {
        const buttonId = button.id;
        // Keep Save and CONFIRM buttons functional for multiple saves
        if (buttonId !== 'saveIconBtn' && buttonId !== 'confirmBtn' && buttonId !== 'cancelBtn' && buttonId !== 'sendEmailBtn') {
            button.disabled = true;
        }
    });
    
    // Add visual indicator that form is locked
    const formCards = document.querySelectorAll('.card');
    formCards.forEach(card => {
        card.style.opacity = '0.9';
        card.style.pointerEvents = 'none';
    });
    
    console.log('All form fields have been locked.');
}

function cancelOrder() {
    Swal.fire({
        title: 'Cancel Order?',
        text: 'Are you sure you want to cancel this order?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel Order',
        cancelButtonText: 'No, Keep Order',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            showToast('Order cancelled', 'success');
            // Here you would handle the cancellation
        }
    });
}

function validateForm() {
    const customerName = document.getElementById('customerName').value.trim();
    const invoiceAddress = document.getElementById('invoiceAddress').value.trim();
    const deliveryAddress = document.getElementById('deliveryAddress').value.trim();
    
    if (!customerName) {
        showToast('Customer name is required', 'error');
        document.getElementById('customerName').focus();
        return false;
    }
    
    if (!invoiceAddress) {
        showToast('Delivery address is required', 'error');
        document.getElementById('invoiceAddress').focus();
        return false;
    }
    
    // Email address only required if method is delivery
    const isPickup = document.getElementById('pickupForm').style.display === 'block';
    if (!isPickup && !deliveryAddress) {
        showToast('Email address is required', 'error');
        document.getElementById('deliveryAddress').focus();
        return false;
    }
    
    // Check if there are any order lines
    const orderLines = document.querySelectorAll('#orderLineBody tr');
    if (orderLines.length === 0) {
        showToast('Please add at least one order line', 'error');
        return false;
    }
    
    return true;
}

function collectFormData() {
    // Determine which form is active
    const pickupForm = document.getElementById('pickupForm');
    const deliveryForm = document.getElementById('deliveryForm');
    const isPickup = pickupForm.style.display === 'block';
    
    return {
        customerName: document.getElementById('customerName').value,
        invoiceAddress: document.getElementById('invoiceAddress').value,
        deliveryAddress: document.getElementById('deliveryAddress').value,
        orderDate: document.getElementById('orderDate').value,
        deliveryMethod: isPickup ? 'pickup' : 'delivery',
        pickupData: isPickup ? {
            customerName: document.getElementById('pickupCustomerName').value,
            contact: document.getElementById('pickupContact').value,
            date: document.getElementById('pickupDate').value,
            time: document.getElementById('pickupTime').value,
            instructions: document.getElementById('pickupInstructions').value
        } : null,
        deliveryData: !isPickup ? {
            recipientName: document.getElementById('deliveryRecipientName').value,
            contact: document.getElementById('deliveryContact').value,
            address: document.getElementById('deliveryFullAddress').value,
            date: document.getElementById('deliveryDate').value,
            time: document.getElementById('deliveryTime').value,
            instructions: document.getElementById('deliveryInstructions').value,
            driver_id: document.getElementById('assignedDriver').value
        } : null,
        orderLines: Array.from(document.querySelectorAll('#orderLineBody tr')).map(row => {
            const productSelect = row.querySelector('.product-select');
            const selectedOption = productSelect?.options[productSelect?.selectedIndex];
            return {
                product: selectedOption?.textContent || '', // Get product name from option text
                description: row.querySelector('.description-input')?.value || '',
                quantity: row.querySelector('.quantity-input')?.value || 1,
                uom: row.querySelector('.uom-select')?.value || '',
                unit_price: row.querySelector('.unit-price-input')?.value || 0
            };
        })
    };
}

function calculateTotalAmount() {
    const rows = document.querySelectorAll('#orderLineBody tr');
    let total = 0;
    
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input')?.value || 0);
        const unitPrice = parseFloat(row.querySelector('.unit-price-input')?.value || 0);
        total += quantity * unitPrice;
    });
    
    return total.toFixed(2);
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

async function calculateShippingFee() {
    const deliveryAddress = document.getElementById('deliveryFullAddress').value.trim();
    
    if (!deliveryAddress) {
        showToast('Please enter a delivery address first', 'error');
        return;
    }
    
    // Show loading state
    const findBtn = document.getElementById('findDeliveryAddressBtn');
    const originalText = findBtn.innerHTML;
    findBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>CALCULATING...';
    findBtn.disabled = true;
    
    try {
        // Simulate shipping fee calculation (you can replace this with actual API call)
        const shippingFee = await calculateShippingFeeAPI(deliveryAddress);
        
        // Display shipping fee
        document.getElementById('shippingFeeAmount').textContent = `₱${shippingFee.fee.toFixed(2)}`;
        document.getElementById('shippingFeeDetails').textContent = shippingFee.description;
        document.getElementById('shippingFeeCard').style.display = 'block';
        
        // Update total calculation to include shipping
        updateTotalWithShipping(shippingFee.fee);
        
        showToast(`Shipping fee calculated: ₱${shippingFee.fee.toFixed(2)}`, 'success');
        
    } catch (error) {
        console.error('Error calculating shipping fee:', error);
        showToast('Error calculating shipping fee', 'error');
    } finally {
        // Reset button
        findBtn.innerHTML = originalText;
        findBtn.disabled = false;
    }
}

async function calculateShippingFeeAPI(address) {
    try {
        // Use the same API as customer system
        const response = await fetch('/api/map/shipping-calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                origin_address: 'Bangbang, Cordova, Cebu',
                destination_address: address
            })
        });
        
        if (!response.ok) {
            throw new Error('API request failed');
        }
        
        const data = await response.json();
        
        if (data.success) {
            return {
                fee: data.shipping_fee,
                description: data.shipping_fee === 30 ? 'Within Cordova delivery' : `Outside Cordova (+${data.distance}km)`,
                address: address,
                distance: data.distance
            };
        } else {
            throw new Error(data.message || 'Shipping calculation failed');
        }
        
    } catch (error) {
        console.error('Shipping API error, using fallback:', error);
        
        // Fallback calculation using same logic as customer system
        let fee = 30; // Base fee for Cordova
        let description = 'Within Cordova delivery';
        let distance = 0;
        
        const addressLower = address.toLowerCase();
        
        // Check if address is outside Cordova
        if (!addressLower.includes('cordova') && !addressLower.includes('bangbang')) {
            // Calculate additional fee for areas outside Cordova (same as customer logic)
            if (addressLower.includes('minglanilla')) {
                distance = 28;
                fee = 30 + (distance * 10); // P310.00
                description = `Minglanilla delivery (+${distance}km)`;
            } else if (addressLower.includes('kalawisan')) {
                distance = 13;
                fee = 30 + (distance * 10); // P160.00
                description = `Kalawisan delivery (+${distance}km)`;
            } else if (addressLower.includes('cebu city') || addressLower.includes('cebu')) {
                distance = 18;
                fee = 30 + (distance * 10); // P210.00
                description = `Cebu City delivery (+${distance}km)`;
            } else if (addressLower.includes('mandaue')) {
                distance = 14;
                fee = 30 + (distance * 10); // P170.00
                description = `Mandaue delivery (+${distance}km)`;
            } else if (addressLower.includes('lapu-lapu') || addressLower.includes('lapulapu')) {
                distance = 10;
                fee = 30 + (distance * 10); // P130.00
                description = `Lapu-Lapu delivery (+${distance}km)`;
            } else if (addressLower.includes('talisay')) {
                distance = 22;
                fee = 30 + (distance * 10); // P250.00
                description = `Talisay delivery (+${distance}km)`;
            } else {
                distance = 25;
                fee = 30 + (distance * 10); // P280.00
                description = `Extended area delivery (+${distance}km)`;
            }
        }
        
        return {
            fee: fee,
            description: description,
            address: address,
            distance: distance
        };
    }
}

function updateTotalWithShipping(shippingFee) {
    // Get current order total
    const currentTotal = calculateTotalAmount();
    const newTotal = parseFloat(currentTotal) + parseFloat(shippingFee);
    
    // Update total display
    document.getElementById('totalPrice').textContent = newTotal.toFixed(2);
    
    // Store shipping fee for form submission
    window.currentShippingFee = shippingFee;
}

function showDeliveryMap() {
    const deliveryAddress = document.getElementById('deliveryFullAddress').value.trim();
    
    if (!deliveryAddress) {
        showToast('Please enter a delivery address first', 'error');
        return;
    }
    
    // Show map in a modal (same as customer system)
    Swal.fire({
        title: 'Delivery Route Map',
        html: `
            <div id="deliveryMapContainer" style="height: 400px; width: 100%; border-radius: 8px; overflow: hidden;">
                <div id="deliveryMap" style="height: 100%; width: 100%;"></div>
            </div>
            <div class="mt-3">
                <div class="row">
                    <div class="col-6">
                        <strong>Pickup:</strong> J'J Flower Shop, Bangbang, Cordova, Cebu
                    </div>
                    <div class="col-6">
                        <strong>Delivery:</strong> ${deliveryAddress}
                    </div>
                </div>
            </div>
        `,
        width: '80%',
        showConfirmButton: true,
        confirmButtonText: 'Close',
        confirmButtonColor: '#28a745',
        didOpen: () => {
            // Initialize map after modal opens
            setTimeout(() => {
                initializeDeliveryMap(deliveryAddress);
            }, 100);
        }
    });
}

function initializeDeliveryMap(deliveryAddress) {
    // Initialize Leaflet map (same as customer system)
    const map = L.map('deliveryMap').setView([10.2588, 123.9445], 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add shop marker
    const shopMarker = L.marker([10.2588, 123.9445]).addTo(map);
    shopMarker.bindPopup('<b>J\'J Flower Shop</b><br>Bangbang, Cordova, Cebu').openPopup();
    
    // Add delivery marker (approximate location)
    let deliveryLat, deliveryLng;
    
    // Estimate coordinates based on address (same logic as customer system)
    const addressLower = deliveryAddress.toLowerCase();
    if (addressLower.includes('cordova') || addressLower.includes('bangbang')) {
        deliveryLat = 10.2588; deliveryLng = 123.9445; // Same as shop
    } else if (addressLower.includes('minglanilla')) {
        deliveryLat = 10.2436; deliveryLng = 123.7992;
    } else if (addressLower.includes('cebu city') || addressLower.includes('cebu')) {
        deliveryLat = 10.3157; deliveryLng = 123.8854;
    } else if (addressLower.includes('mandaue')) {
        deliveryLat = 10.3333; deliveryLng = 123.9333;
    } else if (addressLower.includes('lapu-lapu') || addressLower.includes('lapulapu')) {
        deliveryLat = 10.3103; deliveryLng = 123.9494;
    } else if (addressLower.includes('talisay')) {
        deliveryLat = 10.2447; deliveryLng = 123.8425;
    } else {
        // Default to Cebu City area
        deliveryLat = 10.3157; deliveryLng = 123.8854;
    }
    
    const deliveryMarker = L.marker([deliveryLat, deliveryLng]).addTo(map);
    deliveryMarker.bindPopup(`<b>Delivery Address</b><br>${deliveryAddress}`);
    
    // Draw route line
    const routeLine = L.polyline([
        [10.2588, 123.9445], // Shop coordinates
        [deliveryLat, deliveryLng] // Delivery coordinates
    ], {
        color: '#007bff',
        weight: 4,
        opacity: 0.7
    }).addTo(map);
    
    // Fit map to show both markers
    const group = new L.featureGroup([shopMarker, deliveryMarker, routeLine]);
    map.fitBounds(group.getBounds().pad(0.1));
}

async function saveOrderData() {
    const formData = collectFormData();
    const orderId = window.location.pathname.split('/')[3];
    
    // Include shipping fee if calculated
    if (window.currentShippingFee) {
        formData.shippingFee = window.currentShippingFee;
    }
    
    const requestData = {
        customer_name: formData.customerName,
        invoice_address: formData.invoiceAddress,
        delivery_address: formData.deliveryAddress,
        order_date: formData.orderDate,
        delivery_method: formData.deliveryMethod,
        pickup_data: formData.pickupData,
        delivery_data: formData.deliveryData,
        order_lines: formData.orderLines,
        total_amount: calculateTotalAmount(),
        shipping_fee: window.currentShippingFee || 0
    };
    
    console.log('Sending data to server:', requestData);
    
    try {
        const response = await fetch(`/admin/orders/${orderId}/walkin/update-invoice`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        });
        
        let result;
        try {
            result = await response.json();
        } catch (_) {
            const text = await response.text();
            throw new Error(text || 'Failed to parse server response');
        }
        
        if (!response.ok || result?.success === false) {
            throw new Error(result?.message || 'Failed to save order data');
        }
        
        console.log('Order saved successfully:', result);
        return result;
    } catch (error) {
        console.error('Error saving order:', error);
        throw error;
    }
}

// Initialize pickup time options based on current time
function initializePickupTimeOptions() {
    const pickupTimeSelect = document.getElementById('pickupTime');
    if (!pickupTimeSelect) return;
    
    // Clear existing options except the first one
    pickupTimeSelect.innerHTML = '<option value="">Select time</option>';
    
    // Get current time and date
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();
    const today = now.toISOString().split('T')[0]; // YYYY-MM-DD format
    
    // Get selected pickup date
    const pickupDateInput = document.getElementById('pickupDate');
    const selectedDate = pickupDateInput ? pickupDateInput.value : today;
    
    // Define all possible time slots (8 AM to 7 PM)
    const timeSlots = [
        { value: '08:00 AM', label: '8:00 AM - 9:00 AM', hour: 8 },
        { value: '09:00 AM', label: '9:00 AM - 10:00 AM', hour: 9 },
        { value: '10:00 AM', label: '10:00 AM - 11:00 AM', hour: 10 },
        { value: '11:00 AM', label: '11:00 AM - 12:00 PM', hour: 11 },
        { value: '12:00 PM', label: '12:00 PM - 1:00 PM', hour: 12 },
        { value: '01:00 PM', label: '1:00 PM - 2:00 PM', hour: 13 },
        { value: '02:00 PM', label: '2:00 PM - 3:00 PM', hour: 14 },
        { value: '03:00 PM', label: '3:00 PM - 4:00 PM', hour: 15 },
        { value: '04:00 PM', label: '4:00 PM - 5:00 PM', hour: 16 },
        { value: '05:00 PM', label: '5:00 PM - 6:00 PM', hour: 17 },
        { value: '06:00 PM', label: '6:00 PM - 7:00 PM', hour: 18 }
    ];
    
    // Filter time slots based on date and current time
    let availableSlots;
    
    if (selectedDate === today) {
        // For today: only show times AFTER current time
        availableSlots = timeSlots.filter(slot => {
            // If it's the same hour, check minutes (need at least 30 minutes buffer)
            if (slot.hour === currentHour) {
                return currentMinute < 30; // Allow if current time is before 30 minutes of the hour
            }
            // Otherwise, just check if the slot hour is after current hour
            return slot.hour > currentHour;
        });
    } else {
        // For other days: show all times from 8 AM to 7 PM
        availableSlots = timeSlots;
    }
    
    // Add available time slots to the select
    availableSlots.forEach(slot => {
        const option = document.createElement('option');
        option.value = slot.value;
        option.textContent = slot.label;
        pickupTimeSelect.appendChild(option);
    });
    
    // If no slots available for today, show message
    if (availableSlots.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No pickup slots available today';
        option.disabled = true;
        pickupTimeSelect.appendChild(option);
    }
    
    console.log('Pickup time options initialized. Selected date:', selectedDate, 'Current time:', now.toLocaleTimeString(), 'Available slots:', availableSlots.length);
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Show 'sales-order' in URL instead of 'invoice' without reloading the page
    try {
        const href = window.location.href;
        if (href.includes('/walkin/invoice')) {
            const newHref = href.replace('/walkin/invoice', '/walkin/sales-order');
            window.history.replaceState({}, '', newHref);
        }
    } catch (err) {}
    console.log('DOM loaded, setting up all functionality');
    
    // Initialize pickup time options based on current time
    initializePickupTimeOptions();
    
    // Add Order button
    const addOrderBtn = document.getElementById('addOrderBtn');
    if (addOrderBtn) {
        addOrderBtn.addEventListener('click', addNewOrderLine);
        console.log('Add Order button event listener attached');
    }
    
    // Save Icon button
    const saveIconBtn = document.getElementById('saveIconBtn');
    if (saveIconBtn) {
        saveIconBtn.addEventListener('click', createInvoice);
    }
    
    // Send Email button
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    if (sendEmailBtn) {
        sendEmailBtn.addEventListener('click', sendInvoiceByEmail);
    }
    
    // Confirm button
    const confirmBtn = document.getElementById('confirmBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmInvoice);
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', cancelOrder);
    }
    
    // Delivery method buttons
    const pickupBtn = document.getElementById('pickupBtn');
    const deliveryBtn = document.getElementById('deliveryBtn');
    
    if (pickupBtn) {
        pickupBtn.addEventListener('click', () => selectDeliveryMethod('pickup'));
    }
    
    if (deliveryBtn) {
        deliveryBtn.addEventListener('click', () => selectDeliveryMethod('delivery'));
    }
    
    // Pickup date change listener
    const pickupDateInput = document.getElementById('pickupDate');
    if (pickupDateInput) {
        pickupDateInput.addEventListener('change', function() {
            // Re-initialize pickup time options when date changes
            initializePickupTimeOptions();
        });
    }
    
    // No default selection - forms are hidden until user clicks a button
    
    // Set default dates for forms
    const today = new Date().toISOString().split('T')[0];
    const pickupDate = document.getElementById('pickupDate');
    const deliveryDate = document.getElementById('deliveryDate');
    
    if (pickupDate) pickupDate.value = today;
    if (deliveryDate) deliveryDate.value = today;
    
    // Calculate total when quantities change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
            calculateTotal();
        }
        
        // Update customer display when customer name changes
        if (e.target.id === 'customerName') {
            const customerDisplay = document.getElementById('customerDisplay');
            if (customerDisplay) {
                customerDisplay.textContent = e.target.value || 'Enter customer name';
            }
        }
    });
    
    // Handle product selection changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedValue = e.target.value;
            
            if (selectedValue === 'search-more') {
                // Show the product search modal
                const modal = new bootstrap.Modal(document.getElementById('productSearchModal'));
                modal.show();
                
                // Store reference to the current select element
                window.currentProductSelect = e.target;
                
                // Reset the select to empty
                e.target.selectedIndex = 0;
            } else {
                // Handle normal product selection
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const description = selectedOption.getAttribute('data-description');
                
                if (price && description) {
                    // Find the row and update price and description
                    const row = e.target.closest('tr');
                    const priceInput = row.querySelector('.unit-price-input');
                    const descriptionInput = row.querySelector('.description-input');
                    
                    if (priceInput) priceInput.value = price;
                    if (descriptionInput) descriptionInput.value = description;
                    
                    // Recalculate total
                    calculateTotal();
                }
            }
        }
    });
    
    // Handle product search functionality
    document.addEventListener('input', function(e) {
        if (e.target.id === 'productSearchInput') {
            filterProducts();
        }
    });
    
    // Handle category filter
    document.addEventListener('change', function(e) {
        if (e.target.id === 'categoryFilter') {
            filterProducts();
        }
    });
    
    // Handle product selection from modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('select-product-btn')) {
            const productCard = e.target.closest('.product-item');
            
            // Check if product is out of stock
            if (productCard.classList.contains('out-of-stock')) {
                showToast('This product is out of stock due to insufficient materials', 'error');
                return;
            }
            
            const productId = productCard.getAttribute('data-product-id');
            const productName = productCard.getAttribute('data-product-name');
            const productPrice = productCard.getAttribute('data-product-price');
            const productDescription = productCard.getAttribute('data-product-description');
            
            // Update the current product select
            if (window.currentProductSelect) {
                // Add the new option to the select
                const newOption = document.createElement('option');
                newOption.value = productId;
                newOption.setAttribute('data-price', productPrice);
                newOption.setAttribute('data-description', productDescription);
                newOption.textContent = productName;
                
                // Insert after the "Search More" option
                const searchMoreOption = window.currentProductSelect.querySelector('option[value="search-more"]');
                searchMoreOption.insertAdjacentElement('afterend', newOption);
                
                // Select the new option
                newOption.selected = true;
                
                // Update price and description in the row
                const row = window.currentProductSelect.closest('tr');
                const priceInput = row.querySelector('.unit-price-input');
                const descriptionInput = row.querySelector('.description-input');
                
                if (priceInput) priceInput.value = productPrice;
                if (descriptionInput) descriptionInput.value = productDescription;
                
                // Recalculate total
                calculateTotal();
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('productSearchModal'));
                modal.hide();
                
                // Clear the reference
                window.currentProductSelect = null;
                
                // Show success message
                showToast(`Product "${productName}" added successfully!`, 'success');
            }
        }
    });
    
    // Function to filter products
    function filterProducts() {
        const searchTerm = document.getElementById('productSearchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const productName = card.getAttribute('data-name');
            const productCategory = card.getAttribute('data-category');
            
            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = !categoryFilter || productCategory.includes(categoryFilter);
            
            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        const noResultsMessage = document.getElementById('noResultsMessage');
        if (visibleCount === 0) {
            noResultsMessage.style.display = 'block';
        } else {
            noResultsMessage.style.display = 'none';
        }
    }
    
    // Handle FIND delivery address button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'findDeliveryAddressBtn' || e.target.closest('#findDeliveryAddressBtn')) {
            calculateShippingFee();
        }
    });
    
    // Handle SHOW MAP button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'showMapBtn' || e.target.closest('#showMapBtn')) {
            showDeliveryMap();
        }
    });
    
    // Handle recipient type button switching
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('recipient-type-btn')) {
            // Remove active class from all buttons
            document.querySelectorAll('.recipient-type-btn').forEach(btn => {
                btn.classList.remove('btn-success', 'active');
                btn.classList.add('btn-outline-success');
            });
            
            // Add active class to clicked button
            e.target.classList.remove('btn-outline-success');
            e.target.classList.add('btn-success', 'active');
            
            // Handle form changes based on selection
            const recipientType = e.target.getAttribute('data-type');
            const recipientNameField = document.getElementById('deliveryRecipientName');
            const relationshipField = document.getElementById('deliveryRelationship');
            
            const recipientWrap = document.getElementById('recipientInfoSection');
            const recipientTitle = document.getElementById('recipientInfoTitle');
            const selfWrap = document.getElementById('selfContactSection');
            if (recipientType === 'self') {
                const customerName = document.getElementById('customerName').value || '{{ $customerName }}';
                if (recipientNameField) { recipientNameField.value = customerName; recipientNameField.readOnly = true; }
                if (relationshipField) { relationshipField.value = 'self'; relationshipField.disabled = true; }
                if (recipientWrap) recipientWrap.style.display = 'none';
                if (recipientTitle) recipientTitle.style.display = 'none';
                if (selfWrap) selfWrap.style.display = 'block';
            } else {
                if (recipientNameField) { recipientNameField.value = ''; recipientNameField.readOnly = false; recipientNameField.placeholder = 'Enter Recipient\'s Full Name'; }
                if (relationshipField) { relationshipField.value = ''; relationshipField.disabled = false; }
                if (recipientWrap) recipientWrap.style.display = 'flex';
                if (recipientTitle) recipientTitle.style.display = 'block';
                if (selfWrap) selfWrap.style.display = 'none';
            }
        }
    });
    
    console.log('All functionality initialized');
});
</script>
@endsection
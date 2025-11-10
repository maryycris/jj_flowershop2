@extends('layouts.clerk_app')

@php
    // Extract customer name from notes if provided (same approach as admin)
    $customerName = '';
    if ($order->notes && str_contains($order->notes, 'Customer: ')) {
        $customerName = str_replace('Customer: ', '', $order->notes);
    }
    $catalogProducts = \App\Models\CatalogProduct::where('status', true)
        ->where('is_approved', true)
        ->orderBy('name')
        ->get();
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
<div class="container-fluid" style="margin-top: 1.3rem; padding-top: 1rem;">
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
                                <div class="nav nav-tabs border-0" role="tablist">
                                    <a href="{{ route('clerk.orders.create') }}" class="nav-link active bg-light" style="font-size: 0.9rem;">New</a>
                                    <button class="btn btn-outline-success btn-sm-custom ms-2" id="saveIconBtn" title="Save Sales Order">
                                        <i class="bi bi-download icon-sm"></i>
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#quotations" type="button" role="tab" style="font-size: 0.9rem;">
                                        Quotations
                                    </button>
                                </div>
                                <!-- SO Number (optional, appears after save) -->
                                <div class="mt-2" id="soNumberWrap" style="display:none;">
                                    <h5 class="mb-0 text-primary fw-bold" id="soNumberDisplay" style="font-size: 1rem;"></h5>
                                </div>
                            </div>
                            <!-- Workflow Indicator -->
                            <div class="ms-auto d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2" style="font-size: 0.8rem;">Quotation</span>
                                    <i class="bi bi-arrow-right text-muted icon-sm"></i>
                                    <span class="badge bg-secondary me-2" style="font-size: 0.8rem;">Quotation Sent</span>
                                    <i class="bi bi-arrow-right text-muted icon-sm"></i>
                                    <span class="badge bg-secondary" style="font-size: 0.8rem;">Sales Order</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm-custom" id="sendEmailBtn">
                                    <i class="bi bi-envelope icon-sm me-1"></i>Send by Email
                                </button>
                                <button class="btn btn-primary btn-sm-custom" id="confirmBtn">
                                    <i class="bi bi-check-circle icon-sm me-1"></i>CONFIRM
                                </button>
                                <button class="btn btn-outline-danger btn-sm-custom" id="cancelBtn">
                                    <i class="bi bi-x-circle icon-sm me-1"></i>Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="new" role="tabpanel">
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
                                                        <option value="">Choose time...</option>
                                                        <option value="08:00">8:00 AM - 9:00 AM</option>
                                                        <option value="09:00">9:00 AM - 10:00 AM</option>
                                                        <option value="10:00">10:00 AM - 11:00 AM</option>
                                                        <option value="11:00">11:00 AM - 12:00 PM</option>
                                                        <option value="12:00">12:00 PM - 1:00 PM</option>
                                                        <option value="13:00">1:00 PM - 2:00 PM</option>
                                                        <option value="14:00">2:00 PM - 3:00 PM</option>
                                                        <option value="15:00">3:00 PM - 4:00 PM</option>
                                                        <option value="16:00">4:00 PM - 5:00 PM</option>
                                                        <option value="17:00">5:00 PM - 6:00 PM</option>
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
                        <div class="tab-pane fade" id="quotations" role="tabpanel">
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
<!-- Product Search Modal for "Search More" -->
<div class="modal fade" id="productSearchModal" tabindex="-1" aria-labelledby="productSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productSearchModalLabel"><i class="bi bi-search me-2"></i>Search Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="productSearchInput" placeholder="Search by name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\CatalogProduct::distinct()->pluck('category') as $cat)
                                @if($cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row" id="productsGrid">
                    @foreach($catalogProducts as $cp)
                        @php
                            $availabilityService = new \App\Services\ProductAvailabilityService();
                            $availability = $availabilityService->checkCatalogProductAvailability($cp->id, 1);
                            $isOutOfStock = !($availability['can_fulfill'] ?? false);
                        @endphp
                        <div class="col-md-4 mb-3 product-card" data-name="{{ strtolower($cp->name) }}" data-category="{{ strtolower($cp->category ?? '') }}">
                            <div class="card h-100 product-item {{ $isOutOfStock ? 'out-of-stock' : '' }}" data-product-id="{{ $cp->id }}" data-product-name="{{ $cp->name }}" data-product-price="{{ $cp->price }}" data-product-description="{{ $cp->description }}">
                                <div class="card-img-top-container position-relative" style="height:200px;overflow:hidden;background:#f8f9fa;">
                                    @if($cp->image)
                                        <img src="{{ asset('storage/' . $cp->image) }}" class="card-img-top" alt="{{ $cp->name }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <i class="bi bi-image" style="font-size:3rem;"></i>
                                        </div>
                                    @endif
                                    @if($isOutOfStock)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7);">
                                            <div class="text-center text-white">
                                                <i class="bi bi-x-circle" style="font-size:2rem;"></i>
                                                <div class="fw-bold mt-2">OUT OF STOCK</div>
                                                <small class="text-light">Insufficient materials</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">{{ $cp->name }}</h6>
                                    <p class="card-text text-muted small">{{ $cp->description }}</p>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold text-success">₱{{ number_format($cp->price,2) }}</span>
                                        <span class="badge bg-secondary">{{ $cp->category }}</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    @if($isOutOfStock)
                                        <button class="btn btn-outline-danger btn-sm w-100" disabled><i class="bi bi-x-circle me-1"></i>Out of Stock</button>
                                    @else
                                        <button class="btn btn-success btn-sm w-100 select-product-btn"><i class="bi bi-plus-circle me-1"></i>Select Product</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="noResultsMessage" class="text-center py-5" style="display:none;">
                    <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                    <h5 class="text-muted mt-3">No products found</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helpers
function basePrefix(){
  // Detect '/admin' or '/clerk' and use it; default to 'clerk'
  try { const seg = (location.pathname.split('/')[1]||'').trim(); return seg || 'clerk'; } catch(_) { return 'clerk'; }
}

// Build a simple products map from backend data
const productsData = [
@foreach($products as $p)
  { id: {{ $p->id }}, name: @json($p->name), price: {{ (float)$p->price }}, description: @json($p->description ?? ''), category: @json($p->category ?? '') },
@endforeach
];

function populateProductSelect(selectEl){
  if(!selectEl) return;
  // Remove previous dynamic options except the placeholder
  while(selectEl.options.length > 1){ selectEl.remove(1); }
  // Add search-more sentinel like admin
  const sm = document.createElement('option');
  sm.value = 'search-more';
  sm.textContent = '🔍 Search More Products...';
  selectEl.appendChild(sm);
  productsData.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.name;
    opt.setAttribute('data-price', p.price);
    opt.setAttribute('data-description', p.description || '');
    selectEl.appendChild(opt);
  });
}

function addNewOrderLine(){
  const tbody = document.getElementById('orderLineBody');
  const newRow = document.createElement('tr');
  newRow.innerHTML = `
    <td style="padding: 0.4rem 0.75rem;">
      <select class="form-select form-select-sm product-select input-sm">
        <option value="">Select Product</option>
      </select>
    </td>
    <td style="padding: 0.4rem 0.75rem;"><input type="text" class="form-control form-control-sm description-input input-sm" placeholder="Enter description"></td>
    <td style="padding: 0.4rem 0.75rem;"><input type="number" class="form-control form-control-sm quantity-input input-sm" value="1" min="1"></td>
    <td style="padding: 0.4rem 0.75rem;">
      <select class="form-select form-select-sm uom-select input-sm">
        <option value="pcs" selected>PCS</option>
        <option value="dozen">Dozen</option>
        <option value="box">Box</option>
        <option value="kg">KG</option>
        <option value="g">G</option>
      </select>
    </td>
    <td style="padding: 0.4rem 0.75rem;"><input type="number" class="form-control form-control-sm unit-price-input input-sm" value="0" min="0" step="0.01"></td>
    <td style="padding: 0.4rem 0.75rem;"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOrderLine(this)" style="padding: 0.25rem 0.5rem;"><i class="bi bi-trash icon-sm"></i></button></td>
  `;
  tbody.appendChild(newRow);
  // populate and attach change handler
  const selectEl = newRow.querySelector('.product-select');
  populateProductSelect(selectEl);
  selectEl.addEventListener('change', onProductChange);
  calculateTotal();
}

function removeOrderLine(button){ button.closest('tr').remove(); calculateTotal(); }

function calculateTotal(){
  let total = 0; document.querySelectorAll('#orderLineBody tr').forEach(r=>{ const q=parseFloat(r.querySelector('.quantity-input')?.value||0); const p=parseFloat(r.querySelector('.unit-price-input')?.value||0); total+=q*p; });
  document.getElementById('totalPrice').textContent = total.toFixed(2);
}

function validateForm(){
  const cn=document.getElementById('customerName').value.trim();
  const ia=document.getElementById('invoiceAddress').value.trim();
  const isPickup=document.getElementById('pickupForm').style.display==='block';
  const da=document.getElementById('deliveryAddress').value.trim();
  if(!cn){ alert('Customer name is required'); return false; }
  if(!ia){ alert('Delivery address is required'); return false; }
  if(!isPickup && !da){ alert('Email address is required'); return false; }
  if(document.querySelectorAll('#orderLineBody tr').length===0){ alert('Please add at least one order line'); return false; }
  return true;
}

function collectFormData(){
  const isPickup = document.getElementById('pickupForm').style.display==='block';
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
    orderLines: Array.from(document.querySelectorAll('#orderLineBody tr')).map(r=>({
      product: r.querySelector('.product-select')?.options[r.querySelector('.product-select')?.selectedIndex||0]?.textContent || '',
      description: r.querySelector('.description-input')?.value || '',
      quantity: r.querySelector('.quantity-input')?.value || 1,
      uom: r.querySelector('.uom-select')?.value || '',
      unit_price: r.querySelector('.unit-price-input')?.value || 0
    }))
  };
}

async function saveOrderData(){
  const formData = collectFormData();
  const orderId = (location.pathname.split('/')[3]||'');
  const payload = {
    customer_name: formData.customerName,
    invoice_address: formData.invoiceAddress,
    delivery_address: formData.deliveryAddress,
    order_date: formData.orderDate,
    delivery_method: formData.deliveryMethod,
    pickup_data: formData.pickupData,
    delivery_data: formData.deliveryData,
    order_lines: formData.orderLines,
    total_amount: (()=>{let t=0;formData.orderLines.forEach(i=>{t+=parseFloat(i.quantity||0)*parseFloat(i.unit_price||0)});return t.toFixed(2)})()
  };

  const url = `/${basePrefix()}/orders/${orderId}/walkin/update-invoice`;
  const res = await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},body:JSON.stringify(payload)});
  const data = await res.json();
  if(!res.ok || data?.success===false){ throw new Error(data?.message||'Failed to save order'); }
  return data;
}

function sendInvoiceByEmail(){ alert('Email send to be implemented.'); }

async function createInvoice(){
  if(!validateForm()) return;
  try{
    const resp = await saveOrderData();
    if(resp?.so_number){
      document.getElementById('soNumberDisplay').textContent = resp.so_number;
      document.getElementById('soNumberWrap').style.display = '';
    }
    alert('Sales Order saved successfully');
  }catch(e){ alert('Error saving: '+e.message); }
}

async function confirmInvoice(){
  if(!validateForm()) return;
  const formData = collectFormData();
  Swal.fire({
    title: 'Confirm Sales Order?',
    html: `
      <div class="text-start">
        <p><strong>Customer Name:</strong> ${formData.customerName || 'Not specified'}</p>
        <p><strong>Delivery Address:</strong> ${formData.invoiceAddress || 'Not specified'}</p>
        <p><strong>Email Address:</strong> ${formData.deliveryAddress || 'Not specified'}</p>
        <p><strong>Order Date:</strong> ${formData.orderDate || 'Not specified'}</p>
        <p><strong>Total Items:</strong> ${formData.orderLines.length}</p>
        <p><strong>Total Amount:</strong> ₱${(()=>{let t=0;formData.orderLines.forEach(i=>{t+=parseFloat(i.quantity||0)*parseFloat(i.unit_price||0)});return t.toFixed(2)})()}</p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Confirm Sales Order',
    cancelButtonText: 'No, Review Again',
    confirmButtonColor: '#0d6efd'
  }).then(async (result)=>{
    if(result.isConfirmed){
      try{
        const resp = await saveOrderData();
        Swal.fire({icon:'success', title:'Sales Order confirmed!', timer:1000, showConfirmButton:false});
        // Redirect to Sales Order view similar to admin
        setTimeout(()=>{ location.href = `/${basePrefix()}/sales-orders/${resp.order_id}`; }, 1000);
      }catch(e){
        Swal.fire({icon:'error', title:'Error confirming', text:e.message});
      }
    }
  });
}

function selectDeliveryMethod(method){
  const pickupBtn=document.getElementById('pickupBtn');
  const deliveryBtn=document.getElementById('deliveryBtn');
  const pickupForm=document.getElementById('pickupForm');
  const deliveryForm=document.getElementById('deliveryForm');
  if(method==='pickup'){
    pickupBtn.classList.add('btn-warning'); pickupBtn.classList.remove('btn-outline-secondary');
    deliveryBtn.classList.remove('btn-warning'); deliveryBtn.classList.add('btn-outline-secondary');
    pickupForm.style.display='block'; deliveryForm.style.display='none';
  } else {
    deliveryBtn.classList.add('btn-warning'); deliveryBtn.classList.remove('btn-outline-secondary');
    pickupBtn.classList.remove('btn-warning'); pickupBtn.classList.add('btn-outline-secondary');
    deliveryForm.style.display='block'; pickupForm.style.display='none';
  }
}

// Init
document.addEventListener('DOMContentLoaded', ()=>{
  document.getElementById('addOrderBtn')?.addEventListener('click', addNewOrderLine);
  document.getElementById('saveIconBtn')?.addEventListener('click', createInvoice);
  document.getElementById('sendEmailBtn')?.addEventListener('click', sendInvoiceByEmail);
  document.getElementById('confirmBtn')?.addEventListener('click', confirmInvoice);
  document.getElementById('cancelBtn')?.addEventListener('click', ()=>history.back());
  document.getElementById('pickupBtn')?.addEventListener('click', ()=>selectDeliveryMethod('pickup'));
  document.getElementById('deliveryBtn')?.addEventListener('click', ()=>selectDeliveryMethod('delivery'));
  const today = new Date().toISOString().split('T')[0];
  const pickupDate=document.getElementById('pickupDate'); if(pickupDate) pickupDate.value=today;
  // Populate any existing product selects on load
  document.querySelectorAll('.product-select').forEach(sel => {
    populateProductSelect(sel);
    sel.addEventListener('change', onProductChange);
  });
  document.getElementById('productSearchInput')?.addEventListener('input', filterProducts);
  document.getElementById('categoryFilter')?.addEventListener('change', filterProducts);
  // Toggle recipient type: someone vs self
  document.querySelectorAll('.recipient-type-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      document.querySelectorAll('.recipient-type-btn').forEach(b=>{ b.classList.remove('btn-success','active'); b.classList.add('btn-outline-success'); });
      this.classList.remove('btn-outline-success');
      this.classList.add('btn-success','active');
      const type = this.getAttribute('data-type');
      const nameField = document.getElementById('deliveryRecipientName');
      const relationField = document.getElementById('deliveryRelationship');
      const recipientWrap = document.getElementById('recipientInfoSection');
      const recipientTitle = document.getElementById('recipientInfoTitle');
      const selfWrap = document.getElementById('selfContactSection');
      if(type === 'self'){
        const customerName = document.getElementById('customerName')?.value || '';
        if(nameField){ nameField.value = customerName; nameField.readOnly = true; }
        if(relationField){ relationField.value = 'self'; relationField.disabled = true; }
        if(recipientWrap){ recipientWrap.style.display = 'none'; }
        if(recipientTitle){ recipientTitle.style.display = 'none'; }
        if(selfWrap){ selfWrap.style.display = 'block'; }
      } else {
        if(nameField){ nameField.readOnly = false; nameField.value=''; nameField.placeholder = "Enter Recipient's Full Name"; }
        if(relationField){ relationField.disabled = false; relationField.value=''; }
        if(recipientWrap){ recipientWrap.style.display = 'flex'; }
        if(recipientTitle){ recipientTitle.style.display = 'block'; }
        if(selfWrap){ selfWrap.style.display = 'none'; }
      }
    });
  });
  document.addEventListener('click', function(e){
    if(e.target.classList.contains('select-product-btn')){
      const card = e.target.closest('.product-item');
      const productId = card.getAttribute('data-product-id');
      const productName = card.getAttribute('data-product-name');
      const productPrice = card.getAttribute('data-product-price');
      const productDescription = card.getAttribute('data-product-description');
      if(window.currentProductSelect){
        const newOption = document.createElement('option');
        newOption.value = productId;
        newOption.setAttribute('data-price', productPrice);
        newOption.setAttribute('data-description', productDescription);
        newOption.textContent = productName;
        const smOpt = window.currentProductSelect.querySelector('option[value="search-more"]');
        smOpt.insertAdjacentElement('afterend', newOption);
        newOption.selected = true;
        const row = window.currentProductSelect.closest('tr');
        row.querySelector('.unit-price-input').value = parseFloat(productPrice).toFixed(2);
        row.querySelector('.description-input').value = productDescription || '';
        calculateTotal();
        const modal = bootstrap.Modal.getInstance(document.getElementById('productSearchModal'));
        modal?.hide();
        window.currentProductSelect = null;
      }
    }
  });
});

function onProductChange(e){
  const sel = e.target; const row = sel.closest('tr');
  const opt = sel.options[sel.selectedIndex];
  if(!opt || !row) return;
  if(opt.value === 'search-more'){
    window.currentProductSelect = sel;
    const modal = new bootstrap.Modal(document.getElementById('productSearchModal'));
    modal.show();
    sel.selectedIndex = 0;
    return;
  }
  const price = opt.getAttribute('data-price');
  const desc = opt.getAttribute('data-description') || '';
  const priceInput = row.querySelector('.unit-price-input');
  const descInput = row.querySelector('.description-input');
  if(priceInput && price){ priceInput.value = parseFloat(price).toFixed(2); }
  if(descInput){ descInput.value = desc; }
  calculateTotal();
}

function filterProducts(){
  const term = (document.getElementById('productSearchInput')?.value || '').toLowerCase();
  const cat = (document.getElementById('categoryFilter')?.value || '').toLowerCase();
  let visible = 0;
  document.querySelectorAll('#productsGrid .product-card').forEach(card => {
    const name = card.getAttribute('data-name') || '';
    const category = card.getAttribute('data-category') || '';
    const show = (!term || name.includes(term)) && (!cat || category.includes(cat));
    card.style.display = show ? 'block' : 'none';
    if(show) visible++;
  });
  const msg = document.getElementById('noResultsMessage');
  if(msg) msg.style.display = visible ? 'none' : 'block';
}
</script>
@endpush

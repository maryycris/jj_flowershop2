@extends('layouts.admin_app')

@section('content')
<style>
    /* Font and Icon Hierarchy */
    .page-title {
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
    
    .info-text {
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
    
    .badge-custom {
        font-size: 0.8rem;
    }
</style>
<div class="container-fluid" style="margin-top: 2rem; padding-top: 0.5rem;">
    @php
        $panel = request()->is('clerk/*') ? 'clerk' : 'admin';
    @endphp
    
    <!-- Consolidated Card: Everything in One Box -->
    <div class="card shadow">
        <div class="card-header" style="background-color: white; border-bottom: 1px solid #d9ecd9;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm-custom me-3">
                        <i class="bi bi-arrow-left icon-sm"></i> Back
                    </a>
                    <a href="{{ route($panel . '.orders.create') }}" class="btn btn-outline-primary btn-sm-custom me-3">New</a>
                    <h5 class="page-title mb-0 text-gray-800" style="margin: 0;">Sales Order {{ $salesOrder ? $salesOrder->so_number : 'SO-XXXXX' }}</h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route($panel . '.orders.walkin.validate', $order->id) }}?view=moves" class="d-flex flex-column align-items-center text-decoration-none" style="cursor: pointer;" title="Click to view delivery moves" id="truckIconLinkStatic">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-truck text-primary icon-md"></i>
                            <span class="badge bg-primary rounded-circle badge-custom">1</span>
                        </div>
                        <small class="text-primary mt-1" style="font-size: 0.75rem;">Delivery</small>
                    </a>
                    <span class="badge bg-success px-3 py-2 badge-custom" style="border-radius: 0 15px 15px 0;">Sales Order</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2 mt-3">
                <form method="POST" action="{{ route($panel . '.orders.invoice.create', $order->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm-custom">
                        <i class="bi bi-file-earmark-text icon-sm me-1"></i>Create Invoice
                    </button>
                </form>
                <button class="btn btn-outline-secondary btn-sm-custom">
                    <i class="bi bi-envelope icon-sm me-1"></i>Send by Email
                </button>
                <button class="btn btn-outline-danger btn-sm-custom">
                    <i class="bi bi-x-circle icon-sm me-1"></i>Cancel
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Customer Information Section -->
            <div class="row g-0 border-bottom" style="border-color: #d9ecd9 !important;">
                <!-- Customer Column -->
                <div class="col-md-2" style="background-color: white; border-right: 1px solid #d9ecd9;">
                    <div class="p-3 section-header" style="background-color: #e6f5e6; border-bottom: 1px solid #d9ecd9;">Customer</div>
                    <div class="p-3">
                        <div class="fw-semibold info-text">{{ $order->user->name }}</div>
                        <div class="text-muted" style="font-size: 0.8rem;">{{ $order->user->contact_number ?? 'N/A' }}</div>
                    </div>
                </div>
                
                <!-- Email Address Column -->
                <div class="col-md-3" style="background-color: white; border-right: 1px solid #d9ecd9;">
                    <div class="p-3 section-header" style="background-color: #e6f5e6; border-bottom: 1px solid #d9ecd9;">Email Address</div>
                    <div class="p-3">
                        @php
                            $email = $order->user->email ?? null;
                            if (!empty($order->notes) && preg_match('/Email:\s*([^; ,\n\r]+@[^; ,\n\r]+)/', $order->notes, $m)) {
                                $email = trim($m[1]);
                            }
                        @endphp
                        <div class="info-text">{{ $email ?? 'N/A' }}</div>
                    </div>
                </div>
                
                <!-- Delivery Address Column -->
                <div class="col-md-3" style="background-color: white; border-right: 1px solid #d9ecd9;">
                    <div class="p-3 section-header" style="background-color: #e6f5e6; border-bottom: 1px solid #d9ecd9;">Delivery Address</div>
                    <div class="p-3">
                        @if($order->delivery)
                            <div class="info-text">{{ $order->delivery->delivery_address }}</div>
                        @else
                            <div class="info-text">Pick up at store</div>
                        @endif
                    </div>
                </div>
                
                <!-- Order Date Column -->
                <div class="col-md-2" style="background-color: white; border-right: 1px solid #d9ecd9;">
                    <div class="p-3 section-header" style="background-color: #e6f5e6; border-bottom: 1px solid #d9ecd9;">Order Date</div>
                    <div class="p-3">
                        <div class="info-text">{{ $order->created_at->format('m/d/Y') }}</div>
                    </div>
                </div>
                
                <!-- Price List Column -->
                <div class="col-md-2" style="background-color: white;">
                    <div class="p-3 section-header" style="background-color: #e6f5e6; border-bottom: 1px solid #d9ecd9;">Price List</div>
                    <div class="p-3">
                        <div class="info-text">Standard</div>
                    </div>
                </div>
            </div>
            
            <!-- Order Line Section -->
            <div>
                <div class="p-3 section-header" style="background-color: #e6f5e6; border-bottom: 1px solid #d9ecd9;">
                    Order Line
                </div>
                
                <div class="table-responsive">
                    <table class="table mb-0 table-body">
                        <thead style="background-color: white;">
                            <tr>
                                <th class="table-header" style="width:20%; padding: 0.5rem 0.75rem; border-right: 1px solid #d9ecd9;">Product</th>
                                <th class="table-header" style="width:25%; padding: 0.5rem 0.75rem; border-right: 1px solid #d9ecd9;">Description</th>
                                <th class="table-header" style="width:10%; padding: 0.5rem 0.75rem; border-right: 1px solid #d9ecd9;">Quantity</th>
                                <th class="table-header" style="width:10%; padding: 0.5rem 0.75rem; border-right: 1px solid #d9ecd9;">UoM</th>
                                <th class="table-header" style="width:15%; padding: 0.5rem 0.75rem;">Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->products as $product)
                                <tr>
                                    <td class="info-text" style="padding: 0.4rem 0.75rem; border-right: 1px solid #d9ecd9;">{{ $product->name }}</td>
                                    <td class="info-text" style="padding: 0.4rem 0.75rem; border-right: 1px solid #d9ecd9;">{{ $product->description }}</td>
                                    <td class="info-text" style="padding: 0.4rem 0.75rem; border-right: 1px solid #d9ecd9;">{{ $product->pivot->quantity }}</td>
                                    <td class="info-text" style="padding: 0.4rem 0.75rem; border-right: 1px solid #d9ecd9;">PCS</td>
                                    <td class="info-text" style="padding: 0.4rem 0.75rem;">₱{{ number_format($product->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
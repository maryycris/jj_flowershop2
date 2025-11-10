@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid" style="background:#f4faf4;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-sm" style="border:0;">
                <div class="card-body p-0" style="background:#fff;">
                    <div class="p-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #e6f0e6;">
                        <a href="{{ route('clerk.orders.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Back" aria-label="Back" style="width:34px;height:34px;padding:0;">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div class="ms-2 small text-muted">Order Number</div>
                        <div class="fw-semibold">00{{ $order->id }}</div>
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-truck text-dark" style="font-size: 24px;"></i>
                                <small class="text-muted">Deliver</small>
                                <span class="badge bg-primary">1</span>
                            </div>
                            <a href="{{ route('clerk.orders.online.validate', $order->id) }}" class="btn btn-sm btn-success">Validate Order</a>
                            <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                        </div>
                    </div>

                    <div class="px-3 pt-2 pb-3">
                        <div class="row g-0 border" style="border-color:#d9ecd9 !important;">
                            <div class="col-md-4" style="background:#e6f5e6;">
                                <div class="p-3 fw-semibold" style="border-bottom:1px solid #d9ecd9;">Customer</div>
                                <div class="p-3 small">
                                    <div class="fw-semibold mb-1">{{ $order->user->name ?? 'Customer' }}</div>
                                    @if($order->delivery)
                                        <div>{{ $order->delivery->delivery_address }}</div>
                                        <div>{{ $order->delivery->recipient_phone }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="p-3 fw-semibold" style="background:#e6f5e6;border-bottom:1px solid #d9ecd9;">Delivery Address</div>
                                <div class="p-3 small">
                                    @if($order->delivery)
                                        <div class="mb-1">{{ $order->delivery->delivery_address }}</div>
                                    @else
                                        <div class="text-muted">No delivery address</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 fw-semibold" style="background:#e6f5e6;border-bottom:1px solid #d9ecd9;">Order Date</div>
                                <div class="p-3 small">{{ $order->created_at->format('m/d/Y') }}</div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Order Line</div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th style="width:35%">Product</th>
                                            <th style="width:35%">Description</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:15%">Unit Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->description }}</td>
                                                <td>{{ $product->pivot->quantity }}</td>
                                                <td>₱{{ number_format($product->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach($order->customBouquets as $bouquet)
                                            @php
                                                $orderQty = $bouquet->pivot->quantity;
                                                $customData = $bouquet->customization_data ?? [];
                                                $freshFlowerQty = $customData['freshFlowerQuantity'] ?? 1;
                                                $artificialFlowerQty = $customData['artificialFlowerQuantity'] ?? 1;
                                                
                                                $components = [];
                                                
                                                // Wrapper
                                                if ($bouquet->wrapper) {
                                                    $components[] = "Wrapper: {$bouquet->wrapper} (x{$orderQty})";
                                                }
                                                
                                                // Fresh Flowers
                                                $freshFlowers = [];
                                                if ($bouquet->focal_flower_1) {
                                                    $freshFlowers[] = $bouquet->focal_flower_1;
                                                }
                                                if ($bouquet->focal_flower_2) {
                                                    $freshFlowers[] = $bouquet->focal_flower_2;
                                                }
                                                if ($bouquet->focal_flower_3) {
                                                    $freshFlowers[] = $bouquet->focal_flower_3;
                                                }
                                                if (!empty($freshFlowers)) {
                                                    $totalFreshQty = $freshFlowerQty * $orderQty;
                                                    $components[] = "Fresh Flowers: " . implode(', ', $freshFlowers) . " (x{$totalFreshQty})";
                                                }
                                                
                                                // Greenery
                                                if ($bouquet->greenery) {
                                                    $components[] = "Greenery: {$bouquet->greenery} (x{$orderQty})";
                                                }
                                                
                                                // Artificial Flowers (Filler)
                                                if ($bouquet->filler) {
                                                    $totalArtificialQty = $artificialFlowerQty * $orderQty;
                                                    $components[] = "Artificial Flowers: {$bouquet->filler} (x{$totalArtificialQty})";
                                                }
                                                
                                                // Ribbon
                                                if ($bouquet->ribbon) {
                                                    $components[] = "Ribbon: {$bouquet->ribbon} (x{$orderQty})";
                                                }
                                                
                                                $componentDescription = !empty($components) ? implode('<br>', $components) : '';
                                                $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($orderQty, 1));
                                            @endphp
                                            <tr>
                                                <td>Custom Bouquet</td>
                                                <td>
                                                    <div style="font-size: 0.85rem; line-height: 1.6;">
                                                        {!! $componentDescription !!}
                                                    </div>
                                                </td>
                                                <td>{{ $orderQty }}</td>
                                                <td>₱{{ number_format($unitPrice, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Order Summary with Shipping Fee Breakdown -->
                            <div class="mt-3 p-3" style="background:#f8f9fa;border:1px solid #d9ecd9;border-top:0;">
                                @php
                                    $productsSubtotal = $order->products->sum(function($product) {
                                        return $product->pivot->quantity * $product->price;
                                    });
                                    
                                    $customBouquetsSubtotal = $order->customBouquets->sum(function($bouquet) {
                                        $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($bouquet->pivot->quantity, 1));
                                        return $unitPrice * $bouquet->pivot->quantity;
                                    });
                                    
                                    $subtotal = $productsSubtotal + $customBouquetsSubtotal;
                                    $shippingFee = $order->delivery->shipping_fee ?? 0;
                                    
                                    // If shipping_fee is 0 or null, calculate it from the difference between total_price and subtotal
                                    if ($shippingFee == 0 && $order->total_price > $subtotal) {
                                        $shippingFee = $order->total_price - $subtotal;
                                    }
                                    
                                    $total = $subtotal + $shippingFee;
                                @endphp
                                
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Subtotal:</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <strong>₱{{ number_format($subtotal, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Shipping Fee:</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <strong>₱{{ number_format($shippingFee, 2) }}</strong>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="mb-0">Total:</h5>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h5 class="mb-0">₱{{ number_format($total, 2) }}</h5>
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
@endsection



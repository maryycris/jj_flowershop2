@extends('layouts.admin_app')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Order Invoice</h2>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>JJ Flower Shop</h4>
                    <p class="mb-0">123 Flower St, Blossom City</p>
                    <p>contact@jjflowershop.com</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h3>Invoice #{{ $order->id }}</h3>
                    <p class="mb-0"><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Bill To:</h5>
                    @php
                        // For walk-in orders, extract customer name from notes
                        $customerName = $order->user->name ?? 'N/A';
                        $customerEmail = $order->user->email ?? 'N/A';
                        $customerPhone = $order->user->contact_number ?? 'N/A';
                        
                        if ($order->type === 'walk-in') {
                            // Extract customer name from notes (format: "Customer: [Name]")
                            if ($order->notes && preg_match('/Customer:\s*(.+)/', $order->notes, $matches)) {
                                $customerName = trim($matches[1]);
                            } else {
                                $customerName = 'Walk-in Customer';
                            }
                            
                            // For walk-in orders, email and phone are not available
                            $customerEmail = 'N/A';
                            $customerPhone = 'N/A';
                        }
                    @endphp
                    <p class="mb-0">{{ $customerName }}</p>
                    <p class="mb-0">{{ $customerEmail }}</p>
                    <p>{{ $customerPhone }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Ship To:</h5>
                    <p class="mb-0">{{ $order->delivery->recipient_name ?? $order->user->name ?? 'N/A' }}</p>
                    <p>{{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowIndex = 0; @endphp
                    @foreach($order->products as $product)
                    @php $rowIndex++; @endphp
                    <tr>
                        <td>{{ $rowIndex }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="text-end">{{ $product->pivot->quantity }}</td>
                        <td class="text-end">₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                    </tr>
                    @endforeach
                    @foreach($order->customBouquets as $bouquet)
                    @php 
                        $rowIndex++;
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
                        <td>{{ $rowIndex }}</td>
                        <td>
                            <div><strong>Custom Bouquet</strong></div>
                            @if(!empty($componentDescription))
                                <div style="font-size: 0.85rem; color: #666; margin-top: 4px; line-height: 1.6;">
                                    {!! $componentDescription !!}
                                </div>
                            @endif
                        </td>
                        <td class="text-end">{{ $orderQty }}</td>
                        <td class="text-end">₱{{ number_format($unitPrice, 2) }}</td>
                        <td class="text-end">₱{{ number_format($unitPrice * $orderQty, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
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
                    <tr>
                        <td colspan="4" class="text-end border-0"><strong>Subtotal:</strong></td>
                        <td class="text-end border-0">₱{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @if(strtolower($order->status) !== 'picked_up')
                    <tr>
                        <td colspan="4" class="text-end border-0"><strong>Shipping:</strong></td>
                        <td class="text-end border-0">₱{{ number_format($shippingFee, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="4" class="text-end"><h4>Total:</h4></td>
                        <td class="text-end"><h4>₱{{ number_format($total, 2) }}</h4></td>
                    </tr>
                    @else
                    <tr class="table-light">
                        <td colspan="4" class="text-end"><h4>Total:</h4></td>
                        <td class="text-end"><h4>₱{{ number_format($total, 2) }}</h4></td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection 
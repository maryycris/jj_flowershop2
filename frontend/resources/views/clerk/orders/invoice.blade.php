@extends('layouts.clerk_app')

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
                    <p><strong>Status:</strong> 
                        @php
                            $statusClass = 'bg-warning text-dark';
                            $statusText = ucfirst($order->status);
                            
                            if ($order->order_status) {
                                switch($order->order_status) {
                                    case 'approved':
                                        $statusClass = 'bg-success';
                                        $statusText = 'Approved';
                                        break;
                                    case 'on_delivery':
                                        $statusClass = 'bg-info';
                                        $statusText = 'On Delivery';
                                        break;
                                    case 'completed':
                                        $statusClass = 'bg-primary';
                                        $statusText = 'Completed';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Cancelled';
                                        break;
                                    default:
                                        $statusText = ucfirst($order->order_status);
                                        break;
                                }
                            }
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                    </p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Bill To:</h5>
                    <p class="mb-0">{{ $order->user->name ?? 'N/A' }}</p>
                    <p class="mb-0">{{ $order->user->email ?? 'N/A' }}</p>
                    <p>{{ $order->user->contact_number ?? 'N/A' }}</p>
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
                    @foreach($order->products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="text-end">{{ $product->pivot->quantity }}</td>
                        <td class="text-end">₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background-color: #f8f9fa;">
                    @php
                        $subtotal = $order->products->sum(function($product) {
                            return $product->pivot->quantity * $product->price;
                        });
                        $shippingFee = $order->delivery->shipping_fee ?? 0;
                        
                        // If shipping_fee is 0 or null, calculate it from the difference between total_price and subtotal
                        if ($shippingFee == 0 && $order->total_price > $subtotal) {
                            $shippingFee = $order->total_price - $subtotal;
                        }
                        
                        $total = $subtotal + $shippingFee;
                        
                        // Debug information
                        \Log::info('Invoice Debug', [
                            'order_id' => $order->id,
                            'subtotal' => $subtotal,
                            'shipping_fee' => $shippingFee,
                            'order_total_price' => $order->total_price,
                            'calculated_total' => $total,
                            'delivery_exists' => $order->delivery ? 'yes' : 'no',
                            'delivery_shipping_fee' => $order->delivery ? $order->delivery->shipping_fee : 'no delivery'
                        ]);
                    @endphp
                    <tr style="border-top: 2px solid #dee2e6;">
                        <td colspan="4" class="text-end border-0"><strong>Subtotal:</strong></td>
                        <td class="text-end border-0"><strong>₱{{ number_format($subtotal, 2) }}</strong></td>
                    </tr>
                    <tr style="border-top: 1px solid #dee2e6;">
                        <td colspan="4" class="text-end border-0"><strong>Shipping Fee:</strong></td>
                        <td class="text-end border-0"><strong>₱{{ number_format($shippingFee, 2) }}</strong></td>
                    </tr>
                    <tr class="table-light" style="border-top: 2px solid #dee2e6; background-color: #e9ecef !important;">
                        <td colspan="4" class="text-end"><h4 class="mb-0">Total:</h4></td>
                        <td class="text-end"><h4 class="mb-0">₱{{ number_format($total, 2) }}</h4></td>
                    </tr>
                    <!-- Debug information -->
                    <tr style="background-color: #f8f9fa; font-size: 0.8rem; border-top: 1px solid #dee2e6;">
                        <td colspan="5" class="text-center text-muted">
                            Debug: Subtotal=₱{{ number_format($subtotal, 2) }}, Shipping=₱{{ number_format($shippingFee, 2) }}, Order Total=₱{{ number_format($order->total_price, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Shipping Fee Breakdown - Alternative Display -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @php
                                $subtotal = $order->products->sum(function($product) {
                                    return $product->pivot->quantity * $product->price;
                                });
                                $shippingFee = $order->delivery->shipping_fee ?? 0;
                                
                                // If shipping_fee is 0 or null, calculate it from the difference between total_price and subtotal
                                if ($shippingFee == 0 && $order->total_price > $subtotal) {
                                    $shippingFee = $order->total_price - $subtotal;
                                }
                                
                                $total = $subtotal + $shippingFee;
                            @endphp
                            <h5 class="card-title">Order Summary</h5>
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
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <h4>Total:</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <h4>₱{{ number_format($total, 2) }}</h4>
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

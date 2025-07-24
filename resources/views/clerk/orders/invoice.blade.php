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
                    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
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
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end border-0"><strong>Subtotal:</strong></td>
                        <td class="text-end border-0">₱{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end border-0"><strong>Shipping:</strong></td>
                        <td class="text-end border-0">₱0.00</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="4" class="text-end"><h4>Total:</h4></td>
                        <td class="text-end"><h4>₱{{ number_format($order->total_price, 2) }}</h4></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Order Details #{{ $order->id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">Customer: {{ $order->user->name ?? 'N/A' }}</h5>
            <p class="card-text"><strong>Product:</strong> {{ $order->product->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Quantity:</strong> {{ $order->quantity }}</p>
            <p class="card-text"><strong>Status:</strong> <span class="badge {{ $order->status === 'pending' ? 'bg-warning' : ($order->status === 'completed' ? 'bg-success' : 'bg-info') }}">{{ ucfirst($order->status) }}</span></p>
            <p class="card-text"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i A') }}</p>
            <p class="card-text"><strong>Last Updated:</strong> {{ $order->updated_at->format('M d, Y H:i A') }}</p>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning">Edit Order</a>
        </div>
    </div>
</div>
@endsection 